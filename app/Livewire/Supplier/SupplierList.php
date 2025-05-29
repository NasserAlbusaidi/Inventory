<?php

namespace App\Livewire\Supplier;

use App\Models\Supplier; //
use Livewire\Component;
use Livewire\WithPagination;

class SupplierList extends Component
{
    use WithPagination;

    public $search = '';
    protected $queryString = ['search'];

    public function deleteSupplier($supplierId)
    {
        $supplier = Supplier::find($supplierId);
        if ($supplier) {
            // Consider implications: The purchase_orders table has onDelete('cascade') for supplier_id.
            // This means deleting a supplier will also delete all their associated purchase orders.
            // This might be desired, or you might want to add a check here if $supplier->purchaseOrders()->count() > 0
            $supplier->delete();
            session()->flash('message', 'Supplier and their associated purchase orders deleted successfully.');
        } else {
            session()->flash('error', 'Supplier not found.');
        }
        $this->resetPage();
    }

    public function render()
    {
        $suppliers = Supplier::withCount('purchaseOrders') // Eager load purchase order count
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('contact_person', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.supplier.supplier-list', [
            'suppliers' => $suppliers,
        ])->layout('components.layouts.livewire');
    }
}
