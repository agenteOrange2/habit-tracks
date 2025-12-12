<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Reward;
use App\Services\{PointsService, FocusModeService};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

/**
 * ActiveRewards Component
 * 
 * Displays currently active/available rewards for the authenticated user.
 * Prioritizes affordable rewards and shows progress indicators.
 * Automatically refreshes when the 'rewardClaimed' event is dispatched.
 * 
 * Usage in Blade:
 * <livewire:dashboard.active-rewards />
 * 
 * Requirements: 8.1, 8.2, 8.3, 8.4, 8.5
 */
class ActiveRewards extends Component
{
    public Collection $rewards;
    public int $availablePoints = 0;
    public bool $hasRewards = false;

    public function mount(): void
    {
        $this->loadRewards();
    }

    #[On('rewardClaimed')]
    #[On('pointsUpdated')]
    public function refreshRewards(): void
    {
        $this->loadRewards();
    }

    protected function loadRewards(): void
    {
        $user = Auth::user();
        $this->availablePoints = $user->stats->available_points ?? 0;
        
        // Get all available rewards
        $allRewards = $user->rewards()
            ->where('is_available', true)
            ->get();
        
        $this->hasRewards = $allRewards->isNotEmpty();
        
        // Separate affordable and unaffordable rewards
        $affordable = $allRewards->filter(fn ($r) => $r->cost_points <= $this->availablePoints)
            ->sortBy('cost_points');
        $unaffordable = $allRewards->filter(fn ($r) => $r->cost_points > $this->availablePoints)
            ->sortBy('cost_points');
        
        // Prioritize affordable rewards, then fill with closest unaffordable
        $this->rewards = $affordable->merge($unaffordable)->take(3)->values();
    }

    public function canAfford(Reward $reward): bool
    {
        return $this->availablePoints >= $reward->cost_points;
    }

    public function getProgressPercentage(Reward $reward): int
    {
        if ($reward->cost_points <= 0) {
            return 100;
        }
        
        $percentage = ($this->availablePoints / $reward->cost_points) * 100;
        return min(100, (int) $percentage);
    }

    public function getPointsNeeded(Reward $reward): int
    {
        return max(0, $reward->cost_points - $this->availablePoints);
    }

    public function wasClaimedToday(Reward $reward): bool
    {
        return $reward->claims()
            ->where('user_id', Auth::id())
            ->whereDate('claimed_at', today())
            ->exists();
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
        $categoryValue = $reward->category->value ?? $reward->category;
        if (!$focusModeService->canAccessReward($user, $categoryValue)) {
            session()->flash('error', '🔒 Modo Focus activo. Esta recompensa está bloqueada.');
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

            session()->flash('success', "🎉 ¡Disfruta tu recompensa: {$reward->name}!");

            $this->dispatch('rewardClaimed');
            $this->loadRewards();
        }
    }

    public function render()
    {
        return view('livewire.dashboard.active-rewards');
    }
}
