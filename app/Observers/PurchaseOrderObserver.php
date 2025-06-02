<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use App\Models\InventoryMovement;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth; // To get the currently authenticated user
use Illuminate\Support\Facades\Log;

class PurchaseOrderObserver
{
    /**
     * Handle the PurchaseOrder "updated" event.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return void
     */
    public function updated(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->isDirty('status') && $purchaseOrder->status === 'received') {
            Log::info("Purchase Order #{$purchaseOrder->order_number} status changed to received. Processing stock updates.");

            // Eager load items if not already loaded to avoid N+1 queries
            $purchaseOrder->loadMissing('items.productVariant'); // Ensures items and their variants are loaded

            foreach ($purchaseOrder->items as $item) {
                // $variant = ProductVariant::find($item->product_variant_id); // This would be an N+1 query if items.productVariant wasn't loaded
                $variant = $item->productVariant; // Access via loaded relationship

                if ($variant) {
                    $quantityToAdd = $item->quantity;
                    $variant->increment('stock_quantity', $quantityToAdd);
                    Log::info("Stock updated for Variant ID {$variant->id}. Added {$quantityToAdd}. New stock: {$variant->stock_quantity}");

                    InventoryMovement::create([
                        'product_variant_id' => $item->product_variant_id,
                        'location_id' => $purchaseOrder->receiving_location_id, // Needs a receiving location
                        'type' => 'purchase_receipt',
                        'quantity' => $quantityToAdd,
                        'reason' => 'Received from PO #' . $purchaseOrder->order_number,
                        'referenceable_id' => $purchaseOrder->id,
                        'referenceable_type' => PurchaseOrder::class, // Correct
                        'user_id' => Auth::id(),
                    ]);
                    Log::info("Inventory movement created for Variant ID {$variant->id} from PO #{$purchaseOrder->order_number}.");

                } else {
                    // Corrected Log::error usage
                    Log::error("Product Variant ID {$item->product_variant_id} not found for PO Item ID {$item->id} on PO #{$purchaseOrder->order_number}.");
                }
            }
        }
    }

    /**
     * Handle the PurchaseOrder "created" event.
     */
    public function created(PurchaseOrder $purchaseOrder): void
    {
        //
    }


    /**
     * Handle the PurchaseOrder "deleted" event.
     */
    public function deleted(PurchaseOrder $purchaseOrder): void
    {
        // If a PO is deleted, you might want to reverse stock movements if it was received.
        // This can get complex and depends on your business rules (e.g., can't delete received POs).
    }

    /**
     * Handle the PurchaseOrder "restored" event.
     */
    public function restored(PurchaseOrder $purchaseOrder): void
    {
        //
    }

    /**
     * Handle the PurchaseOrder "force deleted" event.
     */
    public function forceDeleted(PurchaseOrder $purchaseOrder): void
    {
        //
    }
}
