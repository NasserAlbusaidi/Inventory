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
        Relation::morphMap([
            'product' => Product::class,
            'variant' => ProductVariant::class,
        ]);
    }
}
