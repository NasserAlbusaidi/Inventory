<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">
        {{ $locationInstance && $locationInstance->exists  ? 'Edit Location' : 'Create New Location' }}
    </h1>

    <form wire:submit.prevent="saveLocation" class="bg-white p-8 rounded-lg shadow-md max-w-2xl mx-auto">
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Location Name</label>
            <input type="text" id="name" wire:model.defer="name"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                   placeholder="e.g., Main Warehouse, Downtown Boutique">
            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea id="description" wire:model.defer="description" rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                      placeholder="Optional: A brief description of the location or its purpose."></textarea>
            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('locations.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ $locationInstance && $locationInstance->exists  ? 'Update Location' : 'Create Location' }}
            </button>
        </div>
    </form>
</div>
