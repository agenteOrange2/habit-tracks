# Design Document

## Overview

Este documento describe el diseño técnico para implementar el dashboard principal de la aplicación de seguimiento de hábitos. El dashboard será construido usando Livewire Volt (functional API) para la interactividad, Flux UI para componentes de interfaz, y se integrará con los servicios existentes (PointsService, StreakService, AchievementService) para mantener la lógica de negocio centralizada.

El diseño visual se basa en design2.html, adaptándolo a la estructura existente de la aplicación que ya utiliza un sidebar layout con Flux UI.

## Architecture

### Component Structure

```
app/Livewire/Dashboard/
├── DashboardOverview.php (Main dashboard component)
├── StatsCards.php (Stats cards section)
├── PomodoroTimer.php (Pomodoro timer component)
├── HabitsList.php (Today's habits list)
├── EnergyBar.php (Energy bar component)
├── QuickActions.php (Quick actions panel)
├── StreakCalendar.php (Streak heatmap calendar)
└── WeeklyProgress.php (Weekly progress component)

resources/views/livewire/dashboard/
├── dashboard-overview.blade.php
├── stats-cards.blade.php
├── pomodoro-timer.blade.php
├── habits-list.blade.php
├── energy-bar.blade.php
├── quick-actions.blade.php
├── streak-calendar.blade.php
└── weekly-progress.blade.php
```

### Routes

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/', fn() => view('livewire.dashboard.index'))->name('dashboard');
});
```

### Data Flow

1. User accesses dashboard route
2. Livewire Volt component loads and fetches user data
3. Component queries:
   - User stats (level, XP, streaks)
   - Today's scheduled habits
   - Completion rate
4. User interacts (completes habit)
5. Component calls services (PointsService, StreakService, AchievementService)
6. UI updates reactively

## Components and Interfaces

### 1. Dashboard Index Component (Volt Functional)

**Location:** `resources/views/livewire/dashboard/index.blade.php`

**State:**
```php
state([
    'greeting' => '',
    'completionRate' => 0,
    'todayHabits' => [],
]);
```

**Computed Properties:**
```php
$userLevel = computed(fn() => auth()->user()->level);
$userStats = computed(fn() => auth()->user()->stats);
$currentStreak = computed(fn() => auth()->user()->stats->current_global_streak);
```

**Methods:**
- `mount()`: Initialize greeting and load today's habits
- `getGreeting()`: Calculate greeting based on time of day
- `calculateCompletionRate()`: Calculate today's completion percentage

### 2. Stats Cards Component

**Location:** `resources/views/livewire/dashboard/stats-cards.blade.php`

Displays three cards:
- **Level/XP Card**: Shows current level, XP progress bar, and XP to next level
- **Streak Card**: Shows current streak with visual representation of last 7 days
- **Completion Rate Card**: Shows today's completion percentage

**Props:**
- `$userLevel`: UserLevel model instance
- `$userStats`: UserStats model instance

### 3. Pomodoro Timer Component (Volt Functional)

**Location:** `resources/views/livewire/dashboard/pomodoro-timer.blade.php`

**State:**
```php
state([
    'timer' => 1500, // 25 minutes in seconds
    'running' => false,
]);
```

**Methods:**
- `toggleTimer()`: Start/pause timer
- `tick()`: Decrement timer every second (using wire:poll)
- `formatTime()`: Format seconds to MM:SS
- `complete()`: Handle timer completion

### 4. Habits List Component (Volt Functional)

**Location:** `resources/views/livewire/dashboard/habits-list.blade.php`

**State:**
```php
state([
    'habits' => [],
]);
```

**Methods:**
- `mount()`: Load today's scheduled habits
- `toggleHabit($habitId)`: Mark habit as complete/incomplete
- `completeHabit($habitId)`: Process habit completion with services

**Integration with Services:**
```php
public function completeHabit($habitId)
{
    $habit = Habit::findOrFail($habitId);
    $user = auth()->user();
    
    // Create habit log
    $log = HabitLog::create([
        'habit_id' => $habit->id,
        'user_id' => $user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => $habit->points_reward,
    ]);
    
    // Award points
    app(PointsService::class)->awardPoints($user, $habit);
    
    // Update streaks
    app(StreakService::class)->updateStreak($habit);
    app(StreakService::class)->updateGlobalStreak($user);
    
    // Check achievements
    app(AchievementService::class)->checkAndUnlock($user, AchievementType::TOTAL_HABITS, $user->habitLogs()->count());
    
    // Refresh data
    $this->habits = $this->loadTodayHabits();
}
```

### 5. Energy Bar Component

**Location:** `app/Livewire/Dashboard/EnergyBar.php`

**Properties:**
```php
public $energyStatus;
```

**Listeners:**
```php
protected $listeners = [
    'energyUpdated' => 'refresh',
];
```

**Methods:**
- `mount(EnergyService $energyService)`: Initialize energy status
- `refresh(EnergyService $energyService)`: Refresh energy data from service

**Integration:**
- Uses `EnergyService::getEnergyStatus()` to fetch current energy level
- Listens for `energyUpdated` events to refresh automatically

### 6. Quick Actions Component

**Location:** `app/Livewire/Dashboard/QuickActions.php`

**Properties:**
```php
public $actions = [
    [
        'title' => 'Nuevo Hábito',
        'icon' => '➕',
        'route' => 'habits.create',
        'color' => 'blue',
    ],
    [
        'title' => 'Pomodoro',
        'icon' => '🍅',
        'route' => 'pomodoro.index',
        'color' => 'red',
    ],
    [
        'title' => 'Recompensas',
        'icon' => '🎁',
        'route' => 'rewards.index',
        'color' => 'purple',
    ],
    [
        'title' => 'Diario',
        'icon' => '📝',
        'route' => 'journal.create',
        'color' => 'green',
    ],
];
```

**Methods:**
- `render()`: Display quick actions panel

### 7. Streak Calendar Component

**Location:** `app/Livewire/Dashboard/StreakCalendar.php`

**Properties:**
```php
public $heatmapData;
public $year;
```

**Methods:**
- `mount(StatisticsService $statisticsService)`: Load heatmap data for the year
- `render()`: Display calendar heatmap

**Integration:**
- Uses `StatisticsService::getHeatmapData()` to fetch activity data for 365 days
- Displays activity intensity using color gradients

### 8. Weekly Progress Component

**Location:** `app/Livewire/Dashboard/WeeklyProgress.php`

**Properties:**
```php
public $weekDays = [];
```

**Methods:**
- `mount()`: Initialize weekly progress data
- `loadWeekProgress()`: Calculate completion percentage for each day of the week

**Data Structure:**
```php
[
    'date' => Carbon,
    'dayName' => 'Mon',
    'dayNumber' => '01',
    'completed' => 5,
    'total' => 8,
    'percentage' => 62,
    'isToday' => true,
]
```

## Data Models

### Queries Used

**Today's Habits:**
```php
$habits = auth()->user()->habits()
    ->where('is_active', true)
    ->with(['logs' => fn($q) => $q->whereDate('completed_date', today())])
    ->get()
    ->filter(fn($habit) => $habit->isScheduledForToday());
```

**User Stats:**
```php
$stats = auth()->user()->stats;
$level = auth()->user()->level;
```

**Completion Rate:**
```php
$scheduledCount = $todayHabits->count();
$completedCount = $todayHabits->filter(fn($h) => $h->isCompletedToday())->count();
$rate = $scheduledCount > 0 ? ($completedCount / $scheduledCount) * 100 : 0;
```

**Energy Status:**
```php
$energyStatus = app(EnergyService::class)->getEnergyStatus(auth()->user());
```

**Heatmap Data:**
```php
$heatmapData = app(StatisticsService::class)->getHeatmapData(auth()->user(), 365);
```

**Weekly Progress:**
```php
$startOfWeek = now()->startOfWeek();
$weekDays = collect(range(0, 6))->map(function ($day) use ($user, $startOfWeek) {
    $date = $startOfWeek->copy()->addDays($day);
    
    $habitsCompleted = $user->habitLogs()
        ->whereDate('completed_date', $date)
        ->count();

    $scheduledHabits = $user->habits()
        ->where('is_active', true)
        ->get()
        ->filter(fn($habit) => $habit->isScheduledForDay($date))
        ->count();

    return [
        'date' => $date,
        'completed' => $habitsCompleted,
        'total' => $scheduledHabits,
        'percentage' => $scheduledHabits > 0 
            ? round(($habitsCompleted / $scheduledHabits) * 100) 
            : 0,
    ];
});
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Greeting Time Consistency

*For any* hour of the day (0-23), the greeting message should match the expected greeting: "Buenos días" for 0-11, "Buenas tardes" for 12-18, "Buenas noches" for 19-23.

**Validates: Requirements 6.1, 6.2, 6.3**

### Property 2: User Name Display

*For any* authenticated user, the dashboard should display the user's name in both the greeting and the sidebar profile section.

**Validates: Requirements 1.2, 2.4**

### Property 3: Authentication Guard

*For any* unauthenticated request to the dashboard route, the system should redirect to the login page and never display dashboard content.

**Validates: Requirements 1.5**

### Property 4: User Stats Display

*For any* authenticated user, the dashboard should display their current level, XP, and streak values from the database.

**Validates: Requirements 3.1, 3.3, 3.5**

### Property 5: XP Progress Calculation

*For any* user level with current_xp and required_xp, the progress percentage should equal (current_xp / required_xp) * 100 and should be clamped between 0 and 100.

**Validates: Requirements 3.2**

### Property 6: Completion Rate Calculation

*For any* set of scheduled habits and completed habits, the completion rate should equal (completed / scheduled) * 100, or 0 if no habits are scheduled.

**Validates: Requirements 3.6**

### Property 7: Today's Habits Filter

*For any* user's active habits, only habits where `isScheduledForToday()` returns true should appear in the dashboard habits list.

**Validates: Requirements 5.1**

### Property 8: Habit Display Completeness

*For any* habit displayed in the list, it should include all required information: checkbox state, name, category, and XP points.

**Validates: Requirements 5.2**

### Property 9: Habit Completion Toggle

*For any* habit that is not completed today, clicking the checkbox should mark it as completed and invoke all required services.

**Validates: Requirements 5.3, 5.5**

### Property 10: Service Integration on Completion

*For any* habit completion, the system should invoke PointsService, StreakService, and AchievementService in sequence.

**Validates: Requirements 8.1, 8.2, 8.3**

### Property 11: Timer Countdown Behavior

*For any* running pomodoro timer, the timer value should decrease by 1 each second until it reaches 0, at which point it should stop.

**Validates: Requirements 4.3**

### Property 12: Timer Toggle Functionality

*For any* timer state (running or paused), clicking the toggle button should switch to the opposite state.

**Validates: Requirements 4.2, 4.4**

### Property 13: Category Color Mapping

*For any* habit with a category, the displayed color and icon should match the values defined in the HabitCategory enum.

**Validates: Requirements 8.5**

### Property 14: Energy Bar Display

*For any* authenticated user, the energy bar should display the current energy level obtained from EnergyService.

**Validates: Requirements 9.1, 9.2**

### Property 15: Energy Percentage Bounds

*For any* energy status, the displayed percentage should be between 0 and 100 inclusive.

**Validates: Requirements 9.3**

### Property 16: Energy Warning State

*For any* energy level below 30%, the energy bar should display in warning color state.

**Validates: Requirements 9.5**

### Property 17: Quick Actions Navigation

*For any* quick action button clicked, the system should navigate to the corresponding route defined in the action configuration.

**Validates: Requirements 10.3**

### Property 18: Quick Actions Completeness

*For any* quick actions panel, it should display all four actions: Nuevo Hábito, Pomodoro, Recompensas, and Diario.

**Validates: Requirements 10.2**

### Property 19: Heatmap Data Range

*For any* streak calendar, the heatmap should display exactly 365 days of activity data.

**Validates: Requirements 11.1**

### Property 20: Weekly Progress Day Count

*For any* weekly progress component, it should display exactly 7 days starting from the beginning of the current week.

**Validates: Requirements 12.1**

### Property 21: Weekly Progress Calculation

*For any* day in the weekly progress, the completion percentage should equal (completed_habits / scheduled_habits) * 100, or 0 if no habits are scheduled.

**Validates: Requirements 12.3**

### Property 22: Today Highlight in Weekly Progress

*For any* weekly progress display, the current day should be visually highlighted.

**Validates: Requirements 12.4**

**Validates: Requirements 4.2, 4.4**

### Property 13: Category Color Mapping

*For any* habit with a category, the displayed color and icon should match the values defined in the HabitCategory enum.

**Validates: Requirements 8.5**

## Error Handling

### Habit Completion Errors

```php
try {
    DB::transaction(function () use ($habit, $user) {
        // Create log
        // Award points
        // Update streaks
        // Check achievements
    });
} catch (\Exception $e) {
    Log::error('Habit completion failed', [
        'habit_id' => $habit->id,
        'user_id' => $user->id,
        'error' => $e->getMessage()
    ]);
    
    $this->dispatch('notification', [
        'type' => 'error',
        'message' => 'No se pudo completar el hábito. Intenta de nuevo.'
    ]);
}
```

### Data Loading Errors

- If user stats don't exist, create default stats
- If user level doesn't exist, create default level
- If no habits exist, show empty state message

### Timer Errors

- If timer reaches negative values, reset to 0
- If browser tab is inactive, pause timer to prevent drift

## Testing Strategy

### Unit Tests

**Test Coverage:**
- Greeting calculation for different times
- Completion rate calculation with various habit counts
- XP progress percentage calculation
- Habit filtering for today's schedule

**Example Tests:**
```php
test('greeting returns buenos dias for morning hours', function () {
    Carbon::setTestNow('2024-01-01 09:00:00');
    $greeting = getGreeting();
    expect($greeting)->toContain('Buenos días');
});

test('completion rate is zero when no habits scheduled', function () {
    $rate = calculateCompletionRate(collect([]), collect([]));
    expect($rate)->toBe(0);
});

test('completion rate is 100 when all habits completed', function () {
    $scheduled = collect([1, 2, 3]);
    $completed = collect([1, 2, 3]);
    $rate = calculateCompletionRate($scheduled, $completed);
    expect($rate)->toBe(100);
});
```

### Property-Based Tests

**Library:** Pest with custom generators

**Test Configuration:** Minimum 100 iterations per property test

**Property Tests:**

1. **Property 1: Greeting Time Consistency**
   - Generate random hours (0-23)
   - Verify greeting matches expected range
   - Tag: `Feature: dashboard-implementation, Property 1: Greeting Time Consistency`

2. **Property 2: Completion Rate Calculation**
   - Generate random habit counts (0-50)
   - Generate random completion counts (0-scheduled)
   - Verify rate calculation
   - Tag: `Feature: dashboard-implementation, Property 2: Completion Rate Calculation`

3. **Property 3: Habit Completion Idempotence**
   - Generate random habit
   - Complete twice
   - Verify points awarded only once
   - Tag: `Feature: dashboard-implementation, Property 3: Habit Completion Idempotence`

4. **Property 4: XP Progress Consistency**
   - Generate random XP values
   - Verify progress percentage is 0-100
   - Tag: `Feature: dashboard-implementation, Property 4: XP Progress Consistency`

5. **Property 5: Today's Habits Filter**
   - Generate random habits with various schedules
   - Verify only today's habits appear
   - Tag: `Feature: dashboard-implementation, Property 5: Today's Habits Filter`

### Integration Tests

**Livewire Component Tests:**
```php
test('dashboard loads with user data', function () {
    $user = User::factory()->create();
    
    Volt::actingAs($user)
        ->test('dashboard.index')
        ->assertSee($user->name)
        ->assertSee('Buenos')
        ->assertStatus(200);
});

test('completing habit updates stats', function () {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create();
    
    Volt::actingAs($user)
        ->test('dashboard.habits-list')
        ->call('completeHabit', $habit->id)
        ->assertDispatched('habitCompleted');
    
    expect($habit->fresh()->isCompletedToday())->toBeTrue();
});
```

## UI/UX Considerations

### Visual Design

- Use Tailwind CSS classes matching design2.html
- Maintain Flux UI component consistency
- Support dark mode (already in layout)
- Use Inter font family

### Accessibility

- Proper ARIA labels for interactive elements
- Keyboard navigation support
- Screen reader friendly
- Focus indicators on interactive elements

### Performance

- Eager load relationships to prevent N+1 queries
- Cache user stats for 5 minutes
- Use Livewire wire:poll sparingly (only for timer)
- Lazy load non-critical data

### Responsive Breakpoints

- Mobile: < 768px (stacked layout)
- Tablet: 768px - 1024px (2 column grid)
- Desktop: > 1024px (3 column grid)
