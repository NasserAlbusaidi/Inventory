<?php

namespace App\Livewire\Product;

use App\Models\Activity;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductForm extends Component
{
    public ?Product $product = null;
    public ?int $productId = null;

    // Common product fields
    public string $name = '';
    public ?string $description = null;
    public ?int $category_id = null;
    public string $sku = '';
    public ?string $imageUrl = null;


    // Flag to determine product type
    public bool $has_variants = false;

    // Fields for products WITHOUT variants
    public $cost_price = null;
    public $selling_price = null;
    public $barcode = '';
    public $initial_stock = 0; // RENAMED from stock_quantity
    public bool $track_inventory = true;

    // Fields for products WITH variants
    public array $variants = [];

    public Collection $allCategories;

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($this->product?->id)],
            'imageUrl' => 'nullable|url',
            'has_variants' => 'required|boolean',
        ];

        if ($this->has_variants) {
            $rules = array_merge($rules, [
                'variants' => 'required|array|min:1',
                'variants.*.variant_name' => 'required|string|max:255',
                'variants.*.cost_price' => 'required|numeric|min:0',
                'variants.*.selling_price' => 'required|numeric|min:0',
                'variants.*.initial_stock' => 'required|integer|min:0', // RENAMED
                'variants.*.id' => 'nullable|integer|exists:product_variants,id',
            ]);

            foreach ($this->variants as $index => $variant) {
                $variantIdToIgnore = $variant['id'] ?? null;
                if (!empty(trim($variant['barcode'] ?? ''))) {
                    $rules["variants.{$index}.barcode"] = ['nullable', 'string', 'max:255', Rule::unique('product_variants', 'barcode')->when($variantIdToIgnore, fn ($rule) => $rule->ignore($variantIdToIgnore))];
                } else {
                    $rules["variants.{$index}.barcode"] = ['nullable', 'string', 'max:255'];
                }
            }
        } else {
            $rules = array_merge($rules, [
                'cost_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'initial_stock' => 'required|integer|min:0', // RENAMED
                'track_inventory' => 'required|boolean',
                'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products', 'barcode')->ignore($this->product?->id)],
            ]);
        }
        return $rules;
    }

    public function mount($productId = null)
    {
        $this->allCategories = Category::orderBy('name')->get();

        if ($productId) {
            $this->product = Product::with('variants')->findOrFail($productId);
            $this->productId = $this->product->id;
            $this->fill($this->product);
            $this->initial_stock = $this->product->stock_quantity; // Map database column to form property

            if ($this->product->has_variants) {
                $this->variants = $this->product->variants->map(fn ($v) => [
                    'id' => $v->id,
                    'variant_name' => $v->variant_name,
                    'cost_price' => $v->cost_price,
                    'selling_price' => $v->selling_price,
                    'barcode' => $v->barcode,
                    'initial_stock' => $v->stock_quantity, // Map database column to form property
                ])->toArray();
            }
        } else {
            $this->product = new Product();
            $this->has_variants = false;
        }
    }

    public function updatedHasVariants(bool $value): void
    {
        if ($value && empty($this->variants)) {
            $this->initializeDefaultVariant();
        } else {
            $this->variants = [];
        }
    }

    private function initializeDefaultVariant(): void
    {
        $this->variants[] = ['id' => null, 'variant_name' => 'Default', 'cost_price' => null, 'selling_price' => null, 'barcode' => '', 'initial_stock' => 0];
    }

    public function addVariant(): void
    {
        $this->variants[] = ['id' => null, 'variant_name' => '', 'cost_price' => null, 'selling_price' => null, 'barcode' => '', 'initial_stock' => 0];
    }

    public function removeVariant(int $index): void
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function save()
    {
        $validatedData = $this->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                $productData = [
                    'name' => $validatedData['name'],
                    'description' => $validatedData['description'],
                    'category_id' => $validatedData['category_id'],
                    'sku' => $validatedData['sku'] ?? null,
                    'image_url' => $validatedData['imageUrl'] ?? null,
                    'has_variants' => $validatedData['has_variants'],
                ];

                if (!$this->has_variants) {
                    $productData = array_merge($productData, [
                        'cost_price' => $validatedData['cost_price'],
                        'selling_price' => $validatedData['selling_price'],
                        'track_inventory' => $validatedData['track_inventory'],
                        'barcode' => $validatedData['barcode'] ?? null,
                    ]);
                    // ONLY set initial stock on creation.
                    if (!$this->product->exists) {
                        $productData['stock_quantity'] = $validatedData['initial_stock'];
                    }
                    if ($this->product->exists) $this->product->variants()->delete();
                } else {
                    $productData = array_merge($productData, ['cost_price' => null, 'selling_price' => null, 'stock_quantity' => 0, 'track_inventory' => true, 'barcode' => null]);
                }

                $this->product->fill($productData)->save();

                if ($this->has_variants) {
                    $variantIdsToKeep = [];
                    foreach ($validatedData['variants'] as $variantData) {
                        if (empty(trim($variantData['variant_name']))) continue;

                        $payload = [
                            'variant_name' => $variantData['variant_name'],
                            'cost_price' => $variantData['cost_price'] ?? 0,
                            'selling_price' => $variantData['selling_price'] ?? 0,
                            'barcode' => empty(trim($variantData['barcode'])) ? null : trim($variantData['barcode']),
                        ];

                        // ONLY set initial stock for new variants.
                        if (empty($variantData['id'])) {
                            $payload['stock_quantity'] = $variantData['initial_stock'] ?? 0;
                        }

                        $variant = $this->product->variants()->updateOrCreate(['id' => $variantData['id'] ?? null], $payload);
                        $variantIdsToKeep[] = $variant->id;
                    }
                    if ($this->product->exists) $this->product->variants()->whereNotIn('id', $variantIdsToKeep)->delete();
                }
            });

            Activity::create([
                'type' => $this->product->exists ? 'product_updated' : 'product_created',
                'description' => sprintf(
                    '%s product: %s (ID: %d)',
                    $this->product->exists ? 'Updated' : 'Created',
                    $this->product->name,
                    $this->product->id
                ),
            ]);

            session()->flash('message', 'Product saved successfully.');
            return redirect()->route('products.index');
        } catch (\Exception $e) {
            Log::error('Error saving product: ' . $e->getMessage());
            session()->flash('error', 'Could not save product. An unexpected error occurred.');
        }
    }

    public function render()
    {
        return view('livewire.product.product-form')->layoutData(['title' => $this->product->exists ? 'Edit Product' : 'Create New Product']);
    }
}
