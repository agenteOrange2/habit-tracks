<?php

namespace App\Livewire\Pomodoro;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Habit, PomodoroSession};
use App\Services\{EnergyService, PomodoroService};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PomodoroTimer extends Component
{
    use WithPagination;

    public ?int $selectedHabit = null;
    public int $duration = 25;
    public ?int $customDuration = null;
    public array $recentCustomDurations = [];
    public string $timerState = 'idle'; // idle, running, paused, completed
    public string $breakType = ''; // 'short_break' | 'long_break' | ''
    public int $remainingSeconds = 1500; // Default: 25 minutes
    public ?int $currentSessionId = null;
    public array $recentSessions = [];
    public string $sessionFilter = 'all'; // 'all' | 'completed' | 'interrupted'
    public ?int $filterByHabit = null; // Filter by specific habit
    public ?string $filterByDate = null; // Filter by date range ('today', 'week', 'month', 'all')
    public bool $showFullHistory = false;
    public int $perPage = 10;
    public array $todayStats = [];
    public array $weeklyStats = [];
    public array $monthlyStats = [];
    public array $productivityMetrics = [];
    public array $energyStatus = [];
    public array $userSettings = [];
    public string $statsView = 'today'; // 'today' | 'week' | 'month' | 'productivity'
    public bool $showFullStats = false;
    public int $dailyGoal = 8; // Default daily goal
    public bool $showGoalSettings = false;
    public bool $focusMode = false;
    public int $maxCycles = 4; // Ciclos para descanso largo (default 4, o estimated_pomodoros del hábito)

    protected $listeners = [
        'timerCompleted' => 'completeTimer',
        'timerTick' => 'handleTick',
        'syncTimerState' => 'handleSyncTimerState',
        'syncCycleCount' => 'handleSyncCycleCount',
    ];

    public function mount(): void
    {
        $this->remainingSeconds = $this->duration * 60;
        $this->dailyGoal = session()->get('daily_pomodoro_goal', 8);
        $this->loadInitialData();
        $this->loadRecentCustomDurations();
        $this->loadUserSettings();
        $this->loadRecentSessions();
    }

    public function loadInitialData(): void
    {
        $pomodoroService = app(PomodoroService::class);
        $energyService = app(EnergyService::class);
        $user = Auth::user();

        $this->todayStats = $pomodoroService->getTodayStats($user);
        $this->weeklyStats = $pomodoroService->getWeeklyStats($user);
        $this->monthlyStats = $pomodoroService->getMonthlyStats($user);
        $this->productivityMetrics = $pomodoroService->getProductivityMetrics($user);
        $this->energyStatus = $energyService->getEnergyStatus($user);
        $this->userSettings = $pomodoroService->getUserSettings($user);
        // Note: recentSessions are loaded separately with loadRecentSessions() to apply filters
    }

    private function loadUserSettings(): void
    {
        $pomodoroService = app(PomodoroService::class);
        $this->userSettings = $pomodoroService->getUserSettings(Auth::user());
    }

    public function selectHabit(?int $habitId): void
    {
        if ($this->timerState !== 'idle') {
            return; // Can't change habit while timer is running
        }

        $this->selectedHabit = $habitId;
        
        // Actualizar maxCycles basado en el hábito seleccionado
        if ($habitId) {
            $habit = Habit::find($habitId);
            $this->maxCycles = $habit?->estimated_pomodoros ?? 4;
        } else {
            $this->maxCycles = 4; // Default sin hábito
        }
    }

    public function setDuration(int $minutes): void
    {
        if ($this->timerState !== 'idle') {
            return; // Can't change duration while timer is running
        }

        $allowedDurations = app(PomodoroService::class)->getAllowedDurations();
        
        if (!in_array($minutes, $allowedDurations)) {
            session()->flash('error', 'Duración inválida');
            return;
        }

        $this->duration = $minutes;
        $this->customDuration = null; // Clear custom duration when selecting preset
        $this->remainingSeconds = $minutes * 60;
    }

    public function setCustomDuration(int $minutes): void
    {
        if ($this->timerState !== 'idle') {
            return; // Can't change duration while timer is running
        }

        // Validate custom duration (1-120 minutes)
        if ($minutes < 1 || $minutes > 120) {
            session()->flash('error', 'La duración personalizada debe estar entre 1 y 120 minutos');
            return;
        }

        $this->customDuration = $minutes;
        $this->duration = $minutes;
        $this->remainingSeconds = $minutes * 60;

        // Save to recent custom durations
        $this->addToRecentCustomDurations($minutes);
    }

    private function loadRecentCustomDurations(): void
    {
        // Load from localStorage via JavaScript (will be synced)
        // For now, initialize empty array
        $this->recentCustomDurations = [];
    }

    private function addToRecentCustomDurations(int $duration): void
    {
        // Remove if already exists
        $filtered = [];
        foreach ($this->recentCustomDurations as $d) {
            if ($d !== $duration) {
                $filtered[] = $d;
            }
        }
        $this->recentCustomDurations = $filtered;

        // Add to beginning
        array_unshift($this->recentCustomDurations, $duration);

        // Keep only last 3
        $this->recentCustomDurations = array_slice($this->recentCustomDurations, 0, 3);

        // Dispatch event to save to localStorage
        $this->dispatch('saveRecentDurations', durations: $this->recentCustomDurations);
    }

    public function startTimer(): void
    {
        if ($this->timerState !== 'idle') {
            session()->flash('error', 'Ya hay una sesión en progreso');
            return;
        }

        try {
            $pomodoroService = app(PomodoroService::class);
            $user = Auth::user();

            // Check if user can start session
            if (!$pomodoroService->canStartSession($user)) {
                session()->flash('error', '⚡ No tienes suficiente energía. Descansa un poco.');
                $this->loadInitialData(); // Refresh energy status
                return;
            }

            // Start session
            $session = $pomodoroService->startSession(
                $user,
                $this->selectedHabit,
                $this->duration
            );

            $this->currentSessionId = $session->id;
            $this->timerState = 'running';
            $this->remainingSeconds = $this->duration * 60;

            // Refresh data
            $this->loadInitialData();

            $this->dispatch('timerStarted', remainingSeconds: $this->remainingSeconds);

        } catch (\Exception $e) {
            Log::error('Failed to start Pomodoro timer', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            
            session()->flash('error', 'Error al iniciar el Pomodoro: ' . $e->getMessage());
        }
    }

    public function pauseTimer(): void
    {
        if ($this->timerState !== 'running') {
            return;
        }

        $this->timerState = 'paused';
        $this->dispatch('timerPaused');
    }

    public function resumeTimer(): void
    {
        if ($this->timerState !== 'paused') {
            return;
        }

        $this->timerState = 'running';
        $this->dispatch('timerResumed', remainingSeconds: $this->remainingSeconds);
    }

    public function stopTimer(): void
    {
        if (!$this->currentSessionId || $this->timerState === 'idle') {
            return;
        }

        try {
            $session = PomodoroSession::find($this->currentSessionId);
            
            if ($session) {
                $pomodoroService = app(PomodoroService::class);
                
                // Save remaining seconds for resume functionality
                $session->remaining_seconds = $this->remainingSeconds;
                $session->save();
                
                $pomodoroService->interruptSession($session);
            }

            $this->resetTimer();
            $this->loadInitialData();

            session()->flash('info', 'Pomodoro interrumpido');
            $this->dispatch('timerStopped');

        } catch (\Exception $e) {
            Log::error('Failed to stop Pomodoro timer', [
                'session_id' => $this->currentSessionId,
                'error' => $e->getMessage(),
            ]);
            
            session()->flash('error', 'Error al detener el Pomodoro');
        }
    }

    public function updateSettings(array $settings): void
    {
        try {
            // Convert numeric strings to integers
            if (isset($settings['short_break_duration'])) {
                $settings['short_break_duration'] = (int) $settings['short_break_duration'];
            }
            if (isset($settings['long_break_duration'])) {
                $settings['long_break_duration'] = (int) $settings['long_break_duration'];
            }
            
            $pomodoroService = app(PomodoroService::class);
            $pomodoroService->updateUserSettings(Auth::user(), $settings);
            
            $this->loadUserSettings();
            
            // Dispatch event to update Alpine.js store
            $this->dispatch('settingsUpdated', settings: $this->userSettings);
            
            session()->flash('success', '⚙️ Configuración actualizada');
            
        } catch (\Exception $e) {
            Log::error('Failed to update Pomodoro settings', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            
            session()->flash('error', 'Error al actualizar la configuración: ' . $e->getMessage());
        }
    }

    public function resumeSession(int $sessionId): void
    {
        try {
            $session = PomodoroSession::find($sessionId);
            
            if (!$session || !$session->canBeResumed()) {
                session()->flash('error', 'Esta sesión no puede ser reanudada');
                return;
            }
            
            $pomodoroService = app(PomodoroService::class);
            $user = Auth::user();
            
            // Check if user can start session
            if (!$pomodoroService->canStartSession($user)) {
                session()->flash('error', '⚡ No tienes suficiente energía. Descansa un poco.');
                $this->loadInitialData();
                return;
            }
            
            // Calculate remaining minutes (round up)
            $remainingMinutes = ceil($session->remaining_seconds / 60);
            
            // Resume the session
            $resumedSession = $pomodoroService->resumeSession($session, $remainingMinutes);
            
            $this->currentSessionId = $resumedSession->id;
            $this->selectedHabit = $session->habit_id;
            $this->duration = $remainingMinutes;
            $this->timerState = 'running';
            $this->remainingSeconds = $session->remaining_seconds;
            
            session()->flash('success', '↻ Sesión reanudada desde ' . $remainingMinutes . ' minutos');
            
            $this->dispatch('timerStarted', remainingSeconds: $this->remainingSeconds);
            $this->loadInitialData();
            
        } catch (\Exception $e) {
            Log::error('Failed to resume session', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
            
            session()->flash('error', 'Error al reanudar la sesión: ' . $e->getMessage());
        }
    }

    public function completeTimer(): void
    {
        if (!$this->currentSessionId) {
            return;
        }

        try {
            $session = PomodoroSession::find($this->currentSessionId);
            $pomodoroService = app(PomodoroService::class);
            
            if ($session) {
                $pomodoroService->completeSession($session);
            }

            $this->timerState = 'completed';
            
            // Check if it was a break or Pomodoro
            $isBreak = $this->breakType !== '';
            
            if ($isBreak) {
                // Break completed
                if ($this->breakType === 'long_break') {
                    $pomodoroService->resetCycle(Auth::user());
                    session()->flash('success', '✨ ¡Descanso largo completado! Ciclo reiniciado.');
                } else {
                    session()->flash('success', '☕ ¡Descanso corto completado! Listo para continuar.');
                }
                
                $this->breakType = '';
                $this->resetTimer();
            } else {
                // Pomodoro completed
                $pomodoroService->incrementCycle(Auth::user());
                
                session()->flash('success', '🍅 ¡Pomodoro completado! Has ganado ' . $this->duration . ' minutos de tiempo enfocado.');
                
                // Determine break type based on cycle and maxCycles
                $cycleInfo = $pomodoroService->getActiveCycle(Auth::user(), $this->maxCycles);
                $breakType = $cycleInfo['next_break_type'];
                $breakDuration = $cycleInfo['next_break_duration'];
                
                // Auto-start break if enabled
                if ($this->userSettings['auto_start_breaks']) {
                    $this->startBreak($breakType, $breakDuration);
                } else {
                    $this->resetTimer();
                }
            }

            // Refresh data
            $this->loadInitialData();

            // Dispatch events
            $this->dispatch('energyUpdated');
            $this->dispatch('pomodoroCompleted');

        } catch (\Exception $e) {
            Log::error('Failed to complete Pomodoro timer', [
                'session_id' => $this->currentSessionId,
                'error' => $e->getMessage(),
            ]);
            
            session()->flash('error', 'Error al completar el Pomodoro');
        }
    }

    public function startBreak(string $type, ?int $duration = null): void
    {
        try {
            $pomodoroService = app(PomodoroService::class);
            $user = Auth::user();
            
            // Get duration from settings if not provided
            if (!$duration) {
                $duration = $type === 'long_break' 
                    ? $this->userSettings['long_break_duration'] 
                    : $this->userSettings['short_break_duration'];
            }
            
            // Start break session
            $session = $pomodoroService->startBreak($user, $type, $duration);
            
            $this->currentSessionId = $session->id;
            $this->breakType = $type;
            $this->duration = $duration;
            $this->timerState = 'running';
            $this->remainingSeconds = $duration * 60;
            
            $breakLabel = $type === 'long_break' ? 'Descanso Largo' : 'Descanso Corto';
            session()->flash('info', "☕ Iniciando {$breakLabel} de {$duration} minutos");
            
            $this->dispatch('breakStarted', type: $type, duration: $duration);
            $this->dispatch('timerStarted', remainingSeconds: $this->remainingSeconds);
            
        } catch (\Exception $e) {
            Log::error('Failed to start break', [
                'user_id' => Auth::id(),
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            
            session()->flash('error', 'Error al iniciar el descanso');
        }
    }

    public function skipBreak(): void
    {
        if ($this->breakType === '') {
            return; // Not in a break
        }
        
        try {
            // Stop the break session
            if ($this->currentSessionId) {
                $session = PomodoroSession::find($this->currentSessionId);
                if ($session) {
                    $pomodoroService = app(PomodoroService::class);
                    $pomodoroService->interruptSession($session);
                }
            }
            
            $this->breakType = '';
            $this->resetTimer();
            
            session()->flash('info', 'Descanso omitido');
            $this->dispatch('breakSkipped');
            
        } catch (\Exception $e) {
            Log::error('Failed to skip break', [
                'session_id' => $this->currentSessionId,
                'error' => $e->getMessage(),
            ]);
            
            session()->flash('error', 'Error al omitir el descanso');
        }
    }

    public function handleTick(int $remainingSeconds): void
    {
        $this->remainingSeconds = $remainingSeconds;
    }

    public function handleSyncTimerState(array $state): void
    {
        // Sync timer state from Alpine.js store to backend
        if ($this->currentSessionId && isset($state['remainingSeconds'])) {
            try {
                $session = PomodoroSession::find($this->currentSessionId);
                if ($session && !$session->completed_at) {
                    $session->remaining_seconds = $state['remainingSeconds'];
                    $session->save();
                }
            } catch (\Exception $e) {
                Log::error('Failed to sync timer state', [
                    'session_id' => $this->currentSessionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function handleSyncCycleCount(int $cycleCount): void
    {
        try {
            $pomodoroService = app(PomodoroService::class);
            $user = Auth::user();
            
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
            
            $settings->update(['cycle_count' => $cycleCount]);
            $this->loadUserSettings();
            
        } catch (\Exception $e) {
            Log::error('Failed to sync cycle count', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function resetTimer(): void
    {
        $this->timerState = 'idle';
        $this->currentSessionId = null;
        $this->breakType = '';
        $this->remainingSeconds = $this->duration * 60;
        $this->dispatch('timerStopped');
    }

    public function loadRecentSessions(): void
    {
        $query = PomodoroSession::with(['habit', 'resumedFrom', 'resumedSessions'])
            ->where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->orderBy('started_at', 'desc');
        
        // Apply status filter
        if ($this->sessionFilter === 'completed') {
            $query->where('was_interrupted', false);
        } elseif ($this->sessionFilter === 'interrupted') {
            $query->where('was_interrupted', true);
        }
        
        // Apply habit filter
        if ($this->filterByHabit) {
            $query->where('habit_id', $this->filterByHabit);
        }
        
        // Apply date filter
        if ($this->filterByDate) {
            switch ($this->filterByDate) {
                case 'today':
                    $query->whereDate('started_at', today());
                    break;
                case 'week':
                    $query->where('started_at', '>=', now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('started_at', '>=', now()->startOfMonth());
                    break;
                // 'all' - no filter
            }
        }
        
        // Limit if not showing full history
        if (!$this->showFullHistory) {
            $query->limit($this->perPage);
        }
        
        $sessions = $query->get();
        
        $this->recentSessions = $sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'habit_name' => $session->habit?->name ?? 'General focus',
                'habit_icon' => $session->habit?->icon ?? '🍅',
                'habit_color' => $session->habit?->color ?? '#3b82f6',
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
        })->toArray();
    }

    public function setFilter(string $filter): void
    {
        if (!in_array($filter, ['all', 'completed', 'interrupted'])) {
            return;
        }
        
        $this->sessionFilter = $filter;
        $this->loadRecentSessions();
        
        // Dispatch event to save to localStorage
        $this->dispatch('saveSessionFilter', filter: $filter);
    }

    public function setHabitFilter(?int $habitId): void
    {
        $this->filterByHabit = $habitId;
        $this->loadRecentSessions();
    }

    public function setDateFilter(?string $dateRange): void
    {
        if ($dateRange && !in_array($dateRange, ['today', 'week', 'month', 'all'])) {
            return;
        }
        
        $this->filterByDate = $dateRange;
        $this->loadRecentSessions();
    }

    public function toggleFullHistory(): void
    {
        $this->showFullHistory = !$this->showFullHistory;
        $this->loadRecentSessions();
    }

    public function clearFilters(): void
    {
        $this->sessionFilter = 'all';
        $this->filterByHabit = null;
        $this->filterByDate = null;
        $this->loadRecentSessions();
    }

    public function setStatsView(string $view): void
    {
        if (!in_array($view, ['today', 'week', 'month', 'productivity'])) {
            return;
        }
        
        $this->statsView = $view;
    }

    public function toggleFullStats(): void
    {
        $this->showFullStats = !$this->showFullStats;
    }

    public function toggleGoalSettings(): void
    {
        $this->showGoalSettings = !$this->showGoalSettings;
    }

    public function setDailyGoal(int $goal): void
    {
        if ($goal < 1 || $goal > 50) {
            session()->flash('error', 'La meta diaria debe estar entre 1 y 50 Pomodoros');
            return;
        }

        $this->dailyGoal = $goal;
        
        // Save to user settings or preferences (can be stored in session for now)
        session()->put('daily_pomodoro_goal', $goal);
        
        $this->showGoalSettings = false;
        session()->flash('success', '🎯 Meta diaria actualizada a ' . $goal . ' Pomodoros');
    }

    public function toggleFocusMode(): void
    {
        $this->focusMode = !$this->focusMode;
        
        if ($this->focusMode) {
            $this->dispatch('enterFocusMode');
        } else {
            $this->dispatch('exitFocusMode');
        }
    }

    private function formatTimestamp($timestamp): string
    {
        $now = now();
        $date = \Carbon\Carbon::instance($timestamp);

        if ($date->isToday()) {
            return 'Hoy a las ' . $date->format('g:i A');
        }

        if ($date->isYesterday()) {
            return 'Ayer a las ' . $date->format('g:i A');
        }

        if ($date->diffInDays($now) < 7) {
            return $date->format('l \a \l\a\s g:i A');
        }

        return $date->format('M j \a \l\a\s g:i A');
    }

    public function getEnergyStatusProperty(): array
    {
        $energyService = app(EnergyService::class);
        return $energyService->getEnergyStatus(Auth::user());
    }

    public function render()
    {
        $habits = Auth::user()->habits()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $allowedDurations = app(PomodoroService::class)->getAllowedDurations();
        
        // Obtener info del hábito seleccionado
        $selectedHabitData = null;
        if ($this->selectedHabit) {
            $habit = $habits->find($this->selectedHabit);
            if ($habit) {
                $selectedHabitData = [
                    'id' => $habit->id,
                    'name' => $habit->name,
                    'icon' => $habit->icon,
                    'estimated_pomodoros' => $habit->estimated_pomodoros ?? 4,
                ];
                $this->maxCycles = $habit->estimated_pomodoros ?? 4;
            }
        }

        return view('livewire.pomodoro.pomodoro-timer', [
            'habits' => $habits,
            'allowedDurations' => $allowedDurations,
            'selectedHabitData' => $selectedHabitData,
            'maxCycles' => $this->maxCycles,
        ])->layout('components.layouts.app');
    }
}