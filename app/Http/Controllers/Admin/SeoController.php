<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSeoRequest;
use App\Models\Media;
use App\Models\Page;
use App\Models\SeoSetting;
use App\Services\MediaService;
use App\Services\SeoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

/**
 * What each page tells search engines and social networks.
 *
 * The wording is per language; the share image and the indexing switch are
 * shared by both. Nothing here touches page content — that stays in the content
 * editor — and the canonical URL is never stored, because routing already knows
 * where a page lives.
 */
class SeoController extends Controller
{
    public function __construct(
        private readonly SeoService $seo,
        private readonly MediaService $media,
    ) {}

    public function index(): View
    {
        return view('admin.seo.index', [
            'pages' => $this->seo->editablePages(),
            'seo' => $this->seo,
        ]);
    }

    public function edit(Page $page): View
    {
        $setting = $this->settingFor($page);

        return view('admin.seo.edit', [
            'page' => $page,
            'setting' => $setting,
            'translations' => $setting->translations->keyBy('locale'),
        ]);
    }

    public function update(UpdateSeoRequest $request, Page $page): RedirectResponse
    {
        $setting = $this->settingFor($page);
        $data = $request->validated();
        $storedFile = null;

        try {
            DB::transaction(function () use ($setting, $data, $request, &$storedFile) {
                foreach (config('locales.supported') as $locale) {
                    /* One row per locale: updated in place, never appended. */
                    $setting->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'meta_title' => $data['seo'][$locale]['meta_title'] ?? null,
                            'meta_description' => $data['seo'][$locale]['meta_description'] ?? null,
                            'og_title' => $data['seo'][$locale]['og_title'] ?? null,
                            'og_description' => $data['seo'][$locale]['og_description'] ?? null,
                        ]
                    );
                }

                $setting->update([
                    'og_media_id' => $this->ogMediaId($setting, $data, $request->file('media_og_image'), $request->user()->id, $storedFile),
                    'is_indexable' => (bool) $data['is_indexable'],
                ]);
            });
        } catch (Throwable) {
            /* The rollback took the media row with it, so its file goes too. */
            if ($storedFile !== null) {
                Storage::disk($storedFile[0])->delete($storedFile[1]);
            }

            return back()->withInput()->with('error', 'Unable to save these SEO settings. No changes were made.');
        }

        return redirect()
            ->route('admin.seo.edit', $page)
            ->with('success', 'SEO settings updated successfully.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array{0: string, 1: string}|null  $storedFile
     */
    private function ogMediaId(SeoSetting $setting, array $data, ?UploadedFile $file, ?int $userId, ?array &$storedFile): ?int
    {
        if ((bool) ($data['media_og_image_remove'] ?? false)) {
            /* Detaches the image only; the file stays in the library. */
            return null;
        }

        if ($file instanceof UploadedFile) {
            $uploaded = $this->media->store($file, $userId);
            $storedFile = [$uploaded->disk, $uploaded->path];

            return $uploaded->id;
        }

        $mediaId = $data['media_og_image_media_id'] ?? null;

        if ($mediaId && Media::whereKey($mediaId)->exists()) {
            return (int) $mediaId;
        }

        return $setting->og_media_id;
    }

    /**
     * The record for a page that the site actually publishes. A page the
     * definition does not describe has no SEO screen.
     */
    private function settingFor(Page $page): SeoSetting
    {
        abort_unless(config("page_content.pages.{$page->key}") !== null, 404);

        $setting = SeoSetting::firstOrCreate(['page_id' => $page->id], ['is_indexable' => true]);

        return $setting->load(['translations', 'ogMedia']);
    }
}
