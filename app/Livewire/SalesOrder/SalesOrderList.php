<?php

namespace App\Livewire\SalesOrder;

use App\Models\SalesOrder; //
use Livewire\Component;
use Livewire\WithPagination;

class SalesOrderList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $channelFilter = '';

    // From SalesOrder model migration: status default 'completed', channel enum ['shopify', 'boutique', 'other']
    public $soStatuses = ['pending', 'processing', 'completed', 'shipped', 'cancelled', 'refunded']; // Example statuses
    public $soChannels = ['shopify', 'boutique', 'other'];


    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'channelFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function deleteSalesOrder($salesOrderId)
    {
        $salesOrder = SalesOrder::find($salesOrderId);
        if ($salesOrder) {
            // Consider rules for deletion: only if not 'completed' or 'shipped'?
            // Deleting SO will also delete its items due to onDelete('cascade')
            // This does NOT currently revert stock. Stock management for cancellations/returns is a more complex feature.
            if (in_array($salesOrder->status, ['pending', 'cancelled'])) {
                $salesOrder->items()->delete();
                $salesOrder->delete();
                session()->flash('message', 'Sales Order deleted successfully.');
            } else {
                session()->flash('error', 'Only pending or cancelled sales orders can be deleted. Consider cancelling or refunding.');
            }
        } else {
            session()->flash('error', 'Sales Order not found.');
        }
        $this->resetPage();
    }

    public function updatingSearch() {$this->resetPage();}
    public function updatingStatusFilter() {$this->resetPage();}
    public function updatingChannelFilter() {$this->resetPage();}


    public function render()
    {
        $salesOrders = SalesOrder::with(['location', 'items']) // Eager load location
            ->when($this->search, function ($query) {
                $query->where('order_number', 'like', '%' . $this->search . '%')
                    ->orWhereJsonContains('customer_details->name', $this->search) // Search by customer name in JSON
                    ->orWhereJsonContains('customer_details->email', $this->search);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->channelFilter, function ($query) {
                $query->where('channel', $this->channelFilter);
            })
            ->orderBy('order_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.sales-order.sales-order-list', [
            'salesOrders' => $salesOrders,
            // statuses and channels are public properties
        ])->layout('components.layouts.livewire');
    }
}
