<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Sales Orders</h1>
        <a href="{{ route('sales-orders.create') }}"
           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            Create New SO
        </a>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-1">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search SO #, Customer..."
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
        </div>
        <div>
            <select wire:model.live="channelFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-10">
                <option value="">All Channels</option>
                @foreach($soChannels as $channel)
                    <option value="{{ $channel }}">{{ ucfirst($channel) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select wire:model.live="statusFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 h-10">
                <option value="">All Statuses</option>
                 @foreach($soStatuses as $status)
                    <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SO #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Channel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total (OMR)</th>
                    <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($salesOrders as $so)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 hover:text-indigo-900">
                            <a href="{{ route('sales-orders.edit', $so) }}">{{ $so->order_number }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $so->customer_details['name'] ?? ($so->customer_details['email'] ?? 'N/A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($so->channel) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $so->order_date ? \Carbon\Carbon::parse($so->order_date)->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @switch($so->status)
                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                    @case('processing') bg-blue-100 text-blue-800 @break
                                    @case('completed') bg-green-100 text-green-800 @break
                                    @case('shipped') bg-purple-100 text-purple-800 @break
                                    @case('cancelled') bg-red-100 text-red-800 @break
                                    @case('refunded') bg-gray-100 text-gray-800 @break
                                    @default bg-gray-200 text-gray-700
                                @endswitch
                            ">
                                {{ ucfirst(str_replace('_', ' ', $so->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($so->total_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('sales-orders.edit', $so) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View/Edit</a>
                            @if(in_array($so->status, ['pending', 'cancelled']))
                                <button wire:click="deleteSalesOrder({{ $so->id }})"
                                        wire:confirm="Are you sure you want to delete this Sales Order?"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            No sales orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $salesOrders->links() }}
    </div>
</div>
