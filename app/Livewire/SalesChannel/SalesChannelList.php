<?php

namespace App\Livewire\SalesChannel;

use App\Models\Activity;
use App\Models\Location;
use App\Models\SalesChannel;
use Livewire\Component;
use Livewire\WithPagination;

class SalesChannelList extends Component
{
    use WithPagination;

    public function delete(SalesChannel $salesChannel)
    {
        $salesChannelName = $salesChannel->name;
        $salesChannel->delete();
        Activity::create([
            'type' => 'sales_channel_deleted',
            'description' => "Sales Channel '{$salesChannelName}' deleted.",
        ]);
        session()->flash('message', 'Sales Channel deleted successfully.');
    }

    public function addSalesChannel($location)
    {
        $locationInstance = Location::where('name', $location)->first();
        if ($locationInstance) {
            $salesChannel = SalesChannel::where('name', $location)->first();
            if (!$salesChannel) {
                $salesChannel = SalesChannel::create(['name' => $location]);
            }
            Activity::create([
                'type' => 'sales_channel_created',
                'description' => "Sales Channel '{$salesChannel->name}' created for location '{$locationInstance->name}'.",
            ]);
            session()->flash('message', 'Sales Channel added successfully.');
        } else {
            session()->flash('error', 'Location not found.');
        }
        return redirect()->route('sales-channels.index');
    }

    public function render()
    {
        $allSalesChannels = SalesChannel::all()->pluck('name');
        $allLocations = Location::all()->pluck('name');
        // compare the sales channels with locations
        $missingSalesChannel = $allLocations->diff($allSalesChannels);

        $salesChannels = SalesChannel::latest()->paginate(10);

        return view('livewire.sales-channel.sales-channel-list', [
            'salesChannels' => $salesChannels,
            'missingSalesChannel' => $missingSalesChannel,
        ])
            ->layoutData(['title' => 'Sales Channels'])
            ->layout('components.layouts.livewire');
    }
}
