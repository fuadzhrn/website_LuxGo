<?php

namespace App\Services;

use App\Models\Page;
use App\Models\SeoSetting;
use App\Support\PageSeo;
use Illuminate\Support\Collection;

/**
 * Resolves what a page tells search engines and social networks.
 *
 * Values are looked up in one order, first match wins:
 *   1. the SEO translation for the requested locale
 *   2. the English SEO translation
 *   3. the page's own meta lines, which is what the site shipped with
 *
 * The canonical URL and the language alternates are built from routing rather
 * than stored, so they cannot drift from where the page actually lives.
 */
class SeoService
{
    private const FALLBACK_LOCALE = 'en';

    /** @var array<string, SeoSetting|null> */
    private array $settings = [];

    /**
     * The page key a route name belongs to, or null for a page without SEO
     * records of its own (the legal pages, for instance).
     */
    public function pageKeyForRoute(?string $routeName): ?string
    {
        if ($routeName === null) {
            return null;
        }

        foreach (config('page_content.pages', []) as $key => $definition) {
            if (($definition['route'] ?? null) === $routeName) {
                return $key;
            }
        }

        return null;
    }

    public function setting(string $pageKey): ?SeoSetting
    {
        return $this->settings[$pageKey] ??= SeoSetting::query()
            ->whereHas('page', fn ($query) => $query->where('key', $pageKey))
            ->with(['translations', 'ogMedia'])
            ->first();
    }

    public function for(string $pageKey, ?string $locale = null): PageSeo
    {
        $locale = $locale ?? app()->getLocale();
        $setting = $this->setting($pageKey);

        $media = $setting?->ogMedia;

        return new PageSeo(
            $this->value($setting, $locale, 'meta_title') ?: $this->fromLang($pageKey, 'title', $locale),
            $this->value($setting, $locale, 'meta_description') ?: $this->fromLang($pageKey, 'description', $locale),
            $this->value($setting, $locale, 'og_title'),
            $this->value($setting, $locale, 'og_description'),
            $media?->exists() ? $media->url() : null,
            $setting?->is_indexable ?? true,
            $this->canonical($pageKey, $locale),
        );
    }

    /**
     * The page this SEO record belongs to, ordered as the site is.
     *
     * @return Collection<int, Page>
     */
    public function editablePages()
    {
        return Page::query()
            ->whereIn('key', array_keys(config('page_content.pages', [])))
            ->with(['seoSetting.translations', 'seoSetting.ogMedia'])
            ->orderBy('sort_order')
            ->get();
    }

    public function canonical(string $pageKey, string $locale): string
    {
        $route = config("page_content.pages.{$pageKey}.route");

        return $route ? route($route, ['locale' => $locale]) : url($locale);
    }

    /**
     * Whether a locale has anything of its own saved — what the list screen
     * reports, rather than a guess.
     */
    public function isComplete(?SeoSetting $setting, string $locale): bool
    {
        return $this->value($setting, $locale, 'meta_title') !== null
            && $this->value($setting, $locale, 'meta_description') !== null;
    }

    private function value(?SeoSetting $setting, string $locale, string $field): ?string
    {
        $translation = $setting?->translations->firstWhere('locale', $locale);
        $value = $translation?->{$field};

        if (is_string($value) && $value !== '') {
            return $value;
        }

        if ($locale === self::FALLBACK_LOCALE) {
            return null;
        }

        $fallback = $setting?->translations->firstWhere('locale', self::FALLBACK_LOCALE)?->{$field};

        return is_string($fallback) && $fallback !== '' ? $fallback : null;
    }

    /**
     * The meta lines the page shipped with. Used before anything is saved, so
     * a page never renders an empty title.
     */
    private function fromLang(string $pageKey, string $field, string $locale): string
    {
        $namespace = config("page_content.pages.{$pageKey}.meta");

        if (! is_string($namespace)) {
            return (string) config('app.name');
        }

        $line = trans("{$namespace}.{$field}", [], $locale);

        return is_string($line) && $line !== "{$namespace}.{$field}"
            ? $line
            : (string) config('app.name');
    }
}
