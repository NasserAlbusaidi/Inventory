<div class="py-8">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        {{-- Flash Messages can stay here --}}

        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl">
            <div class="p-6 sm:p-8">
                {{-- Main Header --}}
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Import Products</h1>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">Bulk add products and variants using a CSV or
                            Excel file.</p>
                    </div>
                    {{-- NEW: Download Template Button --}}
                    <button type="button" wire:click="downloadTemplate" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white border border-transparent rounded-lg font-semibold text-sm hover:bg-green-700 transition ease-in-out duration-150 whitespace-nowrap">
                        <svg wire:loading wire:target="downloadTemplate" class="animate-spin -ml-1 mr-2 h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <svg wire:loading.remove wire:target="downloadTemplate" xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span wire:loading.remove wire:target="downloadTemplate">Download Template</span>
                        <span wire:loading wire:target="downloadTemplate">Generating...</span>
                    </button>
                </div>

                {{-- Feedback Messages (errors/success) --}}
                <div class="mt-6">
                    @if (session()->has('import_errors'))
                        <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-md">
                            <h3 class="font-bold">Errors Found During Import:</h3>
                            <ul class="list-disc list-inside mt-2 text-sm">
                                @foreach (session('import_errors') as $key => $message)
                                    <li><strong>{{ is_numeric($key) ? 'Row ' . ($key + 1) : $key }}:</strong>
                                        {{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session()->has('import_success'))
                        <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-md">
                            <p>{{ session('import_success') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Section Break --}}
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">1. Prepare Your File</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Use the example below and the available categories to structure your data correctly. The first
                        row of your file must contain the headers exactly as shown.
                    </p>
                </div>

                {{-- Available Categories Section --}}
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-2">Available Categories</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Click a category to copy its name to your
                        clipboard.</p>
                    @if ($categories->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                <span
                                    class="px-3 py-1 bg-white text-gray-800 dark:bg-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-full text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition"
                                    onclick="copyToClipboard('{{ $category->name }}')"
                                    title="Click to copy '{{ $category->name }}'">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No categories found. <a
                                href="{{-- route to category create page --}}" class="text-blue-500 hover:underline">Create one first</a>.
                        </p>
                    @endif
                </div>

                {{-- Example Structure Table --}}
                <div class="mt-4">
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                        <table class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    @foreach (['category', 'name', 'sku', 'description', 'has_variants', 'price', 'cost', 'variant_name', 'variant_sku'] as $header)
                                        <th scope="col" class="px-4 py-3 whitespace-nowrap">
                                            {{ str_replace('_', ' ', $header) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-3">Apparel</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">T-Shirt</td>
                                    <td class="px-4 py-3">TEE-001</td>
                                    <td class="px-4 py-3">Comfortable cotton shirt</td>
                                    <td class="px-4 py-3">yes</td>
                                     <td class="px-4 py-3 text-gray-400 italic">(keep empty)</td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-900/50 border-b dark:border-gray-700">
                                    <td class="px-4 py-3 text-gray-400 italic">(keep empty)</td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3">15.00</td>
                                    <td class="px-4 py-3">7.00</td>
                                    <td class="px-4 py-3">Red, M</td>
                                    <td class="px-4 py-3">TEE-001-RM</td>
                                </tr>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-3">Electronics</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Laptop</td>
                                    <td class="px-4 py-3">LP-002</td>
                                    <td class="px-4 py-3">15-inch Laptop</td>
                                    <td class="px-4 py-3">no</td>
                                    <td class="px-4 py-3">1200.00</td>
                                    <td class="px-4 py-3">800.00</td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Form Footer with Upload --}}
            <div
                class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 rounded-b-xl border-t border-gray-200 dark:border-gray-700">
                <form wire:submit.prevent="import">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">2. Upload and Process</h3>
                    <div x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
                        x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-error="uploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress">

                        <div class="mt-4 flex items-center gap-4">
                            <input type="file" id="upload" wire:model="upload"
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                            <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-50 transition ease-in-out duration-150 whitespace-nowrap">
                                Upload & Import
                            </button>
                        </div>
                        @error('upload')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror

                        {{-- Progress Bar --}}
                        <div x-show="uploading" class="mt-4 w-full bg-gray-200 rounded-full dark:bg-gray-700">
                            <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full"
                                :style="`width: ${progress}%`" x-text="`${progress}%`"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    // You could add a small temporary "Copied!" message here if you want
                }).catch(err => {
                    console.error('Failed to copy text: ', err);
                });
            }
        </script>
    @endpush
</div>
