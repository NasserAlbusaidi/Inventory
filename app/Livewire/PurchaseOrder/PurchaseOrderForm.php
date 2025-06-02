<?php

namespace App\Livewire\PurchaseOrder;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\ProductVariant;
use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class PurchaseOrderForm extends Component
{
    public ?PurchaseOrder $purchaseOrderInstance = null;

    // PO Header fields
    public $supplier_id = null;
    public $order_number = '';

    public $receiving_location_id = null;
    public Collection $allLocations; // For receiving locations
    public $order_date;
    // public $expected_delivery_date = null; // Removed
    public $status = 'ordered'; // Default status changed
    public $total_amount = 0;

    public $items = [];

    // For dropdowns
    public Collection $allSuppliers;
    public Collection $allProductVariants;

    // Simplified statuses
    public $poStatuses = ['ordered', 'received', 'cancelled']; // Added 'cancelled' for flexibility

    protected function rules(): array
    {
        $rules = [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_number' => [
                'required', 'string', 'max:255',
                $this->purchaseOrderInstance && $this->purchaseOrderInstance->exists
                    ? Rule::unique('purchase_orders', 'order_number')->ignore($this->purchaseOrderInstance->id)
                    : Rule::unique('purchase_orders', 'order_number')
            ],
            'order_date' => 'required|date',
            // 'expected_delivery_date' => 'nullable|date|after_or_equal:order_date', // Removed
            'status' => 'required|in:' . implode(',', $this->poStatuses),
            'items' => 'required|array|min:1',
            'receiving_location_id' => 'required_if:status,received|nullable|exists:locations,id',
        ];
        foreach ($this->items as $index => $item) {
            $rules["items.{$index}.product_variant_id"] = 'required|exists:product_variants,id';
            $rules["items.{$index}.quantity"] = 'required|integer|min:1';
            $rules["items.{$index}.cost_price_per_unit"] = 'required|numeric|min:0';
        }
        return $rules;
    }

    protected $messages = [
        'items.required' => 'Add at least one item.',
        'items.min' => 'Add at least one item.',
        'items.*.product_variant_id.required' => 'Product is required.',
        'items.*.quantity.required' => 'Qty is required.',
        'items.*.quantity.min' => 'Qty must be >= 1.',
        'items.*.cost_price_per_unit.required' => 'Cost is required.',
        'items.*.cost_price_per_unit.min' => 'Cost cannot be negative.',
        'receiving_location_id.required_if' => 'Receiving location is required when status is "received".',
    ];

    public function mount($purchaseOrder = null)
    {
        $this->allSuppliers = Supplier::orderBy('name')->get();
        $this->loadProductVariants();
        $this->allLocations = Location::orderBy('name')->get();

        if ($purchaseOrder) { // Existing PO ID or instance passed
            if (!($purchaseOrder instanceof PurchaseOrder)) {
                $loadedPo = PurchaseOrder::with('items.productVariant.product')->find($purchaseOrder); // Eager load
                if (!$loadedPo) {
                    session()->flash('error', 'Purchase Order not found. Creating new one.');
                    $this->initializeNewPurchaseOrder();
                    return;
                }
                $this->purchaseOrderInstance = $loadedPo;
            } else {
                 $this->purchaseOrderInstance = $purchaseOrder;
                 $this->purchaseOrderInstance->loadMissing('items.productVariant.product');
            }

            $this->supplier_id = $this->purchaseOrderInstance->supplier_id;
            $this->order_number = $this->purchaseOrderInstance->order_number;
            $this->order_date = Carbon::parse($this->purchaseOrderInstance->order_date)->format('Y-m-d');
            // $this->expected_delivery_date = $this->purchaseOrderInstance->expected_delivery_date ? Carbon::parse($this->purchaseOrderInstance->expected_delivery_date)->format('Y-m-d') : null; // Removed
            $this->status = $this->purchaseOrderInstance->status;
            $this->receiving_location_id = $this->purchaseOrderInstance->receiving_location_id;

            $this->items = $this->purchaseOrderInstance->items->map(function ($item) {
                $variant = $this->allProductVariants->firstWhere('id', $item->product_variant_id);
                return [
                    'id' => $item->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'cost_price_per_unit' => (float)$item->cost_price_per_unit,
                    'variant_name_display' => $variant ? $variant->full_name_with_variant : ($item->productVariant ? $item->productVariant->product->name . ' - ' . $item->productVariant->variant_name : 'Unknown Variant'),
                ];
            })->toArray();

        } else { // New PO
            $this->initializeNewPurchaseOrder();
        }
        $this->calculateTotalAmount();
    }

    protected function loadProductVariants() {
        $this->allProductVariants = ProductVariant::with('product:id,name') // SKU not needed here
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'product_variants.id',
                'product_variants.cost_price', // Default cost price
                DB::raw('CONCAT(products.name, " - ", product_variants.variant_name) as full_name_with_variant') // Simpler name
            )
            ->orderBy('products.name')
            ->orderBy('product_variants.variant_name')
            ->get();
    }

    private function initializeNewPurchaseOrder()
    {
        $this->purchaseOrderInstance = new PurchaseOrder();
        $this->order_date = Carbon::now()->format('Y-m-d');
        $this->status = 'ordered'; // Default to 'ordered'
        $this->supplier_id = $this->allSuppliers->isNotEmpty() ? $this->allSuppliers->first()->id : null;
        // $this->expected_delivery_date = null; // Removed
        $this->receiving_location_id = $this->allLocations->isNotEmpty() ? $this->allLocations->first()->id : null;
        $this->items = [];
        $this->addItem();
        $this->generateOrderNumber();
    }

    private function generateOrderNumber()
    {
        // A simpler unique PO number generator
        $prefix = 'PO-';
        $datePart = Carbon::now()->format('ymd'); // Shorter date
        $lastPo = PurchaseOrder::where('order_number', 'LIKE', "{$prefix}{$datePart}%")->orderBy('order_number', 'desc')->first();
        $sequence = 1;
        if ($lastPo) {
            $lastSequence = (int) substr(strrchr($lastPo->order_number, "-"), 1);
            $sequence = $lastSequence + 1;
        }
        $this->order_number = $prefix . $datePart . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function addItem()
    {
        $this->items[] = [
            'id' => null, // For new items
            'product_variant_id' => null,
            'quantity' => 1,
            'cost_price_per_unit' => 0.00, // Use 2 decimal places for consistency
            'variant_name_display' => 'Select Variant',
        ];
        $this->calculateTotalAmount();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        if (empty($this->items)) { // If all items are removed, add a blank one back
            $this->addItem();
        }
        $this->calculateTotalAmount();
    }

    public function updated($propertyName)
    {
        // Updated hook for item changes
        if (preg_match('/items\.(\d+)\.(product_variant_id|quantity|cost_price_per_unit)/', $propertyName, $matches)) {
            $index = (int)$matches[1];
            $fieldChanged = $matches[2];

            if (!isset($this->items[$index])) return;

            if ($fieldChanged === 'product_variant_id' && !empty($this->items[$index]['product_variant_id'])) {
                $variant = $this->allProductVariants->find($this->items[$index]['product_variant_id']);
                if ($variant) {
                    $this->items[$index]['cost_price_per_unit'] = (float)($variant->cost_price ?? 0.00);
                    $this->items[$index]['variant_name_display'] = $variant->full_name_with_variant;
                } else {
                     // Variant not found or deselected
                    $this->items[$index]['cost_price_per_unit'] = 0.00;
                    $this->items[$index]['variant_name_display'] = 'Select Variant';
                }
            }
            $this->calculateTotalAmount();
        }

        // If status changes to 'received', we might want to disable further editing for some fields (UI only here)
        // The actual stock update is handled by the Observer.
    }


    public function calculateTotalAmount()
    {
        $this->total_amount = collect($this->items)->sum(function ($item) {
            $quantity = $item['quantity'] ?? 0;
            $cost = $item['cost_price_per_unit'] ?? 0.00;
            return (is_numeric($quantity) && is_numeric($cost)) ? $quantity * $cost : 0;
        });
    }

    public function savePurchaseOrder()
    {
        $this->validate();

        DB::transaction(function () {
            $isNewOrder = !($this->purchaseOrderInstance && $this->purchaseOrderInstance->exists);

            if ($isNewOrder) {
                $this->purchaseOrderInstance = new PurchaseOrder();
                if(empty($this->order_number)) {
                    $this->generateOrderNumber(); // Generate if still empty (e.g., if user cleared it)
                }
            }

            // Recalculate total amount just before saving for accuracy
            $this->calculateTotalAmount();

            $this->purchaseOrderInstance->fill([
                'supplier_id' => $this->supplier_id,
                'order_number' => $this->order_number,
                'order_date' => $this->order_date,
                // 'expected_delivery_date' => null, // Removed
                'receiving_location_id' => $this->receiving_location_id,
                'status' => $this->status,
                'total_amount' => $this->total_amount,
            ]);
            $this->purchaseOrderInstance->save();

            $currentItemIds = [];
            foreach ($this->items as $itemData) {
                if (empty($itemData['product_variant_id'])) continue;

                $itemPayload = [
                    'product_variant_id' => $itemData['product_variant_id'],
                    'quantity' => $itemData['quantity'],
                    'cost_price_per_unit' => $itemData['cost_price_per_unit'],
                ];

                $poItem = null;
                if (!$isNewOrder && isset($itemData['id']) && $itemData['id']) {
                    $poItem = $this->purchaseOrderInstance->items()->find($itemData['id']);
                }

                if ($poItem) {
                    $poItem->update($itemPayload);
                } else {
                    // Using updateOrCreate to prevent duplicate variant entries on the same PO if user adds same variant twice
                    $poItem = $this->purchaseOrderInstance->items()->updateOrCreate(
                        ['product_variant_id' => $itemData['product_variant_id']], // Unique key within this PO
                        $itemPayload
                    );
                }
                $currentItemIds[] = $poItem->id;
            }

            // Delete items that were removed from the form if it's an existing PO
            if (!$isNewOrder) {
                $this->purchaseOrderInstance->items()->whereNotIn('id', $currentItemIds)->delete();
            }
        });

        session()->flash('message', 'Purchase Order ' . ($this->purchaseOrderInstance->wasRecentlyCreated ? 'created' : 'updated') . ' successfully.');
        return redirect()->route('purchase-orders.index'); // Adjust route name if different
    }

    public function render()
    {
        $isEditing = $this->purchaseOrderInstance && $this->purchaseOrderInstance->exists;
        return view('livewire.purchase-order.purchase-order-form', [
                'isEditing' => $isEditing,
                'allLocations' => $this->allLocations,
            ])
            ->layoutData(['title' => $isEditing ? 'Edit Purchase Order' : 'Create Purchase Order'])
            ->layout('components.layouts.livewire'); // Or your main app layout
    }
}
