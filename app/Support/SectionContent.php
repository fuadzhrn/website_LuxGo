<?php

namespace App\Support;

use App\Models\FaqItem;
use App\Models\Media;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * One section as the public page needs it: resolved text for the active locale,
 * the images it points at, and its shared settings. Views ask this object for
 * values instead of querying, so no Blade file talks to the database.
 */
class SectionContent
{
    /**
     * @param  array<string, mixed>  $content
     * @param  array<string, Media>  $media
     * @param  array<string, mixed>  $settings
     * @param  array<string, mixed>  $definition
     * @param  array<string, string>  $placeholders  token => current value
     * @param  Collection<int, FaqItem>|null  $faqItems
     */
    public function __construct(
        public readonly string $key,
        private readonly array $content,
        private readonly array $media,
        private readonly array $settings,
        private readonly array $definition = [],
        private readonly array $placeholders = [],
        private readonly ?Collection $faqItems = null,
    ) {}

    /**
     * A single string, addressed by its dotted path inside the content.
     */
    public function text(string $path, string $default = ''): string
    {
        $value = Arr::get($this->content, $path);

        if (! is_scalar($value)) {
            return $default;
        }

        /* Business figures are written as placeholders so the copy cannot go
           stale. This is a plain string replacement over a fixed token list —
           nothing in the content is ever evaluated. */
        return strtr((string) $value, $this->placeholders);
    }

    /**
     * Applies the placeholder substitution to text that lives outside the
     * section content — a FAQ answer, for instance.
     */
    public function substitute(?string $value): string
    {
        return strtr((string) $value, $this->placeholders);
    }

    public function media(string $slot): ?Media
    {
        return $this->media[$slot] ?? null;
    }

    /**
     * The URL for a slot: the chosen media when there is one, otherwise the
     * asset the section shipped with — so a page never renders a broken image
     * while content is still being moved into the CMS.
     */
    public function imageUrl(string $slot): ?string
    {
        if ($media = $this->media($slot)) {
            return $media->exists() ? $media->url() : null;
        }

        $fallback = Arr::get($this->definition, "media.{$slot}.fallback");

        if (is_string($fallback) && file_exists(public_path($fallback))) {
            return asset($fallback);
        }

        return null;
    }

    public function hasImage(string $slot): bool
    {
        return $this->imageUrl($slot) !== null;
    }

    /**
     * A CTA destination. Only keys from the config list are ever stored, and a
     * route-backed one is resolved through route() so the visitor's locale is
     * kept.
     */
    public function link(string $key): string
    {
        $chosen = $this->settings[$key] ?? Arr::get($this->definition, "settings.{$key}.default");
        $targets = config('page_content.cta_targets', []);
        $target = is_string($chosen) ? ($targets[$chosen] ?? null) : null;

        if ($target === null) {
            return route('home');
        }

        return isset($target['route']) ? route($target['route']) : $target['path'];
    }

    /**
     * FAQ entries for this section, active ones only, already ordered.
     *
     * @return Collection<int, FaqItem>
     */
    public function faqItems(): Collection
    {
        return $this->faqItems ?? collect();
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->content;
    }
}
