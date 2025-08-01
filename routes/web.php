<?php

use App\Http\Controllers\DashboardController;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;
use App\Livewire\Product\ProductForm;
use App\Livewire\Product\ProductList;
use App\Livewire\Category\CategoryList;
use App\Livewire\Category\CategoryForm;
use App\Livewire\Location\LocationList;
use App\Livewire\Location\LocationForm;
use App\Livewire\Supplier\SupplierList;
use App\Livewire\Product\ImportProducts;
use App\Livewire\Dashboard;
use App\Livewire\Expense\ExpenseForm;
use App\Livewire\Expense\ExpenseList;
use App\Livewire\Supplier\SupplierForm;
use App\Livewire\Inventory\StockAdjustmentForm;
use App\Livewire\SalesChannel\SalesChannelList;
use App\Livewire\SalesChannel\SalesChannelForm;
use App\Livewire\SalesOrder\SalesImport;
use App\Livewire\PurchaseOrder\PurchaseOrderList;
use App\Livewire\PurchaseOrder\PurchaseOrderForm;
use App\Livewire\SalesOrder\SalesOrderList;
use App\Livewire\SalesOrder\SalesOrderForm;


// Dashboard Route
// This is the route to your NEW Livewire component
Route::get('/', App\Livewire\Dashboard::class)->name('home');
Route::get('/dashboard', App\Livewire\Dashboard::class)->name('dashboard');
Route::post('/dashboard', [DashboardController::class, 'updateMonthlyTarget'])->name('dashboard.update-monthly-target');

// Product Form Routes
Route::get('/products', ProductList::class)->name('products.index');
Route::get('/products/create', ProductForm::class)->name('products.create');
Route::get('/products/{productId}/edit', ProductForm::class)->name('products.edit');
Route::get('/products/import', ImportProducts::class)->name('products.import');
Route::get('/products/import/template', [ImportProducts::class, 'downloadTemplate'])->name('products.import.template');


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
Route::get('/purchase-orders/import', App\Livewire\PurchaseOrder\PurchaseUpload::class)->name('purchase-orders.import');

Route::get('/purchase-orders/{purchaseOrder}/edit', PurchaseOrderForm::class)->name('purchase-orders.edit');
Route::get('/purchase-orders/{purchaseOrder}', PurchaseOrderForm::class)->name('purchase-orders.show');


// Sales Order Management Routes
Route::get('/sales-orders', SalesOrderList::class)->name('sales-orders.index');
Route::get('/sales-orders/create', SalesOrderForm::class)->name('sales-orders.create');

Route::get('/sales-orders/import', SalesImport::class)->name('sales-orders.import');
Route::get('/sales-orders/{salesOrder}/edit', SalesOrderForm::class)->name('sales-orders.edit');
Route::get('/sales-orders/{salesOrder}', SalesOrderForm::class)->name('sales-orders.show');



Route::get('/expenses', ExpenseList::class)->name('expenses.index');
Route::get('/expenses/create', ExpenseForm::class)->name('expenses.create');
Route::get('/expenses/{type}/{id}/edit', ExpenseForm::class)->name('expenses.edit');

// sales Channel
Route::get('/sales-channels', SalesChannelList::class)->name('sales-channels.index');
Route::get('/sales-channels/create', SalesChannelForm::class)->name('sales-channels.create');
Route::get('/sales-channels/{salesChannel}/edit', SalesChannelForm::class)->name('sales-channels.edit');

// Settings
Route::get('/settings', Settings::class)->name('settings.index');

// Activity Logs
Route::get('/activity-logs', App\Livewire\ActivityLogs::class)->name('activity.index');



// ... (other routes)
