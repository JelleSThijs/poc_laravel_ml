<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/fruits/create', 'fruits.create')->name('fruits.create');
Route::view('/preferences', 'preferences')->name('preferences');
