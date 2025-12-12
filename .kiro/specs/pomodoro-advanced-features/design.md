# Pomodoro Advanced Features - Design Document

## Overview

This design document outlines the architecture and implementation strategy for advanced Pomodoro Timer features. The enhancements focus on three main areas: **persistence** (maintaining timer state across navigation and sessions), **portability** (floating widget accessible from any page), and **flexibility** (custom durations, breaks, and session management). The design leverages localStorage for client-side state management, Livewire events for cross-component communication, and a global Alpine.js store for shared timer state.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Browser Layer                         │
├─────────────────────────────────────────────────────────┤
│  localStorage (Timer State)  ←→  Alpine.js Global Store │
│         ↕                              ↕                 │
│  Floating Widget Component    Main Timer Component      │
│         ↕                              ↕                 │
│  ────────────── Livewire Events ──────────────          │
└─────────────────────────────────────────────────────────┘
                         ↕
┌─────────────────────────────────────────────────────────┐
│                    Backend Layer                         │
├─────────────────────────────────────────────────────────┤
│  PomodoroService  ←→  PomodoroSession Model             │
│         ↕                              ↕                 │
│  User Settings    ←→  Database (PostgreSQL)             │
└─────────────────────────────────────────────────────────┘
```

### Component Structure

```
Global Components (Available on all pages):
├── FloatingTimerWidget (Alpine.js + Livewire)
│   ├── Minimized State (Small indicator)
│   ├── Expanded State (Full controls)
│   └── Draggable Positioning

Page-Specific Components:
├── PomodoroTimer (Main page)
│   ├── Timer Display
│   ├── Duration Selector (with custom input)
│   ├── Break Configuration
│   ├── Session History (with filters)
│   └── Cycle Progress Indicator

Shared State Management:
├── Alpine.js Global Store (pomodoroStore)
│   ├── timerState
│   ├── remainingSeconds
│   ├── currentSession
│   ├── cycleCount
│   └── breakType

localStorage Keys:
├── pomodoro_timer_state
├── pomodoro_widget_position
├── pomodoro_widget_collapsed
├── pomodoro_recent_custom_durations
└── pomodoro_session_filter
```

## Components and Interfaces

### Alpine.js Global Store: pomodoroStore

**Purpose**: Centralize timer state accessible from any component

**State:**
```javascript
{
    // Timer state
    timerState: 'idle' | 'running' | 'paused' | 'break',
    timerType: 'pomodoro' | 'short_break' | 'long_break',
    remainingSeconds: number,
    totalSeconds: number,
    currentSessionId: number | null,
    selectedHabit: number | null,
    
    // Cycle tracking
    cycleCount: number, // 0-4
    consecutivePomodoros: number,
    
    // Break configuration
    shortBreakDuration: number, // minutes
    longBreakDuration: number, // minutes
    autoStartBreaks: boolean,
    
    // Widget state
    widgetVisible: boolean,
    widgetCollapsed: boolean,
    widgetPosition: { x: number, y: number },
    
    // Recent custom durations
    recentCustomDurations: number[]
}
```

**Methods:**
```javascript
startTimer(duration, habitId, type)
pauseTimer()
resumeTimer()
stopTimer()
completeTimer()
startBreak(type)
skipBreak()
incrementCycle()
resetCycle()
saveToLocalStorage()
loadFromLocalStorage()
syncWithBackend()
```

### Livewire Component: FloatingTimerWidget

**Purpose**: Global widget displayed on all pages when timer is active

**Properties:**
```php
public bool $visible = false;
public bool $collapsed = false;
public array $position = ['x' => 20, 'y' => 20];
```

**Methods:**
```php
public function mount(): void
public function toggle(): void
public function updatePosition(int $x, int $y): void
public function syncState(): void
```

**Events Listened:**
- `timerStarted`
- `timerPaused`
- `timerResumed`
- `timerCompleted`
- `timerStopped`
- `breakStarted`

### Enhanced PomodoroService

**New Methods:**
```php
public function resumeSession(PomodoroSession $session, int $remainingMinutes): PomodoroSession
public function startBreak(User $user, string $type, int $duration): PomodoroSession
public function getActiveCycle(User $user): array
public function incrementCycle(User $user): void
public function resetCycle(User $user): void
public function getUserSettings(User $user): array
public function updateUserSettings(User $user, array $settings): void
```

### Database Schema Changes

**New Table: user_pomodoro_settings**
```sql
CREATE TABLE user_pomodoro_settings (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    short_break_duration INT DEFAULT 5,
    long_break_duration INT DEFAULT 15,
    auto_start_breaks BOOLEAN DEFAULT true,
    sound_enabled BOOLEAN DEFAULT true,
    cycle_count INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(user_id)
);
```

**Update pomodoro_sessions table:**
```sql
ALTER TABLE pomodoro_sessions ADD COLUMN session_type VARCHAR(20) DEFAULT 'pomodoro';
-- Values: 'pomodoro', 'short_break', 'long_break'

ALTER TABLE pomodoro_sessions ADD COLUMN resumed_from_id BIGINT NULL REFERENCES pomodoro_sessions(id);
-- Links resumed sessions to original interrupted session

ALTER TABLE pomodoro_sessions ADD COLUMN remaining_seconds INT NULL;
-- Stores remaining time when interrupted for resume functionality
```

## Data Models

### UserPomodoroSettings (New Model)

```php
class UserPomodoroSettings extends Model
{
    protected $fillable = [
        'user_id',
        'short_break_duration',
        'long_break_duration',
        'auto_start_breaks',
        'sound_enabled',
        'cycle_count',
    ];

    protected $casts = [
        'auto_start_breaks' => 'boolean',
        'sound_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

### PomodoroSession (Enhanced)

**New Fields:**
- `session_type`: 'pomodoro' | 'short_break' | 'long_break'
- `resumed_from_id`: Foreign key to original session if this is a resume
- `remaining_seconds`: Time left when interrupted

**New Methods:**
```php
public function canBeResumed(): bool
public function getResumedSession(): ?PomodoroSession
public function isBreak(): bool
public function getSessionTypeLabel(): string
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Timer state persistence

*For any* active timer, storing state to localStorage and then loading it should result in the same timer state (within 1 second tolerance)
**Validates: Requirements 1.3, 1.5**

### Property 2: Cross-tab synchronization

*For any* timer state change in one browser tab, all other open tabs should reflect the same state within 1 second
**Validates: Requirements 8.1, 8.2, 8.3, 8.4**

### Property 3: Cycle counter consistency

*For any* sequence of completed Pomodoros, the cycle count should equal (completed_pomodoros % 4)
**Validates: Requirements 10.2, 10.3**

### Property 4: Break type determination

*For any* completed Pomodoro, if cycle_count < 4 then break type is 'short_break', else 'long_break'
**Validates: Requirements 4.2, 4.3**

### Property 5: Resume session time preservation

*For any* interrupted session with remaining_seconds R, resuming should create a new session with duration = R minutes (rounded up)
**Validates: Requirements 6.2, 6.3**

### Property 6: Custom duration bounds

*For any* custom duration input D, the system should accept D if and only if 1 ≤ D ≤ 120
**Validates: Requirements 3.2**

### Property 7: Widget visibility correlation

*For any* timer state, widget should be visible if and only if timerState ∈ {'running', 'paused', 'break'}
**Validates: Requirements 2.1**

### Property 8: localStorage sync frequency

*For any* running timer, localStorage should be updated at least once per second
**Validates: Requirements 1.3**

### Property 9: Break skip non-counting

*For any* skipped break, it should not increment any completion counters or statistics
**Validates: Requirements 5.3**

### Property 10: Filter consistency

*For any* session history filter F, all displayed sessions should match the filter criteria F
**Validates: Requirements 7.2, 7.3, 7.4**

## Implementation Strategy

### Phase 1: Persistence Layer

1. **localStorage Manager**
   - Create `PomodoroStorageManager` class
   - Implement save/load/clear methods
   - Add storage event listeners for cross-tab sync

2. **Alpine.js Global Store**
   - Define `pomodoroStore` with all state
   - Implement timer logic methods
   - Add localStorage integration

3. **Backend Sync**
   - Periodic sync every 30 seconds
   - Immediate sync on critical events (start, complete, stop)
   - Conflict resolution (server wins)

### Phase 2: Floating Widget

1. **Widget Component**
   - Create Livewire component
   - Implement minimized/expanded states
   - Add drag-and-drop functionality

2. **Global Integration**
   - Add widget to main layout
   - Connect to Alpine.js store
   - Implement visibility logic

3. **Positioning**
   - Save position to localStorage
   - Ensure viewport bounds
   - Responsive positioning for mobile

### Phase 3: Custom Durations & Breaks

1. **Custom Duration Input**
   - Add input field with validation
   - Store recent custom durations
   - Quick-select from recent

2. **Break Timers**
   - Implement break start logic
   - Add skip break functionality
   - Auto-start based on settings

3. **Cycle Tracking**
   - Add cycle counter to user settings
   - Implement increment/reset logic
   - Display cycle progress

### Phase 4: Session Management

1. **Resume Functionality**
   - Add "Resume" button to interrupted sessions
   - Calculate remaining time
   - Link resumed to original session

2. **History Filters**
   - Add filter tabs (All/Completed/Interrupted)
   - Implement filter logic
   - Persist filter selection

3. **Enhanced Display**
   - Show session type (Pomodoro/Break)
   - Display resume status
   - Add quick actions

### Phase 5: Settings & Preferences

1. **User Settings Model**
   - Create migration and model
   - Add default values
   - Implement CRUD operations

2. **Settings UI**
   - Add settings section to timer page
   - Break duration inputs
   - Auto-start toggle
   - Sound toggle

3. **Settings Sync**
   - Load settings on mount
   - Save changes immediately
   - Apply to timer logic

## UI/UX Design

### Floating Widget Design

**Expanded State:**
```
┌─────────────────────────────┐
│ 🍅 Estudiar programación  ✕ │
│                             │
│        24:35                │
│     ████████░░░░ 50%        │
│                             │
│  ⏸ Pausar    ⏹ Detener     │
│                             │
│  Hoy: 🍅 3  ⏱ 75 min       │
│  Ciclo: ●●●○ (3/4)         │
│                             │
│  ▼ Minimizar                │
└─────────────────────────────┘
```

**Minimized State:**
```
┌──────────┐
│ 🍅 24:35 │
│    ▲     │
└──────────┘
```

### Custom Duration Selector

```
Duración:
┌────┬────┬────┬──────────┐
│ 15 │ 25 │ 50 │ Custom ▼ │
└────┴────┴────┴──────────┘

Recientes: 30 min | 45 min | 60 min
```

### Cycle Progress Indicator

```
Ciclo Pomodoro:  ●●●○  (3/4)
Próximo: Descanso corto (5 min)
```

### Session History with Filters

```
┌─────────────────────────────────┐
│ [Todas] [Completadas] [Interrumpidas] │
├─────────────────────────────────┤
│ ✅ Estudiar - 25 min            │
│    Hoy a las 10:30 AM           │
│                                 │
│ ⚠️  Leer - 15 min (interrumpido)│
│    Hoy a las 9:00 AM            │
│    [↻ Retomar]                  │
└─────────────────────────────────┘
```

## Error Handling

### localStorage Quota Exceeded
- **Trigger**: localStorage is full
- **Response**: Clear old session data, keep only last 7 days
- **Recovery**: Notify user, continue with in-memory state

### Cross-Tab Conflict
- **Trigger**: Two tabs try to start timer simultaneously
- **Response**: First tab wins, second shows error
- **Recovery**: Sync second tab to first tab's state

### Backend Sync Failure
- **Trigger**: Network error during sync
- **Response**: Queue sync for retry, continue with localStorage
- **Recovery**: Retry every 30 seconds, show offline indicator

### Widget Positioning Out of Bounds
- **Trigger**: Screen resize or resolution change
- **Response**: Reset widget to default position
- **Recovery**: Save new position to localStorage

## Performance Considerations

### localStorage Optimization
- Debounce writes to max 1 per second
- Use JSON.stringify/parse efficiently
- Clear old data automatically (> 7 days)

### Cross-Tab Communication
- Use `storage` event for sync (native browser feature)
- Debounce event handlers to prevent thrashing
- Only sync on actual state changes

### Widget Rendering
- Use CSS transforms for dragging (GPU accelerated)
- Lazy load widget component
- Minimize re-renders with Alpine.js reactivity

### Timer Accuracy
- Use `setInterval` with drift correction
- Sync with server time periodically
- Account for tab sleep/wake cycles

## Security Considerations

- Validate all custom durations server-side
- Prevent session hijacking (verify user_id)
- Rate limit session creation
- Sanitize localStorage data before parsing
- Validate widget position bounds

## Accessibility

- Keyboard shortcuts with visual feedback
- Screen reader announcements for timer changes
- High contrast mode support
- Focus management for widget
- ARIA labels for all controls

## Testing Strategy

### Unit Tests
- localStorage manager save/load/clear
- Timer state transitions
- Cycle counter logic
- Break type determination
- Custom duration validation

### Integration Tests
- Full timer flow with persistence
- Cross-tab synchronization
- Widget show/hide/drag
- Resume interrupted session
- Filter session history

### Property-Based Tests
- Timer state persistence (Property 1)
- Cross-tab sync (Property 2)
- Cycle counter consistency (Property 3)
- Break type determination (Property 4)
- Resume time preservation (Property 5)

### Manual Testing
- Test across different browsers
- Test with multiple tabs open
- Test browser close/reopen
- Test widget dragging and positioning
- Test keyboard shortcuts

## Migration Strategy

### Backward Compatibility
- Existing sessions continue to work
- New fields have sensible defaults
- Gradual rollout of features
- Feature flags for testing

### Data Migration
- Add new columns with defaults
- Create user_pomodoro_settings for existing users
- Migrate any existing localStorage data
- No breaking changes to API

## Future Enhancements (Out of Scope)

- Team Pomodoro sessions (collaborative timers)
- Pomodoro templates (saved configurations)
- Integration with calendar apps
- Detailed analytics dashboard
- Mobile app with sync
- Desktop notifications (Electron app)
- Spotify/music integration for breaks
