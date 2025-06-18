<?php

namespace App\Livewire\SalesChannel;

use App\Models\SalesChannel;
use Livewire\Component;

class SalesChannelForm extends Component
{
    public ?SalesChannel $salesChannelInstance = null;
    public string $name = '';

    protected function rules(): array
    {
        $channelId = $this->salesChannelInstance?->id;
        return [
            // The unique rule ignores the current record when editing
            'name' => 'required|string|max:255|unique:sales_channels,name' . ($channelId ? ',' . $channelId : ''),
        ];
    }

    public function mount($salesChannel = null)
    {
        if ($salesChannel) {
            // Using route model binding, $salesChannel is already an instance
            $this->salesChannelInstance = $salesChannel;
            $this->name = $this->salesChannelInstance->name;
        } else {
            $this->salesChannelInstance = new SalesChannel();
        }
    }

    public function saveSalesChannel()
    {
        $validatedData = $this->validate();

        if ($this->salesChannelInstance->exists) {
            $this->salesChannelInstance->update($validatedData);
            session()->flash('message', 'Sales Channel updated successfully.');
        } else {
            SalesChannel::create($validatedData);
            session()->flash('message', 'Sales Channel created successfully.');
        }

        return redirect()->route('sales-channels.index');
    }

    public function render()
    {
        return view('livewire.sales-channel.sales-channel-form')
            ->layoutData(['title' => $this->salesChannelInstance->exists ? 'Edit Sales Channel' : 'Create Sales Channel'])
            ->layout('components.layouts.livewire');
    }
}
