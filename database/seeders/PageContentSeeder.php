<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Services\MediaService;
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
    public function __construct(private readonly MediaService $media) {}

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

                $this->seedTranslations($section, $sectionDefinition);
                $this->seedSettings($section, $sectionDefinition);
                $this->seedMedia($section, $sectionDefinition);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function seedTranslations(PageSection $section, array $definition): void
    {
        $namespace = $definition['lang'] ?? null;

        if (! is_string($namespace)) {
            return;
        }

        foreach (config('locales.supported') as $locale) {
            $lines = trans($namespace, [], $locale);

            if (! is_array($lines)) {
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
