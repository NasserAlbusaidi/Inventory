<div class="py-8">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        {{-- Step Progress Bar --}}
        <div class="flex items-center justify-center mb-8">
            <ol class="flex items-center w-full max-w-md">
                <li class="flex w-full items-center {{ $step >= 1 ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500' }} after:content-[''] after:w-full after:h-1 after:border-b {{ $step > 1 ? 'after:border-blue-600 dark:after:border-blue-500' : 'after:border-gray-200 dark:after:border-gray-700' }} after:border-1 after:inline-block">
                    <span class="flex items-center justify-center w-10 h-10 {{ $step >= 1 ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700' }} rounded-full lg:h-12 lg:w-12 shrink-0">1</span>
                </li>
                <li class="flex items-center">
                    <span class="flex items-center justify-center w-10 h-10 {{ $step >= 2 ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700' }} rounded-full lg:h-12 lg:w-12 shrink-0">2</span>
                </li>
            </ol>
        </div>

        {{-- STEP 1: UPLOAD FORM --}}
        @if ($step === 1)
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
                <form wire:submit.prevent="proceedToReview">
                    <div class="p-6 sm:p-8">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Import Purchase Order: Step 1 of 2</h1>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select a receiving location and upload the CSV file.</p>
                            </div>
                            <a href="{{ route('purchase-orders.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition ease-in-out duration-150 whitespace-nowrap">Back</a>
                        </div>
                        @if (session()->has('import_error')) <div class="p-4 mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-md"><p>{{ session('import_error') }}</p></div> @endif
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">1. Select Receiving Location</h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose the warehouse or store where this stock will be received.</p>
                                <div class="mt-4">
                                    <label for="location" class="sr-only">Receiving Location</label>
                                    <select id="location" wire:model="receiving_location_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2 px-3 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                        @foreach($allLocations as $location) <option value="{{ $location->id }}">{{ $location->name }}</option> @endforeach
                                    </select>
                                    @error('receiving_location_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">2. Upload CSV File</h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select the file from your supplier.</p>
                                <div class="mt-4">
                                   <label for="file-input" class="sr-only">Choose file</label>
                                   <input type="file" wire:model="file" id="file-input" class="block w-full border border-gray-200 shadow-sm rounded-md text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 file:bg-transparent file:border-0 file:bg-gray-100 file:mr-4 file:py-2 file:px-4 dark:file:bg-gray-700 dark:file:text-gray-400">
                                   @error('file') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                   <div wire:loading wire:target="file" class="mt-2 text-sm text-gray-500">Uploading...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 rounded-b-xl border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50">
                            <svg wire:loading wire:target="proceedToReview" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Next: Review & Confirm</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- STEP 2: REVIEW & FINALIZE --}}
        @if ($step === 2 && !empty($reviewData))
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Import Purchase Order: Step 2 of 2</h1>
                            <p class="mt-1 text-gray-500 dark:text-gray-400">Review the parsed data, map any unassigned items, and confirm the import.</p>
                        </div>
                        <button type="button" wire:click="backToStep1" class="text-sm font-medium text-gray-600 hover:text-indigo-500">← Start Over</button>
                    </div>

                    @if (session()->has('review_error')) <div class="mt-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-md"><p>{{ session('review_error') }}</p></div> @endif
                    @if (session()->has('message')) <div class="mt-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-md"><p>{{ session('message') }}</p></div> @endif

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-600">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Invoice Details & Final Cost</h3>
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <dl class="text-sm">
                                    <div class="flex justify-between"><dt class="text-gray-500">Invoice #:</dt><dd class="font-mono text-gray-700 dark:text-gray-300">{{ $reviewData['header']['order_number'] }}</dd></div>
                                    <div class="flex justify-between"><dt class="text-gray-500">Date:</dt><dd class="font-mono text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($reviewData['header']['order_date'])->format('M d, Y') }}</dd></div>
                                    <div class="flex justify-between"><dt class="text-gray-500">Receiving At:</dt><dd class="font-semibold text-gray-700 dark:text-gray-300">{{ $allLocations->find($reviewData['header']['receiving_location_id'])->name ?? 'N/A' }}</dd></div>
                                    <div class="flex justify-between mt-2 pt-2 border-t dark:border-gray-600"><dt class="font-bold text-gray-600 dark:text-gray-300">Original Total (USD):</dt><dd class="font-bold text-gray-800 dark:text-gray-200">${{ number_format($reviewData['stats']['total_value_original'], 2) }}</dd></div>
                                </dl>
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 rounded-md flex flex-col justify-center">
                                     <label for="finalOmrTotal" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Total Paid (OMR) <span class="text-xs font-normal text-gray-500">(Optional)</span></label>
                                     <p class="text-xs text-gray-500 dark:text-gray-400">If blank, will be auto-calculated using a rate of 0.395.</p>
                                     @php $calculatedOmrTotal = $reviewData['stats']['total_value_original'] * 0.395; @endphp
                                     <input type="number" step="any" wire:model.defer="finalOmrTotal" id="finalOmrTotal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white" placeholder="e.g. {{ number_format($calculatedOmrTotal, 3) }}">
                                     @error('finalOmrTotal') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="p-4 rounded-lg border {{ $reviewData['supplier']['found'] ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700' : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-400 dark:border-yellow-600' }}">
                            <h3 class="font-semibold {{ $reviewData['supplier']['found'] ? 'text-green-800 dark:text-green-200' : 'text-yellow-800 dark:text-yellow-200' }}">Supplier Information</h3>
                            @if($reviewData['supplier']['found'])
                                <p class="mt-2 text-green-700 dark:text-green-300">✓ Supplier '<strong>{{ $reviewData['supplier']['name'] }}</strong>' found and linked.</p>
                            @else
                                <p class="mt-2 text-yellow-700 dark:text-yellow-300">Supplier '<strong>{{ $reviewData['header']['supplier_name'] }}</strong>' not found. Please select an option:</p>
                                <div class="mt-3 space-y-2">
                                    <select wire:model="reviewData.supplier.id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                        <option value="">-- Choose an option --</option>
                                        <option value="new">Create new supplier: '{{ $reviewData['header']['supplier_name'] }}'</option>
                                        <optgroup label="Or link to existing">
                                            @foreach($allSuppliers as $supplier) <option value="{{ $supplier->id }}">{{ $supplier->name }}</option> @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($reviewData['stats']['unmatched_count'] > 0)
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-600">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Bulk Actions for {{ $reviewData['stats']['unmatched_count'] }} Unmapped Items</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">To save time, you can create all unmapped products at once. They will be assigned to the category you select below.</p>
                            <div class="mt-4 flex flex-col sm:flex-row items-center gap-3">
                                <div class="w-full sm:w-1/2">
                                    <label for="bulkCreateCategory" class="sr-only">Default Category</label>
                                    <select id="bulkCreateCategory" wire:model="bulkCreateCategoryId" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                        <option value="">-- Select a category for new products --</option>
                                        @foreach($allCategories as $category) <option value="{{ $category->id }}">{{ $category->name }}</option> @endforeach
                                    </select>
                                    @error('bulkCreateCategoryId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="w-full sm:w-1/2">
                                    <button type="button" wire:click="bulkCreateProducts" wire:loading.attr="disabled" x-data :disabled="!$wire.bulkCreateCategoryId" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg wire:loading wire:target="bulkCreateProducts" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>Bulk Create Products</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flow-root border-t border-gray-200 dark:border-gray-700 mt-6">
                    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                            <th class="py-3.5 px-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-5/12">CSV Item Description</th>
                            <th class="py-3.5 px-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100 w-1/12">Qty</th>
                            <th class="py-3.5 px-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100 w-1/12">Price (USD)</th>
                            <th class="py-3.5 px-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-5/12">Assign to System Product</th>
                        </tr></thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($reviewData['items'] as $index => $item)
                            <tr wire:key="review-item-{{ $index }}" class="{{ empty($item['assigned_key']) ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                <td class="py-3 px-4 align-top font-mono text-sm text-gray-700 dark:text-gray-300">{{ $item['raw_description'] }}</td>
                                <td class="py-3 px-3 align-top text-center text-sm text-gray-500 dark:text-gray-400">{{ $item['quantity'] }}</td>
                                <td class="py-3 px-3 align-top text-center text-sm text-gray-500 dark:text-gray-400">{{ number_format($item['price'], 2) }}</td>
                                <td class="py-2 px-3 align-top">
                                    <div class="space-y-2">
                                        <select wire:model="reviewData.items.{{ $index }}.assigned_key" class="product-tom-select block w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                            <option value="">-- Choose or Create --</option>
                                            @foreach($allPurchasableItems as $purchasable) <option value="{{ $purchasable['key'] }}">{{ $purchasable['display_name'] }}</option> @endforeach
                                        </select>
                                        @if(empty($item['assigned_key']))
                                            <button type="button" wire:click="openCreateProductModal({{ $index }})" class="w-full text-xs text-center px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                                + Create New Product
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 flex justify-between items-center border-t border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        @if($reviewData['stats']['unmatched_count'] > 0)
                            <span class="font-bold text-red-600">{{ $reviewData['stats']['unmatched_count'] }} items require mapping.</span>
                        @else
                            <span class="font-bold text-green-600">✓ All items mapped.</span>
                        @endif
                    </span>
                    <button type="button" wire:click="finalizeImport" wire:loading.attr="disabled" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 disabled:opacity-50">
                        <svg wire:loading wire:target="finalizeImport" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span wire:loading.remove wire:target="finalizeImport">Confirm & Finalize Import</span>
                        <span wire:loading wire:target="finalizeImport">Importing...</span>
                    </button>
                 </div>
            </div>
        @endif

        {{-- CREATE PRODUCT MODAL --}}
        @if ($showCreateProductModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" x-cloak>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg" @click.away="$wire.closeCreateProductModal()">
                    <form wire:submit.prevent="saveNewProduct">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Create New Product</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Create a new product record from the CSV data.</p>
                            @if(session()->has('modal_error')) <div class="mt-2 text-red-500 text-sm">{{ session('modal_error') }}</div> @endif
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="newProductName" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Product Name</label>
                                    <input type="text" wire:model.defer="newProductName" id="newProductName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                    @error('newProductName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="newProductSku" class="block text-sm font-medium text-gray-700 dark:text-gray-200">SKU</label>
                                    <input type="text" wire:model.defer="newProductSku" id="newProductSku" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                    @error('newProductSku') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="newProductCategoryId" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Category</label>
                                    <select wire:model.defer="newProductCategoryId" id="newProductCategoryId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                        <option value="">Select a category...</option>
                                        @foreach($allCategories as $category) <option value="{{ $category->id }}">{{ $category->name }}</option> @endforeach
                                    </select>
                                    @error('newProductCategoryId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                            <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                                <span wire:loading.remove wire:target="saveNewProduct">Save Product</span>
                                <span wire:loading wire:target="saveNewProduct">Saving...</span>
                            </button>
                            <button type="button" wire:click="closeCreateProductModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            function initializeTomSelect(selector) {
                document.querySelectorAll(selector).forEach(el => {
                    if (el.tomselect) {
                        el.tomselect.destroy();
                    }
                    new TomSelect(el, {
                        create: false,
                        sortField: { field: 'text', direction: 'asc' },
                        dropdownParent: 'body'
                    });
                });
            }
            initializeTomSelect('.product-tom-select');

            Livewire.hook('message.processed', (message, component) => {
                if (component.name === 'purchase-order.purchase-upload') {
                    initializeTomSelect('.product-tom-select');
                }
            });
        });
    </script>
@endpush
