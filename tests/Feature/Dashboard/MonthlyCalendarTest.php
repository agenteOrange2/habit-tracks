<?php

use App\Models\User;
use App\Models\Habit;
use App\Models\HabitLog;
use Livewire\Livewire;
use App\Livewire\Dashboard\MonthlyCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('monthly calendar component renders successfully', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(MonthlyCalendar::class)
        ->assertStatus(200)
        ->assertSee(now()->format('F Y'));
});

test('monthly calendar loads activity days for current month', function () {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create();

    // Create habit logs for specific days in current month
    HabitLog::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'completed_date' => now()->startOfMonth()->addDays(5),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    HabitLog::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'completed_date' => now()->startOfMonth()->addDays(10),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    $component = Livewire::actingAs($user)
        ->test(MonthlyCalendar::class);

    $activityDays = $component->get('activityDays');
    
    expect($activityDays)->toBeArray()
        ->and($activityDays)->toContain(6) // Day 6 (5 + 1)
        ->and($activityDays)->toContain(11); // Day 11 (10 + 1)
});

test('monthly calendar navigates to previous month', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(MonthlyCalendar::class);

    $initialMonth = $component->get('currentMonth');
    
    $component->call('previousMonth');
    
    $newMonth = $component->get('currentMonth');
    
    expect($newMonth)->not->toBe($initialMonth);
});

test('monthly calendar navigates to next month', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(MonthlyCalendar::class);

    $initialMonth = $component->get('currentMonth');
    
    $component->call('nextMonth');
    
    $newMonth = $component->get('currentMonth');
    
    expect($newMonth)->not->toBe($initialMonth);
});

test('monthly calendar selects day and shows habits', function () {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create(['name' => 'Test Habit']);

    $testDate = now()->startOfMonth()->addDays(5);
    
    HabitLog::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'completed_date' => $testDate,
        'completed_time' => $testDate->setTime(10, 30),
        'points_earned' => 15,
    ]);

    $component = Livewire::actingAs($user)
        ->test(MonthlyCalendar::class)
        ->call('selectDay', $testDate->format('Y-m-d'));

    $selectedDayHabits = $component->get('selectedDayHabits');
    
    expect($selectedDayHabits)->toBeArray()
        ->and($selectedDayHabits)->toHaveCount(1)
        ->and($selectedDayHabits[0]['name'])->toBe('Test Habit')
        ->and($selectedDayHabits[0]['points'])->toBe(15);
});

test('monthly calendar refreshes on habitCompleted event', function () {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create();

    $component = Livewire::actingAs($user)
        ->test(MonthlyCalendar::class);

    $initialActivityDays = $component->get('activityDays');
    
    // Create a new habit log
    HabitLog::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'completed_date' => now(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    // Dispatch the event
    $component->dispatch('habitCompleted');

    $newActivityDays = $component->get('activityDays');
    
    expect($newActivityDays)->toContain(now()->day);
});

test('monthly calendar shows empty state for day with no habits', function () {
    $user = User::factory()->create();

    $testDate = now()->startOfMonth()->addDays(5);
    
    Livewire::actingAs($user)
        ->test(MonthlyCalendar::class)
        ->call('selectDay', $testDate->format('Y-m-d'))
        ->assertSee('No hay hábitos completados en esta fecha');
});
