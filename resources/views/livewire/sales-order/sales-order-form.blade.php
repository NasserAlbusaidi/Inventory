<div class="py-8">
    {{-- On mobile, use less horizontal padding --}}
    <div class="max-w-full mx-auto px-2 sm:px-6 lg:px-8">

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
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600">
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
            {{-- ... Your message flash code ... --}}
        @endif
        @if (session()->has('stock_update_message'))
            {{-- ... Your stock update flash code ... --}}
        @endif

        {{-- =================================================== --}}
        {{-- Main Form Card --}}
        {{-- =================================================== --}}
        <form wire:submit.prevent="saveSalesOrder">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl">
                <div class="p-4 sm:p-6 md:p-8">

                    {{-- Section 1: Order Details --}}
                    <div class="mb-8">
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3">
                            Order Details</h2>
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label for="order_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Date <span
                                        class="text-red-500">*</span></label>
                                <input type="date" id="order_date" wire:model.defer="order_date"
                                    class="form-input mt-1 block w-full rounded-lg">
                                @error('order_date')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="sales_channel_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sales Channel
                                    <span class="text-red-500">*</span></label>
                                <select id="sales_channel_id" wire:model="sales_channel_id"
                                    class="form-select mt-1 block w-full rounded-lg">
                                    <option value="">Select Channel...</option>
                                    @foreach ($allSalesChannels as $channel)
                                        <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                    @endforeach
                                </select>
                                @error('sales_channel_id')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="location_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fulfillment
                                    Location <span class="text-red-500">*</span></label>
                                <select id="location_id" wire:model.live="location_id"
                                    class="form-select mt-1 block w-full rounded-lg">
                                    <option value="">Select Location...</option>
                                    @foreach ($allLocations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span
                                        class="text-red-500">*</span></label>
                                <select id="status" wire:model.defer="status"
                                    class="form-select mt-1 block w-full rounded-lg">
                                    @foreach ($soStatuses as $stat)
                                        <option value="{{ $stat }}">
                                            {{ ucfirst(str_replace('_', ' ', $stat)) }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 1.5: Customer Details --}}
                    <div class="mb-8">
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3">
                            Customer Details (Optional)</h2>
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label for="customer_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer
                                    Name</label>
                                <input type="text" id="customer_name" wire:model.defer="customer_name"
                                    class="form-input mt-1 block w-full rounded-lg" placeholder="John Doe">
                                @error('customer_name')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="customer_email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer
                                    Email</label>
                                <input type="email" id="customer_email" wire:model.defer="customer_email"
                                    class="form-input mt-1 block w-full rounded-lg" placeholder="john.doe@example.com">
                                @error('customer_email')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="customer_phone"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer
                                    Phone</label>
                                <input type="tel" id="customer_phone" wire:model.defer="customer_phone"
                                    class="form-input mt-1 block w-full rounded-lg">
                                @error('customer_phone')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Order Items --}}
                    <div>
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3">
                            Order Items</h2>
                        @error('items')
                            <div class="mt-2 text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                        @if (!$location_id)
                            <div
                                class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-800/50 border-l-4 border-yellow-400 text-yellow-700 dark:text-yellow-200 rounded-lg">
                                <p>Please select a fulfillment location to add items.</p>
                            </div>
                        @endif

                        <div class="mt-4 space-y-4">
                            @foreach ($items as $index => $item)
                                {{-- Line Item Card --}}
                                <div wire:key="so-item-{{ $index }}"
                                    class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="space-y-4">
                                        <div class="flex justify-end">
                                            <button type="button" wire:click="removeItem({{ $index }})"
                                                class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 p-1 -m-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div>
                                            <label for="so-items-{{ $index }}-select"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product
                                                / Variant</label>

                                            {{-- The `wire:ignore` wrapper is essential --}}
                                            <div wire:ignore x-data="tomSelect({
                                                wireModel: 'items.{{ $index }}.selected_item_key',
                                                options: {{ json_encode($allSellableItems) }},
                                                initialValue: '{{ $item['selected_item_key'] }}'
                                            })" x-init="init()"
                                                @destroy.window="destroy()">

                                                {{-- The target element for Tom Select --}}
                                                <select x-ref="select" id="so-items-{{ $index }}-select"
                                                    placeholder="Search for a product..."
                                                    {{ !$location_id ? 'disabled' : '' }}>
                                                </select>
                                            </div>

                                            @error('items.' . $index . '.selected_item_key')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label for="items.{{ $index }}.quantity"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                                                <input type="number"
                                                    wire:model.live.debounce.300ms="items.{{ $index }}.quantity"
                                                    id="items.{{ $index }}.quantity" min="1"
                                                    max="{{ $item['available_stock'] > 0 ? $item['available_stock'] : 1 }}"
                                                    {{ empty($item['selected_item_key']) ? 'disabled' : '' }}
                                                    class="form-input mt-1 block w-full text-sm rounded-lg">
                                                @error('items.' . $index . '.quantity')
                                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="items.{{ $index }}.price_per_unit"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price/Unit
                                                    (OMR)</label>
                                                <input type="number" step="0.01"
                                                    wire:model.live.debounce.300ms="items.{{ $index }}.price_per_unit"
                                                    id="items.{{ $index }}.price_per_unit" min="0"
                                                    class="form-input mt-1 block w-full text-sm rounded-lg">
                                                @error('items.' . $index . '.price_per_unit')
                                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-2">
                                            <div class="flex justify-between items-center">
                                                <span
                                                    class="text-sm font-medium text-gray-600 dark:text-gray-400">Subtotal</span>
                                                <span
                                                    class="text-sm font-mono font-semibold text-gray-800 dark:text-gray-200">
                                                    OMR
                                                    {{ number_format((float) ($item['quantity'] ?? 0) * (float) ($item['price_per_unit'] ?? 0), 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            <button type="button" wire:click="addItem" {{ !$location_id ? 'disabled' : '' }}
                                class="inline-flex items-center px-4 py-2 border border-dashed border-gray-400 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Add Item
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Form Footer --}}
                <div
                    class="bg-gray-50 dark:bg-gray-800/50 px-4 sm:px-6 py-4 rounded-b-xl border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="w-full sm:w-auto text-center sm:text-left">
                            <span class="text-base font-medium text-gray-600 dark:text-gray-400">Total Amount:</span>
                            <span class="ml-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400 font-mono">OMR
                                {{ number_format($total_amount, 2) }}</span>
                        </div>
                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 disabled:opacity-50">
                            <svg wire:loading wire:target="saveSalesOrder"
                                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>{{ $salesOrderInstance && $salesOrderInstance->exists ? 'Update Order' : 'Create Order' }}</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
