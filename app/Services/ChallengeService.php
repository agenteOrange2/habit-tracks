<?php

namespace App\Services;

use App\Models\{User, Challenge};

class ChallengeService
{
    public function generateWeeklyChallenge(): Challenge
    {
        $types = [
            [
                'type' => 'weekly_streak',
                'name' => 'Racha Semanal Perfecta',
                'description' => 'Completa al menos un hábito cada día de la semana',
                'target' => 7,
                'points' => 200,
            ],
            [
                'type' => 'pomodoro_marathon',
                'name' => 'Maratón Pomodoro',
                'description' => 'Completa 20 pomodoros esta semana',
                'target' => 20,
                'points' => 150,
            ],
            [
                'type' => 'point_goal',
                'name' => 'Meta de Puntos',
                'description' => 'Consigue 500 puntos esta semana',
                'target' => 500,
                'points' => 100,
            ],
        ];
        
        $selected = $types[array_rand($types)];
        
        return Challenge::create([
            'name' => $selected['name'],
            'description' => $selected['description'],
            'type' => $selected['type'],
            'target_value' => $selected['target'],
            'points_reward' => $selected['points'],
            'starts_at' => now()->startOfWeek(),
            'ends_at' => now()->endOfWeek(),
            'is_active' => true,
        ]);
    }
    
    public function updateChallengeProgress(User $user, Challenge $challenge): void
    {
        $progress = match($challenge->type) {
            'weekly_streak' => $this->calculateWeeklyStreak($user),
            'pomodoro_marathon' => $this->calculateWeeklyPomodoros($user),
            'point_goal' => $user->stats->weekly_points,
            default => 0,
        };
        
        $user->challenges()->updateExistingPivot($challenge->id, [
            'progress' => $progress,
        ]);
        
        if ($progress >= $challenge->target_value) {
            $this->completeChallenge($user, $challenge);
        }
    }
    
    private function completeChallenge(User $user, Challenge $challenge): void
    {
        $user->challenges()->updateExistingPivot($challenge->id, [
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // Award points
        $user->stats->increment('available_points', $challenge->points_reward);
        $user->stats->increment('total_points', $challenge->points_reward);
        
        event(new \App\Events\ChallengeCompleted($user, $challenge));
    }
    
    private function calculateWeeklyStreak(User $user): int
    {
        $daysWithActivity = \App\Models\HabitLog::where('user_id', $user->id)
            ->whereBetween('completed_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->distinct('completed_date')
            ->count('completed_date');
            
        return $daysWithActivity;
    }
    
    private function calculateWeeklyPomodoros(User $user): int
    {
        return \App\Models\PomodoroSession::where('user_id', $user->id)
            ->whereBetween('started_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->whereNotNull('completed_at')
            ->count();
    }
}