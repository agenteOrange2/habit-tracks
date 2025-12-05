<?php

namespace App\Livewire\Rewards;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Reward;
use App\Services\{PointsService, FocusModeService};
use Illuminate\Support\Facades\Auth;

class RewardShop extends Component
{
    use WithPagination;

    public $categoryFilter = 'all';
    public $availablePoints;
    public $focusModeActive = false;

    protected $listeners = [
        'rewardClaimed' => '$refresh',
    ];

    public function mount(FocusModeService $focusModeService): void
    {
        $user = Auth::user();
        $this->availablePoints = $user->stats->available_points;
        $this->focusModeActive = $focusModeService->isActive($user);
    }

    public function setCategoryFilter($category): void
    {
        $this->categoryFilter = $category;
        $this->resetPage();
    }

    public function claimReward(
        Reward $reward,
        PointsService $pointsService,
        FocusModeService $focusModeService
    ): void {
        $user = Auth::user();

        // Verificar Focus Mode
        if (!$focusModeService->canAccessReward($user, $reward->category)) {
            session()->flash('error', '🔒 Modo Focus activo. Esta recompensa está bloqueada.');
            return;
        }

        // Verificar si puede canjear
        if (!$reward->canBeClaimed($user)) {
            session()->flash('error', '❌ No tienes suficientes puntos para esta recompensa.');
            return;
        }

        // Gastar puntos
        if ($pointsService->spendPoints($user, $reward->cost_points)) {
            // Crear claim
            $reward->claims()->create([
                'user_id' => $user->id,
                'points_spent' => $reward->cost_points,
                'claimed_at' => now(),
            ]);

            $this->availablePoints = $user->stats->available_points;

            session()->flash('success', "🎉 ¡Disfruta tu recompensa: {$reward->name}!");

            $this->dispatch('rewardClaimed');
        }
    }

    public function render()
    {
        $query = Auth::user()->rewards()
            ->where('is_available', true);

        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }

        $rewards = $query->latest()->paginate(12);

        return view('livewire.rewards.reward-shop', [
            'rewards' => $rewards,
        ])->layout('layouts.app');
    }
}