<div class="py-8">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start mb-6 gap-4">
            {{-- Group for Heading and Description --}}
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    Sales Channels
                </h1>
                {{-- (NEW) Description --}}
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage the platforms where sales occur, such as 'Online Store' or 'In-Person'.
                </p>
            </div>

            {{-- Action Button --}}
            <a href="{{ route('sales-channels.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Add New Channel
            </a>
        </div>

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 dark:bg-green-800/20 dark:border-green-600 dark:text-green-300 px-4 py-3 rounded-lg relative" role="alert">
                {{ session('message') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date Added</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($salesChannels as $channel)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $channel->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $channel->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('sales-channels.edit', $channel) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">Edit</a>
                            <button wire:click="delete({{ $channel->id }})" wire:confirm="Are you sure you want to delete this sales channel?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                            No sales channels found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($salesChannels->hasPages())
            <div class="mt-6">
                {{ $salesChannels->links() }}
            </div>
        @endif
    </div>
</div>
