<?php

namespace App\Livewire\Location;

use App\Models\Activity;
use App\Models\Location;
use Livewire\Component;

class LocationForm extends Component
{
    public ?Location $locationInstance = null; // Renamed to avoid conflict with potential route parameter name
    public $name = '';
    public $description = '';

    protected function rules(): array
    {
        $locationId = $this->locationInstance && $this->locationInstance->exists ? $this->locationInstance->id : null;
        return [
            'name' => 'required|string|max:255|unique:locations,name' . ($locationId ? ',' . $locationId : ''),
            'description' => 'nullable|string|max:1000',
        ];
    }

    // Accept an optional ID instead of direct model binding for mount
    public function mount($location = null) // $location here can be an ID for edit, or null for create
    {
        if ($location) { // If an ID is passed (from an edit route for example)
            $this->locationInstance = Location::find($location); // $location is treated as an ID here.
            // For route model binding to still work on edit,
            // the route parameter name must match 'location'.
            if ($this->locationInstance) {
                $this->name = $this->locationInstance->name;
                $this->description = $this->locationInstance->description;
            } else {
                // Handle case where ID is passed but location not found, e.g., redirect or error
                // For now, treat as creation if not found by ID for simplicity
                $this->locationInstance = new Location();
            }
        } else { // No ID passed, so it's a create form
            $this->locationInstance = new Location();
        }
    }


    public function saveLocation()
    {
        $this->validate();

        // Ensure locationInstance is not null before saving
        if (!$this->locationInstance) {
            $this->locationInstance = new Location();
        }

        $this->locationInstance->name = $this->name;
        $this->locationInstance->description = $this->description;
        $this->locationInstance->save();

        // Save Activity Log
        Activity::create([
            'type' => $this->locationInstance->wasRecentlyCreated ? 'location_created' : 'location_updated',
            'description' => 'Location ' . ($this->locationInstance->wasRecentlyCreated ? 'created' : 'updated') . ': ' . $this->name,
        ]);

        session()->flash('message', 'Location ' . ($this->locationInstance->wasRecentlyCreated ? 'created' : 'updated') . ' successfully.');

        return redirect()->route('locations.index');
    }

    public function render()
    {
        return view('livewire.location.location-form')
            ->layoutData(['title' => $this->locationInstance && $this->locationInstance->exists ? 'Edit Location' : 'Create Location'])
            ->layout('components.layouts.livewire');
    }
}
