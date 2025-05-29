<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
// use App\Http\Livewire\ProductForm;

// If you want the dashboard to be your application's homepage:
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// product Form

// Route::get('/productForm', ProductForm::class)->name('product.form');

// If you want to keep the 'welcome' page and have a separate '/dashboard' URL:
/*
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
*/
