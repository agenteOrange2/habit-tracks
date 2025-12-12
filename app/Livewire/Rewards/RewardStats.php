<?php

namespace App\Livewire\Rewards;

use Livewire\Component;
use App\Services\StatisticsService;
use App\Enums\RewardCategory;
use Illuminate\Support\Facades\Auth;

class RewardStats extends Component
{
    public int $totalPointsSpent = 0;
    public int $totalRewardsClaimed = 0;
    public float $averagePointsPerClaim = 0;
    public array $mostClaimedCategories = [];
    public array $mostClaimedRewards = [];

    public function mount(StatisticsService $statisticsService): void
    {
        $user = Auth::user();
        
        $this->totalPointsSpent = $statisticsService->getTotalPointsSpent($user);
        $this->totalRewardsClaimed = $statisticsService->getTotalRewardsClaimed($user);
        $this->averagePointsPerClaim = $statisticsService->getAveragePointsPerClaim($user);
        
        // Transform categories for display
        $categories = $statisticsService->getMostClaimedCategories($user);
        $this->mostClaimedCategories = $categories->map(function ($item) {
            $category = RewardCategory::tryFrom($item->category);
            return [
                'name' => $category?->label() ?? $item->category,
                'icon' => $category?->icon() ?? '📦',
                'color' => $category?->color() ?? 'bg-gray-100 text-gray-800',
                'count' => $item->claim_count,
            ];
        })->toArray();
        
        // Transform rewards for display
        $rewards = $statisticsService->getMostClaimedRewards($user);
        $this->mostClaimedRewards = $rewards->map(function ($item) {
            return [
                'name' => $item->reward?->name ?? 'Recompensa eliminada',
                'icon' => $item->reward?->icon ?? '🎁',
                'category' => $item->reward?->category?->label() ?? 'Sin categoría',
                'count' => $item->claim_count,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.rewards.reward-stats')
            ->layout('components.layouts.app');
    }
}
