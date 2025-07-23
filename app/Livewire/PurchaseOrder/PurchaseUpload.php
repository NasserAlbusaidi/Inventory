<?php

namespace App\Livewire\PurchaseOrder;

use App\Imports\PurchaseOrderImport;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseUpload extends Component
{
    use WithFileUploads;

    // --- STATE MANAGEMENT ---
    public int $step = 1;
    public $file;

    // --- Data & Collections ---
    public Collection $allSuppliers;
    public Collection $allPurchasableItems;
    public array $reviewData = []; // Holds all data for the review step

    public function mount()
    {
        $this->allSuppliers = Supplier::orderBy('name')->get();
        $this->loadPurchasableItems(); // Pre-load all products and variants
    }

    // --- STEP 1: PARSE CSV & PROCEED TO REVIEW ---
    public function proceedToReview()
    {
        $this->validate(['file' => 'required|mimes:csv,txt|max:10240']);
        $this->resetState(); // Clear previous import attempts

        try {
            $rows = Excel::toCollection(new \stdClass(), $this->file->getRealPath())[0];
            $importer = new PurchaseOrderImport;
            $importer->collection($rows);
            $parsedData = $importer->data;

            // --- Build the data for the review screen (Step 2) ---
            $header = $parsedData['header'];
            $itemsFromCsv = $parsedData['items'];

            // 1. Find Supplier
            $supplier = Supplier::where('name', 'LIKE', '%' . $header['supplier_name'] . '%')->first();

            // 2. Match Products
            $mappedItems = [];
            foreach ($itemsFromCsv as $csvItem) {
                $searchDescription = strtolower(trim($csvItem['description']));
                $bestGuess = $this->findBestMatchProduct($searchDescription);

                $mappedItems[] = [
                    'raw_description' => $csvItem['description'],
                    'quantity' => $csvItem['quantity'],
                    'price' => $csvItem['price'],
                    'assigned_key' => $bestGuess ? $bestGuess['key'] : '', // Pre-fill with best guess
                ];
            }

            // 3. Populate the public reviewData property
            $this->reviewData = [
                'header' => $header,
                'items' => $mappedItems,
                'supplier' => [
                    'found' => (bool)$supplier,
                    'id' => $supplier?->id,
                    'name' => $supplier?->name,
                ],
                'stats' => [
                    'total_value' => collect($itemsFromCsv)->sum('amount'),
                    'item_count' => count($itemsFromCsv),
                    'unmatched_count' => collect($mappedItems)->where('assigned_key', '')->count(),
                ],
            ];

            // 4. Move to the next step
            $this->step = 2;

        } catch (Exception $e) {
            session()->flash('import_error', 'Import failed during parsing: ' . $e->getMessage());
            Log::error('PO Import Parse Error: ' . $e->getMessage());
        }
    }

    // --- STEP 2: FINALIZE IMPORT ---
    public function finalizeImport()
    {
        // Validation for Step 2
        if (!$this->reviewData['supplier']['found'] && empty($this->reviewData['supplier']['id'])) {
            session()->flash('review_error', "Please create or select a supplier before importing.");
            return;
        }

        try {
            DB::transaction(function () {
                $supplierId = $this->reviewData['supplier']['id'];

                // If supplier was just created, the name is in the header, not the supplier block
                $supplierName = $this->reviewData['supplier']['name'] ?? $this->reviewData['header']['supplier_name'];

                // Handle supplier creation if 'new' was selected
                if ($supplierId === 'new') {
                    $newSupplier = Supplier::create(['name' => $supplierName]);
                    $supplierId = $newSupplier->id;
                }

                $purchaseOrder = PurchaseOrder::create([
                    'supplier_id' => $supplierId,
                    'order_number' => $this->reviewData['header']['order_number'],
                    'order_date' => $this->reviewData['header']['order_date'],
                    'status' => 'ordered',
                    'total_amount' => $this->reviewData['stats']['total_value'],
                ]);

                $importedItemCount = 0;
                foreach ($this->reviewData['items'] as $item) {
                    // Skip items that the user didn't map
                    if (empty($item['assigned_key'])) continue;

                    list($type, $id) = explode('_', $item['assigned_key']);
                    $modelClass = ($type === 'ProductVariant') ? ProductVariant::class : Product::class;
                    $purchasable = $modelClass::find($id);

                    if ($purchasable) {
                        $purchaseOrder->items()->create([
                            'purchasable_type' => $purchasable->getMorphClass(),
                            'purchasable_id'   => $purchasable->id,
                            'quantity'         => $item['quantity'],
                            'cost_price_per_unit' => $item['price'],
                        ]);
                        $importedItemCount++;
                    }
                }

                session()->flash('message', "Import complete! Added Purchase Order #{$purchaseOrder->order_number} with {$importedItemCount} items.");
                return redirect()->route('purchase-orders.index'); // Use your PO index route name
            });
        } catch (Exception $e) {
            session()->flash('import_error', 'An unexpected error occurred during final import: ' . $e->getMessage());
            Log::error("PO Finalize Error: " . $e->getMessage() . " | " . $e->getTraceAsString());
        }
    }

    // --- Helper & UI Methods ---

    protected function findBestMatchProduct(string $searchDescription): ?array
    {
        // Simple matching logic, can be enhanced with fuzzy logic later like in SalesImport
        return $this->allPurchasableItems->first(function ($purchasable) use ($searchDescription) {
            return str_contains($purchasable['search_name'], $searchDescription);
        });
    }

    protected function loadPurchasableItems()
    {
        // This is the same logic from before, ensuring we have a list to search against
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
        session()->forget(['import_error', 'review_error']);
        $this->reviewData = [];
    }

    public function render()
    {
        return view('livewire.purchase-order.purchase-upload')
            ->layoutData(['title' => 'Import Purchase Order'])
            ->layout('components.layouts.livewire'); // Ensure your layout is correct
    }
}
