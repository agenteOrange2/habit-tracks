<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RecentNotes extends Component
{
    public function render()
    {
        $recentNotes = Auth::user()->notes()
            ->latest('updated_at')
            ->take(3)
            ->get();

        return view('livewire.dashboard.recent-notes', [
            'notes' => $recentNotes,
            'hasNotes' => $recentNotes->isNotEmpty(),
        ]);
    }
}
