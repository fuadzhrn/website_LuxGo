<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MembershipApplicationController;
use App\Http\Controllers\Admin\MembershipSettingsController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ShellController;
use App\Http\Controllers\Admin\VehicleController;
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

        /* Business figures live on their own screen: they are shared by the
           whole site, not owned by one section. */
        Route::get('content/{page:key}/business-settings', [MembershipSettingsController::class, 'edit'])
            ->name('content.business-settings');
        Route::put('content/{page:key}/business-settings', [MembershipSettingsController::class, 'update'])
            ->name('content.business-settings.update');

        /* A FAQ list belongs to the section that renders it. */
        Route::prefix('content/{page:key}/sections/{section:section_key}/faq')
            ->name('content.faq')
            ->scopeBindings()
            ->group(function () {
                Route::get('/', [FaqController::class, 'index']);
                Route::get('create', [FaqController::class, 'create'])->name('.create');
                Route::post('/', [FaqController::class, 'store'])->name('.store');
                Route::get('{faq_item}/edit', [FaqController::class, 'edit'])->name('.edit');
                Route::put('{faq_item}', [FaqController::class, 'update'])->name('.update');
                Route::delete('{faq_item}', [FaqController::class, 'destroy'])->name('.destroy');
                Route::post('{faq_item}/move', [FaqController::class, 'move'])->name('.move');
            });
        /* The vehicles behind Our Collection. They are their own module: the
           collection page holds the copy, these hold the vehicles. */
        Route::prefix('vehicles')->name('vehicles')->group(function () {
            Route::get('/', [VehicleController::class, 'index']);
            Route::get('create', [VehicleController::class, 'create'])->name('.create');
            Route::post('/', [VehicleController::class, 'store'])->name('.store');
            Route::get('{vehicle}/edit', [VehicleController::class, 'edit'])->name('.edit');
            Route::put('{vehicle}', [VehicleController::class, 'update'])->name('.update');
            Route::delete('{vehicle}', [VehicleController::class, 'destroy'])->name('.destroy');
            Route::post('{vehicle}/status', [VehicleController::class, 'status'])->name('.status');
            Route::post('{vehicle}/move', [VehicleController::class, 'move'])->name('.move');

            Route::post('{vehicle}/gallery', [VehicleController::class, 'addGalleryImage'])->name('.gallery.store');
            Route::delete('{vehicle}/gallery/{media}', [VehicleController::class, 'removeGalleryImage'])->name('.gallery.destroy');
            Route::post('{vehicle}/gallery/{media}/move', [VehicleController::class, 'moveGalleryImage'])->name('.gallery.move');
        });

        Route::get('media', [MediaController::class, 'index'])->name('media');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::patch('media/{media}', [MediaController::class, 'update'])->name('media.update');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
        /* Leads submitted through the public membership form. They are read
           and followed up here; the submission itself is never edited. */
        Route::get('applications', [MembershipApplicationController::class, 'index'])->name('applications');
        Route::get('applications/{application}', [MembershipApplicationController::class, 'show'])->name('applications.show');
        Route::patch('applications/{application}/status', [MembershipApplicationController::class, 'updateStatus'])
            ->name('applications.status');
        /* Search and sharing information, one record per published page. */
        Route::get('seo', [SeoController::class, 'index'])->name('seo');
        Route::get('seo/{page:key}/edit', [SeoController::class, 'edit'])->name('seo.edit');
        Route::put('seo/{page:key}', [SeoController::class, 'update'])->name('seo.update');
        Route::get('settings', [ShellController::class, 'settings'])->name('settings');
        Route::get('profile', [ShellController::class, 'profile'])->name('profile');

        // Development-only component showcase; not linked from the sidebar.
        Route::get('components', [ShellController::class, 'components'])->name('components');

        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    });
});
