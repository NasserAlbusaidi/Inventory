{{-- In resources/views/livewire/product/adjust-stock-modal.blade.php --}}
<div
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75"
    x-data="{ show: @entangle('showAdjustStockModal').live }"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    {{-- Modal Content --}}
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg m-4"
        @click.away="show = false"
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <div class="p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">Adjust Stock for: {{ $product->name }}</h2>

            <form wire:submit.prevent="adjustStock">
                {{-- This is the same form content from the previous implementation --}}
                <div class="space-y-4">
                    @if ($product->has_variants)
                        <div>
                            <label for="selected_variant_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Variant</label>
                            <select id="selected_variant_id" wire:model.live="selected_variant_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-gray-200">
                                <option value="">-- Choose a variant --</option>
                                @foreach($variants as $variant)
                                    <option value="{{ $variant->id }}">{{ $variant->name }} (SKU: {{ $variant->sku }})</option>
                                @endforeach
                            </select>
                            @error('selected_variant_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    @if($selected_variant_id)
                        <div>
                            <label for="location_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                            <select id="location_id" wire:model="location_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-gray-200">
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="adjustment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adjustment Type</label>
                            <select id="adjustment_type" wire:model="adjustment_type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-gray-200">
                                <option value="addition">Addition (+)</option>
                                <option value="deduction">Deduction (-)</option>
                            </select>
                        </div>
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                            <input type="number" id="quantity" wire:model.lazy="quantity" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-gray-200">
                            @error('quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes (Reason)</label>
                            <input type="text" id="notes" wire:model.lazy="notes" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-gray-200" placeholder="e.g., Manufactured in-house, stock correction">
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded" wire:click="$parent.handleStockUpdated">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @if(!$selected_variant_id) disabled @endif>
                        Adjust Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
