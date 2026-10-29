<?php

use Illuminate\Support\Facades\Route;

// Halaman utama diarahkan ke login
Route::get('/', function () {
    return redirect()->route('login');
});
