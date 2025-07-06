<div class="py-8">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        {{-- =================================================================== --}}
        {{-- STEP 1: UPLOAD FORM                                                 --}}
        {{-- =================================================================== --}}
        {{-- =================================================================== --}}
{{-- STEP 1: UPLOAD FORM (REDESIGNED)                                    --}}
{{-- =================================================================== --}}
@if ($step === 1)
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
        {{-- The form now wraps the entire content, including context dropdowns --}}
        <form wire:submit.prevent="parsePdfForMapping">
            <div class="p-6 sm:p-8">
                {{-- Main Header --}}
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Import Sales: Step 1 of 3</h1>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">Select the context for this report and upload the PDF file.</p>
                    </div>
                    <a href="{{ route('sales-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition ease-in-out duration-150 whitespace-nowrap">Back</a>
                </div>

                {{-- Session Errors --}}
                <div class="mt-6">
                   @if (session()->has('import_errors'))
                    <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-md"><p>{{ session('import_errors') }}</p></div>
                   @endif
                </div>

                {{-- Section 1: Context Selection --}}
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">1. Select Report Context</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose the location and sales channel that this entire report applies to.</p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fulfillment Location</label>
                            <select id="location" wire:model="location_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                @foreach($allLocations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                            @error('location_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="sales_channel" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Sales Channel</label>
                            <select id="sales_channel" wire:model="sales_channel_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                               @foreach($allSalesChannels as $channel)
                                    <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                @endforeach
                            </select>
                            @error('sales_channel_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Section 2: File Upload --}}
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">2. Upload PDF File</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select the PDF file provided by the client.</p>

                    <div class="mt-4" x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false; progress = 0" x-on:livewire-upload-error="uploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
                        <input type="file" id="upload" wire:model.defer="upload" accept="application/pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" required>
                        @error('upload') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror

                        {{-- Progress Bar --}}
                        <div x-show="uploading" class="mt-4 w-full bg-gray-200 rounded-full dark:bg-gray-700">
                            <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" :style="`width: ${progress}%`" x-text="`${progress}%`"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions Footer -->
            <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 rounded-b-xl border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50">
                    <svg wire:loading wire:target="parsePdfForMapping" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span wire:loading.remove wire:target="parsePdfForMapping">Next: Map Products</span>
                    <span wire:loading wire:target="parsePdfForMapping">Parsing PDF...</span>
                </button>
            </div>
        </form>
    </div>
@endif

        {{-- =================================================================== --}}
        {{-- STEP 2: MAP PRODUCTS                                                --}}
        {{-- =================================================================== --}}
        @if ($step === 2)
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Import Sales: Step 2 of 3</h1>
                            <p class="mt-1 text-gray-500 dark:text-gray-400">Match items from the PDF to your official products.</p>
                        </div>
                        <button type="button" wire:click="backToStep1" class="text-sm font-medium text-gray-600 hover:text-indigo-500">← Start Over</button>
                    </div>

                    {{-- NEW: Report Summary Stats --}}
                    <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-700/50 px-4 py-5 shadow sm:p-6"><dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Sales Value</dt><dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ number_format($reportGrandTotal, 3) }}</dd></div>
                        <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-700/50 px-4 py-5 shadow sm:p-6"><dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Items Sold</dt><dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $totalItemCount }}</dd></div>
                        <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-700/50 px-4 py-5 shadow sm:p-6"><dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Unique PDF Items</dt><dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $uniqueItemCount }}</dd></div>
                    </dl>

                    @if (session()->has('mapping_error'))
                        <div class="mt-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-md"><p>{{ session('mapping_error') }}</p></div>
                    @endif
                </div>

                <div class="flow-root border-t border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700"><tr class="divide-x divide-gray-200 dark:divide-gray-600">
                            <th class="py-3.5 px-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-1/3">PDF Item Name</th>
                            <th class="py-3.5 px-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-1/3">System's Best Guess</th>
                            <th class="py-3.5 px-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-1/3">Assign to Official Product</th>
                        </tr></thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($unmappedItems as $index => $item)
                            <tr wire:key="unmapped-{{ $index }}" class="divide-x divide-gray-200 dark:divide-gray-600">
                                <td class="py-4 px-4 align-top"><p class="font-mono text-sm text-gray-700 dark:text-gray-300">{{ $item['raw_name'] }}</p><p class="text-xs text-gray-500 mt-1">Sold: <span class="font-semibold">{{ $item['count'] }}</span> | Total: <span class="font-semibold">{{ number_format($item['total_price'], 3) }}</span></p></td>
                                <td class="py-4 px-3 align-top">@if($item['guess_key']) <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-900/50 dark:text-green-200">{{ $allSellableItems->firstWhere('key', $item['guess_key'])['display_name'] ?? 'N/A' }}</span> @else <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20 dark:bg-yellow-900/50 dark:text-yellow-400">No confident match found</span> @endif</td>
                                <td class="py-4 px-3 align-top"><select wire:model="unmappedItems.{{ $index }}.assigned_key" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white"><option value="">Choose a product...</option>@foreach($allSellableItems as $sellable)<option value="{{ $sellable['key'] }}">{{ $sellable['display_name'] }}</option>@endforeach</select></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 flex justify-end">
                    <button type="button" wire:click="proceedToDateAssignment" wire:loading.attr="disabled" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50">Next: Assign Dates</button>
                </div>
            </div>
        @endif

        {{-- =================================================================== --}}
        {{-- STEP 3: ASSIGN DATES                                                --}}
        {{-- =================================================================== --}}
        @if ($step === 3)
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex justify-between items-start">
                        <div><h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Import Sales: Step 3 of 3</h1><p class="mt-1 text-gray-500 dark:text-gray-400">Assign the correct sale date to each item.</p></div>
                        <button type="button" wire:click="backToStep2" class="text-sm font-medium text-gray-600 hover:text-indigo-500">← Back to Product Mapping</button>
                    </div>
                     @if (session()->has('assignment_error')) <div class="mt-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-md"><p>{{ session('assignment_error') }}</p></div> @endif

                    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-600 flex flex-col sm:flex-row items-center gap-4">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">Bulk Actions:</span>
                        <div class="flex-grow"><select wire:model="masterDate" class="w-full block shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-600 dark:text-white"><option value="">Select a date to apply...</option>@foreach($extractedDates as $date)<option value="{{ $date }}">{{ \Carbon\Carbon::createFromFormat('d.m.y', $date)->format('D, M j, Y') }}</option>@endforeach</select></div>
                        <button type="button" wire:click="applyMasterDate" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50" :disabled="!$wire.masterDate">Apply to Selected</button>>
                    </div>
                </div>

                <div class="flow-root border-t border-gray-200 dark:border-gray-700">
                    <div class="inline-block min-w-full align-middle">
                        <div class="relative">
                            <table class="min-w-full table-fixed divide-y divide-gray-300 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                                    <th scope="col" class="relative px-7 sm:w-12 sm:px-6"><input type="checkbox" wire:click="toggleSelectAll" class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></th>
                                    <th scope="col" class="py-3.5 px-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-2/5">Product</th>
                                    <th scope="col" class="py-3.5 px-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-1/5">Price</th>
                                    <th scope="col" class="py-3.5 px-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-2/5">Sale Date</th>
                                </tr></thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    @forelse($mappedItems as $index => $item)
                                        <tr wire:key="mapped-item-{{ $index }}" class="{{ in_array($index, $selectedItems) ? 'bg-indigo-50 dark:bg-indigo-900/20' : '' }}">
                                            <td class="relative px-7 sm:w-12 sm:px-6">@if(in_array($index, $selectedItems))<div class="absolute inset-y-0 left-0 w-0.5 bg-indigo-600"></div>@endif<input type="checkbox" wire:model="selectedItems" value="{{ $index }}" class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                            <td class="py-4 px-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item['display_name'] }}</td>
                                            <td class="py-4 px-3 text-sm text-gray-500 dark:text-gray-300">{{ number_format($item['price'], 3) }}</td>
                                            <td class="py-4 px-3 text-sm text-gray-500"><select wire:model="mappedItems.{{ $index }}.selected_date" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white"><option value="">Choose date...</option>@foreach($extractedDates as $date)<option value="{{ $date }}">{{ \Carbon\Carbon::createFromFormat('d.m.y', $date)->format('D, M j, Y') }}</option>@endforeach</select></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-8 text-gray-500">Something went wrong. No mapped items to display.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Items to Import: <strong>{{ count($mappedItems) }}</strong></span>
                    <button type="button" wire:click="finalizeImport" wire:loading.attr="disabled" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 disabled:opacity-50">
                        <svg wire:loading wire:target="finalizeImport" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span wire:loading.remove wire:target="finalizeImport">Confirm & Import</span>
                        <span wire:loading wire:target="finalizeImport">Importing...</span>
                    </button>
                 </div>
            </div>
        @endif
    </div>
</div>
