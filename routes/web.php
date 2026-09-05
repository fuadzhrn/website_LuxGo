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
