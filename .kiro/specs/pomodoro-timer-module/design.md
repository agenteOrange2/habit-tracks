# Pomodoro Timer Module - Design Document

## Overview

The Pomodoro Timer Module provides users with a focused work session management system integrated into the FocusFlow application. The module leverages the existing energy system, habit tracking, and user statistics to create a comprehensive productivity tool. The design emphasizes simplicity, real-time feedback, and seamless integration with the existing Notion-inspired UI.

## Architecture

### Component Structure

```
PomodoroTimer (Livewire Component)
├── Timer Display (Visual countdown)
├── Habit Selector (Dropdown)
├── Duration Selector (Button group)
├── Control Buttons (Start/Pause/Stop)
├── Energy Display (Progress bar)
├── Statistics Cards (Today's stats)
└── Session History (List)
```

### Data Flow

1. **Session Initialization**: User selects habit and duration → Component validates energy → Creates PomodoroSession record
2. **Timer Execution**: JavaScript interval updates countdown → Livewire polls for state changes → UI updates in real-time
3. **Session Completion**: Timer reaches zero → Backend marks session complete → Updates user statistics → Dispatches events → UI shows notification
4. **Energy Management**: EnergyService checks/updates energy → Prevents session start if insufficient → Regenerates over time

### Technology Stack

- **Backend**: Laravel Livewire for reactive components
- **Frontend**: Alpine.js for client-side timer logic, Tailwind CSS for styling
- **Database**: Existing PostgreSQL with pomodoro_sessions table
- **Real-time**: Livewire polling and JavaScript intervals for countdown
- **Notifications**: Browser Notification API + Livewire flash messages

## Components and Interfaces

### Livewire Component: PomodoroTimer

**Properties:**
```php
public ?int $selectedHabit = null;
public int $duration = 25; // minutes
public string $timerState = 'idle'; // idle, running, paused, completed
public ?int $remainingSeconds = null;
public ?PomodoroSession $currentSession = null;
public array $recentSessions = [];
public array $todayStats = [];
```

**Methods:**
```php
public function mount(): void
public function startTimer(): void
public function pauseTimer(): void
public function resumeTimer(): void
public function stopTimer(): void
public function completeTimer(): void
public function selectHabit(int $habitId): void
public function setDuration(int $minutes): void
public function loadRecentSessions(): void
public function getTodayStats(): array
```

**Events:**
- `timerStarted`: Dispatched when a session begins
- `timerPaused`: Dispatched when timer is paused
- `timerResumed`: Dispatched when timer resumes
- `timerCompleted`: Dispatched when session completes successfully
- `timerStopped`: Dispatched when session is interrupted
- `energyUpdated`: Dispatched to refresh energy display

### Service: PomodoroService (New)

**Purpose**: Centralize Pomodoro business logic

**Methods:**
```php
public function canStartSession(User $user): bool
public function startSession(User $user, ?int $habitId, int $duration): PomodoroSession
public function completeSession(PomodoroSession $session): void
public function interruptSession(PomodoroSession $session): void
public function getTodayStats(User $user): array
public function getRecentSessions(User $user, int $limit = 10): Collection
```

### Frontend Timer Component (Alpine.js)

**Purpose**: Handle client-side countdown without constant server requests

**Data:**
```javascript
{
    remainingSeconds: 1500,
    isRunning: false,
    isPaused: false,
    interval: null
}
```

**Methods:**
```javascript
startCountdown()
pauseCountdown()
resumeCountdown()
stopCountdown()
tick()
formatTime(seconds)
```

## Data Models

### PomodoroSession (Existing - No changes needed)

```php
id: bigint
habit_id: bigint (nullable, foreign key)
user_id: bigint (foreign key)
duration_minutes: integer (default 25)
started_at: timestamp
completed_at: timestamp (nullable)
was_interrupted: boolean (default false)
created_at: timestamp
updated_at: timestamp
```

**Relationships:**
- `belongsTo(User)`
- `belongsTo(Habit)` (nullable)

**Scopes:**
```php
scopeCompleted($query)
scopeInterrupted($query)
scopeToday($query)
scopeForUser($query, User $user)
```

### User Model Extensions

**New Computed Properties:**
```php
public function getTodayPomodorosAttribute(): int
public function getTodayFocusTimeAttribute(): int // in minutes
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Energy consumption consistency

*For any* user with energy level E ≥ 10, starting a Pomodoro session should result in energy level E - 10
**Validates: Requirements 1.2**

### Property 2: Session creation atomicity

*For any* valid session start request, either a PomodoroSession record is created with started_at set OR no record is created and energy is not consumed
**Validates: Requirements 1.3**

### Property 3: Timer countdown accuracy

*For any* running Pomodoro session with duration D minutes, the remaining time should decrease by 1 second for each second elapsed, until reaching zero
**Validates: Requirements 4.1, 4.2**

### Property 4: Habit association preservation

*For any* Pomodoro session created with a habit_id H, querying that session should return habit_id = H
**Validates: Requirements 2.4**

### Property 5: Statistics increment on completion

*For any* completed (not interrupted) Pomodoro session with duration D, the user's total_pomodoros should increase by 1 and total_focus_time should increase by D
**Validates: Requirements 7.3, 7.4**

### Property 6: Interrupted sessions exclusion

*For any* Pomodoro session with was_interrupted = true, it should not contribute to total_pomodoros or total_focus_time statistics
**Validates: Requirements 7.5**

### Property 7: Energy insufficiency prevention

*For any* user with energy level E < 10, attempting to start a Pomodoro session should fail and return an error message
**Validates: Requirements 1.5**

### Property 8: Duration bounds validation

*For any* Pomodoro session, the duration_minutes value should be one of [15, 25, 50]
**Validates: Requirements 3.1, 3.4**

### Property 9: Session state transitions

*For any* Pomodoro session, the state transitions should follow: idle → running → (paused ↔ running)* → completed/stopped
**Validates: Requirements 5.1, 5.2, 5.3, 5.4**

### Property 10: Timestamp consistency

*For any* completed Pomodoro session, completed_at should be greater than started_at
**Validates: Requirements 1.3, 4.3**

## Error Handling

### Energy Insufficient Error
- **Trigger**: User attempts to start session with energy < 10
- **Response**: Display warning message, disable start button, show time until energy regenerates
- **Recovery**: User waits for energy regeneration or claims rewards

### Session Already Running Error
- **Trigger**: User attempts to start a new session while one is active
- **Response**: Display error message, focus on current session
- **Recovery**: User completes or stops current session first

### Database Connection Error
- **Trigger**: Unable to save PomodoroSession record
- **Response**: Rollback energy consumption, display error message
- **Recovery**: User retries after connection is restored

### Browser Notification Permission Denied
- **Trigger**: User denies notification permission
- **Response**: Fall back to in-app notifications only
- **Recovery**: User can re-enable in browser settings

## Testing Strategy

### Unit Tests

**PomodoroService Tests:**
- Test `canStartSession()` with various energy levels
- Test `startSession()` creates correct database records
- Test `completeSession()` updates statistics correctly
- Test `interruptSession()` marks session as interrupted
- Test `getTodayStats()` calculates correct totals

**EnergyService Integration:**
- Test energy consumption on session start
- Test energy validation before session start
- Test energy regeneration over time

**Model Tests:**
- Test PomodoroSession scopes (completed, interrupted, today)
- Test PomodoroSession relationships (user, habit)
- Test computed properties on User model

### Property-Based Tests

We will use **Pest PHP** with the **Pest Property Testing** plugin for property-based testing.

**Property Test 1: Energy Consumption Consistency**
- Generate random users with energy levels 10-100
- Start Pomodoro sessions
- Assert energy decreases by exactly 10 each time

**Property Test 2: Statistics Accumulation**
- Generate random completed sessions with various durations
- Assert total_pomodoros and total_focus_time match sum of sessions

**Property Test 3: Interrupted Session Exclusion**
- Generate mix of completed and interrupted sessions
- Assert only completed sessions count toward statistics

**Property Test 4: Duration Validation**
- Generate random duration values
- Assert only [15, 25, 50] are accepted

**Property Test 5: Timestamp Ordering**
- Generate random session start times
- Complete sessions after random delays
- Assert completed_at > started_at for all sessions

### Integration Tests

- Test full flow: select habit → start timer → complete → verify statistics
- Test pause/resume functionality maintains correct time
- Test stopping session marks as interrupted
- Test energy regeneration during session
- Test multiple sessions in sequence

### UI/Component Tests

- Test Livewire component renders correctly
- Test timer countdown updates in UI
- Test habit selector populates with active habits
- Test duration selector changes timer
- Test control buttons trigger correct methods
- Test energy display updates reactively

## UI/UX Design

### Layout Structure

```
┌─────────────────────────────────────────────┐
│  Header: "Pomodoro Timer" + Back Button     │
├─────────────────────────────────────────────┤
│                                             │
│  ┌───────────────────────────────────────┐ │
│  │     Energy: ⚡ 80/100 (80%)           │ │
│  └───────────────────────────────────────┘ │
│                                             │
│  ┌───────────────────────────────────────┐ │
│  │                                       │ │
│  │          🍅 25:00                     │ │
│  │                                       │ │
│  │     [Progress Circle: 0%]            │ │
│  │                                       │ │
│  └───────────────────────────────────────┘ │
│                                             │
│  Habit: [Dropdown: Select a habit...]      │
│                                             │
│  Duration: [15min] [25min] [50min]         │
│                                             │
│  [        Start Pomodoro        ]          │
│                                             │
│  ┌─────────────┬─────────────┐            │
│  │ Today       │ Total       │            │
│  │ 🍅 3        │ 🍅 127      │            │
│  │ ⏱ 75 min   │ ⏱ 3,175 min│            │
│  └─────────────┴─────────────┘            │
│                                             │
│  Recent Sessions:                           │
│  ┌───────────────────────────────────────┐ │
│  │ ✅ Estudiar programación - 25min      │ │
│  │    Today at 10:30 AM                  │ │
│  ├───────────────────────────────────────┤ │
│  │ ✅ Hacer ejercicio - 25min            │ │
│  │    Today at 9:00 AM                   │ │
│  ├───────────────────────────────────────┤ │
│  │ ⚠️  Leer - 15min (interrupted)        │ │
│  │    Yesterday at 8:45 PM               │ │
│  └───────────────────────────────────────┘ │
│                                             │
└─────────────────────────────────────────────┘
```

### State-Based UI Changes

**Idle State:**
- Large timer display showing selected duration
- Habit selector enabled
- Duration buttons enabled
- "Start Pomodoro" button enabled (if energy sufficient)

**Running State:**
- Timer counting down
- Habit selector disabled
- Duration buttons disabled
- "Pause" and "Stop" buttons visible
- Progress circle animating

**Paused State:**
- Timer frozen at current time
- "Resume" and "Stop" buttons visible
- Subtle visual indicator of paused state

**Completed State:**
- Success animation
- Confetti or celebration visual
- Statistics updated
- "Start Another" button

### Color Scheme

- **Primary Action**: `#2383E2` (Blue - Start button)
- **Success**: `#10B981` (Green - Completed sessions)
- **Warning**: `#F59E0B` (Orange - Low energy, interrupted sessions)
- **Danger**: `#EF4444` (Red - Stop button)
- **Neutral**: `#6B7280` (Gray - Disabled states)
- **Background**: `#FFFFFF` (White - Main background)
- **Text**: `#37352F` (Dark gray - Primary text)

### Responsive Behavior

- **Desktop (≥1024px)**: Side-by-side layout with timer on left, stats/history on right
- **Tablet (768px-1023px)**: Stacked layout with timer on top
- **Mobile (<768px)**: Single column, larger touch targets, simplified history view

## Performance Considerations

### Client-Side Timer

Use JavaScript interval for countdown to avoid excessive server requests:
- Update UI every second locally
- Sync with server every 30 seconds for accuracy
- On completion, immediately notify server

### Database Optimization

- Index on `user_id` and `started_at` for quick today's stats queries
- Index on `user_id` and `completed_at` for recent sessions
- Use eager loading for habit relationships

### Caching Strategy

- Cache user's today stats for 1 minute
- Cache recent sessions for 30 seconds
- Invalidate cache on session completion

### Real-time Updates

- Use Livewire polling (every 5 seconds) for energy updates
- Use JavaScript for timer countdown (every 1 second)
- Dispatch events for cross-component communication

## Security Considerations

- Validate user owns the habit before associating with session
- Prevent starting multiple concurrent sessions per user
- Validate duration is within allowed values
- Ensure energy checks happen server-side (not just client-side)
- Rate limit session creation to prevent abuse

## Accessibility

- Use semantic HTML (`<button>`, `<select>`, etc.)
- Provide ARIA labels for timer state
- Ensure keyboard navigation works for all controls
- Use sufficient color contrast (WCAG AA)
- Provide text alternatives for visual indicators
- Support screen readers with live regions for timer updates

## Future Enhancements (Out of Scope)

- Break timer (5-minute breaks between Pomodoros)
- Long break timer (15-minute breaks after 4 Pomodoros)
- Sound customization (different notification sounds)
- Pomodoro goals (target number per day/week)
- Detailed analytics dashboard
- Pomodoro streaks and achievements
- Team Pomodoro sessions (collaborative focus time)
