<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\PurchaseOrderItem;
use App\Models\SalesOrderItem;
use App\Models\LocationInventory;
use App\Models\InventoryMovement;

class ProductObserver
{
    /**
     * Handle the Product "deleting" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleting(Product $product): void
    {
        // Get all variants associated with this product
        $variants = $product->variants;

        // 1. Delete associated Purchase Order Items
        // Delete items linked directly to the product
        PurchaseOrderItem::where('purchasable_type', 'product')->where('purchasable_id', $product->id)->delete();
        // Delete items linked to any of the product's variants
        if ($variants->isNotEmpty()) {
            PurchaseOrderItem::where('purchasable_type', 'variant')->whereIn('purchasable_id', $variants->pluck('id'))->delete();
        }

        // 2. Delete associated Sales Order Items
        // Delete items linked directly to the product
        SalesOrderItem::where('saleable_type', 'product')->where('saleable_id', $product->id)->delete();
        // Delete items linked to any of the product's variants
        if ($variants->isNotEmpty()) {
            SalesOrderItem::where('saleable_type', 'variant')->whereIn('saleable_id', $variants->pluck('id'))->delete();
        }

        // 3. Delete associated Inventory Records (Snapshots and Ledger)
        // Delete records for the product itself
        LocationInventory::where('inventoriable_type', 'product')->where('inventoriable_id', $product->id)->delete();
        InventoryMovement::where('itemable_type', 'product')->where('itemable_id', $product->id)->delete();
        // Delete records for all its variants
        if ($variants->isNotEmpty()) {
            $variantIds = $variants->pluck('id');
            LocationInventory::where('inventoriable_type', 'variant')->whereIn('inventoriable_id', $variantIds)->delete();
            InventoryMovement::where('itemable_type', 'variant')->whereIn('itemable_id', $variantIds)->delete();
        }

        // 4. Finally, delete the variants themselves
        $product->variants()->delete();
    }
}
