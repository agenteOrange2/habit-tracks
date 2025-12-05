<?php

use App\Models\User;
use App\Models\UserStats;
use App\Models\UserLevel;
use App\Models\Habit;
use App\Models\HabitLog;
use Livewire\Livewire;
use App\Livewire\Dashboard\WeeklyProgress;
use App\Enums\HabitFrequency;

test('weekly progress component renders successfully', function () {
    $user = User::factory()->create();
    
    UserStats::create([
        'user_id' => $user->id,
        'total_points' => 0,
        'available_points' => 0,
        'total_habits_completed' => 0,
        'total_pomodoros' => 0,
        'current_global_streak' => 0,
        'best_global_streak' => 0,
    ]);
    
    UserLevel::create([
        'user_id' => $user->id,
        'current_level' => 1,
        'current_xp' => 0,
        'total_xp' => 0,
    ]);

    Livewire::actingAs($user)
        ->test(WeeklyProgress::class)
        ->assertStatus(200)
        ->assertSee('Progreso Semanal')
        ->assertSee('Últimos 7 días');
});

test('weekly progress displays 7 days', function () {
    $user = User::factory()->create();
    
    UserStats::create([
        'user_id' => $user->id,
        'total_points' => 0,
        'available_points' => 0,
        'total_habits_completed' => 0,
        'total_pomodoros' => 0,
        'current_global_streak' => 0,
        'best_global_streak' => 0,
    ]);
    
    UserLevel::create([
        'user_id' => $user->id,
        'current_level' => 1,
        'current_xp' => 0,
        'total_xp' => 0,
    ]);

    $component = Livewire::actingAs($user)
        ->test(WeeklyProgress::class);
    
    expect($component->get('weekDays'))->toHaveCount(7);
});

test('weekly progress highlights today', function () {
    $user = User::factory()->create();
    
    UserStats::create([
        'user_id' => $user->id,
        'total_points' => 0,
        'available_points' => 0,
        'total_habits_completed' => 0,
        'total_pomodoros' => 0,
        'current_global_streak' => 0,
        'best_global_streak' => 0,
    ]);
    
    UserLevel::create([
        'user_id' => $user->id,
        'current_level' => 1,
        'current_xp' => 0,
        'total_xp' => 0,
    ]);

    Livewire::actingAs($user)
        ->test(WeeklyProgress::class)
        ->assertSee('Hoy');
});

test('weekly progress calculates completion percentage correctly', function () {
    $user = User::factory()->create();
    
    UserStats::create([
        'user_id' => $user->id,
        'total_points' => 0,
        'available_points' => 0,
        'total_habits_completed' => 0,
        'total_pomodoros' => 0,
        'current_global_streak' => 0,
        'best_global_streak' => 0,
    ]);
    
    UserLevel::create([
        'user_id' => $user->id,
        'current_level' => 1,
        'current_xp' => 0,
        'total_xp' => 0,
    ]);

    // Create daily habits
    $habit1 = Habit::factory()->create([
        'user_id' => $user->id,
        'is_active' => true,
        'is_recurring' => true,
        'frequency' => HabitFrequency::DAILY,
    ]);
    
    $habit2 = Habit::factory()->create([
        'user_id' => $user->id,
        'is_active' => true,
        'is_recurring' => true,
        'frequency' => HabitFrequency::DAILY,
    ]);

    // Complete one habit today
    HabitLog::create([
        'habit_id' => $habit1->id,
        'user_id' => $user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    $component = Livewire::actingAs($user)
        ->test(WeeklyProgress::class);
    
    $weekDays = $component->get('weekDays');
    $today = collect($weekDays)->firstWhere('isToday', true);
    
    expect($today['percentage'])->toBe(50.0);
    expect($today['completed'])->toBe(1);
    expect($today['total'])->toBe(2);
});

test('weekly progress shows 0% when no habits scheduled', function () {
    $user = User::factory()->create();
    
    UserStats::create([
        'user_id' => $user->id,
        'total_points' => 0,
        'available_points' => 0,
        'total_habits_completed' => 0,
        'total_pomodoros' => 0,
        'current_global_streak' => 0,
        'best_global_streak' => 0,
    ]);
    
    UserLevel::create([
        'user_id' => $user->id,
        'current_level' => 1,
        'current_xp' => 0,
        'total_xp' => 0,
    ]);

    $component = Livewire::actingAs($user)
        ->test(WeeklyProgress::class);
    
    $weekDays = $component->get('weekDays');
    $today = collect($weekDays)->firstWhere('isToday', true);
    
    expect($today['percentage'])->toBe(0);
});

test('weekly progress shows 100% when all habits completed', function () {
    $user = User::factory()->create();
    
    UserStats::create([
        'user_id' => $user->id,
        'total_points' => 0,
        'available_points' => 0,
        'total_habits_completed' => 0,
        'total_pomodoros' => 0,
        'current_global_streak' => 0,
        'best_global_streak' => 0,
    ]);
    
    UserLevel::create([
        'user_id' => $user->id,
        'current_level' => 1,
        'current_xp' => 0,
        'total_xp' => 0,
    ]);

    // Create daily habits
    $habit1 = Habit::factory()->create([
        'user_id' => $user->id,
        'is_active' => true,
        'is_recurring' => true,
        'frequency' => HabitFrequency::DAILY,
    ]);
    
    $habit2 = Habit::factory()->create([
        'user_id' => $user->id,
        'is_active' => true,
        'is_recurring' => true,
        'frequency' => HabitFrequency::DAILY,
    ]);

    // Complete both habits today
    HabitLog::create([
        'habit_id' => $habit1->id,
        'user_id' => $user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);
    
    HabitLog::create([
        'habit_id' => $habit2->id,
        'user_id' => $user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    $component = Livewire::actingAs($user)
        ->test(WeeklyProgress::class);
    
    $weekDays = $component->get('weekDays');
    $today = collect($weekDays)->firstWhere('isToday', true);
    
    expect($today['percentage'])->toBe(100.0);
});
