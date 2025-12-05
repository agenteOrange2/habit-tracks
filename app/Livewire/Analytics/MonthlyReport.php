<?php

namespace App\Livewire\Analytics;

use Livewire\Component;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Auth;

class MonthlyReport extends Component
{
    public $stats;
    public $comparisonData;

    public function mount(StatisticsService $statisticsService): void
    {
        $user = Auth::user();
        $this->stats = $statisticsService->getMonthlyStats($user);
        $this->comparisonData = $this->stats['weekly_comparison'];
    }

    public function render()
    {
        return view('livewire.analytics.monthly-report')
            ->layout('layouts.app');
    }
}