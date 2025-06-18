<div class="py-8">
    <div class="max-w-4xl mx-auto px-2 sm:px-6 lg:px-8">

        {{-- =================================================== --}}
        {{-- Header Section --}}
        {{-- =================================================== --}}
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $salesChannelInstance && $salesChannelInstance->exists ? 'Edit Sales Channel' : 'Add New Sales Channel' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    A sales channel represents a platform where sales are made, like an online store or physical location.
                </p>
            </div>
            <a href="{{ route('sales-channels.index') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>
                Back to List
            </a>
        </div>

        {{-- =================================================== --}}
        {{-- Main Form Card --}}
        {{-- =================================================== --}}
        <form wire:submit.prevent="saveSalesChannel">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl">
                {{-- Card Body --}}
                <div class="p-4 sm:p-6 md:p-8">

                    {{-- Section: Channel Details --}}
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3">Channel Details</h2>
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Channel Name Field --}}
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Channel Name <span class="text-red-500">*</span></label>
                                <input type="text" id="name" wire:model.lazy="name"
                                       class="form-input mt-1 block w-full rounded-lg"
                                       placeholder="e.g., Online Store, In-Person Event">
                                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Card Footer --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 px-4 sm:px-6 py-4 rounded-b-xl border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-end items-center gap-4">
                        <a href="{{ route('sales-channels.index') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500">
                            Cancel
                        </a>

                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 disabled:opacity-50">
                            <svg wire:loading wire:target="saveSalesChannel" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>{{ $salesChannelInstance && $salesChannelInstance->exists ? 'Update Channel' : 'Save Channel' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
