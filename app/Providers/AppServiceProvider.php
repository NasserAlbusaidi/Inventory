<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PurchaseOrder;
use App\Observers\PurchaseOrderObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\SettingsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, function ($app) {
            return new SettingsService();
        });
        $this->app->singleton('settings', SettingsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PurchaseOrder::observe(PurchaseOrderObserver::class);
        Relation::enforceMorphMap([
            'product' => 'App\Models\Product',
            'variant' => 'App\Models\ProductVariant',
            'purchase_order' => 'App\Models\PurchaseOrder',
            'sales_order' => 'App\Models\SalesOrder',
            'location'       => 'App\Models\Location',
            'sales_channel'  => 'App\Models\SalesChannel',
            'category'      => 'App\Models\Category',
            'supplier'      => 'App\Models\Supplier',
        ]);
    }
}
