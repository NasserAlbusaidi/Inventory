<div class="py-8">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">

        {{-- =================================================== --}}
        {{-- Header Section --}}
        {{-- =================================================== --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Application Settings</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Configure key performance indicators and application behavior.
            </p>
        </div>

        {{-- =================================================== --}}
        {{-- Success Message --}}
        {{-- =================================================== --}}
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-800/50 border-l-4 border-green-400 dark:border-green-600 p-4 shadow-md rounded-md"
                role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400 dark:text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- =================================================== --}}
        {{-- Main Settings Form Card --}}
        {{-- =================================================== --}}
        <form wire:submit.prevent="save">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl">
                {{-- Settings Grid --}}
                <div class="p-6 sm:p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">

                    {{-- Card 1: Financial Targets --}}
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01" />
                                </svg>
                            </div>
                            <div class="flex-grow">
                                <label for="monthly_revenue_target" class="block text-base font-semibold text-gray-900 dark:text-gray-100">Monthly Revenue Target (OMR)</label>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Set your monthly sales goal to track progress on the dashboard.</p>
                                <input type="number" step="0.01" id="monthly_revenue_target" wire:model="monthly_revenue_target"
                                    class="form-input mt-2 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('monthly_revenue_target') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.121 15.536A9.004 9.004 0 0112 15c-1.657 0-3.153-.44-4.5-1.242-1.42-1.02-2.5-2.49-2.5-4.258V6c0-1.105.895-2 2-2h12c1.105 0 2 .895 2 2v3.036c0 .88-.357 1.666-.94 2.22l-2.47 2.47a1.5 1.5 0 01-2.122 0l-2.47-2.47a1.5 1.5 0 00-2.122 0z" />
                                </svg>
                            </div>
                            <div class="flex-grow">
                                <label for="profit_margin_target" class="block text-base font-semibold text-gray-900 dark:text-gray-100">Profit Margin Target (%)</label>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define your ideal profit margin to measure business health.</p>
                                <input type="number" id="profit_margin_target" wire:model="profit_margin_target"
                                    class="form-input mt-2 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('profit_margin_target') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Inventory Alerts --}}
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-yellow-100 dark:bg-yellow-900/50 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-grow">
                                <label for="low_stock_threshold" class="block text-base font-semibold text-gray-900 dark:text-gray-100">Low Stock Threshold (Units)</label>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get alerts on the dashboard when product stock falls to this level.</p>
                                <input type="number" id="low_stock_threshold" wire:model="low_stock_threshold"
                                    class="form-input mt-2 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('low_stock_threshold') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Footer with Save Button --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 rounded-b-xl border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-end">
                        <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-50 transition ease-in-out duration-150">
                            <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="save">Save Settings</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
