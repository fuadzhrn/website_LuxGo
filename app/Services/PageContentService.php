<?php

namespace App\Services;

use App\Models\MembershipSetting;
use App\Models\Page;
use App\Models\PageSection;
use App\Support\MembershipValues;
use App\Support\PageContent;
use App\Support\SectionContent;
use Illuminate\Support\Arr;

/**
 * Reads CMS page content. Every public page goes through here, so the rules for
 * locale fallback, active sections and image slots are written once.
 *
 * Resolution order for a field, first match wins:
 *   1. the translation for the requested locale
 *   2. the English translation
 *   3. the lang file the content was seeded from
 *
 * Step 3 is a safety net for a database that has not been seeded yet; once the
 * content seeder has run, the database answers everything.
 */
class PageContentService
{
    private const FALLBACK_LOCALE = 'en';

    /** @var array<string, Page|null> */
    private array $pages = [];

    /** @var array<string, string>|null */
    private ?array $placeholders = null;

    public function page(string $pageKey): ?Page
    {
        return $this->pages[$pageKey] ??= Page::query()
            ->where('key', $pageKey)
            ->with(['sections.translations', 'sections.sectionMedia.media', 'sections.faqItems.translations'])
            ->first();
    }

    /**
     * The page as the public view needs it: active sections only, in order.
     */
    public function render(string $pageKey, ?string $locale = null): PageContent
    {
        $locale = $locale ?? app()->getLocale();
        $definition = $this->definition($pageKey);
        $page = $this->page($pageKey);

        $sections = [];
        $views = [];

        foreach (Arr::get($definition, 'sections', []) as $sectionKey => $sectionDefinition) {
            $section = $page?->sections->firstWhere('section_key', $sectionKey);

            /* A section switched off keeps its content in the database; it is
               simply not handed to the view. */
            if ($section && ! $section->is_active) {
                continue;
            }

            $sections[$sectionKey] = $this->resolve($pageKey, $sectionKey, $section, $locale);
            $views[$sectionKey] = (array) $sectionDefinition['view'];
        }

        return new PageContent($pageKey, $sections, $views);
    }

    /**
     * One section, whether or not it is active — used by the editor and by
     * anything that needs a section on its own.
     */
    public function section(string $pageKey, string $sectionKey, ?string $locale = null): ?SectionContent
    {
        if (! $this->sectionDefinition($pageKey, $sectionKey)) {
            return null;
        }

        $section = $this->page($pageKey)?->sections->firstWhere('section_key', $sectionKey);

        return $this->resolve($pageKey, $sectionKey, $section, $locale ?? app()->getLocale());
    }

    /**
     * The stored text for one locale, with no fallback applied — what the
     * editor must show so an empty Indonesian field looks empty, not English.
     *
     * @return array<string, mixed>
     */
    public function editableContent(string $pageKey, string $sectionKey, string $locale): array
    {
        $section = $this->page($pageKey)?->sections->firstWhere('section_key', $sectionKey);
        $stored = $section?->translation($locale)?->content;

        if (is_array($stored)) {
            return $stored;
        }

        /* Nothing saved yet: offer the content this section shipped with, so
           the first save starts from the live copy rather than blank fields. */
        return $this->langContent($pageKey, $sectionKey, $locale);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function definition(string $pageKey): ?array
    {
        return config("page_content.pages.{$pageKey}");
    }

    /**
     * @return array<string, mixed>|null
     */
    public function sectionDefinition(string $pageKey, string $sectionKey): ?array
    {
        return config("page_content.pages.{$pageKey}.sections.{$sectionKey}");
    }

    public function isEditable(string $pageKey): bool
    {
        return (bool) config("page_content.pages.{$pageKey}.editable", false);
    }

    /**
     * Content as it came from the lang files. Also used by the content seeder,
     * which is why the lookup lives here rather than in the seeder.
     *
     * @return array<string, mixed>
     */
    public function langContent(string $pageKey, string $sectionKey, string $locale): array
    {
        $namespaces = Arr::get($this->sectionDefinition($pageKey, $sectionKey) ?? [], 'lang');

        if (is_string($namespaces)) {
            $namespaces = ['' => $namespaces];
        }

        if (! is_array($namespaces)) {
            return [];
        }

        $content = [];

        /* A section may draw on more than one namespace: the key says where the
           lines are nested, '' meaning the root of the section content. */
        foreach ($namespaces as $prefix => $namespace) {
            $lines = trans($namespace, [], $locale);

            if (! is_array($lines)) {
                continue;
            }

            if ($prefix === '') {
                $content = array_replace_recursive($content, $lines);

                continue;
            }

            Arr::set($content, $prefix, array_replace_recursive(Arr::get($content, $prefix, []), $lines));
        }

        return $content;
    }

    /**
     * The business figures CMS copy may refer to. Resolved once per request.
     *
     * @return array<string, string>
     */
    private function placeholders(): array
    {
        if ($this->placeholders !== null) {
            return $this->placeholders;
        }

        $settings = MembershipSetting::query()->orderBy('id')->first();

        /* Before the settings are seeded there is nothing to substitute; the
           copy still renders, placeholders and all, instead of failing. */
        return $this->placeholders = $settings
            ? (new MembershipValues($settings))->placeholders()
            : [];
    }

    private function resolve(string $pageKey, string $sectionKey, ?PageSection $section, string $locale): SectionContent
    {
        $definition = $this->sectionDefinition($pageKey, $sectionKey) ?? [];

        /* Layered so a missing Indonesian line falls back to English rather
           than rendering blank, and a missing translation row falls back to the
           copy the page shipped with. */
        $content = array_replace_recursive(
            $this->langContent($pageKey, $sectionKey, self::FALLBACK_LOCALE),
            $this->langContent($pageKey, $sectionKey, $locale),
            $section?->translation(self::FALLBACK_LOCALE)?->content ?? [],
            $locale === self::FALLBACK_LOCALE ? [] : ($section?->translation($locale)?->content ?? []),
        );

        $media = [];

        foreach (array_keys(Arr::get($definition, 'media', [])) as $slot) {
            if ($item = $section?->mediaForSlot($slot)) {
                $media[$slot] = $item;
            }
        }

        return new SectionContent(
            $sectionKey,
            $content,
            $media,
            $section?->settings ?? [],
            $definition,
            $this->placeholders(),
            $section?->faqItems->where('is_active', true)->values(),
        );
    }
}
