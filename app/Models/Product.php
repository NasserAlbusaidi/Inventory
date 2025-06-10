<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\ProductVariant;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'category_id',
        'image_url',
        'has_variants',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'track_inventory',
        'barcode',
    ];

    protected $casts = [
        'has_variants' => 'boolean',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'track_inventory' => 'boolean',
    ];

    /**
     * Get the variants for the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the category that owns the product.
     */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }



    /**
     * Get the display price for the product.
     * If it has variants, this could be a range or a starting price.
     * For simplicity, this returns a formatted string for the price range.
     */
    public function getDisplayPriceAttribute()
    {
        if ($this->has_variants && $this->variants()->exists()) {
            if (! $this->relationLoaded('variants')) {
                $this->load('variants');
            }
            $min = $this->variants->min('selling_price');
            $max = $this->variants->max('selling_price');
            return ($min === $max) ? number_format($min, 2) : number_format($min, 2) . ' - ' . number_format($max, 2);
        }
        return number_format($this->selling_price, 2);
    }

    public function purchaseOrderItems()
    {
        return $this->morphMany(PurchaseOrderItem::class, 'purchasable');
    }

    /**
     * Get the sales order items for the product.
     */
    public function salesOrderItems()
    {
        return $this->morphMany(SalesOrderItem::class, 'saleable');
    }

    public function locationInventories()
    {
        return $this->morphMany(LocationInventory::class, 'inventoriable');
    }

    public function variantsLocationInventories()
{
    return $this->hasManyThrough(
        LocationInventory::class, // The final model we want
        ProductVariant::class,           // The intermediate model
        'product_id',             // Foreign key on variants table
        'inventoriable_id',       // Foreign key on location_inventories table
        'id',                     // Local key on products table
        'id'                      // Local key on variants table
    )->where('location_inventories.inventoriable_type', 'variant');
}

protected function totalStock(): Attribute
{
    return Attribute::make(
        get: function ($value) {
            if ($this->has_variants) {
                // Use the pre-loaded sum from the 'variantsLocationInventories' relationship
                return (int) $this->variant_stock_sum ?? 0;
            } else {
                // Use the pre-loaded sum from the 'locationInventories' relationship
                return (int) $this->direct_stock_sum ?? 0;
            }
        }
    );

}

}
