<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Services\EnergyService;
use Illuminate\Support\Facades\Auth;

class EnergyBar extends Component
{
    public $energyStatus;

    protected $listeners = [
        'energyUpdated' => 'refresh',
    ];

    public function mount(EnergyService $energyService): void
    {
        $this->refresh($energyService);
    }

    public function refresh(EnergyService $energyService): void
    {
        $this->energyStatus = $energyService->getEnergyStatus(Auth::user());
    }

    public function render()
    {
        return view('livewire.dashboard.energy-bar');
    }
}