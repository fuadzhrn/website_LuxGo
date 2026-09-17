<?php

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
| Files written for crawlers, not for visitors.
|
| They live outside the web middleware group on purpose: a crawler has no
| session to keep, and a response carrying Set-Cookie and Cache-Control: private
| is one a CDN will refuse to cache. Their URLs are still built by the router, so
| they name whatever host the application is running on.
*/

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
