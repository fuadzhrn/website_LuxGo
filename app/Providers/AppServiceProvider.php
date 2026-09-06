<?php

namespace App\Providers;

use App\Models\Media;
use App\Support\LocaleUrl;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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

        /* The picker is included at most once per page, so one query serves
           however many image fields that page has. */
        View::composer('admin.partials.media-picker', function ($view) {
            $view->with('pickerMedia', Media::latest()->limit(60)->get());
        });
        //
    }
}
