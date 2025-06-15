<div class="py-8">
    {{-- On mobile, use less horizontal padding --}}
    <div class="max-w-4xl mx-auto px-2 sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                {{ $expenseId ? 'Edit' : 'Create' }} Expense
            </h1>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 dark:bg-green-800/20 dark:border-green-600 dark:text-green-300 px-4 py-3 rounded-lg relative" role="alert">
                {{ session('message') }}
            </div>
        @endif

        {{-- Main Form --}}
        <form wire:submit.prevent="saveExpense" class="bg-white dark:bg-gray-800 p-4 sm:p-6 md:p-8 rounded-lg shadow-md">
            <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded-md">
                <legend class="text-lg font-medium text-gray-700 dark:text-gray-300 px-2">Expense Details</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">

                    {{-- Expense Type Selector --}}
                    <div class="md:col-span-2">
                        <label for="expense_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expense Type</label>
                        <select id="expense_type" wire:model.live="expense_type"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-11"
                                {{ $expenseId ? 'disabled' : '' }}>
                            <option value="recurring">Recurring</option>
                            <option value="one-time">One-Time</option>
                        </select>
                         @if($expenseId)
                            <p class="text-xs text-gray-500 mt-1">Expense type cannot be changed during editing.</p>
                        @endif
                    </div>

                    {{-- Common Fields --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea id="description" wire:model.lazy="description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                  placeholder="e.g., Monthly Rent for Warehouse, New Office Printer"></textarea>
                        @error('description') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="location_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <select id="location_id" wire:model="location_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-11">
                            <option value="">Select a Location</option>
                            @foreach($allLocations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('location_id') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Conditional Recurring Fields --}}
                    @if ($expense_type === 'recurring')
                        <div>
                            <label for="monthly_cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monthly Cost (OMR)</label>
                            <input type="number" step="0.01" id="monthly_cost" wire:model.lazy="monthly_cost"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                   placeholder="e.g., 550.00">
                            @error('monthly_cost') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                             <input id="start_date" type="date" wire:model.lazy="start_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                            @error('start_date') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date (Optional)</label>
                             <input id="end_date" type="date" wire:model.lazy="end_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                            @error('end_date') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    {{-- Conditional One-Time Fields --}}
                    @if ($expense_type === 'one-time')
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (OMR)</label>
                            <input type="number" step="0.01" id="amount" wire:model.lazy="amount"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                   placeholder="e.g., 150.00">
                            @error('amount') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-start-1">
                            <label for="expense_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expense Date</label>
                             <input id="expense_date" type="date" wire:model.lazy="expense_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                            @error('expense_date') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </fieldset>

            {{-- Form Actions --}}
            <div class="mt-8 flex flex-col sm:flex-row-reverse sm:justify-start gap-3">
                 <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                    <svg wire:loading wire:target="saveExpense" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>{{ $expenseId ? 'Update Expense' : 'Save Expense' }}</span>
                </button>
                 <a href="{{ route('expenses.index') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
