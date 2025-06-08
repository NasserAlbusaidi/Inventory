<div>
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Application Settings</h1>

    @if (session()->has('success'))
        <div class="mt-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="mt-6 space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">

        {{-- Monthly Revenue Target --}}
        <div>
            <label for="monthly_revenue_target" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Monthly Revenue Target (OMR)</label>
            <input type="number" id="monthly_revenue_target" wire:model="monthly_revenue_target" class="mt-1 block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
            @error('monthly_revenue_target') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Profit Margin Target --}}
        <div>
            <label for="profit_margin_target" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profit Margin Target (%)</label>
            <input type="number" id="profit_margin_target" wire:model="profit_margin_target" class="mt-1 block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
            @error('profit_margin_target') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Low Stock Threshold --}}
        <div>
            <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Low Stock Threshold (Units)</label>
            <input type="number" id="low_stock_threshold" wire:model="low_stock_threshold" class="mt-1 block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
            @error('low_stock_threshold') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                Save Settings
            </button>
        </div>
    </form>
</div>
