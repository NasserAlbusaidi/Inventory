<div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8"> {{-- Max width for a form page is usually less than full --}}

        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                {{ $productId ? 'Edit Product' : 'Create New Product' }}
            </h1>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 dark:bg-green-800 border-l-4 border-green-400 dark:border-green-600 p-4 shadow-md rounded-md" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400 dark:text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 dark:text-green-200">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-800 border-l-4 border-red-400 dark:border-red-600 p-4 shadow-md rounded-md" role="alert">
                 <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400 dark:text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="saveProduct" class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-xl space-y-8">
            {{-- Product Details Section --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-6 border-b dark:border-gray-700 pb-3">Product Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                        <input type="text" id="sku" wire:model.lazy="sku" {{-- Changed to .lazy for better UX on text inputs --}}
                               class="form-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm"
                               placeholder="Unique Stock Keeping Unit">
                        @error('sku') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name</label>
                        <input type="text" id="name" wire:model.lazy="name"
                               class="form-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm"
                               placeholder="e.g., Rose Oud Perfume">
                        @error('name') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea id="description" wire:model.lazy="description" rows="4"
                                  class="form-textarea block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm"
                                  placeholder="Detailed description of the perfume, notes, occasion, etc."></textarea>
                        @error('description') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category/Collection</label>
                        <select id="category_id" wire:model.defer="category_id"
                                class="form-select block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm">
                            <option value="">Select Category</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="imageUrl" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Image URL (Optional)</label>
                        <input type="url" id="imageUrl" wire:model.lazy="imageUrl"
                               class="form-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm"
                               placeholder="https://example.com/perfume.jpg">
                        @error('imageUrl') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            {{-- Product Variants Section --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-6 border-b dark:border-gray-700 pb-3">Product Variants</h2>
                <div class="space-y-6">
                    @foreach ($variants as $index => $variant)
                        <div wire:key="variant-{{ $index }}" class="bg-gray-50 dark:bg-gray-700/50 p-4 sm:p-6 rounded-lg border border-gray-200 dark:border-gray-600/50 shadow-sm">
                            <input type="hidden" wire:model="variants.{{ $index }}.id">
                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-x-6 gap-y-4 items-end">
                                <div class="lg:col-span-2">
                                    <label for="variant-name-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Variant Name / Size</label>
                                    <input type="text" id="variant-name-{{ $index }}" wire:model.defer="variants.{{ $index }}.variant_name"
                                           class="form-input block w-full rounded-md border-gray-300 dark:border-gray-500 dark:bg-gray-600 dark:text-gray-200 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm"
                                           placeholder="e.g., 50ml EDP">
                                    @error('variants.'.$index.'.variant_name') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="cost-price-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cost Price (OMR)</label>
                                    <input type="number" step="0.001" id="cost-price-{{ $index }}" wire:model.defer="variants.{{ $index }}.cost_price"
                                           class="form-input block w-full rounded-md border-gray-300 dark:border-gray-500 dark:bg-gray-600 dark:text-gray-200 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm">
                                    @error('variants.'.$index.'.cost_price') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="selling-price-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selling Price (OMR)</label>
                                    <input type="number" step="0.001" id="selling-price-{{ $index }}" wire:model.defer="variants.{{ $index }}.selling_price"
                                           class="form-input block w-full rounded-md border-gray-300 dark:border-gray-500 dark:bg-gray-600 dark:text-gray-200 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm">
                                    @error('variants.'.$index.'.selling_price') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex items-center space-x-2 {{ count($variants) > 1 ? 'pt-7' : 'md:col-span-1 pt-7' }}"> {{-- Adjust alignment for remove button --}}
                                     <div class="flex-grow">
                                        <label for="barcode-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barcode</label>
                                        <input type="text" id="barcode-{{ $index }}" wire:model.defer="variants.{{ $index }}.barcode"
                                            class="form-input block w-full rounded-md border-gray-300 dark:border-gray-500 dark:bg-gray-600 dark:text-gray-200 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm"
                                            placeholder="Optional">
                                        @error('variants.'.$index.'.barcode') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    @if (count($variants) > 1)
                                        <button type="button" wire:click="removeVariant({{ $index }})" title="Remove Variant"
                                                class="inline-flex items-center justify-center p-2 border border-transparent rounded-md text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-700/50 hover:bg-red-200 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                            {{-- Heroicon: trash --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56 0c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    <button type="button" wire:click="addVariant"
                            class="inline-flex items-center px-4 py-2 bg-indigo-500 dark:bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-600 dark:hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{-- Heroicon: plus --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2 -ml-1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Add Variant
                    </button>
                </div>
            </section>

            {{-- Form Actions --}}
            <div class="flex justify-end items-center pt-6 border-t dark:border-gray-700">
                <a href="{{ route('products.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium rounded-md px-4 py-2 mr-4 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit"
                        wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed"
                        class="inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-500 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-50 transition ease-in-out duration-150">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="save">{{ $productId ? 'Update Product' : 'Save Product' }}</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>
