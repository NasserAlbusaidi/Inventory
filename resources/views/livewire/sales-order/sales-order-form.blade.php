<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">
        {{ $salesOrderInstance && $salesOrderInstance->exists ? 'Edit Sales Order (' . $salesOrderInstance->order_number . ')' : 'Create New Sales Order' }}
    </h1>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="saveSalesOrder" class="bg-white p-6 md:p-8 rounded-lg shadow-md">
        {{-- SO Header --}}
        <fieldset class="border border-gray-300 p-4 rounded-md mb-6">
            <legend class="text-lg font-medium text-gray-700 px-2">Order Information</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                <div>
                    <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">Order Date</label>
                    <input type="date" id="order_date" wire:model.defer="order_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    @error('order_date')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="channel" class="block text-sm font-medium text-gray-700 mb-1">Sales Channel</label>
                    <select id="channel" wire:model.live="channel"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-10">
                        @foreach ($soChannels as $chan)
                            <option value="{{ $chan }}">{{ ucfirst($chan) }}</option>
                        @endforeach
                    </select>
                    @error('channel')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                @if ($channel === 'boutique')
                    <div>
                        <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">Location
                            (Boutique)</label>
                        <select id="location_id" wire:model.defer="location_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-10">
                            <option value="">Select Location</option>
                            @foreach ($allLocations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('location_id')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" wire:model.defer="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-10">
                        @foreach ($soStatuses as $stat)
                            <option value="{{ $stat }}">{{ ucfirst(str_replace('_', ' ', $stat)) }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="border border-gray-300 p-4 rounded-md mb-6">
            <legend class="text-lg font-medium text-gray-700 px-2">Customer Details</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer
                        Name</label>
                    <input type="text" id="customer_name" wire:model.defer="customer_name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    @error('customer_name')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Customer
                        Email</label>
                    <input type="email" id="customer_email" wire:model.defer="customer_email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    @error('customer_email')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Customer
                        Phone</label>
                    <input type="tel" id="customer_phone" wire:model.defer="customer_phone"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    @error('customer_phone')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </fieldset>

        {{-- SO Items --}}
        <fieldset class="border border-gray-300 p-4 rounded-md mb-6">
            <legend class="text-lg font-medium text-gray-700 px-2">Order Items</legend>
            @error('items')
                <div class="text-red-500 text-xs mt-1 mb-3">{{ $message }}</div>
            @enderror

            <div class="space-y-4">
                @foreach ($items as $index => $item)
                    <div wire:key="so-item-{{ $index }}"
                        class="p-3 bg-gray-50 rounded-md border border-gray-200 grid grid-cols-12 gap-x-4 gap-y-2 items-start">
                        <div class="col-span-12 md:col-span-4">
                            <label for="items.{{ $index }}.product_variant_id"
                                class="block text-xs font-medium text-gray-700">Product Variant</label>
                            <select wire:model.live="items.{{ $index }}.product_variant_id"
                                id="items.{{ $index }}.product_variant_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-10">
                                <option value="">Select Variant</option>
                                @foreach ($allProductVariants as $variant)
                                    <option value="{{ $variant->id }}">{{ $variant->full_name_with_variant }} (Stock:
                                        {{ $variant->stock_quantity }})</option>
                                @endforeach
                            </select>
                            @if (
                                !empty($item['product_variant_id']) &&
                                    $item['available_stock'] < ($item['quantity'] ?? 1) &&
                                    $item['available_stock'] > 0)
                                <p class="text-yellow-600 text-xs mt-1">Low stock: only {{ $item['available_stock'] }}
                                    available.</p>
                            @elseif(!empty($item['product_variant_id']) && $item['available_stock'] == 0)
                                <p class="text-red-600 text-xs mt-1">Out of stock!</p>
                            @endif
                            @error('items.' . $index . '.product_variant_id')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-6 md:col-span-2">
                            <label for="items.{{ $index }}.quantity"
                                class="block text-xs font-medium text-gray-700">Quantity</label>
                            <input type="number" wire:model.live="items.{{ $index }}.quantity"
                                id="items.{{ $index }}.quantity" min="1"
                                {{ empty($item['product_variant_id']) || $item['available_stock'] == 0 ? 'disabled' : '' }}
                                max="{{ $item['available_stock'] > 0 ? $item['available_stock'] : 1 }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                            @error('items.' . $index . '.quantity')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-6 md:col-span-3">
                            <label for="items.{{ $index }}.price_per_unit"
                                class="block text-xs font-medium text-gray-700">Selling Price/Unit (OMR)</label>
                            <input type="number" step="0.001"
                                wire:model.live="items.{{ $index }}.price_per_unit"
                                id="items.{{ $index }}.price_per_unit" min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                            @error('items.' . $index . '.price_per_unit')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-12 md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Subtotal (OMR)</label>
                            <input type="text" readonly
                                value="{{ number_format((int) ($item['quantity'] ?? 0) * (float) ($item['price_per_unit'] ?? 0), 2) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm p-2">
                        </div>
                        <div class="col-span-12 md:col-span-1 flex items-center justify-end pt-5">
                            @if (count($items) > 0)
                                {{-- Show remove button even for one item, can be re-added --}}
                                <button type="button" wire:click="removeItem({{ $index }})"
                                    class="text-red-500 hover:text-red-700 p-2 rounded-md bg-red-100 hover:bg-red-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                <button type="button" wire:click="addItem"
                    class="inline-flex items-center px-3 py-2 border border-dashed border-gray-400 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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

        <div class="mt-6 flex justify-between items-center">
            <div class="text-xl font-semibold text-gray-800">
                Total Amount: <span class="text-indigo-600">OMR {{ number_format($total_amount, 2) }}</span>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('sales-orders.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                    {{ $salesOrderInstance && $salesOrderInstance->exists ? 'Update Sales Order' : 'Create Sales Order' }}
                </button>
            </div>
        </div>
    </form>
</div>
