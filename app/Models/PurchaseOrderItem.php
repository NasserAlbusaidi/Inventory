<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'purchasable_type',
        'purchasable_id',
        'quantity',
        'cost_price_per_unit',
    ];

    protected $casts = [
        'cost_price_per_unit' => 'decimal:2',
    ];

    /**
     * Get the purchase order that owns the item.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the product variant for the item.
     */
    public function purchasable(): BelongsTo
    {
        return $this->morphTo();
    }
    /**
     * Get the product variant that owns the item.
     */
    public function productVariant(): BelongsTo
    {
        return $this->morphTo(__FUNCTION__, 'purchasable_type', 'purchasable_id');
    }
    /**
     * Get the product that owns the item.
     */
    public function product(): BelongsTo
    {
        return $this->morphTo(__FUNCTION__, 'purchasable_type', 'purchasable_id');
    }
}
