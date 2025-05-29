<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Livewire\ProductForm;
use App\Livewire\Category\CategoryList;
use App\Livewire\Category\CategoryForm;
use App\Livewire\Location\LocationList; // Add this
use App\Livewire\Location\LocationForm; // Add this

// ... (other route groups and a '/' route for dashboard) ...

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Product Form Routes
Route::get('/products/create', ProductForm::class)->name('products.create');
Route::get('/products/{productId}/edit', ProductForm::class)->name('products.edit');

// Category Management Routes
Route::get('/categories', CategoryList::class)->name('categories.index');
Route::get('/categories/create', CategoryForm::class)->name('categories.create');
Route::get('/categories/{category}/edit', CategoryForm::class)->name('categories.edit');

// Location Management Routes
Route::get('/locations', LocationList::class)->name('locations.index');
Route::get('/locations/create', LocationForm::class)->name('locations.create');
Route::get('/locations/{location}/edit', LocationForm::class)->name('locations.edit');

// ... (other routes)
