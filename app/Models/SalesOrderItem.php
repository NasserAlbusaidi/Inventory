<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'saleable_type',
        'saleable_id',
        'quantity',
        'price_per_unit',
        'price',
    ];

    protected $casts = [
        'price_per_unit' => 'decimal:2',
    ];

    /**
     * Get the sales order that owns the item.
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    /**
     * Get the product variant for the item.
     */
    public function saleable(): MorphTo
    {
        return $this->morphTo();
    }
}
