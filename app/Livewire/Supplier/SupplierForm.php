<?php

namespace App\Livewire\Supplier;

use App\Models\Activity;
use App\Models\Supplier;
use Livewire\Component;

class SupplierForm extends Component
{
    public ?Supplier $supplierInstance = null;
    public $name = '';
    public $contact_person = '';
    public $email = '';
    public $phone = '';
    public $lead_time_days = null;
    public $payment_terms = '';

    protected function rules(): array
    {
        $supplierId = $this->supplierInstance && $this->supplierInstance->exists ? $this->supplierInstance->id : null;
        return [
            'name' => 'required|string|max:255|unique:suppliers,name' . ($supplierId ? ',' . $supplierId : ''),
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email' . ($supplierId ? ',' . $supplierId : ''),
            'phone' => 'nullable|string|max:50',
            'lead_time_days' => 'nullable|integer|min:0',
            'payment_terms' => 'nullable|string|max:255',
        ];
    }

    public function mount($supplier = null) // $supplier can be an ID or null
    {
        if ($supplier) {
            $this->supplierInstance = Supplier::find($supplier);
            if ($this->supplierInstance) {
                $this->name = $this->supplierInstance->name;
                $this->contact_person = $this->supplierInstance->contact_person;
                $this->email = $this->supplierInstance->email;
                $this->phone = $this->supplierInstance->phone;
                $this->lead_time_days = $this->supplierInstance->lead_time_days;
                $this->payment_terms = $this->supplierInstance->payment_terms;
            } else {
                $this->supplierInstance = new Supplier(); // Not found by ID, treat as create
            }
        } else {
            $this->supplierInstance = new Supplier();
        }
    }

    public function saveSupplier()
    {
        $this->validate();

        if (!$this->supplierInstance) {
            $this->supplierInstance = new Supplier();
        }

        $this->supplierInstance->fill([
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'phone' => $this->phone,
            'lead_time_days' => $this->lead_time_days ?? 5,
            'payment_terms' => $this->payment_terms ?? 'Net 30',
        ]);
        $this->supplierInstance->save();
        Activity::create([
            'type' => $this->supplierInstance->wasRecentlyCreated ? 'supplier_created' : 'supplier_updated',
            'description' => 'Supplier ' . ($this->supplierInstance->wasRecentlyCreated ? 'created' : 'updated') . ': ' . $this->name,
        ]);
        session()->flash('message', 'Supplier ' . ($this->supplierInstance->wasRecentlyCreated ? 'created' : 'updated') . ' successfully.');

        return redirect()->route('suppliers.index');
    }

    public function render()
    {
        return view('livewire.supplier.supplier-form')
            ->layoutData(['title' => $this->supplierInstance && $this->supplierInstance->exists ? 'Edit Supplier' : 'Create Supplier'])
            ->layout('components.layouts.livewire');
    }
}
