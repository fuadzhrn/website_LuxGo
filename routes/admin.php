<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MediaController;
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

        Route::get('content', [ShellController::class, 'content'])->name('content');
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
