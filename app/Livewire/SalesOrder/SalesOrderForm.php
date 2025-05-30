<?php

namespace App\Livewire\SalesOrder;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Location;
use App\Models\ProductVariant;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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

    public Collection $allLocations;
    public Collection $allProductVariants;

    public $soChannels = ['shopify', 'boutique', 'other'];
    public $soStatuses = ['pending', 'processing', 'completed', 'shipped', 'cancelled', 'refunded'];


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
            $rules["items.{$index}.product_variant_id"] = 'required|exists:product_variants,id';
            $rules["items.{$index}.quantity"] = 'required|integer|min:1';
            $rules["items.{$index}.price_per_unit"] = 'required|numeric|min:0';

            if (!empty($item['product_variant_id']) && isset($item['available_stock'])) { // Check if available_stock is set
                $availableStock = (int)$item['available_stock'];
                 if ($availableStock < ($item['quantity'] ?? 1) ) {
                    $rules["items.{$index}.quantity"] .= '|max:' . $availableStock;
                }
            }
        }
        return $rules;
    }

    protected $messages = [
        'items.required' => 'Please add at least one item to the sales order.',
        'items.*.product_variant_id.required' => 'Product variant is required.',
        'items.*.quantity.required' => 'Quantity is required.',
        'items.*.quantity.min' => 'Quantity must be at least 1.',
        'items.*.quantity.max' => 'Quantity cannot exceed available stock for this item.',
        'items.*.price_per_unit.required' => 'Price is required.',
        'location_id.required_if' => 'Location is required for boutique sales.'
    ];

    public function mount($salesOrder = null)
    {
        $this->allLocations = Location::orderBy('name')->get();
        $this->loadProductVariants();

        if ($salesOrder) {
            if (!($salesOrder instanceof SalesOrder)) {
                $loadedSo = SalesOrder::with('items.productVariant.product')->find($salesOrder);
                if (!$loadedSo) {
                    session()->flash('error', 'Sales Order not found.');
                    $this->initializeNewSalesOrder();
                    return;
                }
                $this->salesOrderInstance = $loadedSo;
            } else {
                $this->salesOrderInstance = $salesOrder;
                $this->salesOrderInstance->loadMissing('items.productVariant.product');
            }

            if ($this->salesOrderInstance && $this->salesOrderInstance->exists) {
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
                    $variant = $this->allProductVariants->firstWhere('id', $item->product_variant_id);
                    return [
                        'id' => $item->id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'price_per_unit' => (float)$item->price_per_unit,
                        'variant_name_display' => $variant ? $variant->full_name_with_variant : ($item->productVariant ? $item->productVariant->product->name . ' - ' . $item->productVariant->variant_name : 'Unknown Variant'),
                        'available_stock' => $variant ? $variant->stock_quantity : 0,
                    ];
                })->toArray();
            }
        } else {
            $this->initializeNewSalesOrder();
        }
        $this->calculateTotalAmount();
    }

    protected function loadProductVariants()
    {
        $this->allProductVariants = ProductVariant::with('product:id,name,sku')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'product_variants.id',
                'product_variants.selling_price', // Ensure selling_price is selected
                'product_variants.stock_quantity',
                DB::raw('CONCAT(products.name, " - ", product_variants.variant_name, " (SKU: ", COALESCE(products.sku, "N/A"), ")") as full_name_with_variant')
            )
            ->orderBy('products.name')
            ->orderBy('product_variants.variant_name')
            ->get();
    }

    private function initializeNewSalesOrder() {
        $this->salesOrderInstance = new SalesOrder();
        $this->order_date = Carbon::now()->format('Y-m-d');
        $this->status = 'pending';
        $this->channel = 'boutique';
        $this->location_id = $this->allLocations->first()->id ?? null; // Default to first location for boutique if any
        $this->customer_name = '';
        $this->customer_email = '';
        $this->customer_phone = '';
        $this->generateOrderNumber(); // Generate order number for new SO
        $this->items = [];
        $this->addItem();
        $this->calculateTotalAmount();
    }

    private function generateOrderNumber()
    {
        // Example generation: SO-YYYYMMDD-HHMMSS-RANDOM
        $this->order_number = 'SO-' . Carbon::now()->format('YmdHis') . strtoupper(substr(uniqid(), -4));
    }


    public function addItem()
    {
        $this->items[] = [
            'product_variant_id' => null,
            'quantity' => 1,
            'price_per_unit' => 0.000,
            'variant_name_display' => 'Select Variant',
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

    public function updated($propertyName)
    {
        // Check if an item's product_variant_id, quantity, or price_per_unit was updated
        if (preg_match('/items\.(\d+)\.(product_variant_id|quantity|price_per_unit)/', $propertyName, $matches)) {
            $index = (int)$matches[1];
            $field = $matches[2];

            if (!isset($this->items[$index])) return;

            if ($field === 'product_variant_id' && !empty($this->items[$index]['product_variant_id'])) {
                $variant = $this->allProductVariants->find($this->items[$index]['product_variant_id']);
                if ($variant) {
                    // **This is the key change: Set price_per_unit from variant's selling_price**
                    $this->items[$index]['price_per_unit'] = (float)$variant->selling_price;
                    $this->items[$index]['variant_name_display'] = $variant->full_name_with_variant;
                    $this->items[$index]['available_stock'] = $variant->stock_quantity;

                    // Adjust quantity if it exceeds available stock, or set to 1 if stock is available
                    if ($variant->stock_quantity <= 0) {
                        $this->items[$index]['quantity'] = 0;
                    } elseif (($this->items[$index]['quantity'] ?? 1) > $variant->stock_quantity) {
                        $this->items[$index]['quantity'] = $variant->stock_quantity;
                    } elseif (($this->items[$index]['quantity'] ?? 0) == 0 && $variant->stock_quantity > 0) {
                         $this->items[$index]['quantity'] = 1;
                    }
                } else { // Variant not found or deselected
                    $this->items[$index]['price_per_unit'] = 0.000;
                    $this->items[$index]['variant_name_display'] = 'Select Variant';
                    $this->items[$index]['available_stock'] = 0;
                    $this->items[$index]['quantity'] = 1; // Reset quantity
                }
            }
            // Always recalculate total if any of these fields change for an item
            $this->calculateTotalAmount();
        }

        if ($propertyName === 'channel' && $this->channel !== 'boutique') {
            $this->location_id = null;
        }
    }

    public function calculateTotalAmount()
    {
        $this->total_amount = 0;
        foreach ($this->items as $item) {
            $quantity = $item['quantity'] ?? 0;
            $price = $item['price_per_unit'] ?? 0.000;
            if (is_numeric($quantity) && is_numeric($price)) {
                $this->total_amount += $quantity * $price;
            }
        }
    }

    public function saveSalesOrder()
    {
        $this->validate();

        DB::transaction(function () {
            $isNewOrder = !($this->salesOrderInstance && $this->salesOrderInstance->exists);

            if ($isNewOrder) {
                $this->salesOrderInstance = new SalesOrder();
                // If order_number is not auto-incremented by DB and not set yet for a new order
                if(empty($this->salesOrderInstance->order_number) && empty($this->order_number)){
                    $this->generateOrderNumber();
                }
                 $this->salesOrderInstance->order_number = $this->order_number;
            }

            // Ensure order_number is assigned if it's from the input field and not the instance
            if(!empty($this->order_number) && $this->order_number !== $this->salesOrderInstance->order_number) {
                 $this->salesOrderInstance->order_number = $this->order_number;
            }


            $this->salesOrderInstance->channel = $this->channel;
            $this->salesOrderInstance->location_id = ($this->channel === 'boutique' && $this->location_id) ? $this->location_id : null;
            $this->salesOrderInstance->order_date = $this->order_date;
            $this->salesOrderInstance->customer_details = [
                'name' => $this->customer_name,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
            ];
            $this->salesOrderInstance->status = $this->status;
            $this->salesOrderInstance->total_amount = $this->total_amount;
            $this->salesOrderInstance->save();

            $currentItemIds = [];
            foreach ($this->items as $itemData) {
                if (empty($itemData['product_variant_id']) || ($itemData['quantity'] ?? 0) <= 0) continue;

                $itemPayload = [
                    'product_variant_id' => $itemData['product_variant_id'],
                    'quantity' => $itemData['quantity'],
                    'price_per_unit' => $itemData['price_per_unit'],
                ];

                $soItem = null;
                if (!$isNewOrder && isset($itemData['id'])) {
                    $soItem = $this->salesOrderInstance->items()->find($itemData['id']);
                }

                if ($soItem) {
                    $soItem->update($itemPayload);
                } else {
                    $soItem = $this->salesOrderInstance->items()->create($itemPayload);
                }
                $currentItemIds[] = $soItem->id;
            }
            if (!$isNewOrder) {
                $this->salesOrderInstance->items()->whereNotIn('id', $currentItemIds)->delete();
            }

            // Stock deduction logic will be added here or in a service/event listener
            // based on order status (e.g., when 'completed' or 'shipped')
            if ($this->salesOrderInstance->status === 'completed' || $this->salesOrderInstance->status === 'shipped') {
                // Implement stock deduction and inventory movement creation here
                // This can be complex and should be handled carefully
            }

        });

        session()->flash('message', 'Sales Order #' . $this->salesOrderInstance->order_number . ($this->salesOrderInstance->wasRecentlyCreated && !$this->salesOrderInstance->wasChanged(['id']) ? ' created' : ' updated') . ' successfully.');
        return redirect()->route('sales-orders.index');
    }


    public function render()
    {
        return view('livewire.sales-order.sales-order-form')
            ->layoutData(['title' => $this->salesOrderInstance && $this->salesOrderInstance->exists ? 'Edit Sales Order' : 'Create Sales Order'])
            ->layout('components.layouts.livewire');
    }
}
