<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Auth;

class StreakCalendar extends Component
{
    public $heatmapData;
    public $year;

    public function mount(StatisticsService $statisticsService): void
    {
        $this->year = now()->year;
        $this->heatmapData = $statisticsService->getHeatmapData(Auth::user(), 365);
    }

    public function render()
    {
        return view('livewire.dashboard.streak-calendar');
    }
}