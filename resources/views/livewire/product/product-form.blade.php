{{-- This top-level div is now an Alpine.js component to manage the scanner --}}
<div class="py-8" x-data="{
    isScanning: false,
    variantIndex: null,
    html5QrCode: null,
    startScan(index = null) {
        this.isScanning = true;
        this.variantIndex = index;

        this.html5QrCode = new Html5Qrcode('reader');
        const config = { fps: 10, qrbox: { width: 250, height: 150 } };

        const onScanSuccess = (decodedText, decodedResult) => {
            // Use Livewire's JS API to update the correct property
            if (this.variantIndex !== null) {
                // We're setting the barcode for a specific variant
                @this.set(`variants.${this.variantIndex}.barcode`, decodedText, true); // true for 'lazy' update
            } else {
                // We're setting the barcode for the main product
                @this.set('barcode', decodedText, true); // true for 'lazy' update
            }
            this.stopScan();
            // You can add a success notification here if you like
        };

        this.html5QrCode.start({ facingMode: 'environment' }, config, onScanSuccess)
            .catch(err => {
                console.error('Unable to start scanning.', err);
                alert('Error: Could not start scanner. Please check camera permissions.');
                this.stopScan();
            });
    },
    stopScan() {
        if (this.html5QrCode && this.isScanning) {
            this.html5QrCode.stop().then(() => {
                this.isScanning = false;
                this.html5QrCode.clear();
            }).catch(err => console.error('Failed to stop scanner.', err));
        }
        this.isScanning = false;
    }
}">
    {{-- On mobile, use less horizontal padding --}}
    <div class="max-w-4xl mx-auto px-2 sm:px-6 lg:px-8">
        {{-- Page Header and Flash Messages (Unchanged) --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                {{ $product->exists ? 'Edit Product' : 'Create New Product' }}
            </h1>
        </div>
        @if (session()->has('message'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 dark:bg-green-800/20 dark:border-green-600 dark:text-green-300 px-4 py-3 rounded-lg relative" role="alert">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
             <div class="mb-6 bg-red-100 border border-red-400 text-red-700 dark:bg-red-800/20 dark:border-red-600 dark:text-red-300 px-4 py-3 rounded-lg relative" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="bg-white dark:bg-gray-800 p-4 sm:p-6 md:p-8 rounded-lg shadow-md">
            {{-- Product Details Section (Unchanged) --}}
            <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded-md mb-6">
                <legend class="text-lg font-medium text-gray-700 dark:text-gray-300 px-2">Product Details</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    {{-- All fields here are unchanged... --}}
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                        <input type="text" id="sku" wire:model.lazy="sku" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Unique Stock Keeping Unit">
                        @error('sku') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name</label>
                        <input type="text" id="name" wire:model.lazy="name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="e.g., Rose Oud Perfume">
                        @error('name') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea id="description" wire:model.lazy="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Detailed description of the perfume, notes, etc."></textarea>
                        @error('description') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category/Collection</label>
                        <select id="category_id" wire:model.defer="category_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-11">
                            <option value="">Select Category</option>
                            @foreach ($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="imageUrl" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Image URL (Optional)</label>
                        <input type="url" id="imageUrl" wire:model.lazy="imageUrl" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="https://example.com/perfume.jpg">
                        @error('imageUrl') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </fieldset>

            {{-- Variants Toggle (Unchanged) --}}
            <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-md border border-gray-200 dark:border-gray-700 mb-6">
                <label for="has_variants" class="flex items-center cursor-pointer">
                    <input id="has_variants" type="checkbox" wire:model.live="has_variants" class="form-checkbox h-5 w-5 text-indigo-600 rounded-md focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                    <span class="ml-3 text-sm font-medium text-gray-800 dark:text-gray-200">This product has multiple variants</span>
                </label>
            </div>

            @if ($has_variants)
                {{-- SECTION FOR PRODUCTS WITH VARIANTS --}}
                <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded-md">
                    <legend class="text-lg font-medium text-gray-700 dark:text-gray-300 px-2">Product Variants</legend>
                    <div class="space-y-4 mt-4">
                        @foreach ($variants as $index => $variant)
                            <div wire:key="variant-{{ $index }}" class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-md border border-gray-200 dark:border-gray-700">
                                <input type="hidden" wire:model="variants.{{ $index }}.id">

                                {{-- DESKTOP (GRID) VIEW for Variants - NO SCAN BUTTON HERE --}}
                                <div class="hidden lg:grid grid-cols-6 gap-x-6 gap-y-4 items-end">
                                    {{-- Unchanged fields --}}
                                    <div class="col-span-2">
                                        <label for="variant-name-{{ $index }}" class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Variant Name / Size *</label>
                                        <input type="text" id="variant-name-{{ $index }}" wire:model.lazy="variants.{{ $index }}.variant_name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="e.g., 50ml EDP">
                                        @error('variants.'.$index.'.variant_name') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="variant-cost-price-{{ $index }}" class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Cost (OMR) *</label>
                                        <input type="number" step="0.001" id="variant-cost-price-{{ $index }}" wire:model.lazy="variants.{{ $index }}.cost_price" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="0.00">
                                        @error('variants.'.$index.'.cost_price') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="variant-selling-price-{{ $index }}" class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Price (OMR) *</label>
                                        <input type="number" step="0.001" id="variant-selling-price-{{ $index }}" wire:model.lazy="variants.{{ $index }}.selling_price" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="0.00">
                                        @error('variants.'.$index.'.selling_price') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    {{-- Barcode input for desktop view - no button --}}
                                    <div class="flex items-end space-x-2 col-span-2">
                                        <div class="flex-grow">
                                            <label for="variant-barcode-{{ $index }}" class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Barcode</label>
                                            <input type="text" id="variant-barcode-{{ $index }}" wire:model.lazy="variants.{{ $index }}.barcode" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Optional">
                                            @error('variants.'.$index.'.barcode') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        @if (count($variants) > 1)
                                            <button type="button" wire:click="removeVariant({{ $index }})" title="Remove Variant" class="p-2 text-red-500 bg-red-100 dark:bg-red-800/50 hover:bg-red-200 dark:hover:bg-red-700 rounded-md transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg></button>
                                        @endif
                                    </div>
                                </div>

                                {{-- MOBILE (CARD) VIEW for Variants - SCAN BUTTON ADDED HERE --}}
                                <div class="lg:hidden space-y-4">
                                    {{-- ... mobile variant name and price fields are unchanged ... --}}
                                    <div class="flex justify-between items-center">
                                        <p class="font-semibold text-gray-800 dark:text-gray-200">Variant #{{ $index + 1 }}</p>
                                        @if (count($variants) > 1)
                                            <button type="button" wire:click="removeVariant({{ $index }})" title="Remove Variant" class="p-1 -m-1 text-red-500"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg></button>
                                        @endif
                                    </div>
                                    <div>
                                        <label for="mobile-variant-name-{{ $index }}" class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Variant Name / Size *</label>
                                        <input type="text" id="mobile-variant-name-{{ $index }}" wire:model.lazy="variants.{{ $index }}.variant_name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="e.g., 50ml EDP">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="mobile-variant-cost-price-{{ $index }}" class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Cost (OMR) *</label>
                                            <input type="number" step="0.001" id="mobile-variant-cost-price-{{ $index }}" wire:model.lazy="variants.{{ $index }}.cost_price" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="0.00">
                                        </div>
                                        <div>
                                            <label for="mobile-variant-selling-price-{{ $index }}" class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Price (OMR) *</label>
                                            <input type="number" step="0.001" id="mobile-variant-selling-price-{{ $index }}" wire:model.lazy="variants.{{ $index }}.selling_price" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="0.00">
                                        </div>
                                    </div>
                                    {{-- NEW: Barcode input group with scan button --}}
                                    <div>
                                        <label for="mobile-variant-barcode-{{ $index }}" class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Barcode</label>
                                        <div class="flex items-center">
                                            <input type="text" id="mobile-variant-barcode-{{ $index }}" wire:model.lazy="variants.{{ $index }}.barcode" class="flex-grow mt-1 block w-full rounded-l-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Optional">
                                            <button type="button" @click="startScan({{ $index }})" class="mt-1 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" style="height: 42px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 4.5h2.5v15H3zM7.5 4.5h1v15h-1zM10.5 4.5h1v15h-1zM13.5 4.5h1v15h-1zM16.5 4.5h4v15h-4z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Add Variant Button (Unchanged) --}}
                    <div class="mt-6">
                        <button type="button" wire:click="addVariant" class="inline-flex items-center px-3 py-2 border border-dashed border-gray-400 dark:border-gray-500 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700/20 hover:bg-gray-50 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                            Add Variant
                        </button>
                    </div>
                </fieldset>
            @else
                {{-- SECTION FOR PRODUCTS WITHOUT VARIANTS --}}
                <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded-md">
                    <legend class="text-lg font-medium text-gray-700 dark:text-gray-300 px-2">Pricing & Inventory</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                        {{-- Cost and Selling Price (Unchanged) --}}
                        <div>
                            <label for="cost_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cost Price (OMR) *</label>
                            <input type="number" step="0.001" id="cost_price" wire:model.lazy="cost_price" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="0.00">
                            @error('cost_price') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="selling_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selling Price (OMR) *</label>
                            <input type="number" step="0.001" id="selling_price" wire:model.lazy="selling_price" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="0.00">
                            @error('selling_price') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        {{-- NEW: Barcode input group with mobile-only scan button --}}
                        <div>
                            <label for="barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barcode (optional)</label>
                            <div class="flex items-center">
                                <input type="text" id="barcode" wire:model.lazy="barcode" class="flex-grow mt-1 block w-full rounded-l-md md:rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" placeholder="Optional">
                                {{-- This button is hidden on medium screens and up --}}
                                <button type="button" @click="startScan(null)" class="md:hidden mt-1 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" style="height: 42px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 4.5h2.5v15H3zM7.5 4.5h1v15h-1zM10.5 4.5h1v15h-1zM13.5 4.5h1v15h-1zM16.5 4.5h4v15h-4z"/></svg>
                                </button>
                            </div>
                            @error('barcode') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </fieldset>
            @endif

            {{-- Form Actions (Unchanged) --}}
            <div class="mt-8 flex flex-col sm:flex-row-reverse sm:justify-start gap-3">
                 <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>{{ $product->exists ? 'Update Product' : 'Save Product' }}</span>
                </button>
                 <a href="{{ route('products.index') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- NEW: BARCODE SCANNER MODAL / OVERLAY --}}
    <div x-show="isScanning" x-transition.opacity class="fixed inset-0 bg-gray-900/90 flex flex-col items-center justify-center z-50 p-4">
        <div class="w-full max-w-lg mx-auto">
            {{-- wire:ignore is ESSENTIAL here to prevent Livewire from breaking the scanner library --}}
            <div id="reader" class="bg-gray-900 rounded-lg overflow-hidden shadow-2xl" wire:ignore></div>
        </div>
        <button @click="stopScan()" class="mt-6 px-8 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 shadow-lg">
            Cancel
        </button>
    </div>
</div>
