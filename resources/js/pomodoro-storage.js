/**
 * Pomodoro localStorage Manager
 * Handles all localStorage operations for the Pomodoro timer
 */

const STORAGE_KEYS = {
    TIMER_STATE: 'pomodoro_timer_state',
    WIDGET_POSITION: 'pomodoro_widget_position',
    WIDGET_COLLAPSED: 'pomodoro_widget_collapsed',
    RECENT_DURATIONS: 'pomodoro_recent_custom_durations',
    SESSION_FILTER: 'pomodoro_session_filter',
};

/**
 * Save timer state to localStorage
 * @param {Object} state - Timer state object
 */
export function saveTimerState(state) {
    try {
        const stateToSave = {
            timerState: state.timerState,
            timerType: state.timerType,
            remainingSeconds: state.remainingSeconds,
            totalSeconds: state.totalSeconds,
            currentSessionId: state.currentSessionId,
            selectedHabit: state.selectedHabit,
            cycleCount: state.cycleCount,
            timestamp: Date.now(),
        };
        
        localStorage.setItem(STORAGE_KEYS.TIMER_STATE, JSON.stringify(stateToSave));
        return true;
    } catch (error) {
        if (error.name === 'QuotaExceededError') {
            console.error('localStorage quota exceeded. Clearing old data...');
            clearOldData();
            // Try again after clearing
            try {
                localStorage.setItem(STORAGE_KEYS.TIMER_STATE, JSON.stringify(state));
                return true;
            } catch (retryError) {
                console.error('Failed to save timer state after clearing:', retryError);
                return false;
            }
        }
        console.error('Error saving timer state:', error);
        return false;
    }
}

/**
 * Load timer state from localStorage
 * @returns {Object|null} Timer state or null if not found
 */
export function loadTimerState() {
    try {
        const stored = localStorage.getItem(STORAGE_KEYS.TIMER_STATE);
        if (!stored) return null;
        
        const state = JSON.parse(stored);
        
        // Validate state has required properties
        if (!state.timerState || !state.timestamp) {
            return null;
        }
        
        // Check if state is too old (more than 24 hours)
        const age = Date.now() - state.timestamp;
        const maxAge = 24 * 60 * 60 * 1000; // 24 hours
        
        if (age > maxAge) {
            clearTimerState();
            return null;
        }
        
        return state;
    } catch (error) {
        console.error('Error loading timer state:', error);
        return null;
    }
}

/**
 * Clear timer state from localStorage
 */
export function clearTimerState() {
    try {
        localStorage.removeItem(STORAGE_KEYS.TIMER_STATE);
        return true;
    } catch (error) {
        console.error('Error clearing timer state:', error);
        return false;
    }
}

/**
 * Save widget position to localStorage
 * @param {number} x - X coordinate
 * @param {number} y - Y coordinate
 */
export function saveWidgetPosition(x, y) {
    try {
        const position = { x, y };
        localStorage.setItem(STORAGE_KEYS.WIDGET_POSITION, JSON.stringify(position));
        return true;
    } catch (error) {
        console.error('Error saving widget position:', error);
        return false;
    }
}

/**
 * Load widget position from localStorage
 * @returns {Object|null} Position object {x, y} or null
 */
export function loadWidgetPosition() {
    try {
        const stored = localStorage.getItem(STORAGE_KEYS.WIDGET_POSITION);
        if (!stored) return null;
        
        const position = JSON.parse(stored);
        
        // Validate position
        if (typeof position.x !== 'number' || typeof position.y !== 'number') {
            return null;
        }
        
        return position;
    } catch (error) {
        console.error('Error loading widget position:', error);
        return null;
    }
}

/**
 * Save widget collapsed state
 * @param {boolean} collapsed - Whether widget is collapsed
 */
export function saveWidgetCollapsed(collapsed) {
    try {
        localStorage.setItem(STORAGE_KEYS.WIDGET_COLLAPSED, JSON.stringify(collapsed));
        return true;
    } catch (error) {
        console.error('Error saving widget collapsed state:', error);
        return false;
    }
}

/**
 * Load widget collapsed state
 * @returns {boolean} Collapsed state
 */
export function loadWidgetCollapsed() {
    try {
        const stored = localStorage.getItem(STORAGE_KEYS.WIDGET_COLLAPSED);
        if (!stored) return false;
        
        return JSON.parse(stored);
    } catch (error) {
        console.error('Error loading widget collapsed state:', error);
        return false;
    }
}

/**
 * Save recent custom durations
 * @param {Array<number>} durations - Array of duration values in minutes
 */
export function saveRecentDurations(durations) {
    try {
        // Keep only last 3 durations
        const limited = durations.slice(0, 3);
        localStorage.setItem(STORAGE_KEYS.RECENT_DURATIONS, JSON.stringify(limited));
        return true;
    } catch (error) {
        console.error('Error saving recent durations:', error);
        return false;
    }
}

/**
 * Load recent custom durations
 * @returns {Array<number>} Array of duration values
 */
export function loadRecentDurations() {
    try {
        const stored = localStorage.getItem(STORAGE_KEYS.RECENT_DURATIONS);
        if (!stored) return [];
        
        const durations = JSON.parse(stored);
        
        // Validate it's an array
        if (!Array.isArray(durations)) {
            return [];
        }
        
        // Filter valid durations (1-120 minutes)
        return durations.filter(d => typeof d === 'number' && d >= 1 && d <= 120);
    } catch (error) {
        console.error('Error loading recent durations:', error);
        return [];
    }
}

/**
 * Add a custom duration to recent list
 * @param {number} duration - Duration in minutes
 */
export function addRecentDuration(duration) {
    try {
        const recent = loadRecentDurations();
        
        // Remove if already exists
        const filtered = recent.filter(d => d !== duration);
        
        // Add to beginning
        filtered.unshift(duration);
        
        // Save limited list
        saveRecentDurations(filtered);
        return true;
    } catch (error) {
        console.error('Error adding recent duration:', error);
        return false;
    }
}

/**
 * Save session filter preference
 * @param {string} filter - Filter value ('all', 'completed', 'interrupted')
 */
export function saveSessionFilter(filter) {
    try {
        localStorage.setItem(STORAGE_KEYS.SESSION_FILTER, filter);
        return true;
    } catch (error) {
        console.error('Error saving session filter:', error);
        return false;
    }
}

/**
 * Load session filter preference
 * @returns {string} Filter value
 */
export function loadSessionFilter() {
    try {
        const stored = localStorage.getItem(STORAGE_KEYS.SESSION_FILTER);
        if (!stored) return 'all';
        
        // Validate filter value
        const validFilters = ['all', 'completed', 'interrupted'];
        return validFilters.includes(stored) ? stored : 'all';
    } catch (error) {
        console.error('Error loading session filter:', error);
        return 'all';
    }
}

/**
 * Clear old data (older than 7 days)
 * This is called when localStorage quota is exceeded
 */
function clearOldData() {
    try {
        // For now, just clear timer state if it's old
        const state = loadTimerState();
        if (state) {
            const age = Date.now() - state.timestamp;
            const maxAge = 7 * 24 * 60 * 60 * 1000; // 7 days
            
            if (age > maxAge) {
                clearTimerState();
            }
        }
    } catch (error) {
        console.error('Error clearing old data:', error);
    }
}

/**
 * Clear all Pomodoro-related localStorage data
 */
export function clearAllPomodoroData() {
    try {
        Object.values(STORAGE_KEYS).forEach(key => {
            localStorage.removeItem(key);
        });
        return true;
    } catch (error) {
        console.error('Error clearing all Pomodoro data:', error);
        return false;
    }
}

export default {
    saveTimerState,
    loadTimerState,
    clearTimerState,
    saveWidgetPosition,
    loadWidgetPosition,
    saveWidgetCollapsed,
    loadWidgetCollapsed,
    saveRecentDurations,
    loadRecentDurations,
    addRecentDuration,
    saveSessionFilter,
    loadSessionFilter,
    clearAllPomodoroData,
};
