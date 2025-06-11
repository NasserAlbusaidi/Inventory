<div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                {{ $product->exists ? 'Edit Product' : 'Create New Product' }}
            </h1>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 dark:bg-green-800/20 dark:border-green-600 dark:text-green-300 px-4 py-3 rounded-lg relative"
                role="alert">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 dark:bg-red-800/20 dark:border-red-600 dark:text-red-300 px-4 py-3 rounded-lg relative"
                role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- Use wire:submit.prevent="save" to match the updated component method --}}
        <form wire:submit.prevent="save" class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-lg shadow-md">
            {{-- Product Details Section --}}
            <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded-md mb-6">
                <legend class="text-lg font-medium text-gray-700 dark:text-gray-300 px-2">Product Details</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="sku"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                        <input type="text" id="sku" wire:model.lazy="sku"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                            placeholder="Unique Stock Keeping Unit">
                        @error('sku')
                            <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name</label>
                        <input type="text" id="name" wire:model.lazy="name"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                            placeholder="e.g., Rose Oud Perfume">
                        @error('name')
                            <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea id="description" wire:model.lazy="description" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                            placeholder="Detailed description of the perfume, notes, occasion, etc."></textarea>
                        @error('description')
                            <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category/Collection</label>
                        <select id="category_id" wire:model.defer="category_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-11">
                            <option value="">Select Category</option>
                            @foreach ($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        {{-- The component was updated to handle 'imageUrl' --}}
                        <label for="imageUrl"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Image URL
                            (Optional)</label>
                        <input type="url" id="imageUrl" wire:model.lazy="imageUrl"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                            placeholder="https://example.com/perfume.jpg">
                        @error('imageUrl')
                            <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </fieldset>

            {{-- NEW: Variants Toggle --}}
            <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-md border border-gray-200 dark:border-gray-700 mb-6">
                <label for="has_variants" class="flex items-center cursor-pointer">
                    <input id="has_variants" type="checkbox" wire:model.live="has_variants"
                        class="form-checkbox h-5 w-5 text-indigo-600 rounded-md focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                    <span class="ml-3 text-sm font-medium text-gray-800 dark:text-gray-200">This product has multiple
                        variants (e.g., different sizes or colors)</span>
                </label>
            </div>


            {{-- Conditional Section for Products WITH Variants --}}
            @if ($has_variants)
                <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded-md">
                    <legend class="text-lg font-medium text-gray-700 dark:text-gray-300 px-2">Product Variants</legend>
                    <div class="space-y-4 mt-4">
                        @foreach ($variants as $index => $variant)
                            <div wire:key="variant-{{ $index }}"
                                class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-md border border-gray-200 dark:border-gray-700">
                                <input type="hidden" wire:model="variants.{{ $index }}.id">
                                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-x-6 gap-y-4 items-end">
                                    <div class="lg:col-span-2">
                                        <label for="variant-name-{{ $index }}"
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Variant
                                            Name / Size *</label>
                                        <input type="text" id="variant-name-{{ $index }}"
                                            wire:model.lazy="variants.{{ $index }}.variant_name"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                            placeholder="e.g., 50ml EDP">
                                        @error('variants.' . $index . '.variant_name')
                                            <span
                                                class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="variant-cost-price-{{ $index }}"
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Cost
                                            (OMR) *</label>
                                        <input type="number" step="0.001"
                                            id="variant-cost-price-{{ $index }}"
                                            wire:model.lazy="variants.{{ $index }}.cost_price"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                            placeholder="0.00">
                                        @error('variants.' . $index . '.cost_price')
                                            <span
                                                class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="variant-selling-price-{{ $index }}"
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Price
                                            (OMR) *</label>
                                        <input type="number" step="0.001"
                                            id="variant-selling-price-{{ $index }}"
                                            wire:model.lazy="variants.{{ $index }}.selling_price"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                            placeholder="0.00">
                                        @error('variants.' . $index . '.selling_price')
                                            <span
                                                class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="variant-stock-{{ $index }}"
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Initial
                                            Stock *</label>
                                        <input type="number" id="variant-stock-{{ $index }}"
                                            wire:model.lazy="variants.{{ $index }}.initial_stock"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                            placeholder="0">
                                        @error('variants.' . $index . '.initial_stock')
                                            <span
                                                class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                                        @enderror


                                    </div>
                                    <div class="flex items-end space-x-2">
                                        <div class="flex-grow">
                                            <label for="variant-barcode-{{ $index }}"
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Barcode</label>
                                            <input type="text" id="variant-barcode-{{ $index }}"
                                                wire:model.lazy="variants.{{ $index }}.barcode"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                                placeholder="Optional">
                                            @error('variants.' . $index . '.barcode')
                                                <span
                                                    class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        @if (count($variants) > 1)
                                            <button type="button" wire:click="removeVariant({{ $index }})"
                                                title="Remove Variant"
                                                class="p-2 text-red-500 bg-red-100 dark:bg-red-800/50 hover:bg-red-200 dark:hover:bg-red-700 rounded-md transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        <button type="button" wire:click="addVariant"
                            class="inline-flex items-center px-3 py-2 border border-dashed border-gray-400 dark:border-gray-500 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700/20 hover:bg-gray-50 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Add Variant
                        </button>
                    </div>
                </fieldset>
            @else
                {{-- NEW: Conditional Section for Products WITHOUT Variants --}}
                <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded-md">
                    <legend class="text-lg font-medium text-gray-700 dark:text-gray-300 px-2">Pricing & Inventory
                    </legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-4">
                        <div>
                            <label for="cost_price"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cost Price
                                (OMR) *</label>
                            <input type="number" step="0.001" id="cost_price" wire:model.lazy="cost_price"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                placeholder="0.00">
                            @error('cost_price')
                                <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="selling_price"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selling Price
                                (OMR) *</label>
                            <input type="number" step="0.001" id="selling_price" wire:model.lazy="selling_price"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                placeholder="0.00">
                            @error('selling_price')
                                <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="initial_stock"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">initial Stock
                                Quantity *</label>
                            <input type="number" id="initial_stock" wire:model.lazy="initial_stock"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                placeholder="0">
                            @error('initial_stock')
                                <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                            @enderror

                        </div>
                        <div>
                            <label for="barcode"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barcode</label>
                            <input type="text" id="barcode" wire:model.lazy="barcode"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                placeholder="Optional">
                            @error('barcode')
                                <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="md:col-span-4">
                            <label for="track_inventory" class="flex items-center mt-4">
                                <input id="track_inventory" type="checkbox" wire:model.live="track_inventory"
                                    class="form-checkbox h-4 w-4 text-indigo-600 rounded-md focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Track inventory for this
                                    product</span>
                            </label>
                        </div>
                    </div>
                </fieldset>
            @endif


            {{-- Form Actions --}}
            <div class="mt-8 flex justify-end items-center space-x-4">
                <a href="{{ route('products.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                    {{-- Updated wire:target to "save" --}}
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span>{{ $product->exists ? 'Update Product' : 'Save Product' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
