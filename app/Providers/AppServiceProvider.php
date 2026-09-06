<?php

namespace App\Providers;

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
        //
    }
}
