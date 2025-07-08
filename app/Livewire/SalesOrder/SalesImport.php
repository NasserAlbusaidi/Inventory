<?php

namespace App\Livewire\SalesOrder;

use App\Models\Activity;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SalesChannel;
use App\Models\SalesOrder;
use App\Traits\ManagesStock;
use App\Models\LocationInventory;
use App\Models\InventoryMovement;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Smalot\PdfParser\Parser;

class SalesImport extends Component
{
    use WithFileUploads;

    // --- STATE MANAGEMENT ---
    public int $step = 1;
    public $upload;
    public $location_id = null;
    public $sales_channel_id = null;
    public Collection $allLocations;
    public Collection $allSalesChannels;

    // Step 2 State
    public array $unmappedItems = [];
    public Collection $allSellableItems;

    // Step 3 State
    public array $mappedItems = [];
    public array $extractedDates = [];
    public array $selectedItems = [];
    public ?string $masterDate = null;
    public float $reportGrandTotal = 0.0;
    public int $totalItemCount = 0;
    public int $uniqueItemCount = 0;

    public function mount()
    {
        $this->allLocations = Location::orderBy('name')->get();
        $this->allSalesChannels = SalesChannel::orderBy('name')->get();
        $this->allSellableItems = $this->loadAllSellableItems();

        // Pre-load all sellable products and variants for the dropdowns
        $this->location_id = $this->allLocations->first()?->id;
        $this->sales_channel_id = $this->allSalesChannels->first()?->id;
    }

    // --- STEP 1: PARSE PDF & PROCEED TO PRODUCT MAPPING ---
       public function parsePdfForMapping()
    {
         $this->validate([
            'upload' => 'required|file|mimes:pdf|max:10240',
            'location_id' => 'required|exists:locations,id',
            'sales_channel_id' => 'required|exists:sales_channels,id',
        ]);

        try {
            $path = $this->upload->getRealPath();
            $parser = new Parser();
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();
        } catch (\Exception $e) {
            session()->flash('import_errors', 'Could not read the PDF file. It may be corrupted or in an unsupported format.');
            Log::error('PDF Import Failed (smalot/pdfparser): ' . $e->getMessage());
            return;
        }

        list($rawItems, $this->extractedDates) = $this->parsePdfForRawItemsAndDates($text);

        if (empty($rawItems)) {
            session()->flash('import_errors', 'No valid sales items could be found in the uploaded PDF.');
            $this->reset('upload');
            return;
        }

        $this->reportGrandTotal = collect($rawItems)->sum('price');
        $this->totalItemCount = count($rawItems);

        // --- THE CORRECTED LOGIC IS HERE ---
        $uniqueRawItems = collect($rawItems)->groupBy(function ($item) {
            // Group by a case-insensitive version of the item name.
            // This ensures "FigFigTint06" and "FigfigTint06" are treated as the same.
            return strtolower($item['raw_name']);
        })->map(function ($group, $normalizedRawName) {
            // Use the first item's name for display to preserve original casing.
            $originalDisplayName = $group->first()['raw_name'];

            $bestGuess = $this->findBestMatchProduct($originalDisplayName);

            return [
                'raw_name' => $originalDisplayName,
                'count' => $group->count(), // This will now be the correct total (e.g., 9)
                'total_price' => $group->sum('price'),
                'guess_key' => $bestGuess ? $bestGuess['key'] : null,
                'assigned_key' => $bestGuess ? $bestGuess['key'] : '',
            ];
        })->values()->all();
        // --- END OF CORRECTION ---

        $this->unmappedItems = $uniqueRawItems;
        $this->uniqueItemCount = count($this->unmappedItems);
        $this->step = 2;
    }

    // --- STEP 2: VALIDATE MAPPING & PROCEED TO DATE ASSIGNMENT ---
    public function proceedToDateAssignment()
    {
        foreach ($this->unmappedItems as $item) {
            if (empty($item['assigned_key'])) {
                session()->flash('mapping_error', "Please assign an official product to '{$item['raw_name']}' before proceeding.");
                return;
            }
        }

        $text = (new Parser())->parseFile($this->upload->getRealPath())->getText();
        list($rawItems, $this->extractedDates) = $this->parsePdfForRawItemsAndDates($text);

        $mappingRules = [];
        foreach ($this->unmappedItems as $mapping) {
            $mappingRules[strtolower($mapping['raw_name'])] = $mapping['assigned_key'];
        }

        $this->mappedItems = collect($rawItems)->map(function ($item) use ($mappingRules) {
            $assignedKey = $mappingRules[strtolower($item['raw_name'])] ?? null;
            if (!$assignedKey)  return null;

            $sellable = $this->allSellableItems->firstWhere('key', $assignedKey);

            // --- FIX 2: REMOVE THE OLD PER-ITEM LOCATION/CHANNEL LOGIC ---
            return [
                'sellable_key' => $assignedKey,
                'display_name' => $sellable['display_name'] ?? 'Error - Not Found',
                'price' => $item['price'],
                //'location' => $item['location'],  // This key no longer exists, REMOVE
                //'channel' => $item['channel'],    // This key no longer exists, REMOVE
                'selected_date' => null,
            ];
        })->filter()->values()->all();

        if ($this->totalItemCount !== count($this->mappedItems)) {
            Log::warning('Item count mismatch after mapping.', [ 'expected' => $this->totalItemCount, 'actual' => count($this->mappedItems), ]);
            session()->flash('mapping_error', 'An error occurred matching items. Some items may have been dropped. Please restart the import.');
            return;
        }

        $this->step = 3;
    }

    // --- STEP 3: FINALIZE IMPORT ---
    public function finalizeImport()
    {
        // Validation: Ensure every item has a date
        foreach ($this->mappedItems as $item) {
            if (empty($item['selected_date'])) {
                session()->flash('assignment_error', 'Please assign a date to all items before importing.');
                return;
            }
        }

         $groupedForImport = collect($this->mappedItems)->groupBy('selected_date');

        // Initialize the counters outside the transaction
        $createdOrderCount = 0;
        $successCount = 0;

        // --- THE FIX IS ON THIS LINE ---
        // Pass the counters by reference (&) so they can be modified inside the closure.
        DB::transaction(function () use ($groupedForImport, &$createdOrderCount, &$successCount) {

             foreach ($groupedForImport as $date => $orderItems) {
                $orderDate = Carbon::createFromFormat('d.m.y', $date)->format('Y-m-d');
                $orderNumber = 'SO-IMP-' . Carbon::parse($orderDate)->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

                $salesOrder = SalesOrder::create([
                    'order_number' => $orderNumber,
                    'sales_channel_id' => $this->sales_channel_id,
                    'location_id' => $this->location_id,
                    'order_date' => $orderDate,
                    'status' => 'fulfilled',
                    'total_amount' => $orderItems->sum('price'),
                ]);
                // This will now correctly increment the variable from the parent scope
                $createdOrderCount++;

                foreach ($orderItems as $item) {
                    list($type, $id) = explode('_', $item['sellable_key']);
                    $modelClass = 'App\\Models\\' . $type;
                    $sellable = $modelClass::find($id);

                    if ($sellable) {
                       $salesOrder->items()->create([
                            'saleable_type' => $sellable->getMorphClass(),
                            'saleable_id' => $sellable->id,
                            'quantity' => 1,
                            'price_per_unit' => $item['price'],
                        ]);

                        $this->adjustStockAndLogMovement($sellable, -1, 'sale_import', $this->location_id, $salesOrder->id, $salesOrder->getMorphClass());

                        // This will also correctly increment the variable
                        $successCount++;
                    }
                }
             }

             if ($createdOrderCount > 0) {
                 Activity::create([
                    'type' => 'sales_order_imported',
                    'description' => "Imported {$createdOrderCount} sales orders with {$successCount} items from PDF upload.",
                ]);
            }
        });
        Activity::create([
            'type' => 'sales_order_imported',
            'description' => "Imported {$createdOrderCount} sales orders with {$successCount} items from PDF upload.",
        ]);

        session()->flash('message', "Import complete! Added {$createdOrderCount} new sales orders. Check the sales list for details.");
        return redirect()->route('sales-orders.index');
    }
    // --- FUZZY MATCHING & PARSING LOGIC ---

     private function findBestMatchProduct(string $rawName): ?array
    {
        // --- 1. Normalize and Tokenize the raw name from the PDF ---

        // Split on CamelCase first, e.g., "PimplePatch" -> "Pimple Patch"
        $spacedName = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $rawName);
        // Now use slug to normalize and create tokens
        $pdfTokens = array_filter(explode('-', Str::slug($spacedName)));

        if (empty($pdfTokens)) {
            return null;
        }

        $bestScore = 0;
        $bestMatch = null;

        // --- 2. Loop through all official products and score them ---
        foreach ($this->allSellableItems as $sellable) {
            $officialTokens = $sellable['tokens'];
            if (empty($officialTokens)) continue;

            $currentScore = 0;

            // --- 3. Calculate Score based on multiple signals ---

            // SIGNAL A: Perfect Subset (Very High Confidence)
            // This checks if every word from the PDF name exists in the official name.
            // e.g., ['pimple', 'patch'] is a subset of ['acne', 'pimple', 'master', 'patch']
            if (empty(array_diff($pdfTokens, $officialTokens))) {
                $currentScore += 100; // Assign a very high score for a subset match.
            }

            // SIGNAL B: Word Intersection (Medium Confidence)
            // How many words do they have in common?
            $intersection = array_intersect($pdfTokens, $officialTokens);
            if (count($intersection) > 0) {
                // Score based on the proportion of matched words.
                // e.g., if PDF has 2 words and both match, that's better than if it has 5 and only 2 match.
                $percentageMatch = count($intersection) / count($pdfTokens);
                $currentScore += $percentageMatch * 50; // Add up to 50 points.
            }

            // SIGNAL C: String Similarity (Low Confidence Fallback)
            // Use the original similar_text on the normalized names as a tie-breaker.
            similar_text($sellable['normalized_name'], Str::slug($rawName), $percent);
            $currentScore += $percent / 2; // Add up to 50 points.


            // SIGNAL D: Fuzzy Matching (Very Low Confidence)
            // This is a last resort to catch any close matches that didn't score well otherwise.
            // This is useful for typos or very close names.
            // We use a very low threshold here to avoid false positives.
            if ($currentScore < 50) {
                $fuzzyMatch = similar_text($sellable['normalized_name'], Str::slug($spacedName), $percent);
                if ($fuzzyMatch > 0) {
                    $currentScore += $percent / 2; // Add up to 50 points.
                }
            }


            // --- 4. Update the best match if the current score is higher ---
            if ($currentScore > $bestScore) {
                $bestScore = $currentScore;
                $bestMatch = $sellable;
            }
        }

        // --- 5. Return the match only if it meets a minimum confidence threshold ---
        // This prevents bad matches. Adjust the threshold if needed.
        return $bestScore > 50 ? $bestMatch : null;
    }

    private function loadAllSellableItems(): Collection
    {
        $products = Product::get(['id', 'name']);
        $variants = ProductVariant::with('product:id,name')->get(['id', 'product_id', 'variant_name']);

        $sellableItems = collect([]);
        foreach ($products as $p) {
             $sellableItems->push($this->formatSellableForItem($p));
        }
        foreach ($variants as $v) {
             $sellableItems->push($this->formatSellableForItem($v));
        }
        return $sellableItems->sortBy('display_name')->values();
    }

    private function formatSellableForItem(\Illuminate\Database\Eloquent\Model $item): array
    {
        $isVariant = $item instanceof ProductVariant;
        $displayName = $isVariant
            ? "{$item->product->name} - {$item->variant_name}"
            : $item->name;

        // Use Str::slug for smart, multi-lingual normalization.
        $normalizedName = Str::slug($displayName);

        // Tokenize the name into individual words.
        $tokens = array_filter(explode('-', $normalizedName));

        return [
            'key' => class_basename($item) . '_' . $item->id,
            'display_name' => $displayName,
            'normalized_name' => $normalizedName,
            'tokens' => $tokens, // Store the tokens for fast comparison later.
        ];
    }

    private function parsePdfForRawItemsAndDates(string $text): array
    {
        $lines = explode(PHP_EOL, $text);
        $items = [];
        $dates = [];
        $currentContext = ['location' => 'Toile Boutique', 'channel' => 'In-Store Sale'];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_contains(strtoupper($line), 'SHELVE NO.') || str_contains(strtoupper($line), 'SALES FROM')) continue;

            // Context setting logic remains the same
            if (preg_match('/^([a-zA-Z0-9\.\s&]+?)\s+([a-zA-Z\s]+Shop.*)$/', $line, $matches)) {
                $currentContext['location'] = trim($matches[1]);
                $currentContext['channel'] = trim($matches[2]);
            }
            if (preg_match_all('/(\d{2}\.\d{2}\.\d{2})/', $line, $dateMatches)) {
                foreach ($dateMatches[1] as $date) { $dates[] = $date; }
            }

            // The main item parsing logic
            if (preg_match('/(.+?)\s+([\d\.,]+)$/', $line, $itemMatches)) {
                $rawItemNameString = trim(preg_replace(['/(\d{2}\.\d{2}\.\d{2})/', '/\s+\d+%$/'], '', $itemMatches[1]));
                $totalPrice = (float) str_replace(',', '', $itemMatches[2]);

                // --- NEW QUANTITY DETECTION LOGIC ---
                $quantity = 1;
                $itemName = $rawItemNameString;

                // Look for patterns like "- 2pcs", "-3pcs", "(3 pcs)", etc.
                if (preg_match('/(?:-|\s)\s*(\d+)\s*pcs/i', $rawItemNameString, $quantityMatch)) {
                    $quantity = (int)$quantityMatch[1];
                    // Clean the quantity indicator from the item name for better matching
                    $itemName = trim(preg_replace('/(?:-|\s)\s*\d+\s*pcs/i', '', $rawItemNameString));
                }
                // --- END OF NEW LOGIC ---

                if (!empty($itemName) && !is_numeric($itemName) && strtolower($itemName) !== 'total') {
                    // --- ADJUSTMENT FOR QUANTITY ---
                    // The price in the report is the TOTAL price for all pieces.
                    // We need the price PER unit for our database.
                    $pricePerUnit = ($quantity > 0) ? $totalPrice / $quantity : $totalPrice;

                    // If quantity is > 1, we add the item to the list that many times.
                    // This is the simplest way to handle it without changing the downstream logic.
                    for ($i = 0; $i < $quantity; $i++) {
                        $items[] = [
                            'raw_name' => $itemName, // The cleaned name
                            'price' => $pricePerUnit, // The price for a single unit
                            'location' => $currentContext['location'],
                            'channel' => $currentContext['channel'],
                        ];
                    }
                }
            }
        }
        return [$items, array_values(array_unique($dates))];
    }

    // --- UI HELPER METHODS ---

    public function applyMasterDate()
    {
        if ($this->masterDate && !empty($this->selectedItems)) {
            foreach ($this->selectedItems as $index) {
                if (isset($this->mappedItems[$index])) {
                    $this->mappedItems[$index]['selected_date'] = $this->masterDate;
                }
            }
        }
    }

    public function toggleSelectAll()
    {
        if (count($this->selectedItems) === count($this->mappedItems)) {
            $this->selectedItems = [];
        } else {
            $this->selectedItems = array_keys($this->mappedItems);
        }
    }

    public function backToStep1()
    {
        $this->reset(['step', 'upload', 'unmappedItems', 'mappedItems', 'extractedDates', 'selectedItems', 'masterDate']);
        $this->step = 1;
    }

    public function backToStep2()
    {
        $this->reset(['step', 'mappedItems', 'extractedDates', 'selectedItems', 'masterDate']);
        $this->step = 2;
    }


    private function adjustStockAndLogMovement(\Illuminate\Database\Eloquent\Model $purchasable, int $quantityChange, string $movementType, int $locationId, int $orderId, string $orderMorphClass)
    {
        if ($quantityChange == 0) return;

        $inventoryRecord = LocationInventory::firstOrCreate(
            [
                'inventoriable_type' => $purchasable->getMorphClass(),
                'inventoriable_id'   => $purchasable->id,
                'location_id'        => $locationId,
            ],
            ['stock_quantity' => 0]
        );
        $inventoryRecord->increment('stock_quantity', $quantityChange);

        InventoryMovement::create([
            'itemable_type' => $purchasable->getMorphClass(),
            'itemable_id'   => $purchasable->id,
            'location_id'   => $locationId,
            'quantity'      => $quantityChange,
            'type'          => $movementType,
            'related_type'  => $orderMorphClass, // Use the morph class of PurchaseOrder
            'related_id'    => $orderId,
        ]);
        Log::info("Stock for " . class_basename($purchasable) . " #{$purchasable->id} changed by {$quantityChange} at Location #{$locationId} due to PO #{$orderId} - Type: {$movementType}. New stock: {$inventoryRecord->fresh()->stock_quantity}");
    }

    public function render()
    {
        return view('livewire.sales-order.sales-import')
            ->layoutData(['title' => 'Import Sales Orders'])
            ->layout('components.layouts.livewire');
    }
}
