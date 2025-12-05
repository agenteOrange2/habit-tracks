<?php

namespace App\Livewire\Analytics;

use Livewire\Component;
use App\Services\StatisticsService;
use App\Enums\HabitCategory;
use Illuminate\Support\Facades\Auth;

class CategoryChart extends Component
{
    public $categoryData = [];
    public $period = 'week'; // week, month, all

    public function mount(StatisticsService $statisticsService): void
    {
        $this->loadCategoryData($statisticsService);
    }

    public function setPeriod($period, StatisticsService $statisticsService): void
    {
        $this->period = $period;
        $this->loadCategoryData($statisticsService);
    }

    private function loadCategoryData(StatisticsService $statisticsService): void
    {
        $user = Auth::user();
        
        $dateRange = match($this->period) {
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'all' => [null, null],
        };

        $stats = $this->period === 'week' 
            ? $statisticsService->getWeeklyStats($user)
            : $statisticsService->getMonthlyStats($user);

        $distribution = $stats['category_distribution'] ?? [];

        // Preparar datos para el gráfico
        $this->categoryData = collect($distribution)->map(function ($item) {
            $category = HabitCategory::from($item->category);
            return [
                'category' => $category->label(),
                'count' => $item->count,
                'color' => $category->color(),
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.analytics.category-chart');
    }
}