<?php

namespace App\Support;

use App\Models\Media;
use Illuminate\Support\Arr;

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
     */
    public function __construct(
        public readonly string $key,
        private readonly array $content,
        private readonly array $media,
        private readonly array $settings,
        private readonly array $definition = [],
    ) {}

    /**
     * A single string, addressed by its dotted path inside the content.
     */
    public function text(string $path, string $default = ''): string
    {
        $value = Arr::get($this->content, $path);

        return is_scalar($value) ? (string) $value : $default;
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
     * A CTA destination, resolved through route() so the visitor's locale is
     * kept. Only route names from the config list are ever stored.
     */
    public function link(string $key): string
    {
        $name = $this->settings[$key] ?? Arr::get($this->definition, "settings.{$key}.default");
        $allowed = array_keys(config('page_content.cta_routes', []));

        if (! is_string($name) || ! in_array($name, $allowed, true)) {
            $name = 'home';
        }

        return route($name);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->content;
    }
}
