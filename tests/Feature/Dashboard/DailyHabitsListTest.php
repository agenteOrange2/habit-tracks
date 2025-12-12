<?php

use App\Models\{User, Habit, HabitLog, UserStats, UserLevel};
use App\Enums\{HabitCategory, HabitDifficulty, HabitFrequency};
use Livewire\Livewire;
use App\Livewire\Dashboard\DailyHabitsList;

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

test('daily habits list component loads successfully', function () {
    Livewire::actingAs($this->user)
        ->test(DailyHabitsList::class)
        ->assertStatus(200);
});

test('daily habits list displays habits with filter buttons', function () {
    $habit = Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Morning Exercise',
        'description' => 'Exercise for 30 minutes',
        'category' => HabitCategory::HEALTH,
        'difficulty' => HabitDifficulty::MEDIUM,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 50,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);

    Livewire::actingAs($this->user)
        ->test(DailyHabitsList::class)
        ->assertSee('Misiones de Hoy')
        ->assertSee('Pendientes')
        ->assertSee('Todas')
        ->assertSee('Morning Exercise');
});

test('filter shows only pending habits', function () {
    $habit1 = Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Completed Habit',
        'category' => HabitCategory::HEALTH,
        'difficulty' => HabitDifficulty::EASY,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 30,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);
    
    $habit2 = Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Pending Habit',
        'category' => HabitCategory::LEARNING,
        'difficulty' => HabitDifficulty::MEDIUM,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 50,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);
    
    // Complete habit1
    HabitLog::create([
        'habit_id' => $habit1->id,
        'user_id' => $this->user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 30,
        'pomodoros_used' => 0,
    ]);

    Livewire::actingAs($this->user)
        ->test(DailyHabitsList::class)
        ->assertSet('filter', 'pending')
        ->assertSee('Pending Habit')
        ->assertDontSee('Completed Habit');
});

test('filter shows all habits when set to all', function () {
    $habit1 = Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Completed Habit',
        'category' => HabitCategory::HEALTH,
        'difficulty' => HabitDifficulty::EASY,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 30,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);
    
    $habit2 = Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Pending Habit',
        'category' => HabitCategory::LEARNING,
        'difficulty' => HabitDifficulty::MEDIUM,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 50,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);
    
    // Complete habit1
    HabitLog::create([
        'habit_id' => $habit1->id,
        'user_id' => $this->user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 30,
        'pomodoros_used' => 0,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(DailyHabitsList::class)
        ->call('setFilter', 'all')
        ->assertSet('filter', 'all')
        ->assertSet('totalCount', 2);
    
    // Debug: check what habits are in the collection
    expect($component->get('habits')->count())->toBe(2);
    
    $component->assertSee('Completed Habit')
        ->assertSee('Pending Habit');
});

test('shows correct count in filter buttons', function () {
    $habit1 = Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Habit 1',
        'category' => HabitCategory::HEALTH,
        'difficulty' => HabitDifficulty::EASY,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 30,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);
    
    $habit2 = Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Habit 2',
        'category' => HabitCategory::LEARNING,
        'difficulty' => HabitDifficulty::MEDIUM,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 50,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);
    
    // Complete habit1
    HabitLog::create([
        'habit_id' => $habit1->id,
        'user_id' => $this->user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 30,
        'pomodoros_used' => 0,
    ]);

    Livewire::actingAs($this->user)
        ->test(DailyHabitsList::class)
        ->assertSee('Pendientes (1)')
        ->assertSet('totalCount', 2)
        ->assertSet('completedCount', 1);
});

test('shows empty state when no habits scheduled', function () {
    Livewire::actingAs($this->user)
        ->test(DailyHabitsList::class)
        ->assertSee('No tienes hábitos programados para hoy');
});

test('shows completion message when all habits completed', function () {
    $habit = Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Test Habit',
        'category' => HabitCategory::HEALTH,
        'difficulty' => HabitDifficulty::EASY,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 30,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);
    
    // Complete the habit
    HabitLog::create([
        'habit_id' => $habit->id,
        'user_id' => $this->user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 30,
        'pomodoros_used' => 0,
    ]);

    Livewire::actingAs($this->user)
        ->test(DailyHabitsList::class)
        ->assertSet('filter', 'pending')
        ->assertSee('¡Todas las misiones completadas! 🎉');
});

test('refreshes habits when habitCompleted event is dispatched', function () {
    $habit = Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Test Habit',
        'category' => HabitCategory::HEALTH,
        'difficulty' => HabitDifficulty::EASY,
        'frequency' => HabitFrequency::DAILY,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 30,
        'current_streak' => 0,
        'best_streak' => 0,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(DailyHabitsList::class)
        ->assertSet('completedCount', 0);
    
    // Complete the habit
    HabitLog::create([
        'habit_id' => $habit->id,
        'user_id' => $this->user->id,
        'completed_date' => today(),
        'completed_time' => now(),
        'points_earned' => 30,
        'pomodoros_used' => 0,
    ]);
    
    // Dispatch the event
    $component->dispatch('habitCompleted')
        ->assertSet('completedCount', 1);
});
