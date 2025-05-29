<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category; // Add this
use Livewire\Component;
// use Illuminate\Support\Str; // Not used

class ProductForm extends Component
{
    public $productId;
    public $sku;
    public $name;
    public $description;
    // public $category; // This will now be category_id
    public $category_id; // New property
    public $imageUrl;
    public $variants = [];
    public $allCategories; // To hold categories for the dropdown

    protected function rules() // Changed to a method to allow dynamic rules
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id', // Validate category_id
            'imageUrl' => 'nullable|url|max:255',
            'variants.*.variant_name' => 'required|string|max:255',
            'variants.*.cost_price' => 'required|numeric|min:0',
            'variants.*.selling_price' => 'required|numeric|min:0',
            // Barcode uniqueness rule needs adjustment for updates
            // 'variants.*.barcode' => 'nullable|string|max:255|unique:product_variants,barcode',
        ];

        if ($this->productId) {
            $rules['sku'] = 'required|string|max:255|unique:products,sku,' . $this->productId;
        } else {
            $rules['sku'] = 'required|string|max:255|unique:products,sku';
        }

        // Adjust barcode validation for existing variants
        foreach ($this->variants as $index => $variant) {
            $variantId = $variant['id'] ?? null;
            $rules['variants.'.$index.'.barcode'] = 'nullable|string|max:255|unique:product_variants,barcode,' . $variantId;
        }


        return $rules;
    }


    protected $messages = [
        'category_id.required' => 'Please select a category.',
        'category_id.exists' => 'The selected category is invalid.',
        'variants.*.variant_name.required' => 'Variant name is required.',
        'variants.*.cost_price.required' => 'Cost price is required for each variant.',
        'variants.*.cost_price.numeric' => 'Cost price must be a number.',
        'variants.*.cost_price.min' => 'Cost price cannot be negative.',
        'variants.*.selling_price.required' => 'Selling price is required for each variant.',
        'variants.*.selling_price.numeric' => 'Selling price must be a number.',
        'variants.*.selling_price.min' => 'Selling price cannot be negative.',
        'variants.*.barcode.unique' => 'This barcode is already taken by another variant.',
    ];

    public function mount($productId = null)
    {
        $this->allCategories = Category::orderBy('name')->get(); //

        if ($productId) {
            $this->productId = $productId;
            $product = Product::with('variants')->findOrFail($productId); // Eager load variants
            $this->sku = $product->sku;
            $this->name = $product->name;
            $this->description = $product->description;
            $this->category_id = $product->category_id; // Use category_id
            $this->imageUrl = $product->image_url;

            $this->variants = $product->variants->map(function ($variant) {
                return [
                    'id' => $variant->id, // Important for updating existing variants
                    'variant_name' => $variant->variant_name,
                    'cost_price' => $variant->cost_price,
                    'selling_price' => $variant->selling_price,
                    'barcode' => $variant->barcode,
                ];
            })->toArray();
        }

        if (empty($this->variants)) {
            $this->addVariant();
        }
    }

    public function addVariant()
    {
        $this->variants[] = [
            'id' => null, // For new variants
            'variant_name' => '',
            'cost_price' => 0,
            'selling_price' => 0,
            'barcode' => '',
        ];
    }

    public function removeVariant($index)
    {
        // If the variant has an ID, it means it's an existing one.
        // We might need to handle its deletion from the DB upon saving the main product.
        // For now, just removing from the array. Actual deletion will be handled in save().
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function save()
    {
        $this->validate();

        try {
            $productData = [
                'sku' => $this->sku,
                'name' => $this->name,
                'description' => $this->description,
                'category_id' => $this->category_id, // Save category_id
                'image_url' => $this->imageUrl,
            ];

            if ($this->productId) {
                $product = Product::findOrFail($this->productId);
                $product->update($productData);
                session()->flash('message', 'Product updated successfully!');
            } else {
                $product = Product::create($productData);
                $this->productId = $product->id; // Set productId for new products to handle variant saving correctly
                session()->flash('message', 'Product created successfully!');
            }

            // Save/Update variants
            $variantIdsToKeep = [];
            foreach ($this->variants as $variantData) {
                $variantId = $variantData['id'] ?? null;
                // Prepare data, removing 'id' if it's null or not for creation
                $dbVariantData = [
                    'variant_name' => $variantData['variant_name'],
                    'cost_price' => $variantData['cost_price'],
                    'selling_price' => $variantData['selling_price'],
                    'barcode' => $variantData['barcode'],
                ];

                if ($variantId) {
                    // Update existing variant
                    $variant = ProductVariant::find($variantId);
                    if ($variant && $variant->product_id == $product->id) { // Ensure variant belongs to this product
                        $variant->update($dbVariantData);
                        $variantIdsToKeep[] = $variant->id;
                    }
                } else {
                    // Create new variant
                    $newVariant = $product->variants()->create($dbVariantData);
                    $variantIdsToKeep[] = $newVariant->id;
                }
            }

            // Delete variants that were removed from the form (for existing products)
            if ($this->productId) {
                $product->variants()->whereNotIn('id', $variantIdsToKeep)->delete();
            }


            // Decide where to redirect. Maybe to a product list or product edit page.
            // For now, let's just reset the form if it was a new product or flash message
            if (!$this->productId && $product) { // If it was a creation, maybe redirect to edit
                return redirect()->route('dashboard'); // Or a specific product edit route
            }
            // If editing, we might stay on the page, or redirect to a list
            // $this->mount($this->productId); // To refresh data if staying on page

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are automatically handled by Livewire and displayed in the view
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.product-form')
               ->layout('layouts.app'); // Or components.layouts.app if you moved it
    }
}
