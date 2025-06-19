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
                    Manage the platforms where sales occur, such as 'Online Store' or 'Boutique'
                </p>
                @if ($missingSalesChannel->isNotEmpty())
                    {{-- Improved Notification Block --}}
                    <div
                        class="mt-4 p-4 rounded-lg bg-yellow-50 dark:bg-yellow-800/20 border-l-4 border-yellow-400 dark:border-yellow-500">
                        <div class="flex">
                            {{-- Icon --}}
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-500"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.636-1.214 2.43-1.214 3.066 0l5.857 11.157a1.75 1.75 0 01-1.533 2.493H3.934a1.75 1.75 0 01-1.533-2.493L8.257 3.099zM9 12a1 1 0 112 0 1 1 0 01-2 0zm1-4a1 1 0 00-1 1v2a1 1 0 102 0V9a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            {{-- Text Content and Actions --}}
                            <div class="ml-3 flex-1 md:flex md:justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">
                                        Potential Sales Channels Found
                                    </h3>
                                    <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                        The following locations are not yet set up as sales channels. Click a name to
                                        add it.
                                    </p>
                                    {{-- Actionable Tags --}}
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach ($missingSalesChannel as $location)
                                            <button type="button" wire:click="addSalesChannel('{{ $location }}')"
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-500/20 dark:text-yellow-200 dark:hover:bg-yellow-500/30 transition-colors">
                                                <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                                </svg>
                                                {{ $location }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Action Button --}}
            <a href="{{ route('sales-channels.create') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2 -ml-1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Add New Channel
            </a>
        </div>

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 dark:bg-green-800/20 dark:border-green-600 dark:text-green-300 px-4 py-3 rounded-lg relative"
                role="alert">
                {{ session('message') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Name</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Date Added</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($salesChannels as $channel)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $channel->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $channel->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('sales-channels.edit', $channel) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">Edit</a>
                                <button wire:click="delete({{ $channel->id }})"
                                    wire:confirm="Are you sure you want to delete this sales channel?"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">Delete</button>
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
