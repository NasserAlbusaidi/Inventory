<?php

namespace App\Livewire\SalesOrder;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryMovement;
use App\Models\LocationInventory; // <-- IMPORTANT: Import the new model
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class SalesOrderForm extends Component
{
    public ?SalesOrder $salesOrderInstance = null;

    // SO Header
    public $channel = 'boutique';
    public $location_id = null;
    public $order_date;
    public $customer_name = '';
    public $customer_email = '';
    public $customer_phone = '';
    public $status = 'pending';
    public $total_amount = 0;
    public $order_number = '';

    public $items = [];

    // For dropdowns
    public Collection $allLocations;
    public Collection $allSellableItems;

    public $soChannels = ['Website', 'Instagram', 'boutique', 'other'];
    public $soStatuses = ['pending', 'processing', 'completed', 'refunded'];

    protected function rules(): array
    {
        $rules = [
            'channel' => 'required|in:' . implode(',', $this->soChannels),
            'location_id' => 'required_if:channel,boutique|nullable|exists:locations,id',
            'order_date' => 'required|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'status' => 'required|in:' . implode(',', $this->soStatuses),
            'items' => 'required|array|min:1',
        ];

        foreach ($this->items as $index => $item) {
            $rules["items.{$index}.selected_item_key"] = 'required';
            $rules["items.{$index}.quantity"] = 'required|integer|min:1';
            $rules["items.{$index}.price_per_unit"] = 'required|numeric|min:0';

            if (!empty($item['selected_item_key']) && isset($item['available_stock'])) {
                $availableStock = (int)$item['available_stock'];
                if ($availableStock < ($item['quantity'] ?? 1)) {
                    $rules["items.{$index}.quantity"] .= '|max:' . $availableStock;
                }
            }
        }
        return $rules;
    }

    protected $messages = [
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
        $this->allSellableItems = collect([]); // Initialize as empty

        if ($salesOrder) {
            $this->loadExistingSalesOrder($salesOrder);
        } else {
            $this->initializeNewSalesOrder();
        }

        // Load items based on the determined location
        $this->loadSellableItems($this->location_id);

        $this->calculateTotalAmount();
    }

    private function initializeNewSalesOrder()
    {
        $this->salesOrderInstance = new SalesOrder();
        $this->order_date = Carbon::now()->format('Y-m-d');
        $this->status = 'pending';
        $this->channel = 'boutique';
        $this->location_id = $this->allLocations->first()->id ?? null; // Default to first location
        $this->customer_name = '';
        $this->customer_email = '';
        $this->customer_phone = '';
        $this->generateOrderNumber();
        $this->items = [];
        $this->addItem();
    }



    private function loadExistingSalesOrder($salesOrder)
    {
        // --- STEP 1: Eager load the first-level polymorphic relationship ---
        $loadedSo = ($salesOrder instanceof SalesOrder)
            ? $salesOrder->loadMissing('items.saleable')
            : SalesOrder::with('items.saleable')->find($salesOrder);

        if (!$loadedSo) {
            session()->flash('error', 'Sales Order not found.');
            $this->initializeNewSalesOrder();
            return;
        }

        // --- STEP 2: "Lazy Eager Load" the nested relationship ON THE 'items' RELATION ---
        // This is the correct way to load the nested relationship for the variants.
        $loadedSo->items->load(['saleable' => function ($morphTo) {
            // This tells Eloquent to "look inside" the 'saleable' relation...
            $morphTo->morphWith([
                // ...and for any models of type ProductVariant, load their 'product' relation.
                ProductVariant::class => ['product:id,name,sku'],
            ]);
        }]);

        // --- Now we can safely build the form data ---
        $this->salesOrderInstance = $loadedSo;
        $this->order_number = $this->salesOrderInstance->order_number;
        $this->channel = $this->salesOrderInstance->channel;
        $this->location_id = $this->salesOrderInstance->location_id;
        $this->order_date = Carbon::parse($this->salesOrderInstance->order_date)->format('Y-m-d');
        $customerDetails = $this->salesOrderInstance->customer_details ?? [];
        $this->customer_name = $customerDetails['name'] ?? '';
        $this->customer_email = $customerDetails['email'] ?? '';
        $this->customer_phone = $customerDetails['phone'] ?? '';
        $this->status = $this->salesOrderInstance->status;

        $this->items = $this->salesOrderInstance->items->map(function ($item) {
            $saleable = $item->saleable;
            if (!$saleable) return null;

            $key = null;
            $displayName = 'Unknown Item';

            if ($saleable instanceof Product) {
                $key = 'Product_' . $saleable->id;
                $displayName = "{$saleable->name} (SKU: {$saleable->sku})";
            } elseif ($saleable instanceof ProductVariant) {
                $key = 'ProductVariant_' . $saleable->id;
                // This is now safe because the 'product' relationship was pre-loaded by morphWith.
                $displayName = "{$saleable->product->name} - {$saleable->variant_name} (SKU: {$saleable->product->sku})";
            }

            $inventory = LocationInventory::where('location_id', $this->location_id)
                ->where('inventoriable_id', $saleable->id)
                ->where('inventoriable_type', get_class($saleable))
                ->first();
            $stock = $inventory->stock_quantity ?? 0;

            return [
                'id' => $item->id,
                'selected_item_key' => $key,
                'quantity' => $item->quantity,
                'price_per_unit' => (float)$item->price_per_unit,
                'variant_name_display' => $displayName,
                'available_stock' => $stock,
            ];
        })->filter()->values()->toArray();
    }
    public function updatedLocationId($locationId)
    {
        // When location changes, reload the list of available items
        $this->loadSellableItems($locationId);

        // Reset the items array because the old items might not be valid for the new location
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

        // This is the corrected query
        $inventoriesInStock = LocationInventory::where('location_id', $locationId)
            ->where('stock_quantity', '>', 0)
            ->with(['inventoriable' => function ($morphTo) {
                // Use morphWith to specify nested relations for different polymorphic types
                $morphTo->morphWith([
                    // For ProductVariant models, ALSO load their parent 'product' relationship
                    ProductVariant::class => ['product:id,name,sku'],
                    // For Product models, we don't need to load anything extra
                ]);
            }])
            ->get();


        // The rest of the function remains the same, but it will now work correctly
        $sellableItems = collect([]);
        foreach ($inventoriesInStock as $inventory) {
            $item = $inventory->inventoriable;
            if (!$item) continue;

            $key = class_basename($item) . '_' . $item->id;
            $stockAtLocation = $inventory->stock_quantity;
            $displayName = '';

            if ($item instanceof Product) {
                $displayName = "{$item->name} (SKU: {$item->sku})";
            } elseif ($item instanceof ProductVariant) {
                // This is now safe and efficient because the 'product' relation was pre-loaded
                $displayName = "{$item->product->name} - {$item->variant_name} (SKU: {$item->product->sku})";
            }

            $sellableItems->push([
                'key' => $key,
                'display_name' => $displayName,
                'price' => $item->selling_price,
                'stock' => $stockAtLocation,
            ]);
        }

        $this->allSellableItems = $sellableItems->sortBy('display_name')->values();
    }

    public function updated($propertyName)
    {
        if (preg_match('/items\.(\d+)\.selected_item_key/', $propertyName, $matches)) {
            $index = (int)$matches[1];
            $key = $this->items[$index]['selected_item_key'];

            if (empty($key)) {
                $this->items[$index]['price_per_unit'] = 0.000;
                $this->items[$index]['variant_name_display'] = 'Select Item';
                $this->items[$index]['available_stock'] = 0;
            } else {
                $selectedItem = $this->allSellableItems->firstWhere('key', $key);
                if ($selectedItem) {
                    $this->items[$index]['price_per_unit'] = (float)$selectedItem['price'];
                    $this->items[$index]['variant_name_display'] = $selectedItem['display_name'];
                    $this->items[$index]['available_stock'] = $selectedItem['stock'];
                    $this->items[$index]['quantity'] = 1; // Reset quantity to 1 on new selection
                }
            }
        }

        if (preg_match('/items\.(\d+)\.(quantity|price_per_unit)/', $propertyName)) {
            $this->calculateTotalAmount();
        }

        if ($propertyName === 'channel' && $this->channel !== 'boutique') {
            $this->location_id = null;
            $this->loadSellableItems(null); // Clear the sellable items list
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

    public function calculateTotalAmount()
    {
        $this->total_amount = collect($this->items)->sum(function ($item) {
            return (float)($item['quantity'] ?? 0) * (float)($item['price_per_unit'] ?? 0);
        });
    }

    public function saveSalesOrder()
    {
        $this->validate();

        DB::transaction(function () {
            // --- 1. HANDLE THE SALES ORDER HEADER ---
            $isNewOrder = !($this->salesOrderInstance && $this->salesOrderInstance->exists);
            $previousStatus = $isNewOrder ? null : $this->salesOrderInstance->getOriginal('status');

            if ($isNewOrder) {
                $this->salesOrderInstance = new SalesOrder();
                if (empty($this->order_number)) {
                    $this->generateOrderNumber();
                }
                $this->salesOrderInstance->order_number = $this->order_number;
            }

            $this->salesOrderInstance->fill([
                'channel' => $this->channel,
                'location_id' => ($this->channel === 'boutique' && $this->location_id) ? $this->location_id : null,
                'order_date' => $this->order_date,
                'customer_details' => [
                    'name' => $this->customer_name,
                    'email' => $this->customer_email,
                    'phone' => $this->customer_phone,
                ],
                'status' => $this->status,
                'total_amount' => $this->total_amount,
            ]);
            $this->salesOrderInstance->save();

            // --- 2. HANDLE THE SALES ORDER ITEMS ---
            $currentItemIds = [];
            foreach ($this->items as $itemData) {
                if (empty($itemData['selected_item_key']) || ($itemData['quantity'] ?? 0) <= 0) continue;

                list($type, $id) = explode('_', $itemData['selected_item_key']);
                $saleableType = match (strtolower($type)) {
                    'product'        => 'product',
                    'productvariant' => 'variant',
                    'variant'        => 'variant',
                    default => throw new \Exception("Unknown saleable type: {$type}"),
                };
                $saleableId = (int)$id;

                $itemPayload = [
                    'quantity' => $itemData['quantity'],
                    'price_per_unit' => $itemData['price_per_unit'],
                ];

                $soItem = $this->salesOrderInstance->items()->updateOrCreate(
                    ['saleable_type' => $saleableType, 'saleable_id' => $saleableId],
                    $itemPayload
                );
                $currentItemIds[] = $soItem->id;
            }
            if (!$isNewOrder) {
                $this->salesOrderInstance->items()->whereNotIn('id', $currentItemIds)->delete();
            }

            // --- 3. HANDLE INVENTORY UPDATES ---
            $currentStatus = $this->salesOrderInstance->status;
            $isStatusChangedToCompleted = !in_array($previousStatus, ['completed', 'refunded']) && in_array($currentStatus, ['completed']);
            $isNewAndCompleted = $isNewOrder && in_array($currentStatus, ['completed']);

            if (($isStatusChangedToCompleted || $isNewAndCompleted) && $this->salesOrderInstance->location_id) {
                foreach ($this->salesOrderInstance->fresh()->items as $item) {
                    $saleable = $item->saleable;
                    if ($saleable) {
                        LocationInventory::where([
                            'inventoriable_type' => $saleable->getMorphClass(),
                            'inventoriable_id'   => $saleable->id,
                            'location_id'        => $this->salesOrderInstance->location_id,
                        ])->decrement('stock_quantity', $item->quantity);
                    }
                }
            }
        });

        // --- 4. Destroy current Cache ---
        Cache::forget('dashboard_data');

        session()->flash('message', 'Sales Order #' . $this->salesOrderInstance->order_number . ' saved successfully.');
        return redirect()->route('sales-orders.index');
    }

    public function render()
    {
        return view('livewire.sales-order.sales-order-form')
            ->layoutData(['title' => $this->salesOrderInstance?->exists ? 'Edit Sales Order' : 'Create Sales Order'])
            ->layout('components.layouts.livewire');
    }
}
