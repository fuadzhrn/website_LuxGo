<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home.index');
})->name('home');

Route::get('/membership', function () {
    return view('pages.membership.index');
})->name('membership');

Route::get('/collection', function () {
    return view('pages.collection.index');
})->name('collection');

Route::get('/terms-of-use', function () {
    return view('pages.legal.show', ['legalTitle' => 'Terms of Use']);
})->name('legal.terms');

Route::get('/privacy-policy', function () {
    return view('pages.legal.show', ['legalTitle' => 'Privacy Policy']);
})->name('legal.privacy');

Route::get('/cookies-policy', function () {
    return view('pages.legal.show', ['legalTitle' => 'Cookies Policy']);
})->name('legal.cookies');
