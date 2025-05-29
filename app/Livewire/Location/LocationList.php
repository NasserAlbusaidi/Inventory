<?php

namespace App\Livewire\Location;

use App\Models\Location; //
use Livewire\Component;
use Livewire\WithPagination;

class LocationList extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function deleteLocation($locationId)
    {
        $location = Location::find($locationId);
        if ($location) {
            // Consider implications: what happens to InventoryMovements or SalesOrders linked to this location?
            // The current migration for inventory_movements has onDelete('cascade') for location_id
            // and sales_orders has onDelete('set null') for location_id.
            // This means deleting a location will delete its inventory movements and set location_id to null for sales orders.
            // You might want to add a check here: if ($location->inventoryMovements()->count() > 0 || $location->salesOrders()->count() > 0) ...
            $location->delete();
            session()->flash('message', 'Location deleted successfully.');
        } else {
            session()->flash('error', 'Location not found.');
        }
        $this->resetPage();
    }

    public function render()
    {
        $locations = Location::withCount(['inventoryMovements', 'salesOrders']) // Eager load counts
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.location.location-list', [
            'locations' => $locations,
        ])->layout('components.layouts.livewire'); // Using the new Livewire layout
    }
}
