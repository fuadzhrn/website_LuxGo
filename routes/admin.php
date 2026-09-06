<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\ShellController;
use Illuminate\Support\Facades\Route;

/*
| The admin area is an internal tool, so it sits outside the {locale} prefix
| that every public page carries. There is deliberately no register route.
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', 'administrator'])->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        /* One set of routes for every CMS page. Scoped bindings mean a section
           key only resolves when it really belongs to the page in the URL. */
        Route::get('content', [PageContentController::class, 'index'])->name('content');
        Route::get('content/{page:key}', [PageContentController::class, 'show'])->name('content.page');
        Route::get('content/{page:key}/sections/{section:section_key}/edit', [PageContentController::class, 'edit'])
            ->scopeBindings()->name('content.section.edit');
        Route::put('content/{page:key}/sections/{section:section_key}', [PageContentController::class, 'update'])
            ->scopeBindings()->name('content.section.update');
        Route::get('media', [MediaController::class, 'index'])->name('media');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::patch('media/{media}', [MediaController::class, 'update'])->name('media.update');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
        Route::get('applications', [ShellController::class, 'applications'])->name('applications');
        Route::get('seo', [ShellController::class, 'seo'])->name('seo');
        Route::get('settings', [ShellController::class, 'settings'])->name('settings');
        Route::get('profile', [ShellController::class, 'profile'])->name('profile');

        // Development-only component showcase; not linked from the sidebar.
        Route::get('components', [ShellController::class, 'components'])->name('components');

        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    });
});
