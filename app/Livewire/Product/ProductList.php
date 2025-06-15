<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Category;
use App\Models\SalesOrderItem;
use App\Models\LocationInventory;
use Livewire\Component;
use App\Models\ProductVariant;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Termwind\Components\Dd;
use Illuminate\Support\Facades\Log;

class ProductList extends Component
{
    use WithPagination;

    // Existing properties
    public $search = '';
    public $categoryFilter = '';
    public $allCategoriesList = [];

    // (NEW) Property to hold the status filter from the dashboard
    public $statusFilter = '';

    public int $perPage = 10;


    public bool $showDeleteModal = false;
    public ?Product $productToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'page' => ['except' => 1],
        'statusFilter' => ['as' => 'filter[status]'],
        'perPage' => ['except' => 10],
    ];

    public function mount(Request $request) // <-- Inject Request to get filter from URL on initial load
    {
        $this->allCategoriesList = Category::orderBy('name')->get();

        // (NEW) Read the filter from the request on initial mount
        if ($request->has('filter.status')) {
            $this->statusFilter = $request->input('filter.status');
        }
    }

    // This method is no longer needed since we handle it in render()
    // public function deleteProduct($productId) { ... }

    public function updating() // Combined hook for all property updates
    {
        $this->resetPage();
    }

    public function confirmDelete($productId)
    {
        $this->productToDelete = Product::find($productId);
        $this->showDeleteModal = true;
    }

    /**
     * Closes the modal without taking action.
     */
    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->productToDelete = null;
    }

    /**
     * Performs the actual deletion.
     */
    public function deleteProduct()
    {
        if (!$this->productToDelete) {
            return; // Safety check
        }

        // Eager load the relationships we need to check
        $this->productToDelete->load('variants');

        // --- NEW, EXPLICIT CHECKS ---

        // 1. Check for any stock
        if ($this->productToDelete->fresh()->total_stock > 0) {
            session()->flash('error', "Cannot delete '{$this->productToDelete->name}' because it still has stock. Please adjust inventory first.");
            $this->closeModal();
            return;
        }

        // 2. Check for any linked sales or purchase order items
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

        // --- If all checks pass, then we can safely delete ---
        try {
            $productName = $this->productToDelete->name;

            // At this point, the delete operation should succeed without a QueryException,
            // but we still keep the try/catch for any other unexpected database errors.

            // The Observer will handle deleting variants and inventory records
            $this->productToDelete->delete();

            session()->flash('message', "Product '{$productName}' has been successfully deleted.");
        } catch (\Exception $e) {
            Log::error("Unexpected error deleting product {$this->productToDelete->id}: " . $e->getMessage());
            session()->flash('error', 'An unexpected error occurred while deleting the product.');
        }

        $this->closeModal();
    }

    public function render()
    {
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
