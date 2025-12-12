/**
 * Alpine.js Global Store for Pomodoro Timer
 * Manages timer state across all components and pages
 */

import {
    saveTimerState,
    loadTimerState,
    clearTimerState,
    saveWidgetPosition,
    loadWidgetPosition,
    saveWidgetCollapsed,
    loadWidgetCollapsed,
    loadRecentDurations,
    addRecentDuration,
} from './pomodoro-storage.js';

export default function pomodoroStore() {
    return {
        // Timer state
        timerState: 'idle', // 'idle' | 'running' | 'paused' | 'break'
        timerType: 'pomodoro', // 'pomodoro' | 'short_break' | 'long_break'
        remainingSeconds: 0,
        totalSeconds: 0,
        currentSessionId: null,
        selectedHabit: null,
        
        // Cycle tracking
        cycleCount: 0,
        consecutivePomodoros: 0,
        
        // Break configuration
        shortBreakDuration: 5,
        longBreakDuration: 15,
        autoStartBreaks: true,
        soundEnabled: true,
        
        // Widget state
        widgetVisible: false,
        widgetCollapsed: false,
        widgetPosition: { x: 20, y: 20 },
        
        // Recent custom durations
        recentCustomDurations: [],
        
        // Intervals
        countdownInterval: null,
        syncInterval: null,
        
        /**
         * Initialize the store
         */
        init() {
            console.log('Initializing Pomodoro store...');
            
            // Load saved state
            this.loadFromLocalStorage();
            
            // Load widget position
            const savedPosition = loadWidgetPosition();
            if (savedPosition) {
                this.widgetPosition = savedPosition;
            }
            
            // Load widget collapsed state
            this.widgetCollapsed = loadWidgetCollapsed();
            
            // Load recent durations
            this.recentCustomDurations = loadRecentDurations();
            
            // Start intervals if timer is running
            if (this.timerState === 'running') {
                this.startCountdown();
                this.startBackendSync();
            }
            
            // Setup cross-tab synchronization
            this.setupCrossTabSync();
            
            /*
            console.log('Pomodoro store initialized', {
                timerState: this.timerState,
                remainingSeconds: this.remainingSeconds,
            });
            */
        },
        
        /**
         * Start a new timer
         */
        startTimer(duration, habitId = null, type = 'pomodoro') {
            // console.log('Starting timer', { duration, habitId, type });
            
            // Check if timer is already running in another tab
            if (this.isTimerRunningInAnotherTab() && this.timerState === 'idle') {
                // console.warn('Timer is already running in another tab');
                // Load and sync with that state
                this.loadFromLocalStorage();
                if (this.timerState === 'running') {
                    this.startCountdown();
                    this.startBackendSync();
                    this.widgetVisible = true;
                }
                return;
            }
            
            this.timerState = 'running';
            this.timerType = type;
            this.totalSeconds = duration * 60;
            this.remainingSeconds = this.totalSeconds;
            this.selectedHabit = habitId;
            this.widgetVisible = true;
            
            this.startCountdown();
            this.startBackendSync();
            this.saveToLocalStorage();
            
            // Dispatch event for Livewire components
            window.dispatchEvent(new CustomEvent('timer-started', {
                detail: { duration, habitId, type }
            }));
        },
        
        /**
         * Pause the timer
         */
        pauseTimer() {
            // console.log('Pausing timer');
            
            this.timerState = 'paused';
            this.stopCountdown();
            this.saveToLocalStorage();
            this.updatePageTitle();
            
            window.dispatchEvent(new CustomEvent('timer-paused'));
        },
        
        /**
         * Resume the timer
         */
        resumeTimer() {
            console.log('Resuming timer');
            
            this.timerState = 'running';
            this.startCountdown();
            this.saveToLocalStorage();
            
            window.dispatchEvent(new CustomEvent('timer-resumed'));
        },
        
        /**
         * Stop the timer
         */
        stopTimer() {
            console.log('Stopping timer');
            
            this.timerState = 'idle';
            this.stopCountdown();
            this.stopBackendSync();
            this.widgetVisible = false;
            this.currentSessionId = null;
            this.clearTimerData();
            this.updatePageTitle();
            
            window.dispatchEvent(new CustomEvent('timer-stopped'));
        },
        
        /**
         * Complete the timer
         */
        completeTimer() {
            console.log('Timer completed');
            
            const wasBreak = this.timerType !== 'pomodoro';
            
            this.stopCountdown();
            this.stopBackendSync();
            
            // Increment cycle if it was a Pomodoro
            if (this.timerType === 'pomodoro') {
                this.incrementCycle();
            }
            
            // Reset cycle if it was a long break
            if (this.timerType === 'long_break') {
                this.resetCycle();
            }
            
            window.dispatchEvent(new CustomEvent('timer-completed', {
                detail: { 
                    type: this.timerType,
                    sessionId: this.currentSessionId 
                }
            }));
            
            // Auto-start break if enabled and it was a Pomodoro
            if (this.autoStartBreaks && this.timerType === 'pomodoro') {
                setTimeout(() => {
                    const breakType = this.cycleCount >= 4 ? 'long_break' : 'short_break';
                    const duration = breakType === 'long_break' 
                        ? this.longBreakDuration 
                        : this.shortBreakDuration;
                    this.startBreak(breakType, duration);
                }, 1000);
            } else {
                this.timerState = 'idle';
                this.widgetVisible = false;
                this.clearTimerData();
            }
        },
        
        /**
         * Start a break
         */
        startBreak(type, duration = null) {
            console.log('Starting break', { type });
            
            const breakDuration = duration || (type === 'long_break' 
                ? this.longBreakDuration 
                : this.shortBreakDuration);
            
            this.startTimer(breakDuration, null, type);
            
            window.dispatchEvent(new CustomEvent('break-started', {
                detail: { type, duration: breakDuration }
            }));
        },
        
        /**
         * Skip the current break
         */
        skipBreak() {
            console.log('Skipping break');
            
            if (this.timerType === 'short_break' || this.timerType === 'long_break') {
                this.stopTimer();
                
                window.dispatchEvent(new CustomEvent('break-skipped', {
                    detail: { type: this.timerType }
                }));
            }
        },
        
        /**
         * Increment cycle counter
         */
        incrementCycle() {
            this.cycleCount++;
            this.consecutivePomodoros++;
            
            console.log('Cycle incremented', { cycleCount: this.cycleCount });
            
            // Sync with backend
            this.syncCycleWithBackend();
        },
        
        /**
         * Reset cycle counter
         */
        resetCycle() {
            this.cycleCount = 0;
            this.consecutivePomodoros = 0;
            
            console.log('Cycle reset');
            
            // Sync with backend
            this.syncCycleWithBackend();
        },
        
        /**
         * Start countdown interval
         */
        startCountdown() {
            if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
            }
            
            let lastTick = Date.now();
            
            this.countdownInterval = setInterval(() => {
                // Drift correction
                const now = Date.now();
                const elapsed = now - lastTick;
                lastTick = now;
                
                // Adjust for drift (should be ~1000ms)
                const adjustment = elapsed > 1100 ? 2 : 1;
                
                this.remainingSeconds -= adjustment;
                
                if (this.remainingSeconds <= 0) {
                    this.remainingSeconds = 0;
                    this.completeTimer();
                } else {
                    this.saveToLocalStorage();
                    // Update page title with timer
                    this.updatePageTitle();
                }
            }, 1000);
        },
        
        /**
         * Stop countdown interval
         */
        stopCountdown() {
            if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
                this.countdownInterval = null;
            }
        },
        
        /**
         * Start backend sync interval
         */
        startBackendSync() {
            if (this.syncInterval) {
                clearInterval(this.syncInterval);
            }
            
            // Sync every 30 seconds
            this.syncInterval = setInterval(() => {
                this.syncWithBackend();
            }, 30000);
        },
        
        /**
         * Stop backend sync interval
         */
        stopBackendSync() {
            if (this.syncInterval) {
                clearInterval(this.syncInterval);
                this.syncInterval = null;
            }
        },
        
        /**
         * Save state to localStorage
         */
        saveToLocalStorage() {
            const state = {
                timerState: this.timerState,
                timerType: this.timerType,
                remainingSeconds: this.remainingSeconds,
                totalSeconds: this.totalSeconds,
                currentSessionId: this.currentSessionId,
                selectedHabit: this.selectedHabit,
                cycleCount: this.cycleCount,
            };
            
            saveTimerState(state);
        },
        
        /**
         * Load state from localStorage
         */
        loadFromLocalStorage() {
            const state = loadTimerState();
            
            if (state) {
                this.timerState = state.timerState || 'idle';
                this.timerType = state.timerType || 'pomodoro';
                this.remainingSeconds = state.remainingSeconds || 0;
                this.totalSeconds = state.totalSeconds || 0;
                this.currentSessionId = state.currentSessionId || null;
                this.selectedHabit = state.selectedHabit || null;
                this.cycleCount = state.cycleCount || 0;
                
                // Show widget if timer was running
                if (this.timerState === 'running' || this.timerState === 'paused') {
                    this.widgetVisible = true;
                }
            }
        },
        
        /**
         * Clear timer data from localStorage
         */
        clearTimerData() {
            clearTimerState();
        },
        
        /**
         * Sync with backend (Livewire)
         */
        syncWithBackend() {
            console.log('Syncing with backend...');
            
            window.dispatchEvent(new CustomEvent('sync-timer-state', {
                detail: {
                    timerState: this.timerState,
                    remainingSeconds: this.remainingSeconds,
                    currentSessionId: this.currentSessionId,
                }
            }));
        },
        
        /**
         * Sync cycle count with backend
         */
        syncCycleWithBackend() {
            window.dispatchEvent(new CustomEvent('sync-cycle-count', {
                detail: { cycleCount: this.cycleCount }
            }));
        },
        
        /**
         * Update widget position
         */
        updateWidgetPosition(x, y) {
            this.widgetPosition = { x, y };
            saveWidgetPosition(x, y);
        },
        
        /**
         * Toggle widget collapsed state
         */
        toggleWidgetCollapsed() {
            this.widgetCollapsed = !this.widgetCollapsed;
            saveWidgetCollapsed(this.widgetCollapsed);
        },
        
        /**
         * Add custom duration to recent list
         */
        addCustomDuration(duration) {
            addRecentDuration(duration);
            this.recentCustomDurations = loadRecentDurations();
        },
        
        /**
         * Get formatted time string
         */
        getFormattedTime() {
            const minutes = Math.floor(this.remainingSeconds / 60);
            const seconds = this.remainingSeconds % 60;
            return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        },
        
        /**
         * Get progress percentage
         */
        getProgress() {
            if (this.totalSeconds === 0) return 0;
            return ((this.totalSeconds - this.remainingSeconds) / this.totalSeconds) * 100;
        },
        
        /**
         * Update settings from backend
         */
        updateSettings(settings) {
            if (settings.shortBreakDuration) {
                this.shortBreakDuration = settings.shortBreakDuration;
            }
            if (settings.longBreakDuration) {
                this.longBreakDuration = settings.longBreakDuration;
            }
            if (typeof settings.autoStartBreaks === 'boolean') {
                this.autoStartBreaks = settings.autoStartBreaks;
            }
            if (typeof settings.soundEnabled === 'boolean') {
                this.soundEnabled = settings.soundEnabled;
            }
            if (typeof settings.cycleCount === 'number') {
                this.cycleCount = settings.cycleCount;
            }
        },
        
        /**
         * Update page title with timer
         */
        updatePageTitle() {
            if (this.timerState === 'running' || this.timerState === 'paused') {
                const time = this.getFormattedTime();
                const emoji = this.timerType === 'pomodoro' ? '🍅' : '☕';
                const status = this.timerState === 'paused' ? '⏸' : '';
                document.title = `${status}${emoji} ${time} - Habit Tracker`;
            } else {
                document.title = 'Habit Tracker';
            }
        },
        
        /**
         * Setup cross-tab synchronization
         */
        setupCrossTabSync() {
            // Listen for storage events from other tabs
            window.addEventListener('storage', (event) => {
                this.handleStorageEvent(event);
            });
            
            // console.log('Cross-tab synchronization enabled');
        },
        
        /**
         * Handle storage events from other tabs
         */
        handleStorageEvent(event) {
            // Only handle pomodoro timer state changes
            if (event.key !== 'pomodoro_timer_state') {
                return;
            }
            
            // console.log('Storage event detected from another tab', event);
            
            // If timer state was cleared in another tab
            if (!event.newValue) {
                console.log('Timer stopped in another tab');
                this.stopCountdown();
                this.stopBackendSync();
                this.timerState = 'idle';
                this.widgetVisible = false;
                this.currentSessionId = null;
                return;
            }
            
            try {
                const newState = JSON.parse(event.newValue);
                const oldState = event.oldValue ? JSON.parse(event.oldValue) : null;
                
                // Prevent duplicate timer starts
                if (newState.timerState === 'running' && this.timerState === 'running') {
                    // console.log('Timer already running in this tab, syncing state');
                    this.syncStateFromOtherTab(newState);
                    return;
                }
                
                // Timer started in another tab
                if (newState.timerState === 'running' && this.timerState !== 'running') {
                    console.log('Timer started in another tab, syncing...');
                    this.syncStateFromOtherTab(newState);
                    this.startCountdown();
                    this.startBackendSync();
                    this.widgetVisible = true;
                    return;
                }
                
                // Timer paused in another tab
                if (newState.timerState === 'paused' && this.timerState === 'running') {
                    console.log('Timer paused in another tab');
                    this.timerState = 'paused';
                    this.stopCountdown();
                    this.syncStateFromOtherTab(newState);
                    return;
                }
                
                // Timer resumed in another tab
                if (newState.timerState === 'running' && this.timerState === 'paused') {
                    console.log('Timer resumed in another tab');
                    this.timerState = 'running';
                    this.syncStateFromOtherTab(newState);
                    this.startCountdown();
                    return;
                }
                
                // Timer completed in another tab
                if (oldState && oldState.timerState === 'running' && newState.timerState === 'idle') {
                    console.log('Timer completed in another tab');
                    this.stopCountdown();
                    this.stopBackendSync();
                    this.timerState = 'idle';
                    this.widgetVisible = false;
                    this.currentSessionId = null;
                    
                    // Show completion notification
                    window.dispatchEvent(new CustomEvent('timer-completed-other-tab', {
                        detail: { type: oldState.timerType }
                    }));
                    return;
                }
                
                // General state sync
                this.syncStateFromOtherTab(newState);
                
            } catch (error) {
                // console.error('Error handling storage event:', error);
            }
        },
        
        /**
         * Sync state from another tab
         */
        syncStateFromOtherTab(newState) {
            this.timerState = newState.timerState;
            this.timerType = newState.timerType;
            this.remainingSeconds = newState.remainingSeconds;
            this.totalSeconds = newState.totalSeconds;
            this.currentSessionId = newState.currentSessionId;
            this.selectedHabit = newState.selectedHabit;
            this.cycleCount = newState.cycleCount;
            
            // console.log('State synced from another tab', {
            //     timerState: this.timerState,
            //     remainingSeconds: this.remainingSeconds,
            // });
        },
        
        /**
         * Check if timer is running in another tab
         */
        isTimerRunningInAnotherTab() {
            const state = loadTimerState();
            return state && (state.timerState === 'running' || state.timerState === 'paused');
        },
    };
}
