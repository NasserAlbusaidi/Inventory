<?php

namespace App\Livewire\PurchaseOrder;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\ProductVariant;
use Livewire\Component;
use Illuminate\Support\Collection; // Added for type hinting
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule; // For unique validation with ignore

class PurchaseOrderForm extends Component
{
    public ?PurchaseOrder $purchaseOrderInstance = null;

    // PO Header fields
    public $supplier_id = null;
    public $order_number = '';
    public $order_date;
    public $expected_delivery_date = null;
    public $status = 'draft'; // Default status
    public $total_amount = 0;

    public $items = []; // Initialized as empty, will be populated in mount

    // For dropdowns
    public Collection $allSuppliers; // Type hinted
    public Collection $allProductVariants; // Type hinted

    public $poStatuses = ['draft', 'approved', 'ordered', 'partially_received', 'received', 'cancelled'];

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
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'status' => 'required|in:' . implode(',', $this->poStatuses),
            'items' => 'required|array|min:1',
        ];
        foreach ($this->items as $index => $item) {
            $rules["items.{$index}.product_variant_id"] = 'required|exists:product_variants,id';
            $rules["items.{$index}.quantity"] = 'required|integer|min:1';
            $rules["items.{$index}.cost_price_per_unit"] = 'required|numeric|min:0';
        }
        return $rules;
    }

    protected $messages = [
        'items.required' => 'Please add at least one item to the purchase order.',
        'items.min' => 'Please add at least one item to the purchase order.',
        'items.*.product_variant_id.required' => 'Product variant is required for each item.',
        'items.*.quantity.required' => 'Quantity is required for each item.',
        'items.*.quantity.min' => 'Quantity must be at least 1.',
        'items.*.cost_price_per_unit.required' => 'Cost price is required for each item.',
        'items.*.cost_price_per_unit.min' => 'Cost price cannot be negative.',
    ];

    public function mount($purchaseOrder = null)
    {
        $this->allSuppliers = Supplier::orderBy('name')->get();
        $this->loadProductVariants();

        if ($purchaseOrder) {
            if (!($purchaseOrder instanceof PurchaseOrder)) {
                $loadedPo = PurchaseOrder::with('items.productVariant.product')->find($purchaseOrder);
                if (!$loadedPo) {
                    session()->flash('error', 'Purchase Order not found.');
                    $this->initializeNewPurchaseOrder(); // Fallback to new PO
                    return;
                }
                $this->purchaseOrderInstance = $loadedPo;
            } else {
                 $this->purchaseOrderInstance = $purchaseOrder;
                 $this->purchaseOrderInstance->loadMissing('items.productVariant.product'); // Ensure relations are loaded
            }


            if ($this->purchaseOrderInstance && $this->purchaseOrderInstance->exists) {
                $this->supplier_id = $this->purchaseOrderInstance->supplier_id;
                $this->order_number = $this->purchaseOrderInstance->order_number;
                $this->order_date = Carbon::parse($this->purchaseOrderInstance->order_date)->format('Y-m-d');
                $this->expected_delivery_date = $this->purchaseOrderInstance->expected_delivery_date ? Carbon::parse($this->purchaseOrderInstance->expected_delivery_date)->format('Y-m-d') : null;
                $this->status = $this->purchaseOrderInstance->status;

                $this->items = $this->purchaseOrderInstance->items->map(function ($item) {
                    $variant = $this->allProductVariants->firstWhere('id', $item->product_variant_id);
                    return [
                        'id' => $item->id, // For existing items
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'cost_price_per_unit' => (float)$item->cost_price_per_unit,
                        'variant_name_display' => $variant ? $variant->full_name_with_variant : ($item->productVariant ? $item->productVariant->product->name . ' - ' . $item->productVariant->variant_name : 'Unknown Variant'),
                    ];
                })->toArray();
            }
        } else {
            $this->initializeNewPurchaseOrder();
        }
        $this->calculateTotalAmount(); // Calculate total after mount
    }

    protected function loadProductVariants() {
        $this->allProductVariants = ProductVariant::with('product:id,name,sku') // Select only necessary product fields
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'product_variants.id',
                'product_variants.cost_price',
                DB::raw('CONCAT(products.name, " - ", product_variants.variant_name, " (SKU: ", COALESCE(products.sku, "N/A"), ")") as full_name_with_variant')
            )
            ->orderBy('products.name')
            ->orderBy('product_variants.variant_name')
            ->get();
    }

    private function initializeNewPurchaseOrder()
    {
        $this->purchaseOrderInstance = new PurchaseOrder();
        $this->order_date = Carbon::now()->format('Y-m-d');
        $this->status = 'draft'; // Reset status for new PO
        $this->supplier_id = null;
        $this->expected_delivery_date = null;
        $this->items = []; // Crucial: Reset items array
        $this->addItem();  // Add the first fully structured item
        $this->generateOrderNumber(); // Generate new order number
    }

    private function generateOrderNumber()
    {
        $this->order_number = 'PO-' . Carbon::now()->format('YmdHis') . strtoupper(substr(uniqid(), -4));
    }

    public function addItem()
    {
        $this->items[] = [
            'product_variant_id' => null,
            'quantity' => 1,
            'cost_price_per_unit' => 0.000,
            'variant_name_display' => 'Select Variant',
        ];
        $this->calculateTotalAmount();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotalAmount();
    }

    public function updated($propertyName)
    {
        if (preg_match('/items\.(\d+)\.product_variant_id/', $propertyName, $matches) ||
            preg_match('/items\.(\d+)\.quantity/', $propertyName, $matches) ||
            preg_match('/items\.(\d+)\.cost_price_per_unit/', $propertyName, $matches)) {

            $index = (int)$matches[1];
            // $field = $matches[2]; // Not strictly needed if we re-evaluate based on index

            if (!isset($this->items[$index])) {
                return;
            }

            if (preg_match('/items\.(\d+)\.product_variant_id/', $propertyName) && !empty($this->items[$index]['product_variant_id'])) {
                $variant = $this->allProductVariants->find($this->items[$index]['product_variant_id']);
                if ($variant) {
                    $this->items[$index]['cost_price_per_unit'] = (float)$variant->cost_price;
                    $this->items[$index]['variant_name_display'] = $variant->full_name_with_variant;
                } else {
                    $this->items[$index]['cost_price_per_unit'] = 0.000;
                    $this->items[$index]['variant_name_display'] = 'Select Variant';
                }
            }
            $this->calculateTotalAmount();
        }
    }

    public function calculateTotalAmount()
    {
        $this->total_amount = 0;
        foreach ($this->items as $item) {
            $quantity = $item['quantity'] ?? 0;
            $cost = $item['cost_price_per_unit'] ?? 0.000;
            if (is_numeric($quantity) && is_numeric($cost)) {
                $this->total_amount += $quantity * $cost;
            }
        }
    }

    public function savePurchaseOrder()
    {
        $this->validate();

        DB::transaction(function () {
            $isNewOrder = !($this->purchaseOrderInstance && $this->purchaseOrderInstance->exists);

            if ($isNewOrder) {
                $this->purchaseOrderInstance = new PurchaseOrder();
                 // Set order number only if it's a new order and not already set,
                // or if your logic requires regenerating it.
                if(empty($this->order_number)) {
                    $this->generateOrderNumber();
                }
            }

            $this->purchaseOrderInstance->supplier_id = $this->supplier_id;
            $this->purchaseOrderInstance->order_number = $this->order_number;
            $this->purchaseOrderInstance->order_date = $this->order_date;
            $this->purchaseOrderInstance->expected_delivery_date = $this->expected_delivery_date;
            $this->purchaseOrderInstance->status = $this->status;
            $this->purchaseOrderInstance->total_amount = $this->total_amount; // Recalculate just before save for safety
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
                // For existing PO, try to find an existing item by its ID if available
                // Or update/create based on product_variant_id to prevent duplicates of same variant
                if (!$isNewOrder && isset($itemData['id'])) {
                    $poItem = $this->purchaseOrderInstance->items()->find($itemData['id']);
                }

                if ($poItem) { // If found by its own ID, update it
                    $poItem->update($itemPayload);
                } else { // Otherwise, try to update or create based on product_variant_id
                    $poItem = $this->purchaseOrderInstance->items()->updateOrCreate(
                        ['product_variant_id' => $itemData['product_variant_id']], // Find by this
                        $itemPayload // Create or update with this
                    );
                }
                $currentItemIds[] = $poItem->id;
            }
            // Delete items that were removed from the form
            if (!$isNewOrder) {
                $this->purchaseOrderInstance->items()->whereNotIn('id', $currentItemIds)->delete();
            }
        });

        session()->flash('message', 'Purchase Order ' . ($this->purchaseOrderInstance->wasRecentlyCreated && !$this->purchaseOrderInstance->wasChanged() ? 'created' : 'updated') . ' successfully.');
        return redirect()->route('purchase-orders.index');
    }

    public function render()
    {
        return view('livewire.purchase-order.purchase-order-form')
            ->layoutData(['title' => $this->purchaseOrderInstance && $this->purchaseOrderInstance->exists ? 'Edit Purchase Order' : 'Create Purchase Order'])
            ->layout('components.layouts.livewire');
    }
}
