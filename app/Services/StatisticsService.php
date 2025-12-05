<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function getWeeklyStats(User $user): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        return [
            'habits_completed' => $this->getHabitsCompleted($user, $startOfWeek, $endOfWeek),
            'pomodoros_completed' => $this->getPomodorosCompleted($user, $startOfWeek, $endOfWeek),
            'points_earned' => $user->stats->weekly_points,
            'daily_breakdown' => $this->getDailyBreakdown($user, $startOfWeek, $endOfWeek),
            'category_distribution' => $this->getCategoryDistribution($user, $startOfWeek, $endOfWeek),
        ];
    }
    
    public function getMonthlyStats(User $user): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        return [
            'habits_completed' => $this->getHabitsCompleted($user, $startOfMonth, $endOfMonth),
            'pomodoros_completed' => $this->getPomodorosCompleted($user, $startOfMonth, $endOfMonth),
            'points_earned' => $user->stats->monthly_points,
            'best_streak' => $user->stats->best_global_streak,
            'weekly_comparison' => $this->getWeeklyComparison($user),
        ];
    }
    
    public function getHeatmapData(User $user, int $days = 365): array
    {
        $data = DB::table('habit_logs')
            ->where('user_id', $user->id)
            ->whereBetween('completed_date', [
                now()->subDays($days),
                now()
            ])
            ->select(
                DB::raw('DATE(completed_date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->get();
        
        return $data->mapWithKeys(function ($item) {
            return [$item->date => $item->count];
        })->toArray();
    }
    
    private function getHabitsCompleted(User $user, Carbon $start, Carbon $end): int
    {
        return DB::table('habit_logs')
            ->where('user_id', $user->id)
            ->whereBetween('completed_date', [$start, $end])
            ->count();
    }
    
    private function getPomodorosCompleted(User $user, Carbon $start, Carbon $end): int
    {
        return DB::table('pomodoro_sessions')
            ->where('user_id', $user->id)
            ->whereBetween('started_at', [$start, $end])
            ->whereNotNull('completed_at')
            ->count();
    }
    
    private function getDailyBreakdown(User $user, Carbon $start, Carbon $end): array
    {
        $data = DB::table('habit_logs')
            ->where('user_id', $user->id)
            ->whereBetween('completed_date', [$start, $end])
            ->select(
                DB::raw('DATE(completed_date) as date'),
                DB::raw('COUNT(*) as habits'),
                DB::raw('SUM(points_earned) as points')
            )
            ->groupBy('date')
            ->get();
        
        return $data->toArray();
    }
    
    private function getCategoryDistribution(User $user, Carbon $start, Carbon $end): array
    {
        return DB::table('habit_logs')
            ->join('habits', 'habit_logs.habit_id', '=', 'habits.id')
            ->where('habit_logs.user_id', $user->id)
            ->whereBetween('habit_logs.completed_date', [$start, $end])
            ->select('habits.category', DB::raw('COUNT(*) as count'))
            ->groupBy('habits.category')
            ->get()
            ->toArray();
    }
    
    private function getWeeklyComparison(User $user): array
    {
        $thisWeek = $this->getHabitsCompleted(
            $user, 
            now()->startOfWeek(), 
            now()->endOfWeek()
        );
        
        $lastWeek = $this->getHabitsCompleted(
            $user, 
            now()->subWeek()->startOfWeek(), 
            now()->subWeek()->endOfWeek()
        );
        
        return [
            'this_week' => $thisWeek,
            'last_week' => $lastWeek,
            'difference' => $thisWeek - $lastWeek,
            'percentage' => $lastWeek > 0 
                ? round((($thisWeek - $lastWeek) / $lastWeek) * 100, 2) 
                : 0,
        ];
    }
}
