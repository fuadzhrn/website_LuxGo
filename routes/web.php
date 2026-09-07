<?php

use App\Models\Vehicle;
use App\Services\PageContentService;
use App\Support\MembershipValues;
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
        /* Home reads its copy and imagery from the CMS; the service applies
           the locale fallback so the view never has to. */
        Route::get('/', function (PageContentService $content) {
            return view('pages.home.index', ['page' => $content->render('home')]);
        })->name('home');

        /* Copy and imagery come from the CMS; every figure on the page comes
           from the membership settings, never from the copy itself. */
        Route::get('/membership', function (PageContentService $content, MembershipValues $membership) {
            return view('pages.membership.index', [
                'page' => $content->render('membership'),
                'membership' => $membership,
            ]);
        })->name('membership');

        /* Copy comes from the CMS; the vehicles come from their own module,
           active ones only and in the order the admin set. */
        Route::get('/our-collection', function (PageContentService $content) {
            return view('pages.collection.index', [
                'page' => $content->render('collection'),
                'vehicles' => Vehicle::query()
                    ->active()
                    ->with(['mainMedia', 'translations', 'galleryMedia'])
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get(),
            ]);
        })->name('collection');

        Route::get('/experience', function (PageContentService $content) {
            return view('pages.experience.index', ['page' => $content->render('experience')]);
        })->name('experience');

        Route::get('/how-it-works', function (PageContentService $content) {
            return view('pages.how-it-works.index', ['page' => $content->render('how_it_works')]);
        })->name('how-it-works');

        Route::get('/about', function (PageContentService $content) {
            return view('pages.about-contact.index', ['page' => $content->render('about')]);
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
