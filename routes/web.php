<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Product\ProductForm;
use App\Livewire\Product\ProductList;
use App\Livewire\Category\CategoryList;
use App\Livewire\Category\CategoryForm;
use App\Livewire\Location\LocationList;
use App\Livewire\Location\LocationForm;
use App\Livewire\Supplier\SupplierList;
use App\Livewire\Supplier\SupplierForm;
use App\Livewire\Inventory\StockAdjustmentForm;
use App\Livewire\PurchaseOrder\PurchaseOrderList;
use App\Livewire\PurchaseOrder\PurchaseOrderForm;
use App\Livewire\SalesOrder\SalesOrderList;
use App\Livewire\SalesOrder\SalesOrderForm;

// Dashboard Route
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Product Form Routes
Route::get('/products', ProductList::class)->name('products.index'); // Add this for listing
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

// Supplier Management Routes
Route::get('/suppliers', SupplierList::class)->name('suppliers.index');
Route::get('/suppliers/create', SupplierForm::class)->name('suppliers.create');
Route::get('/suppliers/{supplier}/edit', SupplierForm::class)->name('suppliers.edit');

// Inventory Management Routes
Route::get('/inventory/adjustments/create', StockAdjustmentForm::class)->name('inventory.adjustments.create');

// Purchase Order Management Routes
Route::get('/purchase-orders', PurchaseOrderList::class)->name('purchase-orders.index');
Route::get('/purchase-orders/create', PurchaseOrderForm::class)->name('purchase-orders.create');
Route::get('/purchase-orders/{purchaseOrder}/edit', PurchaseOrderForm::class)->name('purchase-orders.edit'); // For editing/viewing
Route::get('/purchase-orders/{purchaseOrder}', PurchaseOrderForm::class)->name('purchase-orders.show');

// Sales Order Management Routes
Route::get('/sales-orders', SalesOrderList::class)->name('sales-orders.index');
Route::get('/sales-orders/create', SalesOrderForm::class)->name('sales-orders.create');
Route::get('/sales-orders/{salesOrder}/edit', SalesOrderForm::class)->name('sales-orders.edit'); // For editing/viewing
Route::get('/sales-orders/{salesOrder}', SalesOrderForm::class)->name('sales-orders.show'); // Can be same as edit or a dedicated view

// ... (other routes)
