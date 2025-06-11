<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'itemable_type',
        'itemable_id',
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
    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the source document for the movement (can be a PurchaseOrder, SalesOrder, etc.).
     * Renamed from 'referenceable' to 'related' to match standard conventions,
     * but you can keep 'referenceable' if you prefer.
     */
    public function related(): MorphTo
    {
        // This assumes your column names are 'related_type' and 'related_id'
        return $this->morphTo();
    }

    /**
     * Get the location associated with the movement.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

}
