<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Get comprehensive reward statistics for a user
     */
    public function getRewardStatistics(User $user): array
    {
        return [
            'total_points_spent' => $this->getTotalPointsSpent($user),
            'total_rewards_claimed' => $this->getTotalRewardsClaimed($user),
            'most_claimed_categories' => $this->getMostClaimedCategories($user),
            'most_claimed_rewards' => $this->getMostClaimedRewards($user),
            'average_points_per_claim' => $this->getAveragePointsPerClaim($user),
        ];
    }

    /**
     * Calculate total points spent on rewards
     */
    public function getTotalPointsSpent(User $user): int
    {
        return $user->rewardClaims()->sum('points_spent');
    }

    /**
     * Get total number of rewards claimed
     */
    public function getTotalRewardsClaimed(User $user): int
    {
        return $user->rewardClaims()->count();
    }

    /**
     * Get most claimed reward categories with counts
     */
    public function getMostClaimedCategories(User $user, int $limit = 5): Collection
    {
        return $user->rewardClaims()
            ->join('rewards', 'reward_claims.reward_id', '=', 'rewards.id')
            ->select('rewards.category', DB::raw('COUNT(*) as claim_count'))
            ->groupBy('rewards.category')
            ->orderByDesc('claim_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get most claimed individual rewards with counts
     */
    public function getMostClaimedRewards(User $user, int $limit = 5): Collection
    {
        return $user->rewardClaims()
            ->select('reward_id', DB::raw('COUNT(*) as claim_count'))
            ->with('reward:id,name,icon,category')
            ->groupBy('reward_id')
            ->orderByDesc('claim_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Calculate average points spent per claim
     */
    public function getAveragePointsPerClaim(User $user): float
    {
        $totalClaims = $this->getTotalRewardsClaimed($user);
        
        if ($totalClaims === 0) {
            return 0.0;
        }

        $totalSpent = $this->getTotalPointsSpent($user);
        
        return round($totalSpent / $totalClaims, 2);
    }

    /**
     * Get heatmap data for habit activity visualization
     * Returns array with date => count of completed habits
     */
    public function getHeatmapData(User $user, int $days = 365): array
    {
        $startDate = now()->subDays($days)->startOfDay();
        
        $habitLogs = $user->habitLogs()
            ->where('completed_date', '>=', $startDate)
            ->selectRaw('DATE(completed_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return $habitLogs;
    }
}
