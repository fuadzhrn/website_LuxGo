<?php

namespace App\Http\Controllers;

use App\Services\SeoService;
use Illuminate\Http\Response;

/**
 * The crawler-facing files. Both are served from routes rather than static files
 * so the domain comes from the running application: the same code answers on
 * localhost and on the production host without anything to remember to edit.
 */
class SitemapController extends Controller
{
    /**
     * The legal pages have no CMS record, so they are named here. Anything not
     * in this list or in the page registry stays out of the sitemap — the admin
     * panel included.
     *
     * @var array<int, string>
     */
    private const EXTRA_ROUTES = [
        'legal.terms',
        'legal.privacy',
        'legal.cookies',
    ];

    public function sitemap(SeoService $seo): Response
    {
        $locales = config('locales.supported');
        $entries = [];

        foreach ($this->routeNames($seo) as $routeName) {
            /* Every locale of a page is one entry, and each entry lists the
               others, which is what tells Google they are translations rather
               than duplicates. */
            $alternates = [];
            foreach ($locales as $locale) {
                $alternates[$locale] = route($routeName, ['locale' => $locale]);
            }

            foreach ($locales as $locale) {
                $entries[] = ['loc' => $alternates[$locale], 'alternates' => $alternates];
            }
        }

        return response()
            ->view('sitemap', ['entries' => $entries, 'defaultLocale' => config('locales.default')])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            /* The panel and its sign-in screen are not for search results. */
            'Disallow: /admin',
            'Disallow: /admin/',
            '',
            'Sitemap: '.route('sitemap'),
            '',
        ];

        return response(implode("\n", $lines))
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /**
     * The CMS pages an editor has left indexable, then the legal pages.
     *
     * @return array<int, string>
     */
    private function routeNames(SeoService $seo): array
    {
        $names = [];

        foreach (config('page_content.pages', []) as $key => $definition) {
            $route = $definition['route'] ?? null;

            if (is_string($route) && $seo->for($key, config('locales.default'))->isIndexable()) {
                $names[] = $route;
            }
        }

        return array_merge($names, self::EXTRA_ROUTES);
    }
}
