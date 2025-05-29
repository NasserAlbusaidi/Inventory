<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'location_id',
        'type',
        'quantity',
        'reason',
        'reference_type',
        'reference_id',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Get the product variant associated with the movement.
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the location associated with the movement.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the user who performed the movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // Assuming a User model exists
    }
}
