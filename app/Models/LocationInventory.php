<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LocationInventory extends Model
{
    use HasFactory;
    protected $fillable = ['inventoriable_id', 'inventoriable_type', 'location_id', 'stock_quantity'];

    public function inventoriable()
    {
        return $this->morphTo();
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the stock quantity of a specific product in all locations.
     */
    public static function getStockQuantityForProduct($productId)
    {
        return self::where('inventoriable_id', $productId)
            ->where('inventoriable_type', Product::class)
            ->sum('stock_quantity');
    }
    /**
     * Get the stock quantity of a specific product variant in all locations.
     */
    public static function getStockQuantityForVariant($variantId)
    {
        return self::where('inventoriable_id', $variantId)
            ->where('inventoriable_type', ProductVariant::class)
            ->sum('stock_quantity');
    }
}
