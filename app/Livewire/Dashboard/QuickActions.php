<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class QuickActions extends Component
{
    public $actions = [
        [
            'title' => 'Nuevo Hábito',
            'icon' => '➕',
            'route' => 'admin.habits.create',
            'color' => 'blue',
        ],
        [
            'title' => 'Pomodoro',
            'icon' => '🍅',
            'route' => 'admin.pomodoro',
            'color' => 'red',
        ],
        [
            'title' => 'Recompensas',
            'icon' => '🎁',
            'route' => 'rewards.index',
            'color' => 'purple',
        ],
        [
            'title' => 'Diario',
            'icon' => '📝',
            'route' => 'admin.journal.create',
            'color' => 'green',
        ],
    ];

    public function render()
    {
        return view('livewire.dashboard.quick-actions');
    }
}