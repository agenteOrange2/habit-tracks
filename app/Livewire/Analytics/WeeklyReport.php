<?php

namespace App\Livewire\Analytics;

use Livewire\Component;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Auth;

class WeeklyReport extends Component
{
    public $stats;
    public $chartData;

    public function mount(StatisticsService $statisticsService): void
    {
        $user = Auth::user();
        $this->stats = $statisticsService->getWeeklyStats($user);
        
        // Preparar datos para el gráfico
        $this->chartData = [
            'labels' => collect($this->stats['daily_breakdown'])->pluck('date')->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('D');
            })->toArray(),
            'habits' => collect($this->stats['daily_breakdown'])->pluck('habits')->toArray(),
            'points' => collect($this->stats['daily_breakdown'])->pluck('points')->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.analytics.weekly-report')
            ->layout('layouts.app');
    }
}
