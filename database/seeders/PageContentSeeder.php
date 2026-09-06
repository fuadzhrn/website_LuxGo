<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Services\MediaService;
use App\Services\PageContentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/**
 * Moves the copy a page shipped with — the lang files — into the database, and
 * brings the images it uses into the media library.
 *
 * This is initial content, not a content reset. A value the admin has already
 * saved is never overwritten: only keys that are missing get filled in, and an
 * image is only attached to a slot that is empty. Running it again is therefore
 * safe, and adding a field to a section fills that field in without touching
 * the rest.
 */
class PageContentSeeder extends Seeder
{
    public function __construct(
        private readonly MediaService $media,
        private readonly PageContentService $content,
    ) {}

    public function run(): void
    {
        foreach (config('page_content.pages', []) as $pageKey => $definition) {
            if (! ($definition['editable'] ?? false)) {
                continue;
            }

            $page = Page::where('key', $pageKey)->first();

            if (! $page) {
                continue;
            }

            foreach ($definition['sections'] ?? [] as $sectionKey => $sectionDefinition) {
                $section = $page->sections()->where('section_key', $sectionKey)->first();

                if (! $section) {
                    continue;
                }

                $this->seedTranslations($pageKey, $sectionKey, $section, $sectionDefinition);
                $this->seedSettings($section, $sectionDefinition);
                $this->seedMedia($section, $sectionDefinition);
                $this->seedFaq($section, $sectionDefinition);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function seedTranslations(string $pageKey, string $sectionKey, PageSection $section, array $definition): void
    {
        foreach (config('locales.supported') as $locale) {
            /* The service knows how a section's lang namespaces are laid out,
               including the sections that draw on more than one. */
            $lines = $this->content->langContent($pageKey, $sectionKey, $locale);

            if ($lines === []) {
                continue;
            }

            $seed = [];

            /* Only the fields the editor knows about travel into the database,
               so a lang file entry the page does not render is left behind. */
            foreach (array_keys($definition['fields'] ?? []) as $path) {
                $value = Arr::get($lines, $path);

                if ($value !== null) {
                    Arr::set($seed, $path, $value);
                }
            }

            if ($seed === []) {
                continue;
            }

            $translation = $section->translations()->firstOrNew(['locale' => $locale]);

            /* Existing values win, so a save made in the admin survives a
               re-run of the seeder. */
            $translation->content = array_replace_recursive($seed, $translation->content ?? []);
            $translation->save();
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function seedSettings(PageSection $section, array $definition): void
    {
        $settings = $section->settings ?? [];

        foreach ($definition['settings'] ?? [] as $key => $setting) {
            $settings[$key] ??= $setting['default'] ?? null;
        }

        if ($settings !== ($section->settings ?? [])) {
            $section->update(['settings' => $settings]);
        }
    }

    /**
     * The questions a section shipped with, created once. After that the list
     * belongs to the admin: a re-run adds nothing and removes nothing.
     *
     * @param  array<string, mixed>  $definition
     */
    private function seedFaq(PageSection $section, array $definition): void
    {
        $faq = $definition['faq'] ?? null;

        if (! is_array($faq) || $section->faqItems()->exists()) {
            return;
        }

        foreach ($faq['items'] ?? [] as $order => $item) {
            $entry = $section->faqItems()->create([
                'shows_usage_breakdown' => (bool) ($item['breakdown'] ?? false),
                'is_active' => true,
                'sort_order' => $order + 1,
            ]);

            foreach (config('locales.supported') as $locale) {
                $lines = trans($faq['lang'], [], $locale);

                if (! is_array($lines)) {
                    continue;
                }

                $entry->translations()->create([
                    'locale' => $locale,
                    'question' => Arr::get($lines, $item['question'], ''),
                    'answer' => Arr::get($lines, $item['answer'], ''),
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function seedMedia(PageSection $section, array $definition): void
    {
        foreach ($definition['media'] ?? [] as $slot => $slotDefinition) {
            $source = $slotDefinition['fallback'] ?? null;

            if (! is_string($source) || ! is_file(public_path($source))) {
                continue;
            }

            /* An empty slot only: an image the admin removed stays removed
               unless it is the very first run. */
            if ($section->sectionMedia()->where('slot', $slot)->exists()) {
                continue;
            }

            $name = basename($source);

            /* One library record per shipped asset, however many sections and
               runs point at it. */
            $media = Media::where('filename', $name)
                ->where('size_bytes', filesize(public_path($source)))
                ->first()
                ?? $this->media->storeFromPath(public_path($source), $name);

            $section->sectionMedia()->create([
                'slot' => $slot,
                'media_id' => $media->id,
            ]);
        }
    }
}
