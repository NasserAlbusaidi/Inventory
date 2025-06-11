<?php

namespace App\Livewire\PurchaseOrder;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\LocationInventory;
use App\Models\ProductVariant;
use App\Models\Location;
use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class PurchaseOrderForm extends Component
{
    public ?PurchaseOrder $purchaseOrderInstance = null;

    // PO Header fields (No changes here)
    public $supplier_id = null;
    public $order_number = '';
    public $receiving_location_id = null;
    public $order_date;
    public $status = 'ordered';
    public $total_amount = 0;

    public $items = [];

    // For dropdowns
    public Collection $allSuppliers;
    public Collection $allLocations;
    public Collection $allPurchasableItems; // <-- RENAMED for clarity

    public $poStatuses = ['ordered', 'received', 'cancelled'];

    protected function rules(): array
    {
        $rules = [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_number' => [
                'required',
                'string',
                'max:255',
                $this->purchaseOrderInstance && $this->purchaseOrderInstance->exists
                    ? Rule::unique('purchase_orders', 'order_number')->ignore($this->purchaseOrderInstance->id)
                    : Rule::unique('purchase_orders', 'order_number')
            ],
            'order_date' => 'required|date',
            'status' => 'required|in:' . implode(',', $this->poStatuses),
            'items' => 'required|array|min:1',
            'receiving_location_id' => 'required|nullable|exists:locations,id',
        ];

        foreach ($this->items as $index => $item) {
            // UPDATED validation key
            $rules["items.{$index}.selected_item_key"] = 'required';
            $rules["items.{$index}.quantity"] = 'required|integer|min:1';
            $rules["items.{$index}.cost_price_per_unit"] = 'required|numeric|min:0';
        }
        return $rules;
    }

    protected $messages = [
        // UPDATED messages for new keys
        'items.required' => 'Please add at least one item.',
        'items.*.selected_item_key.required' => 'A product or variant is required.',
        'items.*.quantity.required' => 'Qty is required.',
        'items.*.cost_price_per_unit.required' => 'Cost is required.',
        'receiving_location_id.required_if' => 'Receiving location is required when status is "received".',
    ];

    public function mount($purchaseOrder = null)
    {
        $this->allSuppliers = Supplier::orderBy('name')->get();
        $this->allLocations = Location::orderBy('name')->get();
        $this->loadPurchasableItems(); // <-- USE NEW METHOD

        if ($purchaseOrder) {
            $this->loadExistingPurchaseOrder($purchaseOrder);
        } else {
            $this->initializeNewPurchaseOrder();
        }
        $this->calculateTotalAmount();
    }

    // NEW METHOD: Loads a unified list of simple products and variants
    protected function loadPurchasableItems()
    {
        // 1. Get ONLY products that are marked as having NO variants.
        // This is the critical filter that fixes your issue.
        $simpleProducts = Product::where('has_variants', false) // <-- THE FIX
            ->select('id', 'name', 'sku', 'cost_price')
            ->orderBy('name')
            ->get();


        // 2. Get ALL product variants. Their parent products (like "Body lotion")
        // will be ignored by the first query because they have has_variants = true.
        $variantProducts = ProductVariant::with('product:id,name,sku')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select('product_variants.id', 'product_variants.variant_name', 'product_variants.cost_price', 'product_variants.product_id')
            ->orderBy('products.name')
            ->orderBy('product_variants.variant_name')
            ->get();

        // 3. Combine them into a single, clean collection.
        $purchasableItems = collect([]);

        foreach ($simpleProducts as $product) {
            $purchasableItems->push([
                'key' => 'Product_' . $product->id,
                'display_name' => "{$product->name} (SKU: {$product->sku})",
                'cost' => $product->cost_price,
            ]);
        }

        foreach ($variantProducts as $variant) {
            $displayName = "{$variant->product->name} - {$variant->variant_name} (SKU: {$variant->product->sku})";
            $purchasableItems->push([
                'key' => 'ProductVariant_' . $variant->id,
                'display_name' => $displayName,
                'cost' => $variant->cost_price,
            ]);
        }

        $this->allPurchasableItems = $purchasableItems->sortBy('display_name')->values();
    }
    // NEW METHOD: To properly load an existing polymorphic PO
    private function loadExistingPurchaseOrder($purchaseOrder)
    {
        $loadedPo = ($purchaseOrder instanceof PurchaseOrder) ? $purchaseOrder : PurchaseOrder::with('items.purchasable')->find($purchaseOrder);

        if (!$loadedPo) {
            session()->flash('error', 'Purchase Order not found.');
            $this->initializeNewPurchaseOrder();
            return;
        }

        $this->purchaseOrderInstance = $loadedPo;
        $this->supplier_id = $this->purchaseOrderInstance->supplier_id;
        $this->order_number = $this->purchaseOrderInstance->order_number;
        $this->order_date = Carbon::parse($this->purchaseOrderInstance->order_date)->format('Y-m-d');
        $this->status = $this->purchaseOrderInstance->status;
        $this->receiving_location_id = $this->purchaseOrderInstance->receiving_location_id;

        $this->items = $this->purchaseOrderInstance->items->map(function ($item) {
            $purchasable = $item->purchasable;
            $key = null;
            $displayName = 'Unknown Item';

            if ($purchasable instanceof Product) {
                $key = 'Product_' . $purchasable->id;
                $displayName = "{$purchasable->name} (SKU: {$purchasable->sku})";
            } elseif ($purchasable instanceof ProductVariant) {
                $purchasable->loadMissing('product:id,name,sku');
                $key = 'ProductVariant_' . $purchasable->id;
                $displayName = "{$purchasable->product->name} - {$purchasable->variant_name} (SKU: {$purchasable->product->sku})";
            }

            return [
                'id' => $item->id,
                'selected_item_key' => $key,
                'quantity' => $item->quantity,
                'cost_price_per_unit' => (float)$item->cost_price_per_unit,
                'variant_name_display' => $displayName,
            ];
        })->toArray();
    }


    private function initializeNewPurchaseOrder()
    {
        $this->purchaseOrderInstance = new PurchaseOrder();
        $this->order_date = Carbon::now()->format('Y-m-d');
        $this->status = 'ordered';
        $this->supplier_id = $this->allSuppliers->first()->id ?? null;
        $this->receiving_location_id = $this->allLocations->first()->id ?? null;
        $this->items = [];
        $this->addItem();
        $this->generateOrderNumber();
    }

    private function generateOrderNumber($return = false)
    {
        $prefix = 'PO-';
        $datePart = Carbon::now()->format('ymd');
        $lastPo = PurchaseOrder::where('order_number', 'LIKE', "{$prefix}{$datePart}%")->orderBy('order_number', 'desc')->first();
        $sequence = $lastPo ? ((int) substr(strrchr($lastPo->order_number, "-"), 1)) + 1 : 1;
        $orderNumber = $prefix . $datePart . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        if ($return) {
            return $orderNumber;
        }
        $this->order_number = $orderNumber;
    }

    public function addItem()
    {
        $this->items[] = [
            'id' => null,
            'selected_item_key' => null,
            'quantity' => 1,
            'cost_price_per_unit' => 0.00,
            'variant_name_display' => 'Select Item',
        ];
        $this->calculateTotalAmount();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        if (empty($this->items)) {
            $this->addItem();
        }
        $this->calculateTotalAmount();
    }

    // UPDATED updated method
    public function updated($propertyName)
    {
        if (preg_match('/items\.(\d+)\.selected_item_key/', $propertyName, $matches)) {
            $index = (int)$matches[1];
            $key = $this->items[$index]['selected_item_key'];

            if (empty($key)) {
                $this->items[$index]['cost_price_per_unit'] = 0.00;
                $this->items[$index]['variant_name_display'] = 'Select Item';
            } else {
                $selectedItem = $this->allPurchasableItems->firstWhere('key', $key);
                if ($selectedItem) {
                    $this->items[$index]['cost_price_per_unit'] = (float)$selectedItem['cost'];
                    $this->items[$index]['variant_name_display'] = $selectedItem['display_name'];
                }
            }
        }
        $this->calculateTotalAmount();


        if (preg_match('/items\.(\d+)\.(quantity|cost_price_per_unit)/', $propertyName)) {
            $this->calculateTotalAmount();
        }
    }

    public function calculateTotalAmount($return = false)
    {
        $total = collect($this->items)->sum(function ($item) {
            return (float)($item['quantity'] ?? 0) * (float)($item['cost_price_per_unit'] ?? 0);
        });

        if ($return) {
            return $total;
        }
        $this->total_amount = $total;
    }

    public function savePurchaseOrder()
    {
        $this->validate();

        $stockUpdated = false;

        $isNewOrder = !$this->purchaseOrderInstance?->exists;
        $originalStatus = $isNewOrder ? null : $this->purchaseOrderInstance->getOriginal('status');
        $originalReceivingLocationId = $isNewOrder ? null : $this->purchaseOrderInstance->getOriginal('receiving_location_id');

        $originalReceivedItemsData = [];
        if (!$isNewOrder && $originalStatus === 'received' && $this->purchaseOrderInstance) {
            $originalReceivedItemsData = $this->purchaseOrderInstance->items()->with('purchasable')->get()->map(function ($item) {
                if (!$item->purchasable) return null;
                return [
                    'purchasable_model' => $item->purchasable,
                    'quantity' => $item->quantity,
                ];
            })->filter()->values()->all();
        }
        DB::transaction(function () use (&$stockUpdated, $isNewOrder, $originalStatus, $originalReceivedItemsData, $originalReceivingLocationId) {
            $poData = [
                'supplier_id' => $this->supplier_id,
                'order_number' => $this->order_number ?: $this->generateOrderNumber(true),
                'order_date' => $this->order_date,
                'receiving_location_id' => $this->receiving_location_id,
                'status' => $this->status,
                'total_amount' => $this->calculateTotalAmount(true),
            ];

            $this->purchaseOrderInstance = PurchaseOrder::updateOrCreate(
                ['id' => $this->purchaseOrderInstance?->id],
                $poData
            );

            // Item saving logic is fine...
            $currentItemIds = [];
            foreach ($this->items as $itemData) {
                if (empty($itemData['selected_item_key'])) continue;
                list($type, $id) = explode('_', $itemData['selected_item_key']);
                $morphAlias = ($type === 'ProductVariant') ? 'variant' : 'product';
                $poItem = $this->purchaseOrderInstance->items()->updateOrCreate(
                    [
                        'purchasable_type' => $morphAlias,
                        'purchasable_id' => (int)$id
                    ],
                    [
                        'quantity' => $itemData['quantity'],
                        'cost_price_per_unit' => $itemData['cost_price_per_unit'],
                    ]
                );
                $currentItemIds[] = $poItem->id;
            }
            if (!$isNewOrder) {
                $this->purchaseOrderInstance->items()->whereNotIn('id', $currentItemIds)->delete();
            }

            // --- 3. REVISED STOCK UPDATE LOGIC ---
            $currentStatus = $this->purchaseOrderInstance->status;
            $locationId = $this->purchaseOrderInstance->receiving_location_id;
            $orderId = $this->purchaseOrderInstance->id;
            $orderMorphClass = $this->purchaseOrderInstance->getMorphClass();

            // Scenario 1: Order becomes 'received' (was not 'received' before)
            if ($currentStatus === 'received' && $originalStatus !== 'received' && $locationId) {
                Log::info("PO #{$this->purchaseOrderInstance->order_number}: Status changed to 'received'. Processing stock updates.");
                foreach ($this->purchaseOrderInstance->fresh()->items as $item) {
                    $purchasable = $item->purchasable;
                    if ($purchasable) {
                        $this->adjustStockAndLogMovement($purchasable, $item->quantity, 'purchase_receipt', $locationId, $orderId, $orderMorphClass);
                    }
                }
                $stockUpdated = true;
            }
            // Scenario 2: Order was 'received' and now is 'cancelled'
            elseif ($currentStatus === 'cancelled' && $originalStatus === 'received' && $originalReceivingLocationId) {
                Log::info("PO #{$this->purchaseOrderInstance->order_number}: Status changed from 'received' to 'cancelled'. Reverting stock.");

                // Check if this cancellation might lead to negative stock
                $potentialNegativeStockWarning = false;
                foreach ($originalReceivedItemsData as $originalItem) {
                    $purchasable = $originalItem['purchasable_model'];
                    $currentStock = LocationInventory::where('inventoriable_type', $purchasable->getMorphClass())
                        ->where('inventoriable_id', $purchasable->id)
                        ->where('location_id', $originalReceivingLocationId)
                        ->value('stock_quantity') ?? 0;
                        if (($currentStock - $originalItem['quantity']) < 0) {
                        $potentialNegativeStockWarning = true;
                        break;
                    }
                }
                if ($potentialNegativeStockWarning) {
                    session()->flash('warning_negative_stock', 'Warning: Cancelling this received Purchase Order may result in negative stock levels for some items, possibly due to prior sales of these items.');
                }

                foreach ($originalReceivedItemsData as $originalItem) {
                    $this->adjustStockAndLogMovement($originalItem['purchasable_model'], -$originalItem['quantity'], 'purchase_cancellation', $originalReceivingLocationId, $orderId, $orderMorphClass);
                }
                $stockUpdated = true;
            }
            // Scenario 3: Order was 'received' and REMAINS 'received' but location or items might have changed
            // This is more complex and would involve reverting old stock and applying new.
            // For now, we only handle the distinct transitions to/from 'received'.
            // If you need to handle item/location changes on an already received PO, this section would need expansion.
            elseif ($currentStatus === 'received' && $originalStatus === 'received') {
                if ($locationId != $originalReceivingLocationId) {
                    Log::info("PO #{$this->purchaseOrderInstance->order_number}: Receiving location changed on an already received order. Manual stock adjustment might be needed or implement full reversal/reapplication logic.");
                    // Potentially revert from $originalReceivingLocationId and apply to $locationId
                    // This requires careful handling of item changes as well.
                }
            }
        });

        // Final checks and redirect
        cache()->forget('dashboard_data');
        session()->flash('message', 'Purchase Order ' . ($this->purchaseOrderInstance->wasRecentlyCreated ? 'created' : 'updated') . ' successfully.');

        // Add a specific message if stock was updated
        if ($stockUpdated) {
            session()->flash('stock_update_message', 'Stock levels have been updated.');
        }

        return redirect()->route('purchase-orders.index');
    }

    private function adjustStockAndLogMovement(\Illuminate\Database\Eloquent\Model $purchasable, int $quantityChange, string $movementType, int $locationId, int $orderId, string $orderMorphClass)
    {
        if ($quantityChange == 0) return;

        $inventoryRecord = LocationInventory::firstOrCreate(
            [
                'inventoriable_type' => $purchasable->getMorphClass(),
                'inventoriable_id'   => $purchasable->id,
                'location_id'        => $locationId,
            ],
            ['stock_quantity' => 0]
        );
        $inventoryRecord->increment('stock_quantity', $quantityChange);

        InventoryMovement::create([
            'itemable_type' => $purchasable->getMorphClass(),
            'itemable_id'   => $purchasable->id,
            'location_id'   => $locationId,
            'quantity'      => $quantityChange,
            'type'          => $movementType,
            'related_type'  => $orderMorphClass, // Use the morph class of PurchaseOrder
            'related_id'    => $orderId,
        ]);
        Log::info("Stock for " . class_basename($purchasable) . " #{$purchasable->id} changed by {$quantityChange} at Location #{$locationId} due to PO #{$orderId} - Type: {$movementType}. New stock: {$inventoryRecord->fresh()->stock_quantity}");
    }

    public function render()
    {
        return view('livewire.purchase-order.purchase-order-form')
            ->layoutData(['title' => $this->purchaseOrderInstance?->exists ? 'Edit Purchase Order' : 'Create Purchase Order'])
            ->layout('components.layouts.livewire');
    }
}
