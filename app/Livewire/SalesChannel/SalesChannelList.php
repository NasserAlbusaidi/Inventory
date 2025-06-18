<?php

namespace App\Livewire\SalesChannel;

use App\Models\SalesChannel;
use Livewire\Component;
use Livewire\WithPagination;

class SalesChannelList extends Component
{
    use WithPagination;

    public function delete(SalesChannel $salesChannel)
    {
        $salesChannel->delete();
        session()->flash('message', 'Sales Channel deleted successfully.');
    }

    public function render()
    {
        $salesChannels = SalesChannel::latest()->paginate(10);
        return view('livewire.sales-channel.sales-channel-list', [
            'salesChannels' => $salesChannels
        ])
            ->layoutData(['title' => 'Sales Channels'])
            ->layout('components.layouts.livewire');
    }
}
