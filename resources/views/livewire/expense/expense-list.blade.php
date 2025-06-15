<div class="py-8">
    {{-- On mobile, use less horizontal padding for more space --}}
    <div class="max-w-full mx-auto px-2 sm:px-6 lg:px-8">

        {{-- =================================================== --}}
        {{-- Header Section --}}
        {{-- =================================================== --}}
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Expenses</h1>
            <a href="{{ route('expenses.create') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 -ml-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Add New Expense
            </a>
        </div>

        {{-- =================================================== --}}
        {{-- Flash Messages --}}
        {{-- =================================================== --}}
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 dark:bg-green-800/50 border-l-4 border-green-400 dark:border-green-600 p-4 shadow-md rounded-md" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0"><svg class="h-5 w-5 text-green-400 dark:text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg></div>
                    <div class="ml-3"><p class="text-sm text-green-700 dark:text-green-200">{{ session('message') }}</p></div>
                </div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-800/50 border-l-4 border-red-400 dark:border-red-600 p-4 shadow-md rounded-md" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400 dark:text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg></div>
                    <div class="ml-3"><p class="text-sm text-red-700 dark:text-red-200">{{ session('error') }}</p></div>
                </div>
            </div>
        @endif

        {{-- =================================================== --}}
        {{-- Filters Section --}}
        {{-- =================================================== --}}
        <div class="mb-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
            <div class="max-w-md">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Expenses</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                    </div>
                    <input type="text" id="search" wire:model.live.debounce.300ms="search" placeholder="Search by description or location..."
                        class="form-input block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
        </div>

        {{-- =================================================== --}}
        {{-- Expenses List (Desktop Table + Mobile Cards) --}}
        {{-- =================================================== --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
            {{-- Desktop Table View --}}
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 hidden md:table">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Location</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($expenses as $expense)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $expense->description }}</div></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($expense->type === 'recurring')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800/50 dark:text-blue-200">Recurring</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/50 dark:text-green-200">One-Time</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $expense->location?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 text-right font-mono">OMR {{ number_format($expense->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                <a href="{{ route('expenses.edit', ['type' => $expense->type, 'id' => $expense->id]) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold">Edit</a>
                                <button wire:click="delete({{ $expense->id }}, '{{ $expense->type }}')" wire:confirm="Are you sure?" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-semibold">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No expenses found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Mobile Card View --}}
            <div class="md:hidden">
                <div class="px-2 py-2 space-y-3">
                    @forelse ($expenses as $expense)
                        <div class="bg-white dark:bg-gray-800/50 p-4 rounded-lg shadow ring-1 ring-gray-200 dark:ring-gray-700">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900 dark:text-gray-100">{{ $expense->description }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M, Y') }} • {{ $expense->location?->name ?? 'N/A' }}</p>
                                </div>
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 p-1 -m-1 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-900 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-20">
                                        <a href="{{ route('expenses.edit', ['type' => $expense->type, 'id' => $expense->id]) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">Edit Expense</a>
                                        <button wire:click="delete({{ $expense->id }}, '{{ $expense->type }}')" wire:confirm="Are you sure?" class="w-full text-left block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-800">Delete</button>
                                    </div>
                                </div>
                            </div>
                             <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                                <div class="flex justify-between items-center text-sm">
                                    <p class="text-gray-500 dark:text-gray-400">Amount</p>
                                    <p class="font-semibold font-mono text-gray-800 dark:text-gray-200">OMR {{ number_format($expense->amount, 2) }}</p>
                                </div>
                                <div class="flex justify-between items-center text-sm mt-1">
                                     <p class="text-gray-500 dark:text-gray-400">Type</p>
                                     @if ($expense->type === 'recurring')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800/50 dark:text-blue-200">Recurring</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/50 dark:text-green-200">One-Time</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-sm text-gray-500 dark:text-gray-400">
                             <div class="flex flex-col items-center">
                                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75-.75v-.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                No expenses found. Click "Add New Expense" to get started.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($expenses->hasPages())
            <div class="mt-6 px-2">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
