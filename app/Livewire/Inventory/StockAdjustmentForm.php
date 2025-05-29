<?php

namespace App\Livewire\Inventory;

use App\Models\ProductVariant;
use App\Models\Location;
use App\Models\InventoryMovement;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentForm extends Component
{
    public $product_variant_id = null;
    public $selected_location_id = null;
    public $current_stock = 0;
    public $new_stock_quantity = null;
    public $reason = '';

    public $product_variants_list = [];
    public $locations_list = [];
    public $selected_variant_name = '';

    protected function rules(): array
    {
        return [
            'product_variant_id' => 'required|exists:product_variants,id',
            'selected_location_id' => 'required|exists:locations,id',
            'new_stock_quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ];
    }

    protected $messages = [
        'product_variant_id.required' => 'Please select a product variant.',
        'selected_location_id.required' => 'Please select a location for this adjustment.',
        'new_stock_quantity.required' => 'New stock quantity is required.',
        'new_stock_quantity.integer' => 'Stock quantity must be a whole number.',
        'new_stock_quantity.min' => 'Stock quantity cannot be negative.',
        'reason.required' => 'A reason for the adjustment is required.',
    ];

    public function mount()
    {
        // Optimized to load only necessary fields for the dropdown
        $this->product_variants_list = ProductVariant::join('products', 'product_variants.product_id', '=', 'products.id')
            ->select('product_variants.id', DB::raw('CONCAT(products.name, " - ", product_variants.variant_name, " (SKU: ", COALESCE(products.sku, "N/A"), ")") as full_name'))
            ->orderBy('products.name')
            ->orderBy('product_variants.variant_name')
            ->get();
        $this->locations_list = Location::orderBy('name')->get(); //
    }

    public function updatedProductVariantId($value)
    {
        if ($value) {
            $variant = ProductVariant::find($value); //
            if ($variant) {
                $this->current_stock = $variant->stock_quantity;
                $this->new_stock_quantity = $variant->stock_quantity; // Pre-fill new stock with current
                $this->selected_variant_name = $variant->product->name . ' - ' . $variant->variant_name;
            } else {
                $this->reset(['current_stock', 'new_stock_quantity', 'selected_variant_name']);
            }
        } else {
            $this->reset(['current_stock', 'new_stock_quantity', 'selected_variant_name']);
        }
    }

    public function saveAdjustment()
    {
        $this->validate();

        $variant = ProductVariant::find($this->product_variant_id);
        if (!$variant) {
            session()->flash('error', 'Selected product variant not found.');
            return;
        }

        $oldStock = $variant->stock_quantity;
        $quantityChange = $this->new_stock_quantity - $oldStock;

        DB::transaction(function () use ($variant, $quantityChange, $oldStock) {
            // Update the stock quantity on the ProductVariant model
            $variant->stock_quantity = $this->new_stock_quantity;
            $variant->save();

            // Create an InventoryMovement record
            InventoryMovement::create([ //
                'product_variant_id' => $this->product_variant_id,
                'location_id' => $this->selected_location_id,
                'type' => 'adjustment', // This type signifies a manual change
                'quantity' => $quantityChange, // Record the change (+ve for increase, -ve for decrease)
                'reason' => $this->reason,
                'reference_type' => 'Stock Adjustment', // Optional: for context
                'reference_id' => null, // No specific order reference for manual adjustment
                'user_id' => Auth::id(),
            ]);
        });

        session()->flash('message', 'Stock for ' . $this->selected_variant_name . ' adjusted successfully from ' . $oldStock . ' to ' . $this->new_stock_quantity . '.');
        $this->reset(['product_variant_id', 'selected_location_id', 'current_stock', 'new_stock_quantity', 'reason', 'selected_variant_name']);
        // Optionally, re-fetch the variants list if stock changes in it are important for display immediately
        // $this->product_variants_list = ProductVariant::with('product')->get();
    }

    public function render()
    {
        return view('livewire.inventory.stock-adjustment-form')
            ->layoutData(['title' => 'Stock Adjustment'])
            ->layout('components.layouts.livewire');
    }
}
