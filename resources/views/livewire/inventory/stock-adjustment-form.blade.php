<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Manual Stock Adjustment</h1>

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

    <form wire:submit.prevent="saveAdjustment" class="bg-white p-8 rounded-lg shadow-md max-w-2xl mx-auto">
        {{-- Product Variant Selection --}}
        <div class="mb-4">
            <label for="product_variant_id" class="block text-sm font-medium text-gray-700 mb-1">Product Variant</label>
            <select id="product_variant_id" wire:model.live="product_variant_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                <option value="">Select Product Variant...</option>
                @foreach($product_variants_list as $variant)
                    <option value="{{ $variant->id }}">{{ $variant->full_name }}</option>
                @endforeach
            </select>
            @error('product_variant_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        @if($product_variant_id)
            <div class="mb-4 p-3 bg-gray-50 rounded-md">
                <p class="text-sm text-gray-700">Selected: <span class="font-semibold">{{ $selected_variant_name }}</span></p>
                <p class="text-sm text-gray-700">Current Stock: <span class="font-semibold">{{ $current_stock }}</span> units</p>
            </div>
        @endif

        {{-- New Stock Quantity --}}
        <div class="mb-4">
            <label for="new_stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">New Stock Quantity</label>
            <input type="number" id="new_stock_quantity" wire:model.defer="new_stock_quantity"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                   placeholder="Enter the new total stock">
            @error('new_stock_quantity') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Location for Adjustment --}}
        <div class="mb-4">
            <label for="selected_location_id" class="block text-sm font-medium text-gray-700 mb-1">Adjustment Location</label>
            <select id="selected_location_id" wire:model.defer="selected_location_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                <option value="">Select Location...</option>
                @foreach($locations_list as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
            @error('selected_location_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Reason --}}
        <div class="mb-6">
            <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Adjustment</label>
            <textarea id="reason" wire:model.defer="reason" rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                      placeholder="e.g., Stocktake correction, Damaged goods write-off, Found items"></textarea>
            @error('reason') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('dashboard') }}" {{-- Or perhaps product list --}}
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                Adjust Stock
            </button>
        </div>
    </form>
</div>
