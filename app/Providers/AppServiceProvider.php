<?php

namespace App\Providers;

use App\Models\Media;
use App\Models\MembershipSetting;
use App\Services\SeoService;
use App\Support\LocaleUrl;
use App\Support\MembershipValues;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* One row of published figures, resolved once per request. Everything
           that shows a price, a usage right or a duration reads it from here. */
        /* One row of company details, read once per request. */
        $this->app->scoped(SiteSettings::class);

        $this->app->scoped(MembershipValues::class, function () {
            return new MembershipValues(MembershipSetting::query()->orderBy('id')->firstOrFail());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /* The two partials that need language links get them from one place,
           rather than each Blade rebuilding the locale logic. */
        View::composer(['partials.seo', 'partials.header'], function ($view) {
            $view->with('localeAlternates', LocaleUrl::alternates());
        });

        /* The head reads its title, description, sharing card and robots rule
           from the database; a page without an SEO record simply gets null and
           falls back to what the view passed in. */
        View::composer('partials.seo', function ($view) {
            $seo = app(SeoService::class);
            $pageKey = $seo->pageKeyForRoute(Route::currentRouteName());

            $view->with('seo', $pageKey ? $seo->for($pageKey) : null);
        });

        /* The company details reach the footer and the contact section without
           either of them querying for themselves. */
        View::composer(
            ['partials.footer', 'pages.about-contact.sections.contact-head-office', 'pages.legal.show'],
            fn ($view) => $view->with('site', app(SiteSettings::class))
        );

        /* The picker is included at most once per page, so one query serves
           however many image fields that page has. */
        View::composer('admin.partials.media-picker', function ($view) {
            $view->with('pickerMedia', Media::latest()->limit(60)->get());
        });
        //
    }
}
