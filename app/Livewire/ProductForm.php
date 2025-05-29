<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;
use Illuminate\Support\Str;

class ProductForm extends Component
{
    public $productId;
    public $sku;
    public $name;
    public $description;
    public $category;
    public $imageUrl;
    public $variants = []; // Array to hold variant data

    protected $rules = [
        'sku' => 'required|string|max:255|unique:products,sku',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category' => 'nullable|string|max:255',
        'imageUrl' => 'nullable|url|max:255',
        'variants.*.variant_name' => 'required|string|max:255',
        'variants.*.cost_price' => 'required|numeric|min:0',
        'variants.*.selling_price' => 'required|numeric|min:0',
        'variants.*.barcode' => 'nullable|string|max:255|unique:product_variants,barcode',
    ];

    protected $messages = [
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
        if ($productId) {
            $this->productId = $productId;
            $product = Product::findOrFail($productId);
            $this->sku = $product->sku;
            $this->name = $product->name;
            $this->description = $product->description;
            $this->category = $product->category;
            $this->imageUrl = $product->image_url;

            // Load existing variants
            $this->variants = $product->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'variant_name' => $variant->variant_name,
                    'cost_price' => $variant->cost_price,
                    'selling_price' => $variant->selling_price,
                    'barcode' => $variant->barcode,
                ];
            })->toArray();
        }

        // Ensure at least one variant input field is present for new products
        if (empty($this->variants)) {
            $this->addVariant();
        }
    }

    public function addVariant()
    {
        $this->variants[] = [
            'variant_name' => '',
            'cost_price' => 0,
            'selling_price' => 0,
            'barcode' => '',
        ];
    }

    public function removeVariant($index)
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants); // Re-index array
    }

    public function save()
    {
        // Adjust validation rule for SKU uniqueness if updating an existing product
        if ($this->productId) {
            $this->rules['sku'] = 'required|string|max:255|unique:products,sku,' . $this->productId;
        }

        $this->validate();

        try {
            if ($this->productId) {
                $product = Product::findOrFail($this->productId);
                $product->update([
                    'sku' => $this->sku,
                    'name' => $this->name,
                    'description' => $this->description,
                    'category' => $this->category,
                    'image_url' => $this->imageUrl,
                ]);
                session()->flash('message', 'Product updated successfully!');
            } else {
                $product = Product::create([
                    'sku' => $this->sku,
                    'name' => $this->name,
                    'description' => $this->description,
                    'category' => $this->category,
                    'image_url' => $this->imageUrl,
                ]);
                session()->flash('message', 'Product created successfully!');
            }

            // Save/Update variants
            $existingVariantIds = [];
            foreach ($this->variants as $variantData) {
                if (isset($variantData['id']) && $variantData['id']) {
                    // Update existing variant
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant) {
                        $variant->update($variantData);
                        $existingVariantIds[] = $variant->id;
                    }
                } else {
                    // Create new variant
                    $product->variants()->create($variantData);
                }
            }

            // Delete variants that were removed from the form
            if ($this->productId) {
                $product->variants()->whereNotIn('id', $existingVariantIds)->delete();
            }

            // Redirect or clear form
            return redirect()->route('products.create'); // Or to a product list page
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.product-form');
    }
}
