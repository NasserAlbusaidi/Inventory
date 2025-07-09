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

    public function canBeDeleted(): array
    {
        if ($this->total_stock > 0) {
            return [false, "This product cannot be deleted because it is still in stock."];
        }

        $isLinkedToOrders = $this->salesOrderItems()->exists() || $this->purchaseOrderItems()->exists();

        if ($isLinkedToOrders) {
            return [false, "This product cannot be deleted because it is linked to existing sales or purchase orders."];
        }

        return [true, null];
    }

    public function scopeWhereStatus($query, $status)
    {
        $lowStockThreshold = app('settings')->get('low_stock_threshold', 5);

        if ($status === 'low_stock') {
            return $query->where(function ($query) use ($lowStockThreshold) {
                $query->where(fn($q) => $q->where('has_variants', false)->whereHas('locationInventories', fn($i) => $i->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', $lowStockThreshold)))
                    ->orWhere(fn($q) => $q->where('has_variants', true)->whereHas('variants.locationInventories', fn($i) => $i->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', $lowStockThreshold)));
            });
        }

        if ($status === 'dead_stock') {
            $deadStockCutoffDate = now()->subDays(90);
            $soldItems = SalesOrderItem::whereHas('salesOrder', fn($q) => $q->where('created_at', '>=', $deadStockCutoffDate))
                ->select('saleable_id', 'saleable_type')->distinct()->get();
            $soldProductIds = $soldItems->where('saleable_type', 'product')->pluck('saleable_id');
            $soldVariantIds = $soldItems->where('saleable_type', 'variant')->pluck('saleable_id');

            return $query->where(function ($query) use ($soldProductIds, $soldVariantIds) {
                $query->where(function ($q) use ($soldProductIds) {
                    $q->where('has_variants', false)
                        ->whereHas('locationInventories', fn($i) => $i->where('stock_quantity', '>', 0))
                        ->whereNotIn('id', $soldProductIds);
                })->orWhere(function ($q) use ($soldVariantIds) {
                    $q->where('has_variants', true)
                        ->where(fn($sub) => $sub->whereHas('locationInventories', fn($i) => $i->where('stock_quantity', '>', 0))
                            ->orWhereHas('variants.locationInventories', fn($i) => $i->where('stock_quantity', '>', 0)))
                        ->whereDoesntHave('variants', fn($varQ) => $varQ->whereIn('id', $soldVariantIds));
                });
            });
        }

        if ($status === 'out_of_stock') {
            return $query->where(function ($query) {
                $query->where(fn($q) => $q->where('has_variants', false)->whereDoesntHave('locationInventories', fn($i) => $i->where('stock_quantity', '>', 0)))
                    ->orWhere(fn($q) => $q->where('has_variants', true)->whereDoesntHave('variants.locationInventories', fn($i) => $i->where('stock_quantity', '>', 0)));
            });
        }

        if ($status === 'in_stock') {
            return $query->where(function ($query) {
                $query->where(fn($q) => $q->where('has_variants', false)->whereHas('locationInventories', fn($i) => $i->where('stock_quantity', '>', 0)))
                    ->orWhere(fn($q) => $q->where('has_variants', true)->whereHas('variants.locationInventories', fn($i) => $i->where('stock_quantity', '>', 0)));
            });
        }

        return $query;
    }
}
