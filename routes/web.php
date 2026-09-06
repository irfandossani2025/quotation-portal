<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/quotations', [PortalController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [PortalController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [PortalController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [PortalController::class, 'show'])->name('quotations.show');
    Route::post('/quotations/{quotation}/issue', [PortalController::class, 'issue'])->name('quotations.issue');
    Route::get('/quotations/{quotation}/pdf', [PortalController::class, 'pdf'])->name('quotations.pdf');
    Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');
    Route::get('/pricing/{quotation}', [PricingController::class, 'edit'])->name('pricing.edit');
    Route::put('/pricing/{quotation}', [PricingController::class, 'update'])->name('pricing.update');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
});
