<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\InventoryMovement;
use App\Models\SalesOrder;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the inventory movements for the location.
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get the sales orders for the location (for boutique sales).
     */
    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    /**
     * Get the recurring expenses for the location.
     */

    public function recurringExpenses(): HasMany
    {
        return $this->hasMany(RecurringExpense::class);
    }

    public function inventories()
    {
        // A location has many inventory stock records.
        return $this->hasMany(LocationInventory::class);
    }
}
