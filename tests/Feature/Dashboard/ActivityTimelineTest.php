<?php

use App\Models\{User, Habit, HabitLog, UserStats, UserLevel};
use App\Enums\{HabitFrequency, HabitCategory, HabitDifficulty};
use Livewire\Livewire;
use App\Livewire\Dashboard\ActivityTimeline;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create();
    
    UserStats::create([
        'user_id' => $this->user->id,
        'total_points' => 0,
        'available_points' => 0,
        'total_habits_completed' => 0,
        'total_pomodoros' => 0,
        'current_global_streak' => 0,
        'best_global_streak' => 0,
    ]);
    
    UserLevel::create([
        'user_id' => $this->user->id,
        'current_level' => 1,
        'current_xp' => 0,
        'total_xp' => 0,
    ]);
});

test('activity timeline component loads successfully', function () {
    Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class)
        ->assertStatus(200);
});

test('displays completed habits in timeline', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'name' => 'Morning Exercise',
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 50,
    ]);
    
    HabitLog::create([
        'habit_id' => $habit->id,
        'user_id' => $this->user->id,
        'completed_date' => today(),
        'completed_time' => now()->setTime(8, 30),
        'points_earned' => 50,
    ]);

    Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class)
        ->assertSee('Cronología del Día')
        ->assertSee('Morning Exercise')
        ->assertSee('+50 XP');
});

test('displays pending habits in timeline', function () {
    Habit::factory()->for($this->user)->create([
        'name' => 'Evening Meditation',
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
        'points_reward' => 30,
    ]);

    Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class)
        ->assertSee('Evening Meditation')
        ->assertSee('Pendiente');
});

test('orders events chronologically', function () {
    $habit1 = Habit::factory()->for($this->user)->create([
        'name' => 'Morning Habit',
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
    ]);
    
    $habit2 = Habit::factory()->for($this->user)->create([
        'name' => 'Afternoon Habit',
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
    ]);
    
    // Complete habits at different times
    HabitLog::create([
        'habit_id' => $habit2->id,
        'user_id' => $this->user->id,
        'completed_date' => today(),
        'completed_time' => now()->setTime(14, 0),
        'points_earned' => 30,
    ]);
    
    HabitLog::create([
        'habit_id' => $habit1->id,
        'user_id' => $this->user->id,
        'completed_date' => today(),
        'completed_time' => now()->setTime(8, 0),
        'points_earned' => 50,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class);
    
    $events = $component->get('events');
    
    expect(count($events))->toBeGreaterThanOrEqual(2);
    expect($events[0]['name'])->toBe('Morning Habit');
});

test('shows empty state when no events', function () {
    Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class)
        ->assertSee('No hay actividades para hoy');
});

test('refreshes timeline when habitCompleted event is dispatched', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'name' => 'New Habit',
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class);
    
    $initialCount = count($component->get('events'));
    
    // Complete the habit
    HabitLog::create([
        'habit_id' => $habit->id,
        'user_id' => $this->user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 50,
    ]);
    
    // Dispatch the event
    $component->dispatch('habitCompleted');
    
    $newCount = count($component->get('events'));
    
    expect($newCount)->toBeGreaterThanOrEqual($initialCount);
});

test('completed events show points earned', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'name' => 'Test Habit',
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 75,
    ]);
    
    HabitLog::create([
        'habit_id' => $habit->id,
        'user_id' => $this->user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 75,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class);
    
    $events = $component->get('events');
    $completedEvent = collect($events)->firstWhere('type', 'completed');
    
    expect($completedEvent)->not->toBeNull();
    expect($completedEvent['points'])->toBe(75);
});

test('pending events do not show points', function () {
    Habit::factory()->for($this->user)->create([
        'name' => 'Pending Habit',
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class);
    
    $events = $component->get('events');
    $pendingEvent = collect($events)->firstWhere('type', 'pending');
    
    expect($pendingEvent)->not->toBeNull();
    expect($pendingEvent['points'])->toBeNull();
});

test('only shows today\'s events', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'name' => 'Yesterday Habit',
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
    ]);
    
    // Complete habit yesterday
    HabitLog::create([
        'habit_id' => $habit->id,
        'user_id' => $this->user->id,
        'completed_date' => today()->subDay(),
        'completed_time' => now()->subDay(),
        'points_earned' => 50,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class);
    
    $events = $component->get('events');
    
    // The habit should appear as pending for today (not completed yesterday)
    // But the completed event from yesterday should not appear
    $yesterdayCompletedEvents = collect($events)->filter(function($event) {
        return $event['type'] === 'completed' && $event['name'] === 'Yesterday Habit';
    });
    
    expect($yesterdayCompletedEvents->count())->toBe(0);
});

test('excludes inactive habits from pending events', function () {
    Habit::factory()->for($this->user)->create([
        'name' => 'Inactive Habit',
        'is_active' => false,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class);
    
    $events = $component->get('events');
    
    expect(collect($events)->where('name', 'Inactive Habit')->count())->toBe(0);
});

test('uses reminder time for pending habits when available', function () {
    Habit::factory()->for($this->user)->create([
        'name' => 'Habit with Reminder',
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
        'reminder_enabled' => true,
        'reminder_time' => '09:00:00',
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class);
    
    $events = $component->get('events');
    $pendingEvent = collect($events)->firstWhere('name', 'Habit with Reminder');
    
    expect($pendingEvent)->not->toBeNull();
    expect($pendingEvent['time_formatted'])->toContain('9:00');
});
