<div class="py-8">
    {{-- On mobile, use less horizontal padding --}}
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                @if ($purchaseOrderInstance && $purchaseOrderInstance->exists)
                    Edit Purchase Order <span
                        class="text-2xl text-indigo-500 ml-2">#{{ $purchaseOrderInstance->order_number }}</span>
                @else
                    Create New Purchase Order
                @endif
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

        <form wire:submit.prevent="savePurchaseOrder" class="space-y-8">
            {{-- Order Details Section --}}
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 md:p-8 rounded-lg shadow-md">
                <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded-md">
                    <legend class="text-lg font-medium text-gray-700 dark:text-gray-300 px-2">Order Details</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                        <div>
                            <label for="supplier_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier</label>
                            <select id="supplier_id" wire:model="supplier_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-11">
                                <option value="">Select Supplier</option>
                                @foreach ($allSuppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="order_number"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Order
                                Number</label>
                            <input type="text" id="order_number" wire:model.defer="order_number"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                            @error('order_number')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="order_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Order
                                Date</label>
                            <input type="date" id="order_date" wire:model.defer="order_date"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                            @error('order_date')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="status"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select id="status" wire:model.defer="status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-11">
                                @foreach ($poStatuses as $stat)
                                    <option value="{{ $stat }}">{{ ucfirst(str_replace('_', ' ', $stat)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="lg:col-span-2">
                            <label for="receiving_location_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Receiving
                                Location</label>
                            <select id="receiving_location_id" wire:model.defer="receiving_location_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-11">
                                <option value="">Select Location</option>
                                @foreach ($allLocations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                            @error('receiving_location_id')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </fieldset>
            </div>

            {{-- Order Items Section --}}
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 md:p-8 rounded-lg shadow-md">
                <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded-md">
                    <legend class="text-lg font-medium text-gray-700 dark:text-gray-300 px-2">Order Items</legend>
                    @error('items')
                        <div class="text-red-500 text-xs mt-1 mb-3 p-2">{{ $message }}</div>
                    @enderror

                    {{-- Search bar can be replaced with the simple dropdown if you prefer --}}
                    {{-- <livewire:product.product-search-bar wire:model="selectedProduct" /> --}}

                    <div class="space-y-4 mt-4">
                        @foreach ($items as $index => $item)
                            <div wire:key="item-{{ $index }}"
                                class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-md border border-gray-200 dark:border-gray-700">
                                <div class="space-y-4">
                                    <div class="flex justify-end">
                                        <button type="button" wire:click="removeItem({{ $index }})"
                                            class="text-red-500 hover:text-red-700 p-1 -m-1 rounded-full"><svg
                                                xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg></button>
                                    </div>
                                    <div class="w-full">
                                        <label for="items-{{ $index }}-select"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product
                                            / Variant</label>

                                        {{-- This `wire:ignore` is crucial. It tells Livewire to let Alpine and Tom Select manage this part of the DOM. --}}
                                        <div wire:ignore x-data="tomSelect({
                                            wireModel: 'items.{{ $index }}.selected_item_key',
                                            options: {{ json_encode($allPurchasableItems) }},
                                            initialValue: '{{ $item['selected_item_key'] }}'
                                        })" x-init="init()"
                                            @destroy.window="destroy()">

                                            {{-- Tom Select will target this element --}}
                                            <select x-ref="select" id="items-{{ $index }}-select"
                                                placeholder="Search for a product..."></select>
                                        </div>

                                        @error('items.' . $index . '.selected_item_key')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="items.{{ $index }}.quantity"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                                            <input type="number" wire:model.live="items.{{ $index }}.quantity"
                                                id="items.{{ $index }}.quantity" min="1"
                                                {{ empty($item['selected_item_key']) ? 'disabled' : '' }}
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 disabled:bg-gray-200 dark:disabled:bg-gray-800">
                                            @error('items.' . $index . '.quantity')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="items.{{ $index }}.cost_price_per_unit"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cost/Unit
                                                (OMR)
                                            </label>
                                            <input type="number" step="0.001"
                                                wire:model.live="items.{{ $index }}.cost_price_per_unit"
                                                id="items.{{ $index }}.cost_price_per_unit" min="0"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                                            @error('items.' . $index . '.cost_price_per_unit')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="border-t border-gray-200 dark:border-gray-700 mt-4 pt-3">
                                        <div class="flex justify-between items-center">
                                            <span
                                                class="text-sm font-medium text-gray-600 dark:text-gray-400">Subtotal</span>
                                            <span
                                                class="text-sm font-mono font-semibold text-gray-800 dark:text-gray-200">OMR
                                                {{ number_format((float) ($item['quantity'] ?? 0) * (float) ($item['cost_price_per_unit'] ?? 0), 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        <button type="button" wire:click="addItem"
                            class="inline-flex items-center px-3 py-2 border border-dashed border-gray-400 dark:border-gray-500 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700/20 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Add Item
                        </button>
                    </div>
                </fieldset>
            </div>

            {{-- Totals & Actions --}}
            <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="text-center sm:text-left">
                    <a href="{{ route('purchase-orders.index') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500">
                        Cancel
                    </a>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 text-center sm:text-right">
                    <div class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                        Total: <span class="text-indigo-500 font-mono">OMR
                            {{ number_format($total_amount, 2) }}</span>
                    </div>
                    <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                        <svg wire:loading wire:target="savePurchaseOrder"
                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>{{ $purchaseOrderInstance && $purchaseOrderInstance->exists ? 'Update PO' : 'Create PO' }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
