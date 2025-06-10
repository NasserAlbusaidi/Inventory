<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use App\Models\InventoryMovement;
use App\Models\Product;          // <-- Import the Product model
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use App\Models\LocationInventory;
use Illuminate\Support\Facades\Log;

class PurchaseOrderObserver
{
    /**
     * Handle the PurchaseOrder "created" event.
     * This runs when a new PO is created.
     */
    public function created(PurchaseOrder $purchaseOrder): void
    {
        // // If a PO is created with the status 'received' right away
        // if ($purchaseOrder->status === 'received') {
        //     Log::info("PO #{$purchaseOrder->order_number} created with status 'received'. Processing stock updates.");
        //     $this->handleStockUpdate($purchaseOrder);
        // }
    }

    /**
     * Handle the PurchaseOrder "updated" event.
     * This runs when an existing PO is updated.
     */
    public function updated(PurchaseOrder $purchaseOrder)
    {
        // // Only run the logic if the 'status' field was changed TO 'received'
        // if ($purchaseOrder->isDirty('status') && $purchaseOrder->status === 'received') {
        //     Log::info("PO #{$purchaseOrder->order_number} updated to status 'received'. Processing stock updates.");
        //     $this->handleStockUpdate($purchaseOrder);
        // }
    }

    /**
     * A helper method to handle the actual stock and inventory movement logic.
     * This avoids repeating code in both created() and updated().
     *
     * @param PurchaseOrder $purchaseOrder
     * @return void
     */
    private function handleStockUpdate(PurchaseOrder $purchaseOrder)
    {
        Log::info("PO #{$purchaseOrder->order_number} received. Processing stock updates.");
        $purchaseOrder->loadMissing('items.purchasable');

        // Get the location where the items are being received.
        $locationId = $purchaseOrder->receiving_location_id;
        if (!$locationId) {
            Log::error("Cannot update stock for PO #{$purchaseOrder->order_number}. No receiving location set.");
            return; // Stop if there's no location
        }

        foreach ($purchaseOrder->items as $item) {
            $purchasable = $item->purchasable;
            if ($purchasable) {
                // Find or create the specific inventory record for this item at this location.
                $inventoryRecord = LocationInventory::firstOrCreate(
                    [
                        'inventoriable_type' => get_class($purchasable),
                        'inventoriable_id'   => $purchasable->id,
                        'location_id'        => $locationId,
                    ]
                    // The stock_quantity will default to 0 if it's a new record.
                );

                // Add the new quantity to the existing stock at this location.
                $inventoryRecord->increment('stock_quantity', $item->quantity);

                Log::info("Stock for " . class_basename($purchasable) . " #{$purchasable->id} updated at Location #{$locationId}. New stock: {$inventoryRecord->stock_quantity}");
            }
        }
    }
    // ... other observer methods like deleted() can remain empty for now ...
}
