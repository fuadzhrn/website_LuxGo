<?php

namespace App\Support;

/**
 * Resolves the CTA destinations listed in config/page_content.php.
 *
 * Every "become a member" link on the site — the navbar, the footer and the
 * CTAs the CMS points at it — comes through here, so the destination is written
 * once. A route-backed target keeps the visitor's locale; a fragment is appended
 * when the target names one.
 */
class CtaTarget
{
    public static function url(string $key): string
    {
        $target = config("page_content.cta_targets.{$key}");

        if (! is_array($target)) {
            return route('home');
        }

        $url = isset($target['route']) ? route($target['route']) : ($target['path'] ?? route('home'));

        return isset($target['fragment']) ? $url.'#'.$target['fragment'] : $url;
    }

    /**
     * The labels the editor offers, keyed the way they are stored.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(config('page_content.cta_targets', []))
            ->map(fn (array $target) => $target['label'])
            ->all();
    }
}
