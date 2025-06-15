<?php

namespace App\Livewire\Location;

use App\Models\Location;
use App\Models\LocationInventory; // <-- Add this
use Livewire\Component;
use Livewire\WithPagination;

class LocationList extends Component
{
    // Use two separate pagination traits to avoid conflicts
    use WithPagination;

    public $search = '';
    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    // --- (NEW) Modal Properties ---
    public bool $showProductsModal = false;
    public ?Location $viewingLocation = null;

    // We will paginate the products inside the modal
    // To avoid conflicts with the main pagination, we give it a name
    protected $paginationTheme = 'tailwind';


    public function deleteLocation($locationId)
    {
        $location = Location::find($locationId);

        // (NEW) Add a check to prevent deleting locations with stock
        if ($location && $location->inventories()->where('stock_quantity', '>', 0)->exists()) {
            session()->flash('error', "Cannot delete '{$location->name}' because it still has stock. Please move or adjust the inventory first.");
            return;
        }

        if ($location) {
            $location->delete();
            session()->flash('message', 'Location deleted successfully.');
        } else {
            session()->flash('error', 'Location not found.');
        }
        $this->resetPage();
    }


    // --- (NEW) Method to open the modal and load products ---
    public function showProducts(Location $location)
    {
        $this->viewingLocation = $location;
        $this->showProductsModal = true;

        // Reset to the first page of the modal's pagination each time it's opened
        $this->resetPage('productsPage');
    }

    // --- (NEW) Method to close the modal ---
    public function closeModal()
    {
        $this->showProductsModal = false;
        $this->viewingLocation = null;
    }


    public function render()
    {
        // --- (UPDATED) Query for the main locations list ---
        $locations = Location::query()
            // Eager load counts for related models
            ->withCount(['inventoryMovements', 'salesOrders'])
            // (NEW) Sum the stock_quantity from the related inventories table
            ->withSum('inventories as total_stock', 'stock_quantity')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10, ['*'], 'page'); // Main pagination

        // --- (NEW) Query for the products inside the modal ---
        $productsInLocation = [];
        if ($this->viewingLocation) {
            $productsInLocation = LocationInventory::where('location_id', $this->viewingLocation->id)
                ->where('stock_quantity', '>', 0)
                ->with('inventoriable')
                ->paginate(5, ['*'], 'productsPage');

        }




        return view('livewire.location.location-list', [
            'locations' => $locations,
            'productsInLocation' => $productsInLocation, // Pass to the view
        ])->layout('components.layouts.livewire');
    }
}
