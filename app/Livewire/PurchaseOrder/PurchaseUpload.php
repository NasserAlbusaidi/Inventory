<?php

namespace App\Livewire\PurchaseOrder;

use App\Imports\PurchaseOrderImport;
use App\Models\Category;
use App\Models\Location;
use App\Models\OneTimeExpense;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseUpload extends Component
{
    use WithFileUploads;

    // --- MAIN STATE ---
    public int $step = 1;
    public $file;
    public Collection $allSuppliers;
    public array $reviewData = [];

    // --- Location State ---
    public Collection $allLocations;
    public ?int $receiving_location_id = null;

    // --- Collections & Modal State ---
    public Collection $allPurchasableItems;
    public bool $showCreateProductModal = false;
    public ?int $creatingProductIndex = null;
    public Collection $allCategories;
    public string $newProductName = '';
    public string $newProductSku = '';
    public ?int $newProductCategoryId = null;
    public ?int $bulkCreateCategoryId = null;

    // --- Final Cost State ---
    public ?string $finalOmrTotal = null;

    public function mount()
    {
        $this->allSuppliers = Supplier::orderBy('name')->get();
        $this->allCategories = Category::orderBy('name')->get();
        $this->allLocations = Location::orderBy('name')->get();
        $this->loadPurchasableItems();
        $this->receiving_location_id = $this->allLocations->first()?->id;
    }

    public function proceedToReview()
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
            'receiving_location_id' => 'required|exists:locations,id'
        ]);
        $this->resetState();

        try {
            $rows = Excel::toCollection(new \stdClass(), $this->file->getRealPath())[0];
            $importer = new PurchaseOrderImport;
            $importer->collection($rows);
            $parsedData = $importer->data;

            $header = $parsedData['header'];
            $itemsFromCsv = $parsedData['items'];
            $supplier = Supplier::where('name', 'LIKE', '%' . $header['supplier_name'] . '%')->first();
            $mappedItems = [];

            foreach ($itemsFromCsv as $csvItem) {
                $searchDescription = strtolower(trim($csvItem['description']));
                $bestGuess = $this->findBestMatchProduct($searchDescription);
                $mappedItems[] = [
                    'raw_description' => $csvItem['description'],
                    'quantity' => $csvItem['quantity'],
                    'price' => $csvItem['price'],
                    'assigned_key' => $bestGuess ? $bestGuess['key'] : '',
                ];
            }

            $header['receiving_location_id'] = $this->receiving_location_id;

            $this->reviewData = [
                'header' => $header,
                'items' => $mappedItems,
                'supplier' => ['found' => (bool)$supplier, 'id' => $supplier?->id, 'name' => $supplier?->name],
                'stats' => [
                    'total_value_original' => $header['final_total_usd'],
                    'item_count' => count($itemsFromCsv),
                    'unmatched_count' => collect($mappedItems)->where('assigned_key', '')->count(),
                ],
            ];

            $this->step = 2;
        } catch (Exception $e) {
            session()->flash('import_error', 'Import failed during parsing: ' . $e->getMessage());
        }
    }

    public function openCreateProductModal(int $index)
    {
        $this->creatingProductIndex = $index;
        $this->newProductName = $this->reviewData['items'][$index]['raw_description'];
        $this->newProductSku = Str::slug($this->newProductName);
        $this->newProductCategoryId = $this->allCategories->first()?->id;
        $this->showCreateProductModal = true;
    }

    public function closeCreateProductModal()
    {
        $this->showCreateProductModal = false;
        $this->reset(['creatingProductIndex', 'newProductName', 'newProductSku', 'newProductCategoryId']);
        $this->resetErrorBag();
    }

    public function saveNewProduct()
    {
        $this->validate([
            'newProductName' => 'required|string|max:255|unique:products,name',
            'newProductSku' => 'required|string|max:255|unique:products,sku',
            'newProductCategoryId' => 'required|exists:categories,id',
        ]);

        try {
            $newProduct = Product::create([
                'name' => $this->newProductName,
                'sku' => $this->newProductSku,
                'category_id' => $this->newProductCategoryId,
                'has_variants' => false,
                'cost_price' => $this->reviewData['items'][$this->creatingProductIndex]['price'],
            ]);

            $this->loadPurchasableItems();
            $this->reviewData['items'][$this->creatingProductIndex]['assigned_key'] = 'Product_' . $newProduct->id;
            $this->reviewData['stats']['unmatched_count'] = collect($this->reviewData['items'])->where('assigned_key', '')->count();
            $this->closeCreateProductModal();
            session()->flash('message', "Successfully created product: {$newProduct->name}");
        } catch (Exception $e) {
            $this->addError('modal_error', 'Could not save the product. Error: ' . $e->getMessage());
        }
    }

    public function bulkCreateProducts()
    {
        $this->validate(
            ['bulkCreateCategoryId' => 'required|exists:categories,id'],
            ['bulkCreateCategoryId.required' => 'Please select a default category before bulk creating.']
        );

        $createdCount = 0;
        $skippedCount = 0;
        $skippedProducts = [];

        foreach ($this->reviewData['items'] as $index => $item) {
            if (empty($item['assigned_key'])) {
                $productName = $item['raw_description'];
                $productSku = Str::slug($productName);
                if (Product::where('name', $productName)->orWhere('sku', $productSku)->exists()) {
                    $skippedCount++;
                    $skippedProducts[] = $productName;
                    continue;
                }
                $newProduct = Product::create([
                    'name' => $productName,
                    'sku' => $productSku,
                    'category_id' => $this->bulkCreateCategoryId,
                    'has_variants' => false,
                    'cost_price' => $item['price'],
                ]);
                $this->reviewData['items'][$index]['assigned_key'] = 'Product_' . $newProduct->id;
                $createdCount++;
            }
        }

        if ($createdCount > 0) {
            $this->loadPurchasableItems();
            $this->reviewData['stats']['unmatched_count'] = collect($this->reviewData['items'])->where('assigned_key', '')->count();
            session()->flash('message', "Successfully bulk created {$createdCount} new products.");
        }
        if ($skippedCount > 0) {
            session()->flash('review_error', "Skipped {$skippedCount} products because a product with the same name or SKU already exists: " . implode(', ', $skippedProducts));
        }
        $this->reset('bulkCreateCategoryId');
    }

    public function finalizeImport()
    {
        $this->validate(['finalOmrTotal' => 'nullable|numeric|min:0']);

        if (!$this->reviewData['supplier']['found'] && empty($this->reviewData['supplier']['id'])) {
            session()->flash('review_error', "Please create or select a supplier before importing.");
            return;
        }

        try {
            DB::transaction(function () {
                $supplierId = $this->reviewData['supplier']['id'];
                if ($supplierId === 'new') {
                    $supplierName = $this->reviewData['header']['supplier_name'];
                    $newSupplier = Supplier::create(['name' => $supplierName]);
                    $supplierId = $newSupplier->id;
                }

                $orderNumber = 'PO-IMP-' . Carbon::parse($this->reviewData['header']['order_date'])->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
                $originalUsdTotal = (float) $this->reviewData['stats']['total_value_original'];
                $finalOmrTotal = (!empty($this->finalOmrTotal))
                    ? (float) $this->finalOmrTotal
                    : $originalUsdTotal * 0.395;

                $itemsOmrTotal = 0;
                $processedItems = [];

                foreach ($this->reviewData['items'] as $item) {
                    if (empty($item['assigned_key'])) continue;
                    $itemUsdAmount = (float) $item['price'] * (int) $item['quantity'];
                    $itemCostInOmr = ($originalUsdTotal > 0) ? ($itemUsdAmount / $originalUsdTotal) * $finalOmrTotal : 0;
                    $itemsOmrTotal += $itemCostInOmr;
                    $processedItems[] = [
                        'assigned_key' => $item['assigned_key'],
                        'quantity' => $item['quantity'],
                        'cost_price_per_unit_omr' => ($item['quantity'] > 0) ? $itemCostInOmr / $item['quantity'] : 0,
                    ];
                }

                $expenseAmount = $finalOmrTotal - $itemsOmrTotal;

                $purchaseOrder = PurchaseOrder::create([
                    'supplier_id' => $supplierId,
                    'order_number' => $orderNumber,
                    'order_date' => $this->reviewData['header']['order_date'],
                    'receiving_location_id' => $this->reviewData['header']['receiving_location_id'],
                    'status' => 'ordered',
                    'total_amount' => $itemsOmrTotal,
                ]);

                foreach ($processedItems as $item) {
                    list($type, $id) = explode('_', $item['assigned_key']);
                    $modelClass = ($type === 'ProductVariant') ? ProductVariant::class : Product::class;
                    $purchasable = $modelClass::find($id);
                    if ($purchasable) {
                        $purchaseOrder->items()->create([
                            'purchasable_type' => $purchasable->getMorphClass(),
                            'purchasable_id'   => $purchasable->id,
                            'quantity'         => $item['quantity'],
                            'cost_price_per_unit' => $item['cost_price_per_unit_omr'],
                        ]);
                    }
                }

                if ($expenseAmount > 0.01) {
                    OneTimeExpense::create([
                        'location_id' => $purchaseOrder->receiving_location_id,
                        'description' => "Delivery/Fees for Imported PO: " . $purchaseOrder->order_number,
                        'amount' => $expenseAmount,
                        'expense_date' => $purchaseOrder->order_date,
                    ]);
                }

                session()->flash('message', "Import complete! Added PO #{$orderNumber}. An expense of ".number_format($expenseAmount, 3)." OMR was also recorded.");
                return redirect()->route('purchase-orders.index');
            });
        } catch (Exception $e) {
            session()->flash('import_error', 'An unexpected error occurred during final import: ' . $e->getMessage());
            Log::error("PO Finalize Error: " . $e->getMessage() . " | " . $e->getTraceAsString());
        }
    }

    protected function findBestMatchProduct(string $searchDescription): ?array
    {
        return $this->allPurchasableItems->first(function ($purchasable) use ($searchDescription) {
            return str_contains($purchasable['search_name'], $searchDescription);
        });
    }

    protected function loadPurchasableItems()
    {
        $simpleProducts = Product::where('has_variants', false)->get();
        $variantProducts = ProductVariant::with('product:id,name')->get();
        $items = collect([]);
        foreach ($simpleProducts as $product) {
            $items->push(['key' => 'Product_'.$product->id, 'search_name' => strtolower(trim($product->name)), 'display_name' => $product->name]);
        }
        foreach ($variantProducts as $variant) {
            $displayName = "{$variant->product->name} - {$variant->variant_name}";
            $items->push(['key' => 'ProductVariant_'.$variant->id, 'search_name' => strtolower(trim($displayName)), 'display_name' => $displayName]);
        }
        $this->allPurchasableItems = $items->sortBy('display_name');
    }

    public function backToStep1()
    {
        $this->resetState();
        $this->step = 1;
    }

    protected function resetState()
    {
        $this->resetErrorBag();
        session()->forget(['import_error', 'review_error', 'message']);
        $this->reviewData = [];
    }

    public function render()
    {
        return view('livewire.purchase-order.purchase-upload')
            ->layoutData(['title' => 'Import Purchase Order'])
            ->layout('components.layouts.livewire');
    }
}
