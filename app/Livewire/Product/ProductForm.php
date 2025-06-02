<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant; // Make sure this is imported
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // Import DB facade if not already

class ProductForm extends Component
{
    public ?Product $product = null;
    public ?int $productId = null;
    public string $name = '';
    public ?string $description = null;
    public ?int $category_id = null;
    public string $sku = ''; // Main product SKU

    public array $variants = [];

    public Collection $allCategories;

    public bool $showVariantManagement = false;
    public ?int $productIdBeingEdited = null;


     protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($this->product?->id),
            ],
            'variants' => 'present|array', // Ensure variants array is always present
            'variants.*.variant_name' => 'required|string|max:255', // Make variant_name always required if a variant row exists
            'variants.*.cost_price' => 'nullable|numeric|min:0',
            'variants.*.selling_price' => 'nullable|numeric|min:0',
            // Barcode validation should be per variant and ignore self if editing
            'variants.*.barcode' => 'nullable|string|max:255', // Simplified for now, see complex rule below
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.id' => 'nullable|integer|exists:product_variants,id', // For existing variants
        ];

        foreach ($this->variants as $index => $variant) {
            $variantIdToIgnore = $variant['id'] ?? null;
            // Apply unique rule for barcode only if barcode is not empty
            if (!empty(trim($variant['barcode'] ?? ''))) {
                $rules["variants.{$index}.barcode"] = [
                    'string',
                    'max:255',
                    Rule::unique('product_variants', 'barcode')
                        ->when($variantIdToIgnore, function ($rule) use ($variantIdToIgnore) {
                            return $rule->ignore($variantIdToIgnore);
                        })
                ];
            }
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'variants.*.variant_name.required' => 'The variant name is required.',
            'variants.*.barcode.unique' => 'This barcode is already in use.',
        ];
    }

    public function mount($productId = null)
    {
        $this->allCategories = Category::orderBy('name')->get();
        if ($productId) {
            $this->product = Product::with('variants')->findOrFail($productId);
            $this->productId = $this->product->id;
            // $this->productIdBeingEdited = $this->product->id; // Not strictly needed if using $this->product->exists
            $this->name = $this->product->name;
            $this->description = $this->product->description;
            $this->category_id = $this->product->category_id;
            $this->sku = $this->product->sku;

            if ($this->product->variants->isNotEmpty()) {
                $this->variants = $this->product->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'variant_name' => $variant->variant_name,
                        'cost_price' => $variant->cost_price,
                        'selling_price' => $variant->selling_price,
                        'barcode' => $variant->barcode ?? '', // Ensure barcode is string, not null
                        'stock_quantity' => $variant->stock_quantity ?? 0,
                    ];
                })->toArray();
            } else {
                $this->initializeDefaultVariant(); // Add one if none exist
            }
        } else {
            $this->product = new Product();
            $this->initializeDefaultVariant();
        }
         // This state is mostly for UI logic, not critical for save
        $this->showVariantManagement = !empty($this->variants);
    }

    private function initializeDefaultVariant()
    {
        if (empty($this->variants)) {
             $this->variants = [[
                'id' => null,
                'variant_name' => 'Default', // Or empty if you prefer user to fill it
                'cost_price' => null,
                'selling_price' => null,
                'barcode' => '',
                'stock_quantity' => 0
            ]];
        }
    }

    public function addVariant()
    {
        $this->variants[] = [
            'id' => null,
            'variant_name' => '',
            'cost_price' => null,
            'selling_price' => null,
            'barcode' => '',
            'stock_quantity' => 0
        ];
        $this->showVariantManagement = true; // Ensure section is visible
    }

    public function removeVariant($index)
    {
        // Actual deletion from DB will happen in save if this variant had an ID
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants); // Re-index
        if (empty($this->variants)) {
            // Optionally re-add a default if all are removed, or let save handle it.
            // $this->initializeDefaultVariant();
            $this->showVariantManagement = false; // Or true if you re-add default
        }
    }

    // Renamed from saveProduct to just save to match wire:submit.prevent="save"
    public function saveProduct()
    {
        $validatedData = $this->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                $this->product->name = $validatedData['name'];
                $this->product->description = $validatedData['description'];
                $this->product->category_id = $validatedData['category_id'];
                $this->product->sku = $validatedData['sku'] ?: null;

                $isNewProduct = !$this->product->exists;
                $this->product->save(); // Save product first to get ID if new

                $variantIdsToKeep = [];

                if (!empty($validatedData['variants'])) {
                    foreach ($validatedData['variants'] as $variantData) {
                        // Skip if variant_name is empty - effectively deleting it if it existed
                        if (empty(trim($variantData['variant_name']))) {
                            if (!empty($variantData['id'])) {
                                // Log::info("Marking variant ID for deletion (empty name): " . $variantData['id']);
                                // Deletion of unkept variants happens later
                            }
                            continue;
                        }

                        $barcodeValue = trim($variantData['barcode'] ?? '');
                        $barcodeToSave = $barcodeValue === '' ? null : $barcodeValue;

                        $payload = [
                            'variant_name' => $variantData['variant_name'],
                            'cost_price' => $variantData['cost_price'] ?? null,
                            'selling_price' => $variantData['selling_price'] ?? null,
                            'barcode' => $barcodeToSave,
                            'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                        ];

                        if (!empty($variantData['id'])) { // Existing variant
                            $variant = ProductVariant::find($variantData['id']);
                            if ($variant) {
                                $variant->update($payload);
                                $variantIdsToKeep[] = $variant->id;
                                // Log::info("Updated variant ID: " . $variant->id);
                            }
                        } else { // New variant
                            // Associate with the product
                            $newVariant = $this->product->variants()->create($payload);
                            $variantIdsToKeep[] = $newVariant->id;
                            // Log::info("Created new variant ID: " . $newVariant->id . " for product ID: " . $this->product->id);
                        }
                    }
                }

                // Delete variants associated with this product that are not in $variantIdsToKeep
                // This handles variants removed from the form by the user or those with empty names
                if (!$isNewProduct) { // Only do this for existing products
                    $this->product->variants()->whereNotIn('id', $variantIdsToKeep)->delete();
                    // Log::info("Deleted variants not in: " . implode(',', $variantIdsToKeep));
                }

                // If no variants are left and it's an old product, consider what to do.
                // Or if it's a new product and variants array was empty but SKU exists, create default.
                if (empty($variantIdsToKeep) && !empty($this->product->sku)) {
                    if ($this->product->variants()->count() == 0) { // Double check no variants exist
                        $this->product->variants()->create([
                            'variant_name' => 'Default',
                            'barcode' => $this->product->sku,
                            'stock_quantity' => 0,
                        ]);
                        // Log::info("Created default variant for product ID: " . $this->product->id);
                    }
                }
            }); // End DB Transaction

            session()->flash('message', 'Product ' . ($this->product->wasRecentlyCreated && !$this->productId ? 'created' : 'updated') . ' successfully.');
            return redirect()->route('products.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are automatically handled by Livewire and displayed
            // No need to re-throw or catch specifically unless you want to log them
            // Log::error('Validation failed during product save: ', $e->errors());
            throw $e; // Re-throw to let Livewire handle it
        } catch (\Exception $e) {
            Log::error('Error saving product: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'Could not save product. An unexpected error occurred. ' . $e->getMessage());
            // Optionally, do not redirect, so user can see the error and try again.
            // Or redirect back with error: return redirect()->back()->withInput();
        }
    }

    public function render()
    {
        // Determine title based on whether product exists and has an ID
        $pageTitle = ($this->product && $this->product->exists && $this->product->id) ? 'Edit Product' : 'Create New Product';
        return view('livewire.product.product-form')
            ->layoutData(['title' => $pageTitle]);
    }
}
