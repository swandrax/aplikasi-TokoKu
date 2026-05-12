<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('backend.login');
    }

    return Auth::user()->isCustomer()
        ? redirect()->route('frontend.catalog.index')
        : redirect()->route('backend.beranda');
});

Route::get('backend/login', [LoginController::class, 'loginBackend'])->name('backend.login');
Route::post('backend/login', [LoginController::class, 'authenticateBackend'])->name('backend.login.authenticate');
Route::get('backend/register', [LoginController::class, 'registerBackend'])->name('backend.register');
Route::post('backend/register', [LoginController::class, 'storeRegisterBackend'])->name('backend.register.store');

Route::post('backend/logout', [LoginController::class, 'logoutBackend'])
    ->name('backend.logout')
    ->middleware('auth');

Route::middleware(['auth', 'role:1,0'])->group(function () {
    Route::get('backend/beranda', [BerandaController::class, 'berandaBackend'])
        ->name('backend.beranda');

    Route::get('backend/realtime/summary', [BerandaController::class, 'summaryRealtime'])
        ->name('backend.realtime.summary');

    Route::resource('backend/user', UserController::class, ['as' => 'backend']);
    Route::resource('backend/kategori', KategoriController::class, ['as' => 'backend']);
    Route::resource('backend/produk', ProdukController::class, ['as' => 'backend']);
    Route::get('backend/produk/{produk}/gallery', [ProdukController::class, 'gallery'])
        ->name('backend.produk.gallery');

    Route::post('foto-produk/store', [ProdukController::class, 'storeFoto'])
        ->name('backend.foto_produk.store');
    Route::delete('foto-produk/{id}', [ProdukController::class, 'destroyFoto'])
        ->name('backend.foto_produk.destroy');
});

Route::middleware(['auth', 'role:2'])->group(function () {
    Route::get('frontend/catalog', [CatalogController::class, 'index'])->name('frontend.catalog.index');
    Route::get('frontend/catalog/{produk}', [CatalogController::class, 'show'])->name('frontend.catalog.show');
    Route::get('frontend/catalog/{produk}/gallery', [CatalogController::class, 'gallery'])
        ->name('frontend.catalog.gallery');
    Route::get('frontend/realtime/produk', [CatalogController::class, 'realtimeProduk'])
        ->name('frontend.catalog.realtime');
});
