<div class="py-8">
    {{-- On mobile, use less horizontal padding --}}
    <div class="max-w-4xl mx-auto px-2 sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                {{ $categoryInstance && $categoryInstance->exists ? 'Edit Category' : 'Create New Category' }}
            </h1>
        </div>

        {{-- Flash Messages (Styled like the other forms) --}}
        @if (session()->has('message'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 dark:bg-green-800/20 dark:border-green-600 dark:text-green-300 px-4 py-3 rounded-lg relative"
                role="alert">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
             <div class="mb-6 bg-red-100 border border-red-400 text-red-700 dark:bg-red-800/20 dark:border-red-600 dark:text-red-300 px-4 py-3 rounded-lg relative"
                role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="saveCategory" class="bg-white dark:bg-gray-800 p-4 sm:p-6 md:p-8 rounded-lg shadow-md">
            {{-- Category Details Section --}}
            <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded-md mb-6">
                <legend class="text-lg font-medium text-gray-700 dark:text-gray-300 px-2">Category Details</legend>
                <div class="grid grid-cols-1 gap-6 mt-4">

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <input type="text" id="name" wire:model.defer="name"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                               placeholder="Category Name (e.g., Floral, Oriental)">
                        @error('name') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea id="description" wire:model.defer="description" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                  placeholder="Optional: A brief description of the category."></textarea>
                        @error('description') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </fieldset>

            {{-- Form Actions --}}
            <div class="mt-8 flex flex-col sm:flex-row-reverse sm:justify-start gap-3">
                 <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                    <svg wire:loading wire:target="saveCategory" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>{{ $categoryInstance && $categoryInstance->exists ? 'Update Category' : 'Create Category' }}</span>
                </button>
                 <a href="{{ route('categories.index') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
