<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
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

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // KEAMANAN: data grafik dashboard hanya untuk yang sudah login
    Route::get('/dashboard/chart', [DashboardController::class, 'chart'])->name('dashboard.chart');

    // KEAMANAN: daftar barang boleh dilihat semua role yang login (staff & admin)
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');

    // JSON live search barang untuk form transaksi
    Route::get('/items/search', [ItemController::class, 'search'])->name('items.search');

    // KEAMANAN: catat & lihat transaksi stok — semua role yang login.
    // Staff hanya melihat riwayat miliknya (dicek di controller).
    Route::resource('transactions', StockTransactionController::class)
        ->only(['index', 'create', 'store']);

    // KEAMANAN: master data (kategori, supplier, CRUD barang) hanya admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
        // index sudah didefinisikan di atas untuk semua role
        Route::resource('items', ItemController::class)->except(['show', 'index']);

        // KEAMANAN: kelola user (ubah role, hapus) hanya admin
        Route::resource('users', UserController::class)->only(['index', 'update', 'destroy']);

        // KEAMANAN: laporan hanya admin
        Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    });
});
