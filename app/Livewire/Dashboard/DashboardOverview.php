<?php
// app/Livewire/Dashboard/DashboardOverview.php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Services\{StatisticsService, EnergyService};
use Illuminate\Support\Facades\Auth;

class DashboardOverview extends Component
{
    public $todayHabits;
    public $completedToday = 0;
    public $totalToday = 0;
    public $currentStreak;
    public $availablePoints;
    public $energyStatus;
    public $weeklyStats;

    protected $listeners = [
        'habitCompleted' => 'refreshDashboard',
        'habitUncompleted' => 'refreshDashboard',
    ];

    public function mount(
        StatisticsService $statisticsService,
        EnergyService $energyService
    ): void {
        $this->loadDashboardData($statisticsService, $energyService);
    }

    public function refreshDashboard(
        StatisticsService $statisticsService,
        EnergyService $energyService
    ): void {
        $this->loadDashboardData($statisticsService, $energyService);
    }

    private function loadDashboardData(
        StatisticsService $statisticsService,
        EnergyService $energyService
    ): void {
        $user = Auth::user();

        // Hábitos de hoy
        $this->todayHabits = $user->habits()
            ->where('is_active', true)
            ->get()
            ->filter->isScheduledForToday();

        $this->totalToday = $this->todayHabits->count();
        $this->completedToday = $this->todayHabits->filter->isCompletedToday()->count();

        // Stats (with null safety)
        $this->currentStreak = $user->stats?->current_global_streak ?? 0;
        $this->availablePoints = $user->stats?->available_points ?? 0;

        // Energía
        $this->energyStatus = $energyService->getEnergyStatus($user);

        // Stats semanales
        $this->weeklyStats = $statisticsService->getWeeklyStats($user);
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-overview')
            ->layout('layouts.app');
    }
}