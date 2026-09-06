<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class LocaleUrl
{
    /**
     * The current page expressed in every supported locale.
     *
     * Route parameters are carried over, so /en/membership resolves to
     * /id/membership rather than dropping the visitor back on the home page.
     *
     * @return array<string, array{url: string, active: bool}>
     */
    public static function alternates(): array
    {
        $current = app()->getLocale();
        $route = Route::current();
        $name = $route?->getName();
        $parameters = $route?->parameters() ?? [];

        $alternates = [];

        foreach (config('locales.supported') as $locale) {
            $alternates[$locale] = [
                'url' => $name !== null
                    ? route($name, array_merge($parameters, ['locale' => $locale]))
                    : url($locale),
                'active' => $locale === $current,
            ];
        }

        return $alternates;
    }
}
