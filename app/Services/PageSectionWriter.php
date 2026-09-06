<?php

namespace App\Services;

use App\Models\Media;
use App\Models\PageSection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Saves one section: both translations, the shared settings, the images and the
 * status, in a single transaction. Reusable by every page — what a section may
 * contain comes from its definition, not from this class.
 */
class PageSectionWriter
{
    /**
     * Files written during this save, so they can be cleared away if the
     * transaction is rolled back and their rows disappear with it.
     *
     * @var array<int, array{0: string, 1: string}>
     */
    private array $storedFiles = [];

    public function __construct(private readonly MediaService $media) {}

    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $data  validated input
     * @param  array<string, UploadedFile|null>  $files  slot => uploaded file
     */
    public function save(PageSection $section, array $definition, array $data, array $files = [], ?int $userId = null): void
    {
        $this->storedFiles = [];

        try {
            DB::transaction(function () use ($section, $definition, $data, $files, $userId) {
                $this->saveTranslations($section, $definition, $data);
                $this->saveMedia($section, $definition, $data, $files, $userId);

                $section->fill([
                    'settings' => $this->settings($section, $definition, $data),
                    'is_active' => (bool) ($data['is_active'] ?? true),
                ])->save();
            });
        } catch (Throwable $e) {
            /* The rollback took the media rows with it, so the files they
               pointed at must go too rather than linger unreferenced. */
            foreach ($this->storedFiles as [$disk, $path]) {
                Storage::disk($disk)->delete($path);
            }

            throw $e;
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $data
     */
    private function saveTranslations(PageSection $section, array $definition, array $data): void
    {
        foreach (config('locales.supported') as $locale) {
            $translation = $section->translations()->firstOrNew(['locale' => $locale]);

            /* Only the defined fields are written, and they are written into a
               copy of what is already stored — so a key this editor does not
               know about is preserved rather than dropped. */
            $content = $translation->content ?? [];

            foreach (array_keys(Arr::get($definition, 'fields', [])) as $path) {
                Arr::set($content, $path, Arr::get($data, "content.{$locale}.{$path}"));
            }

            $translation->content = $content;
            $translation->save();
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function settings(PageSection $section, array $definition, array $data): array
    {
        $settings = $section->settings ?? [];

        foreach (Arr::get($definition, 'settings', []) as $key => $setting) {
            $settings[$key] = Arr::get($data, "settings.{$key}", $setting['default'] ?? null);
        }

        return $settings;
    }

    /**
     * Attaches, replaces or detaches the image in each slot. Detaching removes
     * the relation only: the file stays in the media library.
     *
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $data
     * @param  array<string, UploadedFile|null>  $files
     */
    private function saveMedia(PageSection $section, array $definition, array $data, array $files, ?int $userId): void
    {
        foreach (array_keys(Arr::get($definition, 'media', [])) as $slot) {
            $relation = $section->sectionMedia()->where('slot', $slot);

            if ((bool) ($data["media_{$slot}_remove"] ?? false)) {
                $relation->delete();

                continue;
            }

            $file = $files[$slot] ?? null;

            if ($file instanceof UploadedFile) {
                /* A new upload goes into the library first, so every image on
                   the site is a media record like any other. */
                $uploaded = $this->media->store($file, $userId);
                $this->storedFiles[] = [$uploaded->disk, $uploaded->path];
                $mediaId = $uploaded->id;
            } else {
                $mediaId = $data["media_{$slot}_media_id"] ?? null;
            }

            if (! $mediaId || ! Media::whereKey($mediaId)->exists()) {
                continue;
            }

            $section->sectionMedia()->updateOrCreate(
                ['slot' => $slot],
                ['media_id' => $mediaId],
            );
        }

        $section->load('sectionMedia.media');
    }
}
