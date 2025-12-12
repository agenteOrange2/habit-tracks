# Design Document

## Overview

El Habit Tracking Dashboard es una interfaz visual completa que permite a los usuarios monitorear su progreso diario en hábitos, visualizar rachas de actividad, completar misiones y ver estadísticas en tiempo real. El diseño sigue los patrones establecidos en design2.html y design6-2.html, utilizando Livewire para interactividad del lado del servidor y Alpine.js para interacciones del lado del cliente.

El dashboard se compone de múltiples componentes Livewire que trabajan juntos para proporcionar una experiencia fluida y reactiva, actualizándose automáticamente cuando el usuario completa hábitos sin necesidad de recargar la página.

## Architecture

### Component Structure

```
Dashboard Layout (Livewire Component)
├── Stats Cards Section
│   ├── Level Card (existing: StatsCards)
│   ├── Streak Card (existing: StatsCards)
│   └── Completion Rate Card (existing: StatsCards)
├── Main Content Area
│   ├── Achievements Section (new: RecentAchievements)
│   ├── Active Rewards Section (new: ActiveRewards)
│   └── Daily Missions List (enhanced: DailyHabitsList)
└── Sidebar Area
    ├── Calendar Widget (enhanced: MonthlyCalendar)
    └── Timeline Widget (new: ActivityTimeline)
```

### Technology Stack

- **Backend**: Laravel 11, Livewire 3
- **Frontend**: Blade templates, Alpine.js, Tailwind CSS
- **Database**: Existing models (Habit, HabitLog, Achievement, Reward, User)
- **Real-time Updates**: Livewire events and wire:poll

### Data Flow

1. User loads dashboard → Livewire components fetch data from database
2. User completes habit → HabitCard dispatches 'habitCompleted' event
3. All dashboard components listen for 'habitCompleted' event
4. Components refresh their data and update UI automatically
5. Animations and transitions provide visual feedback

## Components and Interfaces

### 1. DailyHabitsList Component

**Purpose**: Display today's scheduled habits with ability to complete/uncomplete them

**Livewire Class**: `App\Livewire\Dashboard\DailyHabitsList`

**Properties**:
- `$filter` (string): 'pending' or 'all'
- `$habits` (Collection): Today's scheduled habits
- `$completedCount` (int): Number of completed habits today
- `$totalCount` (int): Total scheduled habits today

**Methods**:
- `mount()`: Load today's habits
- `setFilter(string $filter)`: Change filter between pending/all
- `refreshHabits()`: Reload habits (called on habitCompleted event)

**Events Listened**:
- `habitCompleted`: Refresh habits list and counts

**Events Dispatched**: None (uses existing HabitCard component)

**View**: `resources/views/livewire/dashboard/daily-habits-list.blade.php`

---

### 2. RecentAchievements Component

**Purpose**: Display the 2-3 most recently unlocked achievements

**Livewire Class**: `App\Livewire\Dashboard\RecentAchievements`

**Properties**:
- `$achievements` (Collection): Recent achievements with pivot data

**Methods**:
- `mount()`: Load recent achievements
- `refreshAchievements()`: Reload achievements

**Events Listened**:
- `achievementUnlocked`: Refresh achievements list

**View**: `resources/views/livewire/dashboard/recent-achievements.blade.php`

---

### 3. ActiveRewards Component

**Purpose**: Display currently active/available rewards

**Livewire Class**: `App\Livewire\Dashboard\ActiveRewards`

**Properties**:
- `$rewards` (Collection): Active rewards

**Methods**:
- `mount()`: Load active rewards
- `refreshRewards()`: Reload rewards

**Events Listened**:
- `rewardClaimed`: Refresh rewards list

**View**: `resources/views/livewire/dashboard/active-rewards.blade.php`

---

### 4. MonthlyCalendar Component (Enhanced)

**Purpose**: Display calendar with activity indicators

**Livewire Class**: `App\Livewire\Dashboard\MonthlyCalendar`

**Properties**:
- `$currentMonth` (Carbon): Currently displayed month
- `$activityDays` (array): Days with completed habits
- `$selectedDay` (Carbon|null): Currently selected day

**Methods**:
- `mount()`: Initialize calendar for current month
- `previousMonth()`: Navigate to previous month
- `nextMonth()`: Navigate to next month
- `selectDay(string $date)`: Select a day and show details
- `loadActivityDays()`: Load days with activity for current month

**Events Listened**:
- `habitCompleted`: Refresh activity days

**View**: `resources/views/livewire/dashboard/monthly-calendar.blade.php`

---

### 5. ActivityTimeline Component

**Purpose**: Display chronological timeline of today's events

**Livewire Class**: `App\Livewire\Dashboard\ActivityTimeline`

**Properties**:
- `$events` (Collection): Timeline events (completed habits, scheduled habits)

**Methods**:
- `mount()`: Load today's events
- `refreshTimeline()`: Reload timeline

**Events Listened**:
- `habitCompleted`: Refresh timeline

**View**: `resources/views/livewire/dashboard/activity-timeline.blade.php`

---

### 6. HabitTrackingDashboard Component (Main Container)

**Purpose**: Main container that orchestrates all dashboard components

**Livewire Class**: `App\Livewire\Dashboard\HabitTrackingDashboard`

**Properties**: None (container only)

**Methods**:
- `render()`: Render dashboard layout

**View**: `resources/views/livewire/dashboard/habit-tracking-dashboard.blade.php`

## Data Models

### Existing Models (No Changes Required)

**Habit Model**:
- Already has `isCompletedToday()` method
- Already has `isScheduledForToday()` method
- Already has relationship with HabitLog

**HabitLog Model**:
- Stores completion records
- Has `completed_date` and `completed_time`
- Has `points_earned` field

**Achievement Model**:
- Has pivot table `user_achievements` with `unlocked_at`
- Has `name`, `description`, `icon`, `xp_reward`

**Reward Model**:
- Has `name`, `description`, `cost`, `category`
- Has relationship with RewardClaim

**User Model**:
- Has `stats` relationship (UserStats)
- Has `level` relationship (UserLevel)
- Has `habits` relationship
- Has `habitLogs` relationship

### Query Optimization

**For Daily Habits List**:
```php
$habits = Auth::user()->habits()
    ->where('is_active', true)
    ->with(['logs' => fn($q) => $q->whereDate('completed_date', today())])
    ->get()
    ->filter(fn($habit) => $habit->isScheduledForToday());
```

**For Calendar Activity**:
```php
$activityDays = Auth::user()->habitLogs()
    ->whereYear('completed_date', $year)
    ->whereMonth('completed_date', $month)
    ->select('completed_date')
    ->distinct()
    ->pluck('completed_date')
    ->map(fn($date) => $date->day)
    ->toArray();
```

**For Timeline Events**:
```php
$completedHabits = Auth::user()->habitLogs()
    ->with('habit')
    ->whereDate('completed_date', today())
    ->orderBy('completed_time')
    ->get();

$scheduledHabits = Auth::user()->habits()
    ->where('is_active', true)
    ->whereDoesntHave('logs', fn($q) => $q->whereDate('completed_date', today()))
    ->get()
    ->filter(fn($habit) => $habit->isScheduledForToday());
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Stats consistency after habit completion

*For any* user and any habit completion action, when a habit is marked as completed, all dashboard statistics (level progress, streak count, completion rate) should reflect the updated state consistently across all components.

**Validates: Requirements 1.1, 2.5**

### Property 2: Filter correctness

*For any* filter state ('pending' or 'all'), the displayed habits should match the filter criteria: pending filter shows only uncompleted habits scheduled for today, all filter shows all habits scheduled for today.

**Validates: Requirements 6.2, 6.3**

### Property 3: Calendar activity indicators

*For any* month displayed in the calendar, a day should be marked as having activity if and only if there exists at least one HabitLog record for that user on that date.

**Validates: Requirements 3.2**

### Property 4: Timeline chronological ordering

*For any* set of events in the timeline, events should be ordered chronologically by time, with completed events showing actual completion time and pending events showing scheduled time.

**Validates: Requirements 4.1, 4.2, 4.3**

### Property 5: Real-time update propagation

*For any* habit completion or uncompletion action, all dashboard components listening to the 'habitCompleted' event should update their displayed data within the same request cycle.

**Validates: Requirements 2.5, 8.4**

### Property 6: Completion rate calculation

*For any* set of habits scheduled for today, the completion rate should equal (number of completed habits / total scheduled habits) * 100, rounded to the nearest integer.

**Validates: Requirements 1.4**

### Property 7: Responsive layout adaptation

*For any* viewport width, the dashboard layout should adapt appropriately: mobile (< 768px) shows single column, tablet (768px-1279px) shows 2-column grid, desktop (≥ 1280px) shows full layout with sidebar.

**Validates: Requirements 7.1, 7.2, 7.3**

## Error Handling

### Component Loading Errors

**Scenario**: Database query fails or times out

**Handling**:
- Display skeleton loading state with `wire:loading` directive
- Show error message if data fails to load after timeout
- Provide retry button for user to manually refresh

**Implementation**:
```blade
<div wire:loading.class="animate-pulse">
    <!-- Content -->
</div>
<div wire:loading.remove>
    @if($errors->any())
        <div class="text-red-600">Error loading data. <button wire:click="$refresh">Retry</button></div>
    @endif
</div>
```

### Habit Completion Errors

**Scenario**: Habit completion fails due to validation or database error

**Handling**:
- Catch exception in HabitCard component
- Display error notification to user
- Revert UI state to pre-completion state
- Log error for debugging

**Implementation**:
```php
try {
    // Completion logic
} catch (\Exception $e) {
    $this->dispatch('error', message: 'No se pudo completar el hábito. Intenta de nuevo.');
    Log::error('Habit completion failed', ['habit_id' => $this->habit->id, 'error' => $e->getMessage()]);
}
```

### Empty State Handling

**Scenario**: User has no habits scheduled for today

**Handling**:
- Display friendly empty state message
- Provide call-to-action button to create first habit
- Show motivational message

**Implementation**:
```blade
@if($habits->isEmpty())
    <div class="text-center py-12">
        <p class="text-slate-500 mb-4">No tienes hábitos programados para hoy</p>
        <a href="{{ route('habits.create') }}" class="btn-primary">Crear tu primer hábito</a>
    </div>
@endif
```

### Network/Connectivity Errors

**Scenario**: Livewire request fails due to network issues

**Handling**:
- Livewire automatically retries failed requests
- Show loading indicator during retry
- Display error message if all retries fail
- Preserve user's action to retry when connection restored

## Testing Strategy

### Unit Testing

**Component Unit Tests**:
- Test each Livewire component's methods in isolation
- Mock database queries and relationships
- Verify correct data transformations
- Test event dispatching and listening

**Example Test Cases**:
```php
// Test DailyHabitsList filter
test('pending filter shows only uncompleted habits', function() {
    $user = User::factory()->create();
    $habit1 = Habit::factory()->for($user)->create();
    $habit2 = Habit::factory()->for($user)->create();
    HabitLog::factory()->for($habit1)->for($user)->create(['completed_date' => today()]);
    
    Livewire::actingAs($user)
        ->test(DailyHabitsList::class)
        ->set('filter', 'pending')
        ->assertSee($habit2->name)
        ->assertDontSee($habit1->name);
});

// Test calendar activity indicators
test('calendar marks days with completed habits', function() {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create();
    HabitLog::factory()->for($habit)->for($user)->create([
        'completed_date' => today()->subDays(5)
    ]);
    
    Livewire::actingAs($user)
        ->test(MonthlyCalendar::class)
        ->assertSet('activityDays', fn($days) => in_array(today()->subDays(5)->day, $days));
});
```

### Property-Based Testing

Property-based tests will use **Pest with Faker** for generating random test data.

**Property Test 1: Stats consistency**
```php
test('stats remain consistent after multiple habit completions', function() {
    $user = User::factory()->create();
    $habits = Habit::factory()->count(rand(3, 10))->for($user)->create();
    
    $initialXP = $user->level->current_xp;
    $completedCount = 0;
    
    foreach($habits->random(rand(1, $habits->count())) as $habit) {
        // Complete habit
        $log = HabitLog::factory()->for($habit)->for($user)->create([
            'completed_date' => today(),
            'points_earned' => $habit->points_reward
        ]);
        $completedCount++;
    }
    
    $user->refresh();
    
    // Verify XP increased correctly
    expect($user->level->current_xp)->toBeGreaterThan($initialXP);
    
    // Verify completion rate is correct
    $expectedRate = ($completedCount / $habits->count()) * 100;
    $component = Livewire::actingAs($user)->test(StatsCards::class);
    expect($component->completionRate)->toBe(round($expectedRate));
})->repeat(100);
```

**Property Test 2: Filter correctness**
```php
test('filter always shows correct subset of habits', function() {
    $user = User::factory()->create();
    $totalHabits = rand(5, 15);
    $habits = Habit::factory()->count($totalHabits)->for($user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY
    ]);
    
    // Complete random subset
    $completedCount = rand(0, $totalHabits);
    $habits->random($completedCount)->each(function($habit) use ($user) {
        HabitLog::factory()->for($habit)->for($user)->create(['completed_date' => today()]);
    });
    
    $component = Livewire::actingAs($user)->test(DailyHabitsList::class);
    
    // Test 'all' filter
    $component->set('filter', 'all');
    expect($component->habits)->toHaveCount($totalHabits);
    
    // Test 'pending' filter
    $component->set('filter', 'pending');
    expect($component->habits)->toHaveCount($totalHabits - $completedCount);
})->repeat(100);
```

**Property Test 3: Calendar activity correctness**
```php
test('calendar activity indicators match actual logs', function() {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create();
    
    // Create random logs in current month
    $daysWithLogs = collect(range(1, 28))->random(rand(5, 20));
    $daysWithLogs->each(function($day) use ($user, $habit) {
        HabitLog::factory()->for($habit)->for($user)->create([
            'completed_date' => today()->startOfMonth()->addDays($day - 1)
        ]);
    });
    
    $component = Livewire::actingAs($user)->test(MonthlyCalendar::class);
    
    // Verify all logged days are marked
    foreach($daysWithLogs as $day) {
        expect($component->activityDays)->toContain($day);
    }
    
    // Verify no extra days are marked
    expect($component->activityDays)->toHaveCount($daysWithLogs->count());
})->repeat(100);
```

### Integration Testing

**Full Dashboard Flow**:
- Test complete user journey from loading dashboard to completing habits
- Verify all components update correctly
- Test responsive behavior at different breakpoints
- Verify animations and transitions work

**Browser Testing with Dusk**:
```php
test('user can complete habit and see all updates', function() {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create(['is_active' => true, 'frequency' => HabitFrequency::DAILY]);
    
    $this->browse(function (Browser $browser) use ($user, $habit) {
        $browser->loginAs($user)
            ->visit('/dashboard')
            ->waitForText($habit->name)
            ->click('@complete-habit-' . $habit->id)
            ->waitForText('+' . $habit->points_reward . ' puntos')
            ->assertSee('Nivel')
            ->assertSee('Racha de Fuego');
    });
});
```

### Performance Testing

**Load Time Benchmarks**:
- Dashboard initial load: < 500ms
- Habit completion action: < 200ms
- Component refresh after event: < 100ms

**Query Optimization**:
- Use eager loading for all relationships
- Cache computed properties for 5 minutes
- Use database indexing on frequently queried columns

