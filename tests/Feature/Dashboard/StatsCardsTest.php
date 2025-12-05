<?php

use App\Models\User;
use App\Models\UserStats;
use App\Models\UserLevel;
use App\Models\Habit;
use App\Models\HabitLog;
use Livewire\Livewire;
use App\Livewire\Dashboard\StatsCards;

test('stats cards component renders successfully', function () {
    $user = User::factory()->create();
    
    UserStats::create([
        'user_id' => $user->id,
        'total_points' => 100,
        'available_points' => 50,
        'total_habits_completed' => 10,
        'total_pomodoros' => 5,
        'current_global_streak' => 7,
        'best_global_streak' => 10,
    ]);
    
    UserLevel::create([
        'user_id' => $user->id,
        'current_level' => 5,
        'current_xp' => 250,
        'total_xp' => 250,
    ]);

    Livewire::actingAs($user)
        ->test(StatsCards::class)
        ->assertStatus(200)
        ->assertSee('Nivel 5')
        ->assertSee('7 Días')
        ->assertSee('Racha de Actividad');
});

test('stats cards displays correct level information', function () {
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
        'current_level' => 12,
        'current_xp' => 750,
        'total_xp' => 750,
    ]);

    Livewire::actingAs($user)
        ->test(StatsCards::class)
        ->assertSee('Nivel 12')
        ->assertSee('750 / 1200 XP');
});

test('stats cards displays correct streak information', function () {
    $user = User::factory()->create();
    
    UserStats::create([
        'user_id' => $user->id,
        'total_points' => 0,
        'available_points' => 0,
        'total_habits_completed' => 0,
        'total_pomodoros' => 0,
        'current_global_streak' => 14,
        'best_global_streak' => 20,
    ]);
    
    UserLevel::create([
        'user_id' => $user->id,
        'current_level' => 1,
        'current_xp' => 0,
        'total_xp' => 0,
    ]);

    Livewire::actingAs($user)
        ->test(StatsCards::class)
        ->assertSee('14 Días');
});

test('stats cards calculates completion rate correctly with no habits', function () {
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
        ->test(StatsCards::class)
        ->assertSee('0%');
});

test('stats cards calculates completion rate correctly with habits', function () {
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

    // Create habits
    $habit1 = Habit::factory()->create([
        'user_id' => $user->id,
        'is_active' => true,
        'is_recurring' => true,
        'frequency' => 'daily',
    ]);
    
    $habit2 = Habit::factory()->create([
        'user_id' => $user->id,
        'is_active' => true,
        'is_recurring' => true,
        'frequency' => 'daily',
    ]);

    // Complete one habit
    HabitLog::create([
        'habit_id' => $habit1->id,
        'user_id' => $user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 10,
    ]);

    Livewire::actingAs($user)
        ->test(StatsCards::class)
        ->assertSee('50%');
});
