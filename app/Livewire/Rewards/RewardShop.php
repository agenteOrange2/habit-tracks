<?php

namespace App\Livewire\Rewards;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Reward;
use App\Enums\RewardCategory;
use App\Services\{PointsService, FocusModeService};
use Illuminate\Support\Facades\Auth;

class RewardShop extends Component
{
    use WithPagination;

    public string $categoryFilter = 'all';
    public int $availablePoints = 0;
    public bool $focusModeActive = false;
    public array $blockedCategories = [];

    protected $listeners = [
        'rewardClaimed' => '$refresh',
        'pointsUpdated' => 'refreshPoints',
    ];

    public function mount(FocusModeService $focusModeService): void
    {
        $user = Auth::user();
        $this->availablePoints = $user->stats->available_points ?? 0;
        $this->focusModeActive = $focusModeService->isActive($user);
        
        // Get blocked categories if Focus Mode is active
        $activeFocusMode = $focusModeService->getActiveFocusMode($user);
        $this->blockedCategories = $activeFocusMode?->blocked_categories ?? [];
    }

    public function refreshPoints(): void
    {
        $this->availablePoints = Auth::user()->stats->available_points ?? 0;
    }

    public function setCategoryFilter(string $category): void
    {
        $this->categoryFilter = $category;
        $this->resetPage();
    }

    public function isRewardBlocked(Reward $reward): bool
    {
        if (!$this->focusModeActive) {
            return false;
        }
        
        $categoryValue = $reward->category instanceof RewardCategory 
            ? $reward->category->value 
            : $reward->category;
            
        return in_array($categoryValue, $this->blockedCategories);
    }

    public function canAfford(Reward $reward): bool
    {
        return $this->availablePoints >= $reward->cost_points;
    }

    public function wasClaimedToday(Reward $reward): bool
    {
        return $reward->claims()
            ->where('user_id', Auth::id())
            ->whereDate('claimed_at', today())
            ->exists();
    }

    public function getProgressPercentage(Reward $reward): int
    {
        if ($reward->cost_points <= 0) {
            return 100;
        }
        
        $percentage = ($this->availablePoints / $reward->cost_points) * 100;
        return min(100, (int) $percentage);
    }

    public function claimReward(
        Reward $reward,
        PointsService $pointsService,
        FocusModeService $focusModeService
    ): void {
        $user = Auth::user();

        // Verify ownership
        if ($reward->user_id !== $user->id) {
            session()->flash('error', '❌ No puedes canjear esta recompensa.');
            return;
        }

        // Check Focus Mode
        $categoryValue = $reward->category instanceof RewardCategory 
            ? $reward->category->value 
            : $reward->category;
            
        if (!$focusModeService->canAccessReward($user, $categoryValue)) {
            session()->flash('error', '🔒 Modo Focus activo. Esta recompensa está bloqueada.');
            return;
        }

        // Check availability
        if (!$reward->is_available) {
            session()->flash('error', '❌ Esta recompensa no está disponible.');
            return;
        }

        // Check if already claimed today
        if ($this->wasClaimedToday($reward)) {
            session()->flash('error', '⏰ Ya canjeaste esta recompensa hoy. Vuelve mañana.');
            return;
        }

        // Check points
        if (!$reward->canBeClaimed($user)) {
            session()->flash('error', '❌ No tienes suficientes puntos para esta recompensa.');
            return;
        }

        // Spend points and create claim
        if ($pointsService->spendPoints($user, $reward->cost_points)) {
            $reward->claims()->create([
                'user_id' => $user->id,
                'points_spent' => $reward->cost_points,
                'claimed_at' => now(),
            ]);

            $user->refresh();
            $this->availablePoints = $user->stats->available_points;

            session()->flash('success', "🎉 ¡Disfruta tu recompensa: {$reward->name}!");

            $this->dispatch('rewardClaimed');
            $this->dispatch('pointsUpdated');
        } else {
            session()->flash('error', '❌ Error al procesar el canje. Intenta de nuevo.');
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
            'categories' => RewardCategory::cases(),
        ])->layout('components.layouts.app');
    }
}