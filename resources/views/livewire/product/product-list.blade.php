<div class="py-8">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Products</h1>
            <div class="flex items-center space-x-3">
                <a href="{{ route('products.import') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Products
                </a>
                <a href="{{ route('products.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 mr-2 -ml-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Add New Product
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 dark:bg-green-800 border-l-4 border-green-400 dark:border-green-600 p-4 shadow-md rounded-md"
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
            <div class="mb-6 bg-red-50 dark:bg-red-800 border-l-4 border-red-400 dark:border-red-600 p-4 shadow-md rounded-md"
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

        {{-- Filters Section --}}
        <div class="mb-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search
                        Products</label>
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
                            placeholder="Search by name, SKU, or variant name..."
                            class="form-input block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="categoryFilter"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <select id="categoryFilter" wire:model.live="categoryFilter"
                        class="form-select block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900 dark:text-gray-200">
                        <option value="">All Categories</option>
                        @foreach ($allCategoriesList as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- (NEW) Display for active status filter --}}
            @if ($statusFilter)
                <div class="mt-4 flex items-center gap-2 bg-indigo-50 dark:bg-indigo-900/50 p-3 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 dark:text-indigo-400"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.59L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">
                        Filtering by status: <span class="font-bold">{{ str_replace('_', ' ', $statusFilter) }}</span>
                    </span>
                    <button wire:click="$set('statusFilter', '')"
                        class="ml-auto text-indigo-500 hover:text-indigo-700 dark:hover:text-indigo-300">×</button>
                </div>
            @endif
        </div>

        {{-- Products Table --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                SKU</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Name</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Category</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Variants</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total Stock</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-400">
                                    {{ $product->sku ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $product->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ Str::limit($product->description, 60) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $product->category->name ?? '-' }}</td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 text-center">
                                    {{ $product->variants_count }}</td>

                                @php
                                    $stock = $product->total_stock;
                                @endphp

                                <td @class([
                                    'px-6 py-4 whitespace-nowrap text-sm font-medium text-center',
                                    'text-gray-400 dark:text-gray-500' => $stock <= 0,
                                    'text-red-500 dark:text-red-400' =>
                                        $stock > 0 && $stock <= $lowStockThreshold,
                                    'text-green-600 dark:text-green-400' => $stock > $lowStockThreshold,
                                ])>
                                    {{ $stock }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold">Edit</a>
                                    {{-- The delete action should be moved to the ProductList component --}}
                                    <button wire:click="confirmDelete({{ $product->id }})"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-semibold">
                                        Trash
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                        </svg>
                                        No products found. Try adjusting your search or filter.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="mt-6 px-2">
                {{ $products->links() }}
            </div>
        @endif
        @if ($showDeleteModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75"
                x-data="{ show: @entangle('showDeleteModal') }" x-show="show" x-on:keydown.escape.window="show = false"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                {{-- Modal Content --}}
                <div class="bg-white dark:bg-gray-300 rounded-lg shadow-xl w-full max-w-md m-4"
                    @click.away="show = false" x-show="show" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div class="p-6">
                        <div class="flex items-start space-x-3">
                            <div
                                class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/50">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                    Delete Product
                                </h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Are you sure you want to delete <strong>"{{ $productToDelete?->name }}"</strong>?
                                    All of its data will be permanently removed. This action cannot be undone.
                                </p>
                                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    <strong>Note:</strong> This will also delete all associated variants and stock
                                    records, and even Purchase Orders that include this product.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 dark:bg-gray-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                        <button wire:click="deleteProduct" wire:loading.attr="disabled" type="button"
                            class="inline-flex w-full justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto">
                            Delete
                        </button>
                        <button wire:click="closeModal" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
