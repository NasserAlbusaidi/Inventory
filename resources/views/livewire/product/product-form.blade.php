<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800 rounded-lg p-2">
        {{ $productId ? 'Edit Product' : 'Create New Product' }}
    </h1>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="save" class="bg-white p-8 rounded-lg shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                <input type="text" id="sku" wire:model.defer="sku"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                       placeholder="Unique Stock Keeping Unit">
                @error('sku') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                <input type="text" id="name" wire:model.defer="name"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                       placeholder="e.g., Rose Oud Perfume">
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" wire:model.defer="description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                          placeholder="Detailed description of the perfume."></textarea>
                @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category/Collection</label>
                <select id="category_id" wire:model.defer="category_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    <option value="">Select Category</option>
                    @foreach($allCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="imageUrl" class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                <input type="url" id="imageUrl" wire:model.defer="imageUrl"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                       placeholder="https://example.com/perfume.jpg">
                @error('imageUrl') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Product Variants</h2>
        @foreach ($variants as $index => $variant)
            <div wire:key="variant-{{ $index }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end bg-gray-50 p-4 rounded-md mb-4 border border-gray-200">
                <input type="hidden" wire:model="variants.{{ $index }}.id">
                <div class="col-span-1 md:col-span-2">
                    <label for="variant-name-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">Variant Name</label>
                    <input type="text" id="variant-name-{{ $index }}" wire:model.defer="variants.{{ $index }}.variant_name"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                           placeholder="e.g., 50ml EDP">
                    @error('variants.'.$index.'.variant_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="cost-price-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">Cost Price (OMR)</label>
                    <input type="number" step="0.001" id="cost-price-{{ $index }}" wire:model.defer="variants.{{ $index }}.cost_price"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    @error('variants.'.$index.'.cost_price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="selling-price-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">Selling Price (OMR)</label>
                    <input type="number" step="0.001" id="selling-price-{{ $index }}" wire:model.defer="variants.{{ $index }}.selling_price"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    @error('variants.'.$index.'.selling_price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-end space-x-2">
                    <div class="flex-grow">
                        <label for="barcode-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                        <input type="text" id="barcode-{{ $index }}" wire:model.defer="variants.{{ $index }}.barcode"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                               placeholder="Optional Barcode">
                        @error('variants.'.$index.'.barcode') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    @if (count($variants) > 1)
                        <button type="button" wire:click="removeVariant({{ $index }})"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 h-10 mb-0">
                            Remove
                        </button>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="flex justify-start mb-6">
            <button type="button" wire:click="addVariant"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                Add Variant
            </button>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                Save Product
            </button>
        </div>
    </form>
</div>
