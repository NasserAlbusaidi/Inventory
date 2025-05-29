<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">
        {{ $supplierInstance && $supplierInstance->exists ? 'Edit Supplier' : 'Add New Supplier' }}
    </h1>

    <form wire:submit.prevent="saveSupplier" class="bg-white p-8 rounded-lg shadow-md max-w-3xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Supplier Name</label>
                <input type="text" id="name" wire:model.defer="name"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                <input type="text" id="contact_person" wire:model.defer="contact_person"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                @error('contact_person') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" id="email" wire:model.defer="email"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel" id="phone" wire:model.defer="phone"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="lead_time_days" class="block text-sm font-medium text-gray-700 mb-1">Lead Time (Days)</label>
                <input type="number" id="lead_time_days" wire:model.defer="lead_time_days"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                @error('lead_time_days') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-1">Payment Terms</label>
                <input type="text" id="payment_terms" wire:model.defer="payment_terms"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                       placeholder="e.g., Net 30, Due on Receipt">
                @error('payment_terms') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('suppliers.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ $supplierInstance && $supplierInstance->exists ? 'Update Supplier' : 'Create Supplier' }}
            </button>
        </div>
    </form>
</div>
