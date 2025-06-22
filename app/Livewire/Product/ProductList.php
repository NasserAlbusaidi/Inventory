<?php

namespace App\Livewire\Product;

use App\Models\Activity;
use App\Models\Product;
use App\Models\Category;
use App\Models\Location; // <-- ADDED
use App\Models\LocationInventory;
use App\Models\ProductVariant;
use App\Models\SalesOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    // Existing properties
    public $search = '';
    public $categoryFilter = '';
    public $allCategoriesList = [];
    public $statusFilter = '';
    public int $perPage = 10;

    // Delete Modal properties
    public bool $showDeleteModal = false;
    public ?Product $productToDelete = null;

    // (FIXED) Adjust Stock Modal properties
    public bool $showAdjustStockModal = false;
    public ?Product $adjustingStockForProduct = null; // Use the full model for convenience
    public $variants = [];
    public $locations = [];
    public $selected_variant_id = '';
    public $location_id = '';
    public $adjustment_type = 'addition';
    public $quantity;
    public $notes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'page' => ['except' => 1],
        'statusFilter' => ['as' => 'filter[status]'],
        'perPage' => ['except' => 10],
    ];

    public function mount(Request $request)
    {
        $this->allCategoriesList = Category::orderBy('name')->get();
        if ($request->has('filter.status')) {
            $this->statusFilter = $request->input('filter.status');
        }
    }

    public function updating()
    {
        $this->resetPage();
    }

    // --- Adjust Stock Modal Methods ---

    public function openAdjustStockModal(Product $product)
    {
        $this->resetErrorBag();
        $this->adjustingStockForProduct = $product->load('variants'); // Eager load variants
        $this->locations = Location::orderBy('name')->get();
        $this->location_id = $this->locations->first()?->id ?? '';

        if ($this->adjustingStockForProduct->has_variants) {
            $this->variants = $this->adjustingStockForProduct->variants;
            $this->selected_variant_id = ''; // Reset variant selection
        } else {
            $this->variants = [];
            $this->selected_variant_id = ''; // Not used, but good to clear
        }

        // Reset other form fields
        $this->adjustment_type = 'addition';
        $this->quantity = null;
        $this->notes = '';

        $this->showAdjustStockModal = true;
    }

    public function closeAdjustStockModal()
    {
        $this->showAdjustStockModal = false;
        $this->adjustingStockForProduct = null;
        $this->reset(['variants', 'locations', 'selected_variant_id', 'location_id', 'adjustment_type', 'quantity', 'notes']);
        $this->resetErrorBag();
    }

    public function adjustStock()
    {
        // (FIXED) Dynamic validation rules based on adjustment type
        $rules = [
            'location_id' => 'required|exists:locations,id',
            'adjustment_type' => 'required|in:addition,deduction,set', // <-- Added 'set'
            'notes' => 'nullable|string|max:255',
        ];

        // Quantity must be a positive integer for additions/deductions
        if ($this->adjustment_type === 'addition' || $this->adjustment_type === 'deduction') {
            $rules['quantity'] = 'required|integer|min:1';
        }
        // Quantity can be zero for setting a new value
        if ($this->adjustment_type === 'set') {
            $rules['quantity'] = 'required|integer|min:0';
        }

        if ($this->adjustingStockForProduct->has_variants) {
            $rules['selected_variant_id'] = 'required|exists:product_variants,id';
        }

        $this->validate($rules);

        $adjustable = $this->adjustingStockForProduct->has_variants
            ? ProductVariant::find($this->selected_variant_id)
            : $this->adjustingStockForProduct;

        if (!$adjustable) {
            session()->flash('error', 'Could not find the item to adjust stock for.');
            $this->closeAdjustStockModal();
            return;
        }

        try {
            DB::transaction(function () use ($adjustable) {
                $inventory = LocationInventory::firstOrNew([
                    'location_id' => $this->location_id,
                    'inventoriable_id' => $adjustable->id,
                    'inventoriable_type' => $adjustable->getMorphClass(),
                ]);

                $currentStock = $inventory->stock_quantity ?? 0;
                $adjustmentAmount = (int) $this->quantity;

                // (FIXED) Use a switch to handle all three cases
                switch ($this->adjustment_type) {
                    case 'addition':
                        $inventory->stock_quantity += $adjustmentAmount;
                        break;

                    case 'deduction':
                        if ($currentStock < $adjustmentAmount) {
                            throw ValidationException::withMessages([
                                'quantity' => 'Deduction amount (' . $adjustmentAmount . ') cannot be greater than the current stock (' . $currentStock . ').'
                            ]);
                        }
                        $inventory->stock_quantity -= $adjustmentAmount;
                        break;

                    case 'set':
                        $inventory->stock_quantity = $adjustmentAmount;
                        break;
                }
                $inventory->save();
            });

            $name = $adjustable->name ?? $adjustable->variant_name;
            Activity::create([
                'type' => 'stock_adjustment',
                'description' => sprintf(
                    '%s stock for %s at %s by %d. Notes: %s',
                    $this->adjustment_type === 'addition' ? 'Added' : ($this->adjustment_type === 'deduction' ? 'Deducted' : 'Set'),
                    $name,
                    Location::find($this->location_id)->name,
                    abs($this->quantity),
                    $this->notes
                ),
            ]);
            session()->flash('message', "Stock for '{$name}' has been successfully adjusted.");
            $this->closeAdjustStockModal();

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Stock Adjustment Failed: " . $e->getMessage());
            session()->flash('error', 'An unexpected error occurred while adjusting stock.');
            $this->closeAdjustStockModal();
        }
    }


    // --- Delete Product Modal Methods ---

    public function confirmDelete($productId)
    {
        $this->productToDelete = Product::find($productId);

        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->productToDelete = null;
    }

    public function deleteProduct()
    {
        if (!$this->productToDelete) {
            return;
        }

        $this->productToDelete->load('variants');

        if ($this->productToDelete->fresh()->total_stock > 0) {
            session()->flash('error', "Cannot delete '{$this->productToDelete->name}' because it still has stock. Please adjust inventory first.");
            $this->closeModal();
            return;
        }

        $isLinkedToOrders = \App\Models\SalesOrderItem::where(function ($query) {
            $query->where('saleable_type', 'product')->where('saleable_id', $this->productToDelete->id);
            if ($this->productToDelete->variants->isNotEmpty()) {
                $query->orWhere(function ($q) {
                    $q->where('saleable_type', 'variant')->whereIn('saleable_id', $this->productToDelete->variants->pluck('id'));
                });
            }
        })->exists() || \App\Models\PurchaseOrderItem::where(function ($query) {
            $query->where('purchasable_type', 'product')->where('purchasable_id', $this->productToDelete->id);
            if ($this->productToDelete->variants->isNotEmpty()) {
                $query->orWhere(function ($q) {
                    $q->where('purchasable_type', 'variant')->whereIn('purchasable_id', $this->productToDelete->variants->pluck('id'));
                });
            }
        })->exists();

        if ($isLinkedToOrders) {
            session()->flash('error', "Cannot delete '{$this->productToDelete->name}' because it is part of existing sales or purchase orders.");
            $this->closeModal();
            return;
        }

        try {
            $productName = $this->productToDelete->name;
            $this->productToDelete->delete();
            Activity::create([
                'type' => 'product_deleted',
                'description' => "Product deleted: {$productName}",
            ]);
            session()->flash('message', "Product '{$productName}' has been successfully deleted.");
        } catch (\Exception $e) {
            Log::error("Unexpected error deleting product {$this->productToDelete->id}: " . $e->getMessage());
            session()->flash('error', 'An unexpected error occurred while deleting the product.');
        }

        $this->closeModal();
    }


    // --- Render Method ---

    public function render()
    {
        // ... (render method remains unchanged) ...
        $productsQuery = Product::query()
            ->with(['category'])
            ->withCount('variants')
            ->withSum('locationInventories as direct_stock_sum', 'stock_quantity')
            ->withSum('variantsLocationInventories as variant_stock_sum', 'stock_quantity');

        // --- Handle Search and Category Filters ---
        if ($this->search) {
            $productsQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%')
                    ->orWhereHas('variants', fn($q) => $q->where('variant_name', 'like', '%' . $this->search . '%'));
            });
        }
        if ($this->categoryFilter) {
            $productsQuery->where('category_id', $this->categoryFilter);
        }

        // --- Handle Status Filters from Dashboard ---
        if ($this->statusFilter) {
            if ($this->statusFilter === 'low_stock') {
                $lowStockThreshold = app('settings')->get('low_stock_threshold', 5);
                $productsQuery->where(function ($query) use ($lowStockThreshold) {
                    $query->where(fn($q) => $q->where('has_variants', false)->whereHas('locationInventories', fn($i) => $i->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', $lowStockThreshold)))
                        ->orWhere(fn($q) => $q->where('has_variants', true)->whereHas('variants.locationInventories', fn($i) => $i->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', $lowStockThreshold)));
                });
            }

            if ($this->statusFilter === 'dead_stock') {
                $deadStockCutoffDate = now()->subDays(90);
                $soldItems = SalesOrderItem::whereHas('salesOrder', fn($q) => $q->where('created_at', '>=', $deadStockCutoffDate))
                    ->select('saleable_id', 'saleable_type')->distinct()->get();
                $soldProductIds = $soldItems->where('saleable_type', 'product')->pluck('saleable_id');
                $soldVariantIds = $soldItems->where('saleable_type', 'variant')->pluck('saleable_id');

                $productsQuery->where(function ($query) use ($soldProductIds, $soldVariantIds) {
                    $query->where(function ($q) use ($soldProductIds) {
                        $q->where('has_variants', false)
                            ->whereHas('locationInventories', fn($i) => $i->where('stock_quantity', '>', 0))
                            ->whereNotIn('id', $soldProductIds);
                    })->orWhere(function ($q) use ($soldVariantIds) {
                        $q->where('has_variants', true)
                            ->where(fn($sub) => $sub->whereHas('locationInventories', fn($i) => $i->where('stock_quantity', '>', 0))
                                ->orWhereHas('variants.locationInventories', fn($i) => $i->where('stock_quantity', '>', 0)))
                            ->whereDoesntHave('variants', fn($varQ) => $varQ->whereIn('id', $soldVariantIds));
                    });
                });
            }
        }

        $products = $productsQuery->orderBy('name')->paginate($this->perPage);
        $lowStockThreshold = app('settings')->get('low_stock_threshold', 5);



        return view('livewire.product.product-list', [
            'products' => $products,
            'lowStockThreshold' => $lowStockThreshold,
        ])->layout('components.layouts.livewire');
    }
}
