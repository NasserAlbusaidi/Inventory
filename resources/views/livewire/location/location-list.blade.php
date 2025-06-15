<div class="py-8">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">

        {{-- =================================================== --}}
        {{-- Header Section --}}
        {{-- =================================================== --}}
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Locations</h1>
            <a href="{{ route('locations.create') }}"
                class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2 -ml-1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Create New Location
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
        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-800/50 border-l-4 border-red-400 dark:border-red-600 p-4 shadow-md rounded-md"
                role="alert">
                <div class="flex">
                    <div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400 dark:text-red-500"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg></div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- =================================================== --}}
        {{-- Filters Section --}}
        {{-- =================================================== --}}
        <div class="mb-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
            <div class="max-w-md">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search
                    Locations</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="search" wire:model.live.debounce.300ms="search"
                        placeholder="Search by name or description..."
                        class="form-input block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
        </div>

        {{-- =================================================== --}}
        {{-- Locations Table with Total Stock --}}
        {{-- =================================================== --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Stock</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($locations as $location)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $location->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ Str::limit($location->description, 80) ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    {{ (int) $location->total_stock }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                    <button wire:click="showProducts({{ $location->id }})" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 font-semibold">
                                        View Stock
                                    </button>
                                    <a href="{{ route('locations.edit', $location) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold">Edit</a>
                                    <button wire:click="deleteLocation({{ $location->id }})"
                                            wire:confirm="This location may have records associated with it. Are you sure you want to delete '{{ $location->name }}'?"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-semibold">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                        No Locations found. Click "Create New Location" to get started.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($locations->hasPages())
            <div class="mt-6 px-2">
                {{ $locations->links() }}
            </div>
        @endif

        {{-- =================================================== --}}
        {{-- View Products Modal --}}
        {{-- =================================================== --}}
        @if ($showProductsModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75"
                 x-data="{ show: @entangle('showProductsModal') }" x-show="show" x-on:keydown.escape.window="show = false"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-3xl m-4"
                     @click.away="show = false" x-show="show" x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="p-6">
                        <div class="flex justify-between items-start">
                             <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                Stock in: <span class="text-indigo-600 dark:text-indigo-400">{{ $viewingLocation?->name }}</span>
                            </h2>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">×</button>
                        </div>

                        <div class="mt-4 border-t dark:border-gray-700 pt-4">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product / Variant</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($productsInLocation as $item)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                @if($item->inventoriable instanceof \App\Models\Product)
                                                    {{ $item->inventoriable->name }}
                                                @elseif($item->inventoriable instanceof \App\Models\ProductVariant)
                                                    <span class="text-gray-500 dark:text-gray-400">{{ $item->inventoriable->product->name }} →</span> {{ $item->inventoriable->variant_name }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-400">{{ $item->inventoriable->product->sku ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-right text-gray-800 dark:text-gray-200">{{ $item->stock_quantity }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No products with stock found in this location.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            @if($productsInLocation->hasPages())
                                <div class="mt-4">
                                    {{ $productsInLocation->links() }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-100 dark:bg-gray-700/50 px-6 py-4 flex justify-end rounded-b-lg">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
