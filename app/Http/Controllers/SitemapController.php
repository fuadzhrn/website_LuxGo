<?php

namespace App\Http\Controllers;

use App\Services\SeoService;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * The crawler-facing files. Both are served from routes rather than static files
 * so the domain comes from the running application: the same code answers on
 * localhost and on the production host without anything to remember to edit.
 *
 * They are registered outside the web middleware group, so neither opens a
 * session nor sets a cookie — a crawler has no use for either, and a response
 * that carries them cannot be cached by a CDN.
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
        $edited = $this->lastEdited();
        $entries = [];

        foreach ($this->pages($seo) as $pageKey => $routeName) {
            /* Every locale of a page is one entry, and each entry lists the
               others, which is what tells Google they are translations rather
               than duplicates. */
            $alternates = [];
            foreach ($locales as $locale) {
                $alternates[$locale] = route($routeName, ['locale' => $locale]);
            }

            foreach ($locales as $locale) {
                $entries[] = [
                    'loc' => $alternates[$locale],
                    'alternates' => $alternates,
                    /* Omitted rather than guessed: Google ignores the field
                       across a whole sitemap once it finds dates it cannot
                       trust, so a page with nothing editable gives none. */
                    'lastmod' => $edited[$pageKey][$locale] ?? null,
                ];
            }
        }

        return response()
            ->view('sitemap', ['entries' => $entries, 'defaultLocale' => config('locales.default')])
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            /* Without this the framework sends "no-cache, private" and the
               CDN in front of the site refuses to hold it. An hour is short
               enough that an edit shows up the same morning. */
            ->header('Cache-Control', 'public, max-age=3600');
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
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * The CMS pages an editor has left indexable, then the legal pages, keyed by
     * the page they belong to. The legal pages have no key, so they are indexed
     * by their route name and simply find no edit date later.
     *
     * @return array<string, string>
     */
    private function pages(SeoService $seo): array
    {
        $pages = [];

        foreach (config('page_content.pages', []) as $key => $definition) {
            $route = $definition['route'] ?? null;

            if (is_string($route) && $seo->for($key, config('locales.default'))->isIndexable()) {
                $pages[$key] = $route;
            }
        }

        foreach (self::EXTRA_ROUTES as $route) {
            $pages[$route] = $route;
        }

        return $pages;
    }

    /**
     * When each page was last edited, per locale.
     *
     * A page is more than its sections: the collection page changes when a
     * vehicle is edited, and the membership page when an FAQ answer is. Those
     * are followed too, otherwise the date would sit still while the page
     * visibly changed — and a date that lags is worse than none at all.
     *
     * @return array<string, array<string, string>>
     */
    private function lastEdited(): array
    {
        $edited = [];

        $record = function (string $pageKey, string $locale, ?string $timestamp) use (&$edited) {
            if ($timestamp === null) {
                return;
            }

            $current = $edited[$pageKey][$locale] ?? null;

            if ($current === null || Carbon::parse($timestamp)->greaterThan(Carbon::parse($current))) {
                $edited[$pageKey][$locale] = Carbon::parse($timestamp)->toAtomString();
            }
        };

        /* The copy and imagery of each section. */
        $sections = DB::table('page_section_translations as t')
            ->join('page_sections as s', 's.id', '=', 't.page_section_id')
            ->join('pages as p', 'p.id', '=', 's.page_id')
            ->groupBy('p.key', 't.locale')
            ->select('p.key as page_key', 't.locale', DB::raw('MAX(t.updated_at) as edited_at'))
            ->get();

        foreach ($sections as $row) {
            $record($row->page_key, $row->locale, $row->edited_at);
        }

        /* FAQ answers reach the page that owns the section holding them. */
        $faq = DB::table('faq_item_translations as t')
            ->join('faq_items as f', 'f.id', '=', 't.faq_item_id')
            ->join('page_sections as s', 's.id', '=', 'f.page_section_id')
            ->join('pages as p', 'p.id', '=', 's.page_id')
            ->groupBy('p.key', 't.locale')
            ->select('p.key as page_key', 't.locale', DB::raw('MAX(t.updated_at) as edited_at'))
            ->get();

        foreach ($faq as $row) {
            $record($row->page_key, $row->locale, $row->edited_at);
        }

        /* Vehicles belong to the collection page by design rather than by a
           foreign key, so the link is made here. */
        $vehicles = DB::table('vehicle_translations')
            ->groupBy('locale')
            ->select('locale', DB::raw('MAX(updated_at) as edited_at'))
            ->get();

        foreach ($vehicles as $row) {
            $record('collection', $row->locale, $row->edited_at);
        }

        return $edited;
    }
}
