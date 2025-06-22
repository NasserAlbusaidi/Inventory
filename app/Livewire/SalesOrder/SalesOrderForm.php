<?php

namespace App\Livewire\SalesOrder;

use App\Models\Activity;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryMovement;
use App\Models\LocationInventory; // <-- IMPORTANT: Import the new model
use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\SalesChannel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SalesOrderForm extends Component
{
    public ?SalesOrder $salesOrderInstance = null;

    // --- REVISED HEADER ---
    public $sales_channel_id = null;
    public $location_id = null;
    public $order_date;
    public $customer_name = '';
    public $customer_email = '';
    public $customer_phone = '';
    public $status = 'pending';
    public $total_amount = 0;
    public $order_number = '';

    public $items = [];
    public $originalFulfilledItemsData = [];


    // --- REVISED DROPDOWNS ---
    public Collection $allLocations;
    public Collection $allSalesChannels;
    public Collection $allSellableItems;

    // Use statuses that make sense for a fulfillment workflow
    public $soStatuses = ['pending', 'fulfilled', 'cancelled'];

    protected function rules(): array
    {
        $rules = [
            // --- REVISED RULES ---
            'sales_channel_id' => 'required|exists:sales_channels,id',
            'location_id' => 'required|exists:locations,id', // Always required
            'order_date' => 'required|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255', // Now optional
            'customer_phone' => 'nullable|string|max:50',
            'status' => 'required|in:' . implode(',', $this->soStatuses),
            'items' => 'required|array|min:1',
        ];

        foreach ($this->items as $index => $item) {
            $rules["items.{$index}.selected_item_key"] = 'required';
            $rules["items.{$index}.quantity"] = 'required|integer|min:1';
            $rules["items.{$index}.price_per_unit"] = 'required|numeric|min:0';

            // Dynamic stock validation (your existing logic is good)
            if (!empty($item['selected_item_key']) && isset($item['available_stock'])) {
                $rules["items.{$index}.quantity"] .= '|max:' . (int)$item['available_stock'];
            }
        }
        return $rules;
    }

    protected $messages = [
        'sales_channel_id.required' => 'Please select a sales channel.',
        'items.required' => 'Please add at least one item to the sales order.',
        'items.*.selected_item_key.required' => 'A product or variant is required.',
        'items.*.quantity.required' => 'Quantity is required.',
        'items.*.quantity.min' => 'Quantity must be at least 1.',
        'items.*.quantity.max' => 'Quantity cannot exceed available stock for the selected location.',
        'items.*.price_per_unit.required' => 'Price is required.',
        'location_id.required_if' => 'Location is required for boutique sales.'
    ];

    public function mount($salesOrder = null)
    {
        $this->allLocations = Location::orderBy('name')->get();
        $this->allSalesChannels = SalesChannel::orderBy('name')->get(); // Load from DB
        $this->allSellableItems = collect([]);

        if ($salesOrder) {
            $this->loadExistingSalesOrder($salesOrder);
        } else {
            $this->initializeNewSalesOrder();
        }

        $this->loadSellableItems($this->location_id);
        $this->calculateTotalAmount();
    }

    private function initializeNewSalesOrder()
    {
        $this->salesOrderInstance = new SalesOrder();
        $this->order_date = Carbon::now()->format('Y-m-d');
        $this->status = 'pending';
        $this->sales_channel_id = $this->allSalesChannels->first()->id ?? null;
        $this->location_id = $this->allLocations->first()->id ?? null;
        $this->generateOrderNumber();
        $this->items = [];
        $this->addItem();
    }


    private function loadExistingSalesOrder($salesOrder)
    {
        // Load the order with its relationships
        $loadedSo = ($salesOrder instanceof SalesOrder)
            ? $salesOrder->loadMissing(['items.saleable', 'salesChannel'])
            : SalesOrder::with(['items.saleable', 'salesChannel'])->find($salesOrder);

        if (!$loadedSo) {
            session()->flash('error', 'Sales Order not found.');
            $this->initializeNewSalesOrder();
            return;
        }

        // Pre-load nested relationships for variants
        $loadedSo->items->load(['saleable' => fn($morphTo) => $morphTo->morphWith([
            ProductVariant::class => ['product:id,name,sku']
        ])]);
        $this->salesOrderInstance = $loadedSo;
        $this->order_number = $this->salesOrderInstance->order_number;
        $this->sales_channel_id = $this->salesOrderInstance->sales_channel_id;
        $this->location_id = $this->salesOrderInstance->location_id;
        $this->order_date = Carbon::parse($this->salesOrderInstance->order_date)->format('Y-m-d');
        $customerDetails = $this->salesOrderInstance->customer_details ?? [];
        $this->customer_name = $customerDetails['name'] ?? '';
        $this->customer_email = $customerDetails['email'] ?? '';
        $this->customer_phone = $customerDetails['phone'] ?? '';
        $this->status = $this->salesOrderInstance->status;

        $this->items = $this->salesOrderInstance->items->map(function ($item) {
            $sellable = $item->saleable;
            if (!$sellable) return null;
            $key = class_basename($sellable) . '_' . $sellable->id;
            $displayName = $sellable instanceof Product
                ? "{$sellable->name} (SKU: {$sellable->sku})"
                : "{$sellable->product->name} - {$sellable->variant_name} (SKU: {$sellable->product->sku})";

            $stock = LocationInventory::where('location_id', $this->location_id)
                ->where('inventoriable_type', $sellable->getMorphClass())
                ->where('inventoriable_id', $sellable->id)
                ->value('stock_quantity') ?? 0;
            return [
                'id' => $item->id,
                'selected_item_key' => $key,
                'quantity' => $item->quantity,
                'price_per_unit' => (float)$item->price_per_unit,
                'display_name' => $displayName,
                'available_stock' => $stock,
            ];
        })->filter()->values()->toArray();



    }

    public function updatedLocationId($locationId)
    {
        $this->loadSellableItems($locationId);
        $this->items = [];
        $this->addItem();
        $this->calculateTotalAmount();
    }

    // The REFACTORED method to load items based on location
    protected function loadSellableItems($locationId = null)
    {
        if (!$locationId) {
            $this->allSellableItems = collect([]);
            return;
        }

        // FIX #1: Use the correct relationship name 'inventoriable' here
        $inventoriesInStock = LocationInventory::where('location_id', $locationId)
            ->where('stock_quantity', '>', 0)
            ->with(['inventoriable' => fn($morphTo) => $morphTo->morphWith([
                ProductVariant::class => ['product:id,name,sku'],
            ])])->get();

        $sellableItems = collect([]);
        foreach ($inventoriesInStock as $inventory) {
            // FIX #2: Access the relationship using the correct name 'inventoriable'
            $item = $inventory->inventoriable;
            if (!$item) continue;

            $key = class_basename($item) . '_' . $item->id;
            $displayName = $item instanceof Product
                ? "{$item->name} (SKU: {$item->sku})"
                : "{$item->product->name} - {$item->variant_name} (SKU: {$item->product->sku})";
            $sellableItems->push([
                'key' => $key,
                'display_name' => $displayName,
                'price' => $item->selling_price ?? 0, // Make sure your models have a 'price' attribute
                // This was already correct, using the 'stock_quantity' column
                'stock' => $inventory->stock_quantity,
            ]);
        }

        $this->allSellableItems = $sellableItems->sortBy('display_name')->values();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'location_id') {
            $this->updatedLocationId($this->location_id);
            return;
        }

        if (preg_match('/items\.(\d+)\.selected_item_key/', $propertyName, $matches)) {
            $index = (int)$matches[1];
            $key = $this->items[$index]['selected_item_key'];
            $selectedItem = $this->allSellableItems->firstWhere('key', $key);

            if ($selectedItem) {
                $this->items[$index]['price_per_unit'] = (float)$selectedItem['price'];
                $this->items[$index]['available_stock'] = $selectedItem['stock'];
                $this->items[$index]['quantity'] = 1;
            } else {
                $this->items[$index]['price_per_unit'] = 0.00;
                $this->items[$index]['available_stock'] = 0;
            }
        }
        $this->calculateTotalAmount();

        if (preg_match('/items\.(\d+)\.(quantity|price_per_unit)/', $propertyName)) {
            $this->calculateTotalAmount();
        }
    }

    private function generateOrderNumber()
    {
        $this->order_number = 'SO-' . Carbon::now()->format('YmdHis') . strtoupper(substr(uniqid(), -4));
    }

    public function addItem()
    {
        $this->items[] = [
            'selected_item_key' => null,
            'quantity' => 1,
            'price_per_unit' => 0.000,
            'variant_name_display' => 'Select Item',
            'available_stock' => 0,
        ];
        $this->calculateTotalAmount();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotalAmount();
    }

    /**
     *
     * @param bool $return
     * @return float|void
     */
    public function calculateTotalAmount($return = false)
    {
        // Use the collect() helper to safely work with the items array.
        $total = collect($this->items)->sum(function ($item) {
            // Ensure that quantity and price are treated as numbers, defaulting to 0 if null or invalid.
            $quantity = (float)($item['quantity'] ?? 0);
            $price = (float)($item['price_per_unit'] ?? 0);
            return $quantity * $price;
        });

        // If the $return flag is passed as true...
        if ($return) {
            // ...return the calculated value immediately. This is for the save method.
            return $total;
        }

        // Otherwise, update the public property for Livewire's view rendering.
        $this->total_amount = $total;
    }

    public function saveSalesOrder()
    {
        $this->validate();
        $stockUpdated = false;

        $isNewOrder = !$this->salesOrderInstance?->exists;
        $originalStatus = $isNewOrder ? null : $this->salesOrderInstance->getOriginal('status');

        // Fetch original items if the order was previously fulfilled, for stock reversal/adjustment
        if (!$isNewOrder && $originalStatus === 'fulfilled' && $this->salesOrderInstance) {
            $this->originalFulfilledItemsData = $this->salesOrderInstance->items()->with('saleable')->get()->map(function ($item) {
                if (!$item->saleable) return null;
                return [
                    'saleable_model' => $item->saleable, // Keep the model for easy access to morphClass and id
                    'quantity' => $item->quantity,
                ];
            })->filter()->values()->all();
        }

        DB::transaction(function () use (&$stockUpdated, $isNewOrder, $originalStatus) {
            $currentLocationId = $this->location_id; // Use the location_id from the form

            $this->salesOrderInstance = SalesOrder::updateOrCreate(
                ['id' => $this->salesOrderInstance?->id],
                [
                    'order_number' => $this->order_number ?: $this->generateOrderNumber(true),
                    'sales_channel_id' => $this->sales_channel_id, // from form
                    'location_id' => $currentLocationId,      // from form
                    'order_date' => $this->order_date,
                    'customer_details' => ['name' => $this->customer_name, 'email' => $this->customer_email, 'phone' => $this->customer_phone],
                    'status' => $this->status,
                    'total_amount' => $this->calculateTotalAmount(true),
                ]
            );

            // Sync items
            $currentItemIds = [];
            foreach ($this->items as $itemData) {
                if (empty($itemData['selected_item_key']) || ($itemData['quantity'] ?? 0) <= 0) continue;
                list($type, $id) = explode('_', $itemData['selected_item_key']);
                $morphAlias = ($type === 'ProductVariant') ? 'variant' : 'product';
                $soItem = $this->salesOrderInstance->items()->updateOrCreate(
                    ['saleable_type' => $morphAlias, 'saleable_id' => (int)$id],
                    ['quantity' => $itemData['quantity'], 'price_per_unit' => $itemData['price_per_unit']]
                );
                $currentItemIds[] = $soItem->id;
            }
            if (!$isNewOrder) {
                $this->salesOrderInstance->items()->whereNotIn('id', $currentItemIds)->delete();
            }

            // --- REVISED INVENTORY FULFILLMENT LOGIC ---
            $currentStatus = $this->salesOrderInstance->status;
            $orderId = $this->salesOrderInstance->id;
            $orderMorphClass = $this->salesOrderInstance->getMorphClass();

            // Scenario 1: Order becomes 'fulfilled' (was not 'fulfilled' before)
            if ($currentStatus === 'fulfilled' && $originalStatus !== 'fulfilled' && $currentLocationId) {
                Log::info("SO #{$this->salesOrderInstance->order_number}: Status changed to 'fulfilled'. Processing stock decrements.");
                foreach ($this->items as $itemData) {
                    if (empty($itemData['selected_item_key'])) continue;
                    list($type, $id) = explode('_', $itemData['selected_item_key']);
                    $sellableClass = 'App\\Models\\' . $type;
                    $sellable = $sellableClass::find($id);
                    if ($sellable) {
                        $this->adjustStockAndLogMovement($sellable, -(int)$itemData['quantity'], 'sale', $currentLocationId, $orderId, $orderMorphClass);
                    }
                }
                $stockUpdated = true;
            }
            // Scenario 2: Order was 'fulfilled' and now is NOT 'fulfilled' (e.g., 'pending', 'cancelled')
            elseif ($currentStatus !== 'fulfilled' && $originalStatus === 'fulfilled' && $currentLocationId) {
                Log::info("SO #{$this->salesOrderInstance->order_number}: Status changed from 'fulfilled'. Reverting stock.");
                foreach ($this->originalFulfilledItemsData as $originalItem) {
                    $this->adjustStockAndLogMovement($originalItem['saleable_model'], $originalItem['quantity'], 'sale_return', $currentLocationId, $orderId, $orderMorphClass);
                }
                $stockUpdated = true;
            }
            // Scenario 3: Order was 'fulfilled' and REMAINS 'fulfilled' (items/location might have changed)
            elseif ($currentStatus === 'fulfilled' && $originalStatus === 'fulfilled' && $currentLocationId) {
                Log::info("SO #{$this->salesOrderInstance->order_number}: Status remains 'fulfilled'. Adjusting stock for changes.");
                // Revert stock from original fulfillment
                foreach ($this->originalFulfilledItemsData as $originalItem) {
                    $this->adjustStockAndLogMovement($originalItem['saleable_model'], $originalItem['quantity'], 'sale_edit_reversal', $currentLocationId, $orderId, $orderMorphClass);
                }
                // Apply stock for current items
                foreach ($this->items as $itemData) {
                    if (empty($itemData['selected_item_key'])) continue;
                    list($type, $id) = explode('_', $itemData['selected_item_key']);
                    $sellableClass = 'App\\Models\\' . $type;
                    $sellable = $sellableClass::find($id);
                    if ($sellable) {
                        $this->adjustStockAndLogMovement($sellable, -(int)$itemData['quantity'], 'sale_edit_fulfillment', $currentLocationId, $orderId, $orderMorphClass);
                    }
                }
                $stockUpdated = true; // Assume stock might have changed if we are in this block
            }
        });

        Cache::forget('dashboard_data');
        Activity::create([
            'type' => $isNewOrder ? 'sales_order_created' : 'sales_order_updated',
            'description' => sprintf(
                '%s Sales Order #%s by %s at %s',
                $isNewOrder ? 'Created' : 'Updated',
                $this->salesOrderInstance->order_number,
                $this->customer_name ?: 'Unknown Customer',
                $this->location_id ? Location::find($this->location_id)->name : 'Unknown Location'
            ),
        ]);
        session()->flash('message', 'Sales Order saved successfully.');
        if ($stockUpdated) {
            session()->flash('stock_update_message', 'Stock levels have been adjusted accordingly.');
        }
        return redirect()->route('sales-orders.index');
    }

    private function adjustStockAndLogMovement(\Illuminate\Database\Eloquent\Model $sellable, int $quantityChange, string $movementType, int $locationId, int $orderId, string $orderMorphClass)
    {
        if ($quantityChange == 0) return;

        $inventoryRecord = LocationInventory::firstOrCreate(
            [
                'inventoriable_type' => $sellable->getMorphClass(),
                'inventoriable_id'   => $sellable->id,
                'location_id'        => $locationId,
            ],
            ['stock_quantity' => 0]
        );
        $inventoryRecord->increment('stock_quantity', $quantityChange);
        InventoryMovement::create([
            'itemable_type' => $sellable->getMorphClass(),
            'itemable_id'   => $sellable->id,
            'location_id'   => $locationId,
            'quantity'      => $quantityChange, // This will be negative for decrements, positive for increments
            'type'          => $movementType,
            'related_type'  => $orderMorphClass,
            'related_id'    => $orderId,
        ]);
        Log::info("Stock for " . class_basename($sellable) . " #{$sellable->id} changed by {$quantityChange} at Location #{$locationId} due to SO #{$orderId} - Type: {$movementType}. New stock: {$inventoryRecord->fresh()->stock_quantity}");
    }

    public function render()
    {
        return view('livewire.sales-order.sales-order-form')
            ->layoutData(['title' => $this->salesOrderInstance?->exists ? 'Edit Sales Order' : 'Create Sales Order'])
            ->layout('components.layouts.livewire');
    }
}
