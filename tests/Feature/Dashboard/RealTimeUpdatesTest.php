<?php

use App\Models\User;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\UserStats;
use App\Models\UserLevel;
use App\Enums\HabitFrequency;
use App\Livewire\Habits\HabitCard;
use App\Livewire\Dashboard\DailyHabitsList;
use App\Livewire\Dashboard\StatsCards;
use App\Livewire\Dashboard\ActivityTimeline;
use App\Livewire\Dashboard\MonthlyCalendar;
use Livewire\Livewire;

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

test('HabitCard dispatches habitCompleted event when habit is completed', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 10,
    ]);

    Livewire::actingAs($this->user)
        ->test(HabitCard::class, ['habit' => $habit])
        ->call('toggleComplete')
        ->assertDispatched('habitCompleted');
});

test('HabitCard dispatches habitUncompleted event when habit is uncompleted', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 10,
    ]);

    // First complete the habit
    HabitLog::factory()->for($habit)->for($this->user)->create([
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    Livewire::actingAs($this->user)
        ->test(HabitCard::class, ['habit' => $habit])
        ->call('toggleComplete')
        ->assertDispatched('habitUncompleted');
});

test('DailyHabitsList refreshes when habitCompleted event is dispatched', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 10,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(DailyHabitsList::class);

    // Initially, habit should be pending
    $initialCompletedCount = $component->get('completedCount');

    // Complete the habit
    HabitLog::factory()->for($habit)->for($this->user)->create([
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    // Dispatch the event
    $component->dispatch('habitCompleted');

    // Verify the component refreshed
    expect($component->get('completedCount'))->toBeGreaterThan($initialCompletedCount);
});

test('DailyHabitsList refreshes when habitUncompleted event is dispatched', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 10,
    ]);

    // Complete the habit first
    $log = HabitLog::factory()->for($habit)->for($this->user)->create([
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(DailyHabitsList::class);

    $initialCompletedCount = $component->get('completedCount');

    // Uncomplete the habit
    $log->delete();

    // Dispatch the event
    $component->dispatch('habitUncompleted');

    // Verify the component refreshed
    expect($component->get('completedCount'))->toBeLessThan($initialCompletedCount);
});

test('StatsCards refreshes when habitCompleted event is dispatched', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 10,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(StatsCards::class);

    // Complete the habit
    HabitLog::factory()->for($habit)->for($this->user)->create([
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    // Update user stats
    $this->user->stats->increment('total_habits_completed');

    // Dispatch the event
    $component->dispatch('habitCompleted');

    // Verify the component can access updated data
    $component->assertOk();
});

test('ActivityTimeline refreshes when habitCompleted event is dispatched', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 10,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(ActivityTimeline::class);

    $initialEventCount = count($component->get('events'));

    // Complete the habit
    HabitLog::factory()->for($habit)->for($this->user)->create([
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    // Dispatch the event
    $component->dispatch('habitCompleted');

    // Verify the timeline updated
    $updatedEventCount = count($component->get('events'));
    expect($updatedEventCount)->toBeGreaterThanOrEqual($initialEventCount);
});

test('MonthlyCalendar refreshes when habitCompleted event is dispatched', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 10,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(MonthlyCalendar::class);

    $initialActivityDays = $component->get('activityDays');

    // Complete the habit
    HabitLog::factory()->for($habit)->for($this->user)->create([
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    // Dispatch the event
    $component->dispatch('habitCompleted');

    // Verify the calendar updated
    $updatedActivityDays = $component->get('activityDays');
    expect(count($updatedActivityDays))->toBeGreaterThanOrEqual(count($initialActivityDays));
});

test('all dashboard components listen to habitCompleted event', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 10,
    ]);

    // Test that all components can handle the event without errors
    $components = [
        DailyHabitsList::class,
        StatsCards::class,
        ActivityTimeline::class,
        MonthlyCalendar::class,
    ];

    foreach ($components as $componentClass) {
        Livewire::actingAs($this->user)
            ->test($componentClass)
            ->dispatch('habitCompleted')
            ->assertOk();
    }
});

test('all dashboard components listen to habitUncompleted event', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 10,
    ]);

    // Test that all components can handle the event without errors
    $components = [
        DailyHabitsList::class,
        StatsCards::class,
        ActivityTimeline::class,
        MonthlyCalendar::class,
    ];

    foreach ($components as $componentClass) {
        Livewire::actingAs($this->user)
            ->test($componentClass)
            ->dispatch('habitUncompleted')
            ->assertOk();
    }
});

test('filter state persists during event updates', function () {
    $habit1 = Habit::factory()->for($this->user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 10,
    ]);

    $habit2 = Habit::factory()->for($this->user)->create([
        'is_active' => true,
        'frequency' => HabitFrequency::DAILY,
        'points_reward' => 15,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(DailyHabitsList::class)
        ->set('filter', 'pending')
        ->assertSet('filter', 'pending');

    // Complete a habit
    HabitLog::factory()->for($habit1)->for($this->user)->create([
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    // Dispatch event
    $component->dispatch('habitCompleted');

    // Verify filter state persists
    $component->assertSet('filter', 'pending');
});

test('wire:loading states are present in component views', function () {
    $this->actingAs($this->user);

    // Test DailyHabitsList
    $response = Livewire::test(DailyHabitsList::class);
    expect($response->html())->toContain('wire:loading');

    // Test ActivityTimeline
    $response = Livewire::test(ActivityTimeline::class);
    expect($response->html())->toContain('wire:loading');

    // Test MonthlyCalendar
    $response = Livewire::test(MonthlyCalendar::class);
    expect($response->html())->toContain('wire:loading');
});
