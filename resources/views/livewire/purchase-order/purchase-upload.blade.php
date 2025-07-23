<div class="py-8">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        {{-- Step Progress Bar --}}
        <div class="flex items-center justify-center mb-8">
            <ol class="flex items-center w-full max-w-md">
                <li class="flex w-full items-center {{ $step >= 1 ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500' }} after:content-[''] after:w-full after:h-1 after:border-b {{ $step > 1 ? 'after:border-blue-600' : 'after:border-gray-200' }} after:border-1 after:inline-block">
                    <span class="flex items-center justify-center w-10 h-10 {{ $step >= 1 ? 'bg-blue-100' : 'bg-gray-100' }} rounded-full lg:h-12 lg:w-12 shrink-0">1</span>
                </li>
                <li class="flex items-center">
                    <span class="flex items-center justify-center w-10 h-10 {{ $step >= 2 ? 'bg-blue-100' : 'bg-gray-100' }} rounded-full lg:h-12 lg:w-12 shrink-0">2</span>
                </li>
            </ol>
        </div>

        {{-- =================================================================== --}}
        {{-- STEP 1: UPLOAD FORM                                                 --}}
        {{-- =================================================================== --}}
        @if ($step === 1)
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
                <form wire:submit.prevent="proceedToReview">
                    <div class="p-6 sm:p-8">
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Import Purchase Order: Step 1 of 2</h1>
                                <p class="mt-1 text-gray-500 dark:text-gray-400">Upload the CSV file from your supplier.</p>
                            </div>
                            <a href="{{ route('purchase-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition ease-in-out duration-150 whitespace-nowrap">Back</a>
                        </div>

                        <div class="mt-6">
                           @if (session()->has('import_error'))
                            <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-md"><p>{{ session('import_error') }}</p></div>
                           @endif
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Upload CSV File</h2>
                            <div class="mt-4" x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false; progress = 0" x-on:livewire-upload-error="uploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
                                <input type="file" id="file" wire:model.defer="file" accept=".csv,text/csv" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" required>
                                @error('file') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror

                                <div x-show="uploading" class="mt-4 w-full bg-gray-200 rounded-full dark:bg-gray-700">
                                    <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" :style="`width: ${progress}%`" x-text="`${progress}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 rounded-b-xl border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50">
                            <svg wire:loading wire:target="proceedToReview" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span wire:loading.remove wire:target="proceedToReview">Next: Review & Confirm</span>
                            <span wire:loading wire:target="proceedToReview">Parsing File...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- =================================================================== --}}
        {{-- STEP 2: REVIEW & FINALIZE                                           --}}
        {{-- =================================================================== --}}
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

                    @if (session()->has('review_error'))
                        <div class="mt-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-md"><p>{{ session('review_error') }}</p></div>
                    @endif

                    {{-- Summary & Supplier Section --}}
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- PO Details --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-600">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Invoice Details</h3>
                            <dl class="mt-2 text-sm">
                                <div class="flex justify-between"><dt class="text-gray-500">Invoice #:</dt><dd class="font-mono text-gray-700 dark:text-gray-300">{{ $reviewData['header']['order_number'] }}</dd></div>
                                <div class="flex justify-between"><dt class="text-gray-500">Date:</dt><dd class="font-mono text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($reviewData['header']['order_date'])->format('M d, Y') }}</dd></div>
                                <div class="flex justify-between mt-2 pt-2 border-t dark:border-gray-600"><dt class="font-bold text-gray-600 dark:text-gray-300">Total Value:</dt><dd class="font-bold text-gray-800 dark:text-gray-200">{{ number_format($reviewData['stats']['total_value'], 2) }}</dd></div>
                            </dl>
                        </div>
                        {{-- Supplier Details --}}
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
                                            @foreach($allSuppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="flow-root border-t border-gray-200 dark:border-gray-700 mt-6">
                    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                            <th class="py-3.5 px-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-5/12">CSV Item Description</th>
                            <th class="py-3.5 px-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100 w-1/12">Qty</th>
                            <th class="py-3.5 px-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100 w-1/12">Price</th>
                            <th class="py-3.5 px-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-5/12">Assign to System Product</th>
                        </tr></thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($reviewData['items'] as $index => $item)
                            <tr wire:key="review-item-{{ $index }}" class="{{ empty($item['assigned_key']) ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                <td class="py-3 px-4 align-top font-mono text-sm text-gray-700 dark:text-gray-300">{{ $item['raw_description'] }}</td>
                                <td class="py-3 px-3 align-top text-center text-sm text-gray-500 dark:text-gray-400">{{ $item['quantity'] }}</td>
                                <td class="py-3 px-3 align-top text-center text-sm text-gray-500 dark:text-gray-400">{{ number_format($item['price'], 2) }}</td>
                                <td class="py-2 px-3 align-top">
                                    <select wire:model="reviewData.items.{{ $index }}.assigned_key" class="product-tom-select block w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                        <option value="">-- Skip this item --</option>
                                        @foreach($allPurchasableItems as $purchasable)
                                            <option value="{{ $purchasable['key'] }}">{{ $purchasable['display_name'] }}</option>
                                        @endforeach
                                    </select>
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
    </div>
</div>

{{-- Tom Select for better dropdowns --}}
@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            function initializeTomSelect(selector) {
                document.querySelectorAll(selector).forEach(el => {
                    if (el.tomselect) el.tomselect.destroy();
                    new TomSelect(el, { create: false, sortField: { field: 'text', direction: 'asc' } });
                });
            }
            // Initial load
            initializeTomSelect('.product-tom-select');
            // Re-initialize after Livewire updates the DOM
            Livewire.hook('message.processed', (message, component) => {
                if (component.name === 'purchase-order.purchase-upload') {
                    initializeTomSelect('.product-tom-select');
                }
            });
        });
    </script>
@endpush
