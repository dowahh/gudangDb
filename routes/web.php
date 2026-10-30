<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Hanya untuk guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');

    Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

// Harus sudah login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // KEAMANAN: daftar barang boleh dilihat semua role yang login (staff & admin)
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');

    // KEAMANAN: master data (kategori, supplier, CRUD barang) hanya admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
        // index sudah didefinisikan di atas untuk semua role
        Route::resource('items', ItemController::class)->except(['show', 'index']);

        // Contoh rute admin: hanya role admin yang boleh (CheckRole → 403)
        Route::get('/admin/contoh', function () {
            return view('admin.contoh');
        })->name('admin.contoh');
    });
});
