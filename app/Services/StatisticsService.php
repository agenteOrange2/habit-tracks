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

    /**
     * Get weekly statistics for habits
     */
    public function getWeeklyStats(User $user): array
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // Get habit logs for this week
        $weekLogs = $user->habitLogs()
            ->whereBetween('completed_date', [$weekStart, $weekEnd])
            ->get();

        $totalCompleted = $weekLogs->count();
        $totalPoints = $weekLogs->sum('points_earned');

        // Daily breakdown
        $dailyStats = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $dayLogs = $weekLogs->filter(fn($log) => $log->completed_date->isSameDay($date));
            
            $dailyStats[$date->format('Y-m-d')] = [
                'date' => $date,
                'day_name' => $date->format('D'),
                'completed' => $dayLogs->count(),
                'points' => $dayLogs->sum('points_earned'),
            ];
        }

        // Calculate completion rate
        $scheduledHabits = $user->habits()
            ->where('is_active', true)
            ->get();

        $totalScheduled = 0;
        foreach ($dailyStats as $dateKey => $stats) {
            $date = \Carbon\Carbon::parse($dateKey);
            $scheduled = $scheduledHabits->filter(fn($h) => $h->isScheduledForDay($date))->count();
            $totalScheduled += $scheduled;
        }

        $completionRate = $totalScheduled > 0 ? round(($totalCompleted / $totalScheduled) * 100, 1) : 0;

        return [
            'total_completed' => $totalCompleted,
            'total_points' => $totalPoints,
            'completion_rate' => $completionRate,
            'daily_stats' => $dailyStats,
            'average_per_day' => round($totalCompleted / 7, 1),
        ];
    }

    /**
     * Get monthly statistics for habits
     */
    public function getMonthlyStats(User $user): array
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $monthLogs = $user->habitLogs()
            ->whereBetween('completed_date', [$monthStart, $monthEnd])
            ->get();

        $totalCompleted = $monthLogs->count();
        $totalPoints = $monthLogs->sum('points_earned');

        // Weekly breakdown
        $weeklyStats = $monthLogs->groupBy(function($log) {
            return $log->completed_date->weekOfMonth;
        })->map(function($weekLogs) {
            return [
                'completed' => $weekLogs->count(),
                'points' => $weekLogs->sum('points_earned'),
            ];
        });

        $daysInMonth = now()->daysInMonth;
        $daysPassed = now()->day;

        return [
            'total_completed' => $totalCompleted,
            'total_points' => $totalPoints,
            'weekly_stats' => $weeklyStats,
            'average_per_day' => $daysPassed > 0 ? round($totalCompleted / $daysPassed, 1) : 0,
        ];
    }
}
