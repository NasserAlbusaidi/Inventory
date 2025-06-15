<?php
// In app/Livewire/Product/AdjustStockModal.php

namespace App\Livewire\Product;

use App\Models\Location;
use App\Models\Product;
use App\Models\LocationInventory;
use App\Models\InventoryMovement;
use Livewire\Component; // Note: It's a standard Component now

class AdjustStockModal extends Component
{
    public Product $product;
    public $variants;
    public $locations;

    // Form State
    public $selected_variant_id;
    public $location_id;
    public $quantity;
    public $notes;
    public $adjustment_type = 'addition';

    public function mount(int $productId)
    {
        $this->product = Product::with('variants')->findOrFail($productId);
        $this->variants = $this->product->variants;
        $this->locations = Location::all();

        if (!$this->product->has_variants) {
            $this->selected_variant_id = $this->variants->first()->id;
        }

        $this->location_id = $this->locations->first()->id ?? null;
    }

    public function adjustStock()
    {
        $this->validate([
            'selected_variant_id' => 'required|exists:product_variants,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|integer|min:1',
            'adjustment_type' => 'required|in:addition,deduction',
            'notes' => 'nullable|string|max:255',
        ]);

        $adjustmentQuantity = $this->adjustment_type === 'addition' ? $this->quantity : -$this->quantity;

        $inventory = LocationInventory::firstOrCreate(
            ['product_variant_id' => $this->selected_variant_id, 'location_id' => $this->location_id],
            ['stock' => 0]
        );

        if ($this->adjustment_type === 'deduction' && $inventory->stock < $this->quantity) {
            $this->addError('quantity', 'Cannot deduct more stock than available. Available: ' . $inventory->stock);
            return;
        }

        InventoryMovement::create([
            'product_variant_id' => $this->selected_variant_id,
            'location_id' => $this->location_id,
            'quantity' => $adjustmentQuantity,
            'type' => 'adjustment',
            'notes' => $this->notes,
        ]);

        $inventory->increment('stock', $adjustmentQuantity);

        $this->dispatch('stockUpdated');
    }

    public function render()
    {
        return view('livewire.product.adjust-stock-modal');
    }
}
