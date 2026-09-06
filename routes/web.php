<?php

use Illuminate\Support\Facades\Route;

/*
| Entry point: honour a locale the visitor already chose, otherwise fall back to
| the temporary default while the Indonesian content is being prepared.
*/
Route::get('/', function () {
    $locale = session('locale');

    if (! is_string($locale) || ! in_array($locale, config('locales.supported'), true)) {
        $locale = config('locales.default');
    }

    return redirect()->route('home', ['locale' => $locale]);
});

/*
| One group for both locales — the slugs are shared, only the prefix differs.
| SetLocale registers the URL default, so every route() call inside these views
| keeps the active locale without passing it explicitly.
*/
Route::prefix('{locale}')
    ->where(['locale' => implode('|', config('locales.supported'))])
    ->middleware('locale')
    ->group(function () {
        Route::get('/', function () {
            return view('pages.home.index');
        })->name('home');

        Route::get('/membership', function () {
            return view('pages.membership.index');
        })->name('membership');

        Route::get('/our-collection', function () {
            return view('pages.collection.index');
        })->name('collection');

        Route::get('/experience', function () {
            return view('pages.experience.index');
        })->name('experience');

        Route::get('/how-it-works', function () {
            return view('pages.how-it-works.index');
        })->name('how-it-works');

        Route::get('/about', function () {
            return view('pages.about-contact.index');
        })->name('about');

        Route::get('/terms-of-use', function () {
            return view('pages.legal.show', ['legalTitle' => 'Terms of Use']);
        })->name('legal.terms');

        Route::get('/privacy-policy', function () {
            return view('pages.legal.show', ['legalTitle' => 'Privacy Policy']);
        })->name('legal.privacy');

        Route::get('/cookies-policy', function () {
            return view('pages.legal.show', ['legalTitle' => 'Cookies Policy']);
        })->name('legal.cookies');
    });
