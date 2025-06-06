{{--
    Refactored to match the design of the Purchase Order form.
    - Uses the container, fieldset, and legend structure.
    - Adopts the standard white card with rounded-lg and shadow-md.
    - Applies consistent styling to all labels, inputs, and buttons from the PO form.
--}}
<div class="container mx-auto p-4">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
            {{ $expenseId ? 'Edit Recurring Expense' : 'Create New Recurring Expense' }}
        </h1>
    </div>

    {{-- Flash Messages (Styled like the PO form) --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 dark:bg-green-800/20 dark:border-green-600 dark:text-green-300 px-4 py-3 rounded-lg relative mb-4" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="saveExpense" class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-lg shadow-md">
        {{-- Expense Details Section --}}
        <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded-md">
            <legend class="text-lg font-medium text-gray-700 dark:text-gray-300 px-2">Expense Details</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea id="description" wire:model.lazy="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                              placeholder="e.g., Monthly Rent for Warehouse, Software Subscription"></textarea>
                    @error('description') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Location --}}
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

                {{-- Monthly Cost --}}
                <div>
                    <label for="monthly_cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monthly Cost (OMR)</label>
                    <input type="number" step="0.01" id="monthly_cost" wire:model.lazy="monthly_cost"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                           placeholder="e.g., 550.00">
                    @error('monthly_cost') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Start Date --}}
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                     <input id="start_date" type="date" wire:model.lazy="start_date"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    @error('start_date') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- End Date --}}
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date (Optional)</label>
                     <input id="end_date" type="date" wire:model.lazy="end_date"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    @error('end_date') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </fieldset>

        {{-- Form Actions --}}
        <div class="mt-8 flex justify-end items-center space-x-4">
             <a href="{{ route('expenses.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 disabled:opacity-25 transition ease-in-out duration-150">
                 Cancel
             </a>
             <button type="submit"
                     wire:loading.attr="disabled"
                     class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                 {{ $expenseId ? 'Update Expense' : 'Save Expense' }}
             </button>
        </div>
    </form>
</div>
