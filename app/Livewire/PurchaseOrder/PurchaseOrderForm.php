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
            'receiving_location_id' => 'required_if:status,received|nullable|exists:locations,id',
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

        // Use a variable to track if stock was successfully updated
        $stockUpdated = false;

        DB::transaction(function () use (&$stockUpdated) {
            $isNewOrder = !$this->purchaseOrderInstance->exists;

            // **CRITICAL:** Get the status *before* any changes are made
            $originalStatus = $isNewOrder ? null : $this->purchaseOrderInstance->getOriginal('status');

            // Prepare the data to be saved to the PurchaseOrder model
            $poData = [
                'supplier_id' => $this->supplier_id,
                'order_number' => $this->order_number ?: $this->generateOrderNumber(true), // Generate if needed
                'order_date' => $this->order_date,
                'receiving_location_id' => $this->receiving_location_id,
                'status' => $this->status,
                'total_amount' => $this->calculateTotalAmount(true), // Recalculate just in case
            ];

            // Use updateOrCreate for the main PO record. It's clean and handles both new/edit cases.
            $this->purchaseOrderInstance = PurchaseOrder::updateOrCreate(
                ['id' => $this->purchaseOrderInstance->id],
                $poData
            );

            // --- Save Items ---
            $currentItemIds = [];
            foreach ($this->items as $itemData) {
                if (empty($itemData['selected_item_key'])) continue;

                list($type, $id) = explode('_', $itemData['selected_item_key']);
                $purchasableType = match (strtolower($type)) {
                    'product'        => 'product',
                    'productvariant' => 'variant', // This handles your `ProductVariant` model name
                    'variant'        => 'variant',
                    default => throw new \Exception("Unknown purchasable type: {$type}"),
                };
                $purchasableId = (int)$id;

                $itemPayload = [
                    'quantity' => $itemData['quantity'],
                    'cost_price_per_unit' => $itemData['cost_price_per_unit'],
                ];

                $poItem = $this->purchaseOrderInstance->items()->updateOrCreate(
                    ['purchasable_type' => $purchasableType, 'purchasable_id' => $purchasableId],
                    $itemPayload
                );
                $currentItemIds[] = $poItem->id;
            }

            // Delete removed items if this was an edit
            if (!$isNewOrder) {
                $this->purchaseOrderInstance->items()->whereNotIn('id', $currentItemIds)->delete();
            }

            // --- STOCK UPDATE LOGIC ---

            // The status of the model *after* saving
            $currentStatus = $this->purchaseOrderInstance->status;
            $locationId = $this->purchaseOrderInstance->receiving_location_id;

            // Condition: The status must now be 'received' and it must NOT have been 'received' before.
            // This prevents double-counting if you save a 'received' order multiple times.
            if ($currentStatus === 'received' && $originalStatus !== 'received' && $locationId) {
                Log::info("PO #{$this->purchaseOrderInstance->order_number}: Status changed to 'received'. Processing stock updates.");

                // Loop through the items we just saved. Use fresh() to get the latest from DB.
                foreach ($this->purchaseOrderInstance->fresh()->items as $item) {
                    $purchasable = $item->purchasable;
                    if ($purchasable) {
                        $inventoryRecord = LocationInventory::firstOrCreate(
                            [
                                'inventoriable_type' => $purchasable->getMorphClass(),
                                'inventoriable_id'   => $purchasable->id,
                                'location_id'        => $locationId,
                            ]
                        );
                        $inventoryRecord->increment('stock_quantity', $item->quantity);
                        Log::info("  - Stock for " . class_basename($purchasable) . " #{$purchasable->id} incremented by {$item->quantity} at Location #{$locationId}.");
                    }
                }
                // If we get here, the stock update logic ran.
                $stockUpdated = true;
            } else {
                // Log why the stock update was skipped
                if ($currentStatus !== 'received') {
                    Log::info("PO #{$this->purchaseOrderInstance->order_number}: Stock update skipped. Status is '{$currentStatus}', not 'received'.");
                } elseif ($originalStatus === 'received') {
                    Log::info("PO #{$this->purchaseOrderInstance->order_number}: Stock update skipped. Status was already 'received'.");
                } elseif (!$locationId) {
                    Log::info("PO #{$this->purchaseOrderInstance->order_number}: Stock update skipped. No receiving location was set.");
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

    public function render()
    {
        return view('livewire.purchase-order.purchase-order-form')
            ->layoutData(['title' => $this->purchaseOrderInstance?->exists ? 'Edit Purchase Order' : 'Create Purchase Order'])
            ->layout('components.layouts.livewire');
    }
}
