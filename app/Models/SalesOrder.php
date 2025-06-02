<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'channel',
        'location_id',
        'price',
        'order_date',
        'customer_details',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'customer_details' => 'array', // Cast JSON column to array
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the location associated with the sales order (if boutique sale).
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the items for the sales order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }
}
