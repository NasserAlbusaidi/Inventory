<?php

namespace App\Livewire\PurchaseOrder;

use App\Models\Activity;
use App\Models\Location;
use App\Models\PurchaseOrder; //
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseOrderList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public $allSuppliers = [];
    public $allLocations = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        $this->allSuppliers = Supplier::all();
        $this->allLocations = Location::all();

    }

    public function deletePurchaseOrder($purchaseOrderId)
    {
        $purchaseOrder = PurchaseOrder::find($purchaseOrderId);
        if ($purchaseOrder) {
            // Deleting a PO will also delete its items due to onDelete('cascade')
            // Consider implications if items were partially received.
            // Usually, 'cancelled' status is preferred over hard deletion for auditable records.
            if ($purchaseOrder->status === 'draft' || $purchaseOrder->status === 'cancelled') {
                $orderID = $purchaseOrder->order_number;
                $purchaseOrder->items()->delete();
                $purchaseOrder->delete();
                Activity::create([
                    'type' => 'purchase_order_deleted',
                    'description' => "Purchase Order #{$orderID} deleted.",
                ]);
                session()->flash('message', 'Purchase Order deleted successfully.');
            } else {
                session()->flash('error', 'Only draft or cancelled purchase orders can be deleted. Consider cancelling it instead.');
            }
        } else {
            session()->flash('error', 'Purchase Order not found.');
        }
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {


        $purchaseOrders = PurchaseOrder::with(['supplier', 'items']) // Eager load supplier and items
            ->when($this->search, function ($query) {
                $query->where('order_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('order_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // For status filter dropdown
        // The PurchaseOrder model migration defines status enum:
        // ['draft', 'approved', 'ordered', 'partially_received', 'received', 'cancelled']
        // (Assuming 'approved' was added in previous steps)
        $statuses = ['draft', 'approved', 'ordered', 'partially_received', 'received', 'cancelled'];


        return view('livewire.purchase-order.purchase-order-list', [
            'purchaseOrders' => $purchaseOrders,
            'statuses' => $statuses,
        ])->layout('components.layouts.livewire');
    }
}
