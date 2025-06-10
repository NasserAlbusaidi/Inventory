<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_name',
        'cost_price',
        'selling_price',
        'barcode',
        'track_inventory',
        'location_id',
        'stock_quantity',
    ];



    /**
     * @var array
     */
    protected $appends = ['full_name_with_variant'];


    /**
     * Get the product that owns the variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the inventory movements for the product variant.
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get the purchase order items for the product variant.
     */
    public function purchaseOrderItems()
    {
        return $this->morphMany(PurchaseOrderItem::class, 'purchasable');
    }

    /**
     * get the the product name and variant name concatenated
     */

    public function getFullNameAttribute()
    {
        return $this->product->name . ' - ' . $this->variant_name;
    }

    /**
     * Get the Sales Order Item Count for the Product Variant.
     *
     * @return int
     */
    public function getSalesOrderItemCountAttribute(): int
    {
        return $this->salesOrderItems()->count();
    }

    /**
     * NEW ACCESSOR:
     *
     * @return string
     */
    public function getFullNameWithVariantAttribute(): string // <-- NEW ACCESSOR
    {
        // Eager-load the 'product' relationship for efficiency if not already loaded.
        if (!$this->relationLoaded('product')) {
            $this->load('product');
        }

        $productName = $this->product->name ?? 'Unknown Product';
        $sku = $this->product->sku ?? 'N/A'; // Assuming your Product model has an 'sku' field

        return "{$productName} - {$this->variant_name} (SKU: {$sku})";
    }

    public function salesOrderItems()
    {
        return $this->morphMany(SalesOrderItem::class, 'saleable');
    }

    public function locationInventories()
    {
        return $this->morphMany(LocationInventory::class, 'inventoriable');
    }

}
