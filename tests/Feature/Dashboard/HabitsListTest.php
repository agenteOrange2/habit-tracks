<?php

use App\Models\{User, Habit, UserStats, UserLevel};
use App\Enums\{HabitCategory, HabitDifficulty, HabitFrequency};
use Livewire\Livewire;
use App\Livewire\Dashboard\HabitsList;

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

test('habits list component loads successfully', function () {
    Livewire::actingAs($this->user)
        ->test(HabitsList::class)
        ->assertStatus(200);
});

test('habits list displays daily habits', function () {
    $habit = Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Morning Exercise',
        'description' => 'Exercise for 30 minutes',
        'category' => HabitCategory::HEALTH->value,
        'difficulty' => HabitDifficulty::MEDIUM->value,
        'frequency' => HabitFrequency::DAILY->value,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 50,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);

    Livewire::actingAs($this->user)
        ->test(HabitsList::class)
        ->assertSee('Morning Exercise')
        ->assertSee('50 XP');
});

test('habits list shows empty state when no habits scheduled', function () {
    Livewire::actingAs($this->user)
        ->test(HabitsList::class)
        ->assertSee('No tienes hábitos programados para hoy');
});

test('completing a habit updates the list', function () {
    $habit = Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Read Book',
        'description' => 'Read for 20 minutes',
        'category' => HabitCategory::LEARNING->value,
        'difficulty' => HabitDifficulty::EASY->value,
        'frequency' => HabitFrequency::DAILY->value,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 30,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);

    Livewire::actingAs($this->user)
        ->test(HabitsList::class)
        ->call('toggleHabit', $habit->id)
        ->assertDispatched('habitCompleted')
        ->assertDispatched('notification');
    
    expect($habit->fresh()->isCompletedToday())->toBeTrue();
});

test('cannot complete another users habit', function () {
    $otherUser = User::factory()->create();
    
    UserStats::create([
        'user_id' => $otherUser->id,
        'total_points' => 0,
        'available_points' => 0,
        'total_habits_completed' => 0,
        'total_pomodoros' => 0,
        'current_global_streak' => 0,
        'best_global_streak' => 0,
    ]);
    
    $habit = Habit::create([
        'user_id' => $otherUser->id,
        'name' => 'Other User Habit',
        'description' => 'Test',
        'category' => HabitCategory::PERSONAL->value,
        'difficulty' => HabitDifficulty::EASY->value,
        'frequency' => HabitFrequency::DAILY->value,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 20,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);

    Livewire::actingAs($this->user)
        ->test(HabitsList::class)
        ->call('toggleHabit', $habit->id)
        ->assertDispatched('notification');
    
    expect($habit->fresh()->isCompletedToday())->toBeFalse();
});
