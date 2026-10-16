<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

// Otomatis mengalihkan halaman utama ke login
Route::redirect('/', '/login');

// Rute Dashboard
Route::get('/admin/dashboard', [StockTransactionController::class, 'dashboard']);

// Rute Master Daftar Barang
Route::get('/admin/daftar-barang', [StockTransactionController::class, 'daftarBarang']);
Route::post('/admin/daftar-barang/store', [StockTransactionController::class, 'storeBarang']);
Route::get('/admin/daftar-barang/delete/{id}', [StockTransactionController::class, 'deleteBarang']);
Route::put('/admin/daftar-barang/update/{id}', [ProductController::class, 'update'])->name('products.update');
Route::get('/admin/daftar-barang', [ProductController::class, 'index'])->name('products.index');
Route::post('/admin/daftar-barang/{product}/mutasi', [ProductController::class, 'storeTransaction'])->name('products.mutasi');


// Rute Master Pengguna
Route::get('/admin/pengguna', [StockTransactionController::class, 'pengguna']);
Route::post('/admin/pengguna/store', [StockTransactionController::class, 'storePengguna']);
Route::get('/admin/pengguna/delete/{id}', [StockTransactionController::class, 'deletePengguna']);

// --- RUTE MASTER KATEGORI BARANG ---
Route::get('/admin/kategori', [StockTransactionController::class, 'kategori']);
Route::post('/admin/kategori/store', [StockTransactionController::class, 'storeKategori']);
Route::post('/admin/kategori/update/{id}', [StockTransactionController::class, 'updateKategori']);
Route::get('/admin/kategori/delete/{id}', [StockTransactionController::class, 'deleteKategori']);

// --- RUTE DATA PERSEDIAAN & MUTASI STOK ---
Route::get('/admin/persediaan', [StockTransactionController::class, 'index']);
Route::post('/admin/persediaan/store', [StockTransactionController::class, 'store']);

// --- RUTE LAPORAN ---
Route::get('/admin/laporan', [StockTransactionController::class, 'laporan']);

// --- RUTE LOGOUT ---
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// PASTIKAN ADA ->name('login') DI SINI
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [StockTransactionController::class, 'dashboard']);
    // rute admin lainnya di sini...
});