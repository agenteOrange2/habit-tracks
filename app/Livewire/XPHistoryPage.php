<?php

namespace App\Livewire;

use Livewire\Component;

class XPHistoryPage extends Component
{
    public function render()
    {
        return view('livewire.xp-history-page')
            ->layout('components.layouts.app');
    }
}
