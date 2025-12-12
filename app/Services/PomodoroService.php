<?php

namespace App\Services;

use App\Models\{User, Habit, PomodoroSession};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PomodoroService
{
    private const ALLOWED_DURATIONS = [15, 25, 50];
    private const ENERGY_REQUIRED = 10;

    public function __construct(
        private EnergyService $energyService
    ) {}

    /**
     * Check if user can start a new Pomodoro session
     */
    public function canStartSession(User $user): bool
    {
        $this->energyService->updateEnergy($user);
        
        return $user->energy_level >= self::ENERGY_REQUIRED;
    }

    /**
     * Start a new Pomodoro session
     * 
     * @throws \Exception if energy is insufficient or duration is invalid
     */
    public function startSession(User $user, ?int $habitId, int $duration): PomodoroSession
    {
        // Validate duration (1-120 minutes for custom durations)
        if ($duration < 1 || $duration > 120) {
            throw new \InvalidArgumentException(
                "Duration must be between 1 and 120 minutes"
            );
        }

        // Validate habit belongs to user if provided
        if ($habitId) {
            $habit = Habit::where('id', $habitId)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
                
            if (!$habit) {
                throw new \InvalidArgumentException("Invalid or inactive habit");
            }
        }

        // Check and consume energy
        if (!$this->energyService->consumeForPomodoro($user)) {
            throw new \Exception("Insufficient energy to start Pomodoro session");
        }

        try {
            $session = PomodoroSession::create([
                'user_id' => $user->id,
                'habit_id' => $habitId,
                'duration_minutes' => $duration,
                'session_type' => 'pomodoro',
                'started_at' => now(),
            ]);

            Log::info('Pomodoro session started', [
                'user_id' => $user->id,
                'session_id' => $session->id,
                'habit_id' => $habitId,
                'duration' => $duration,
            ]);

            return $session;
        } catch (\Exception $e) {
            // Rollback energy consumption if session creation fails
            $user->energy_level += self::ENERGY_REQUIRED;
            $user->save();
            
            Log::error('Failed to create Pomodoro session', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Complete a Pomodoro session successfully
     */
    public function completeSession(PomodoroSession $session): void
    {
        DB::transaction(function () use ($session) {
            $session->update([
                'completed_at' => now(),
                'was_interrupted' => false,
            ]);

            // Update user statistics
            $user = $session->user;
            $stats = $user->stats;
            
            if ($stats) {
                $stats->increment('total_pomodoros');
                $stats->increment('total_focus_time', $session->duration_minutes);
            }

            Log::info('Pomodoro session completed', [
                'user_id' => $session->user_id,
                'session_id' => $session->id,
                'duration' => $session->duration_minutes,
            ]);
        });
    }

    /**
     * Mark a Pomodoro session as interrupted
     */
    public function interruptSession(PomodoroSession $session): void
    {
        $session->update([
            'completed_at' => now(),
            'was_interrupted' => true,
        ]);

        Log::info('Pomodoro session interrupted', [
            'user_id' => $session->user_id,
            'session_id' => $session->id,
            'elapsed_time' => $session->elapsed_time,
        ]);
    }

    /**
     * Get today's Pomodoro statistics for a user
     */
    public function getTodayStats(User $user): array
    {
        $todaySessions = PomodoroSession::where('user_id', $user->id)
            ->whereDate('started_at', today())
            ->where('was_interrupted', false)
            ->whereNotNull('completed_at')
            ->get();

        $totalPomodoros = $todaySessions->count();
        $totalFocusTime = $todaySessions->sum('duration_minutes');

        return [
            'pomodoros' => $totalPomodoros,
            'focus_time' => $totalFocusTime,
            'focus_time_formatted' => $this->formatMinutes($totalFocusTime),
        ];
    }

    /**
     * Get weekly Pomodoro statistics for a user
     */
    public function getWeeklyStats(User $user): array
    {
        $weekStart = now()->startOfWeek();
        $weekSessions = PomodoroSession::where('user_id', $user->id)
            ->where('started_at', '>=', $weekStart)
            ->where('was_interrupted', false)
            ->whereNotNull('completed_at')
            ->get();

        $totalPomodoros = $weekSessions->count();
        $totalFocusTime = $weekSessions->sum('duration_minutes');
        
        // Group by day for chart
        $dailyStats = $weekSessions->groupBy(function($session) {
            return $session->started_at->format('Y-m-d');
        })->map(function($daySessions) {
            return [
                'pomodoros' => $daySessions->count(),
                'focus_time' => $daySessions->sum('duration_minutes'),
            ];
        });

        return [
            'pomodoros' => $totalPomodoros,
            'focus_time' => $totalFocusTime,
            'focus_time_formatted' => $this->formatMinutes($totalFocusTime),
            'daily_stats' => $dailyStats,
            'average_per_day' => $totalPomodoros > 0 ? round($totalPomodoros / 7, 1) : 0,
        ];
    }

    /**
     * Get monthly Pomodoro statistics for a user
     */
    public function getMonthlyStats(User $user): array
    {
        $monthStart = now()->startOfMonth();
        $monthSessions = PomodoroSession::where('user_id', $user->id)
            ->where('started_at', '>=', $monthStart)
            ->where('was_interrupted', false)
            ->whereNotNull('completed_at')
            ->get();

        $totalPomodoros = $monthSessions->count();
        $totalFocusTime = $monthSessions->sum('duration_minutes');
        
        // Group by week
        $weeklyStats = $monthSessions->groupBy(function($session) {
            return $session->started_at->weekOfYear;
        })->map(function($weekSessions) {
            return [
                'pomodoros' => $weekSessions->count(),
                'focus_time' => $weekSessions->sum('duration_minutes'),
            ];
        });

        return [
            'pomodoros' => $totalPomodoros,
            'focus_time' => $totalFocusTime,
            'focus_time_formatted' => $this->formatMinutes($totalFocusTime),
            'weekly_stats' => $weeklyStats,
            'average_per_day' => $totalPomodoros > 0 ? round($totalPomodoros / now()->day, 1) : 0,
        ];
    }

    /**
     * Get productivity metrics
     */
    public function getProductivityMetrics(User $user): array
    {
        $last30Days = PomodoroSession::where('user_id', $user->id)
            ->where('started_at', '>=', now()->subDays(30))
            ->whereNotNull('completed_at')
            ->get();

        $completedCount = $last30Days->where('was_interrupted', false)->count();
        $interruptedCount = $last30Days->where('was_interrupted', true)->count();
        $totalCount = $completedCount + $interruptedCount;

        $completionRate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 1) : 0;

        // Best streak
        $bestStreak = $this->calculateBestStreak($user);
        $currentStreak = $this->calculateCurrentStreak($user);

        return [
            'completion_rate' => $completionRate,
            'completed_count' => $completedCount,
            'interrupted_count' => $interruptedCount,
            'total_count' => $totalCount,
            'best_streak' => $bestStreak,
            'current_streak' => $currentStreak,
        ];
    }

    /**
     * Calculate best Pomodoro streak
     */
    private function calculateBestStreak(User $user): int
    {
        $sessions = PomodoroSession::where('user_id', $user->id)
            ->where('was_interrupted', false)
            ->whereNotNull('completed_at')
            ->orderBy('started_at')
            ->pluck('started_at')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->values();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $maxStreak = 1;
        $currentStreak = 1;

        for ($i = 1; $i < $sessions->count(); $i++) {
            $prevDate = \Carbon\Carbon::parse($sessions[$i - 1]);
            $currDate = \Carbon\Carbon::parse($sessions[$i]);

            if ($prevDate->addDay()->isSameDay($currDate)) {
                $currentStreak++;
                $maxStreak = max($maxStreak, $currentStreak);
            } else {
                $currentStreak = 1;
            }
        }

        return $maxStreak;
    }

    /**
     * Calculate current Pomodoro streak
     */
    private function calculateCurrentStreak(User $user): int
    {
        $sessions = PomodoroSession::where('user_id', $user->id)
            ->where('was_interrupted', false)
            ->whereNotNull('completed_at')
            ->orderBy('started_at', 'desc')
            ->pluck('started_at')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->values();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        // Check if there's a session today or yesterday
        if ($sessions[0] !== $today && $sessions[0] !== $yesterday) {
            return 0;
        }

        $streak = 1;
        for ($i = 1; $i < $sessions->count(); $i++) {
            $prevDate = \Carbon\Carbon::parse($sessions[$i - 1]);
            $currDate = \Carbon\Carbon::parse($sessions[$i]);

            if ($currDate->addDay()->isSameDay($prevDate)) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get recent Pomodoro sessions for a user
     */
    public function getRecentSessions(User $user, int $limit = 10): Collection
    {
        return PomodoroSession::with(['habit', 'resumedFrom', 'resumedSessions'])
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->orderBy('started_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'habit_name' => $session->habit?->name ?? 'General focus',
                    'habit_icon' => $session->habit?->icon ?? '🍅',
                    'duration' => $session->duration_minutes,
                    'was_interrupted' => $session->was_interrupted,
                    'started_at' => $session->started_at,
                    'started_at_formatted' => $this->formatTimestamp($session->started_at),
                    'status' => $session->was_interrupted ? 'interrupted' : 'completed',
                    'session_type' => $session->session_type ?? 'pomodoro',
                    'session_type_label' => $session->getSessionTypeLabel(),
                    'is_resumed' => $session->resumed_from_id !== null,
                    'was_resumed' => $session->resumedSessions()->exists(),
                    'can_resume' => $session->canBeResumed(),
                ];
            });
    }

    /**
     * Get allowed durations
     */
    public function getAllowedDurations(): array
    {
        return self::ALLOWED_DURATIONS;
    }

    /**
     * Resume an interrupted session
     */
    public function resumeSession(PomodoroSession $session, int $remainingMinutes): PomodoroSession
    {
        if (!$session->canBeResumed()) {
            throw new \InvalidArgumentException("Session cannot be resumed");
        }

        if ($remainingMinutes <= 0 || $remainingMinutes > 120) {
            throw new \InvalidArgumentException("Remaining minutes must be between 1 and 120");
        }

        // Check and consume energy
        if (!$this->energyService->consumeForPomodoro($session->user)) {
            throw new \Exception("Insufficient energy to resume Pomodoro session");
        }

        try {
            $resumedSession = PomodoroSession::create([
                'user_id' => $session->user_id,
                'habit_id' => $session->habit_id,
                'duration_minutes' => $remainingMinutes,
                'session_type' => $session->session_type,
                'resumed_from_id' => $session->id,
                'started_at' => now(),
            ]);

            Log::info('Pomodoro session resumed', [
                'user_id' => $session->user_id,
                'original_session_id' => $session->id,
                'resumed_session_id' => $resumedSession->id,
                'remaining_minutes' => $remainingMinutes,
            ]);

            return $resumedSession;
        } catch (\Exception $e) {
            // Rollback energy consumption if session creation fails
            $session->user->energy_level += self::ENERGY_REQUIRED;
            $session->user->save();
            
            Log::error('Failed to resume Pomodoro session', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Start a break timer
     */
    public function startBreak(User $user, string $type, int $duration): PomodoroSession
    {
        if (!in_array($type, ['short_break', 'long_break'])) {
            throw new \InvalidArgumentException("Break type must be 'short_break' or 'long_break'");
        }

        if ($duration < 1 || $duration > 60) {
            throw new \InvalidArgumentException("Break duration must be between 1 and 60 minutes");
        }

        $session = PomodoroSession::create([
            'user_id' => $user->id,
            'habit_id' => null,
            'duration_minutes' => $duration,
            'session_type' => $type,
            'started_at' => now(),
        ]);

        Log::info('Break session started', [
            'user_id' => $user->id,
            'session_id' => $session->id,
            'type' => $type,
            'duration' => $duration,
        ]);

        return $session;
    }

    /**
     * Get active cycle information for a user
     * @param User $user
     * @param int $maxCycles Número de ciclos para descanso largo (default 4, o estimated_pomodoros del hábito)
     */
    public function getActiveCycle(User $user, int $maxCycles = 4): array
    {
        $settings = $this->getUserSettings($user);
        
        // Asegurar que maxCycles sea al menos 1
        $maxCycles = max(1, $maxCycles);
        
        return [
            'cycle_count' => $settings['cycle_count'],
            'max_cycles' => $maxCycles,
            'next_break_type' => $settings['cycle_count'] >= $maxCycles ? 'long_break' : 'short_break',
            'next_break_duration' => $settings['cycle_count'] >= $maxCycles 
                ? $settings['long_break_duration'] 
                : $settings['short_break_duration'],
        ];
    }

    /**
     * Increment the cycle counter
     */
    public function incrementCycle(User $user): void
    {
        $settings = $user->pomodoroSettings()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'short_break_duration' => 5,
                'long_break_duration' => 15,
                'auto_start_breaks' => true,
                'sound_enabled' => true,
                'cycle_count' => 0,
            ]
        );

        $settings->increment('cycle_count');

        Log::info('Pomodoro cycle incremented', [
            'user_id' => $user->id,
            'cycle_count' => $settings->cycle_count,
        ]);
    }

    /**
     * Reset the cycle counter
     */
    public function resetCycle(User $user): void
    {
        $settings = $user->pomodoroSettings()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'short_break_duration' => 5,
                'long_break_duration' => 15,
                'auto_start_breaks' => true,
                'sound_enabled' => true,
                'cycle_count' => 0,
            ]
        );

        $settings->update(['cycle_count' => 0]);

        Log::info('Pomodoro cycle reset', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Get user Pomodoro settings
     */
    public function getUserSettings(User $user): array
    {
        $settings = $user->pomodoroSettings()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'short_break_duration' => 5,
                'long_break_duration' => 15,
                'auto_start_breaks' => true,
                'sound_enabled' => true,
                'cycle_count' => 0,
            ]
        );

        return [
            'short_break_duration' => $settings->short_break_duration,
            'long_break_duration' => $settings->long_break_duration,
            'auto_start_breaks' => $settings->auto_start_breaks,
            'sound_enabled' => $settings->sound_enabled,
            'cycle_count' => $settings->cycle_count,
        ];
    }

    /**
     * Update user Pomodoro settings
     */
    public function updateUserSettings(User $user, array $settings): void
    {
        $userSettings = $user->pomodoroSettings()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'short_break_duration' => 5,
                'long_break_duration' => 15,
                'auto_start_breaks' => true,
                'sound_enabled' => true,
                'cycle_count' => 0,
            ]
        );

        // Validate short break duration
        if (isset($settings['short_break_duration'])) {
            if ($settings['short_break_duration'] < 1 || $settings['short_break_duration'] > 30) {
                throw new \InvalidArgumentException("Short break duration must be between 1 and 30 minutes");
            }
        }

        // Validate long break duration
        if (isset($settings['long_break_duration'])) {
            if ($settings['long_break_duration'] < 5 || $settings['long_break_duration'] > 60) {
                throw new \InvalidArgumentException("Long break duration must be between 5 and 60 minutes");
            }
        }

        $userSettings->update(array_intersect_key($settings, array_flip([
            'short_break_duration',
            'long_break_duration',
            'auto_start_breaks',
            'sound_enabled',
        ])));

        Log::info('Pomodoro settings updated', [
            'user_id' => $user->id,
            'settings' => $settings,
        ]);
    }

    /**
     * Format minutes into human-readable string
     */
    private function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} min";
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return "{$hours} hr";
        }

        return "{$hours} hr {$remainingMinutes} min";
    }

    /**
     * Format timestamp for display
     */
    private function formatTimestamp(\DateTime $timestamp): string
    {
        $now = now();
        $date = \Carbon\Carbon::instance($timestamp);

        if ($date->isToday()) {
            return 'Today at ' . $date->format('g:i A');
        }

        if ($date->isYesterday()) {
            return 'Yesterday at ' . $date->format('g:i A');
        }

        if ($date->diffInDays($now) < 7) {
            return $date->format('l \a\t g:i A'); // e.g., "Monday at 3:45 PM"
        }

        return $date->format('M j \a\t g:i A'); // e.g., "Dec 6 at 3:45 PM"
    }
}
