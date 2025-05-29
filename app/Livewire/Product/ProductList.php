<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ProductList extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $allCategoriesList = []; // Renamed to avoid conflict if $categories is used elsewhere

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        $this->allCategoriesList = Category::orderBy('name')->get(); //
    }

    public function deleteProduct($productId)
    {
        $product = Product::find($productId); //
        if ($product) {
            // SoftDeletes trait on Product model will handle this
            $product->delete();
            session()->flash('message', 'Product "' . $product->name . '" moved to trash.');
        } else {
            session()->flash('error', 'Product not found.');
        }
        $this->resetPage(); // Reset pagination to first page
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $productsQuery = Product::with(['category', 'variants'])
            ->withCount('variants') // Gets variants_count
            ->addSelect(['total_stock' => DB::table('product_variants') // Subquery for total stock
                ->selectRaw('SUM(stock_quantity)')
                ->whereColumn('product_variants.product_id', 'products.id')
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) { // Group search conditions
                    $q->where('products.name', 'like', '%' . $this->search . '%')
                      ->orWhere('products.sku', 'like', '%' . $this->search . '%')
                      ->orWhereHas('category', function ($subQuery) {
                          $subQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            });

        // Handle soft deletes - by default, only non-deleted are shown.
        // If you want to add a filter for "trashed" items later, you can add:
        // ->when($this->showTrashed, fn($query) => $query->onlyTrashed())

        $products = $productsQuery->orderBy('products.name')->paginate(10);

        return view('livewire.product.product-list', [
            'products' => $products,
            // 'allCategoriesList' is already set in mount and available
        ])->layout('components.layouts.livewire');
    }
}
