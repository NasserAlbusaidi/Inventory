<div class="py-8">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">

        {{-- =================================================== --}}
        {{-- Header Section --}}
        {{-- =================================================== --}}
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $salesOrderInstance && $salesOrderInstance->exists ? 'Edit Sales Order' : 'Create Sales Order' }}
                </h1>
                @if ($salesOrderInstance && $salesOrderInstance->exists)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 font-mono">
                        {{ $salesOrderInstance->order_number }}
                    </p>
                @endif
            </div>
            <a href="{{ route('sales-orders.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back to List
            </a>
        </div>

        {{-- =================================================== --}}
        {{-- Flash Messages --}}
        {{-- =================================================== --}}
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 dark:bg-green-800/50 border-l-4 border-green-400 dark:border-green-600 p-4 shadow-md rounded-md"
                role="alert">
                <div class="flex">
                    <div class="flex-shrink-0"><svg class="h-5 w-5 text-green-400 dark:text-green-500"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg></div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 dark:text-green-200">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if (session()->has('stock_update_message'))
            <div class="mb-6 bg-blue-50 dark:bg-blue-800/50 border-l-4 border-blue-400 dark:border-blue-600 p-4 shadow-md rounded-md"
                role="alert">
                <div class="flex">
                    <div class="flex-shrink-0"><svg class="h-5 w-5 text-blue-400 dark:text-blue-500"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg></div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-200">{{ session('stock_update_message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- =================================================== --}}
        {{-- Main Form Card --}}
        {{-- =================================================== --}}
        <form wire:submit.prevent="saveSalesOrder">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl">
                <div class="p-6 sm:p-8">

                    {{-- Section 1: Order Details --}}
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3">Order Details</h2>
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                            {{-- Order Date --}}
                            <div>
                                <label for="order_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Date <span class="text-red-500">*</span></label>
                                <input type="date" id="order_date" wire:model.defer="order_date" class="form-input mt-1 block w-full rounded-lg">
                                @error('order_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Sales Channel --}}
                            <div>
                                <label for="sales_channel_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sales Channel <span class="text-red-500">*</span></label>
                                <select id="sales_channel_id" wire:model="sales_channel_id" class="form-select mt-1 block w-full rounded-lg">
                                    <option value="">Select Channel...</option>
                                    @foreach ($allSalesChannels as $channel)
                                        <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                    @endforeach
                                </select>
                                @error('sales_channel_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Fulfillment Location --}}
                            <div>
                                <label for="location_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fulfillment Location <span class="text-red-500">*</span></label>
                                <select id="location_id" wire:model.live="location_id" class="form-select mt-1 block w-full rounded-lg">
                                    <option value="">Select Location...</option>
                                    @foreach ($allLocations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                @error('location_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Status (MOVED HERE) --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                                <select id="status" wire:model.defer="status" class="form-select mt-1 block w-full rounded-lg">
                                    @foreach ($soStatuses as $stat)
                                        <option value="{{ $stat }}">{{ ucfirst(str_replace('_', ' ', $stat)) }}</option>
                                    @endforeach
                                </select>
                                @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 1.5: Customer Details --}}
                    <div class="mb-8">
                         <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3">Customer Details (Optional)</h2>
                         <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {{-- Customer Name --}}
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer Name</label>
                                <input type="text" id="customer_name" wire:model.defer="customer_name" class="form-input mt-1 block w-full rounded-lg" placeholder="John Doe">
                                @error('customer_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            {{-- Customer Email --}}
                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer Email</label>
                                <input type="email" id="customer_email" wire:model.defer="customer_email" class="form-input mt-1 block w-full rounded-lg" placeholder="john.doe@example.com">
                                @error('customer_email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            {{-- Customer Phone --}}
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer Phone</label>
                                <input type="tel" id="customer_phone" wire:model.defer="customer_phone" class="form-input mt-1 block w-full rounded-lg">
                                @error('customer_phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Order Items --}}
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3">Order Items</h2>
                        @error('items')<div class="mt-2 text-red-500 text-sm">{{ $message }}</div>@enderror
                        @if (!$location_id)
                            <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-800/50 border-l-4 border-yellow-400 text-yellow-700 dark:text-yellow-200">
                                <p>Please select a fulfillment location to add items.</p>
                            </div>
                        @endif

                        <div class="mt-4 space-y-4">
                            @foreach ($items as $index => $item)
                                <div wire:key="so-item-{{ $index }}" class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700 grid grid-cols-12 gap-x-4 gap-y-2 items-start">
                                    {{-- Item Select --}}
                                    <div class="col-span-12 md:col-span-5">
                                        <label for="items.{{ $index }}.selected_item_key" class="block text-xs font-medium text-gray-700 dark:text-gray-400">Product / Variant</label>
                                        <select wire:model.live="items.{{ $index }}.selected_item_key" id="items.{{ $index }}.selected_item_key" class="form-select mt-1 block w-full text-sm rounded-lg" {{ !$location_id ? 'disabled' : '' }}>
                                            <option value="">Select Item...</option>
                                            @foreach ($allSellableItems as $sellable)
                                                <option value="{{ $sellable['key'] }}">{{ $sellable['display_name'] }} (Stock: {{ $sellable['stock'] }})</option>
                                            @endforeach
                                        </select>
                                        @error('items.' . $index . '.selected_item_key')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    {{-- Quantity --}}
                                    <div class="col-span-6 md:col-span-2">
                                        <label for="items.{{ $index }}.quantity" class="block text-xs font-medium text-gray-700 dark:text-gray-400">Quantity</label>
                                        <input type="number" wire:model.live.debounce.300ms="items.{{ $index }}.quantity" id="items.{{ $index }}.quantity" min="1" max="{{ $item['available_stock'] > 0 ? $item['available_stock'] : 1 }}" {{ empty($item['selected_item_key']) ? 'disabled' : '' }} class="form-input mt-1 block w-full text-sm rounded-lg">
                                        @error('items.' . $index . '.quantity')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    {{-- Price --}}
                                    <div class="col-span-6 md:col-span-2">
                                        <label for="items.{{ $index }}.price_per_unit" class="block text-xs font-medium text-gray-700 dark:text-gray-400">Price/Unit (OMR)</label>
                                        <input type="number" step="0.01" wire:model.live.debounce.300ms="items.{{ $index }}.price_per_unit" id="items.{{ $index }}.price_per_unit" min="0" class="form-input mt-1 block w-full text-sm rounded-lg">
                                        @error('items.' . $index . '.price_per_unit')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    {{-- Subtotal --}}
                                    <div class="col-span-10 md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-400">Subtotal</label>
                                        <div class="mt-1 px-3 py-2 block w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/50 shadow-sm text-sm text-gray-700 dark:text-gray-300">
                                            {{ number_format((int) ($item['quantity'] ?? 0) * (float) ($item['price_per_unit'] ?? 0), 2) }}
                                        </div>
                                    </div>
                                    {{-- Remove Button --}}
                                    <div class="col-span-2 md:col-span-1 flex items-center justify-end pt-5">
                                        <button type="button" wire:click="removeItem({{ $index }})" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 p-2 rounded-full hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <button type="button" wire:click="addItem" {{ !$location_id ? 'disabled' : '' }}
                                class="inline-flex items-center px-4 py-2 border border-dashed border-gray-400 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                Add Item
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Form Footer --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 rounded-b-xl border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="flex items-baseline">
                            <span class="text-base font-medium text-gray-600 dark:text-gray-400">Total Amount:</span>
                            <span class="ml-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400 font-mono">OMR {{ number_format($total_amount, 2) }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-50 transition ease-in-out duration-150">
                                <svg wire:loading wire:target="saveSalesOrder" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="saveSalesOrder">{{ $salesOrderInstance && $salesOrderInstance->exists ? 'Update Order' : 'Create Order' }}</span>
                                <span wire:loading wire:target="saveSalesOrder">Saving...</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
