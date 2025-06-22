<?php

namespace App\Livewire;

use Livewire\Component;

class ActivityLogs extends Component
{
    public function render()
    {
        // Fetch activity logs from the database
        $activityLogs = \App\Models\Activity::orderBy('created_at', 'desc')
            ->paginate(10); // Adjust pagination as needed

        return view('livewire.activity-logs')
            ->with('activityLogs', $activityLogs);
    }
}
