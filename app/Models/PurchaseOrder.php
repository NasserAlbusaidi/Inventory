<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'order_number',
        'order_date',
        // 'expected_delivery_date',
        'receiving_location_id',
        'status',
        'total_amount',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the supplier that owns the purchase order.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the location associated with the purchase order (receiving location).
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'receiving_location_id');
    }

    /**
     * Get the Product
      */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the items for the purchase order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function receivingLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'receiving_location_id');
    }

    /**
     * Get the total amount of the purchase order.
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->cost_price_per_unit;
        });
    }
    /**
     * Get the status of the purchase order.
     */
    public function getStatusAttribute(): string
    {
        return $this->attributes['status'] ?? 'pending';
    }
    /**
     * Set the status of the purchase order.
     */
    public function setStatusAttribute(string $value): void
    {
        $validStatuses = ['pending', 'received', 'cancelled'];
        if (in_array($value, $validStatuses)) {
            $this->attributes['status'] = $value;
        } else {
            throw new \InvalidArgumentException("Invalid status: $value");
        }
    }
    /**
     * Get Product Name and Variant Name concatenated
     */
    public function getFullNameAttribute(): string
    {
        return $this->supplier->name . ' - ' . $this->order_number;
    }
}
