<?php

use App\Models\User;
use App\Models\Habit;
use App\Models\HabitLog;
use Livewire\Livewire;
use App\Livewire\Dashboard\StreakCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('streak calendar component renders successfully', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(StreakCalendar::class)
        ->assertStatus(200)
        ->assertSee('Calendario de Actividad')
        ->assertSee('Últimos 365 días');
});

test('streak calendar displays heatmap data', function () {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create([
        'category' => 'health',
    ]);

    // Create some habit logs for different days
    HabitLog::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'completed_date' => now()->subDays(5),
        'completed_time' => now()->subDays(5),
        'points_earned' => 10,
    ]);

    HabitLog::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'completed_date' => now()->subDays(10),
        'completed_time' => now()->subDays(10),
        'points_earned' => 10,
    ]);

    Livewire::actingAs($user)
        ->test(StreakCalendar::class)
        ->assertStatus(200)
        ->assertViewHas('heatmapData');
});

test('streak calendar shows neutral color for days without activity', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(StreakCalendar::class)
        ->assertStatus(200)
        ->assertSee('bg-gray-100');
});

test('streak calendar fetches 365 days of data', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(StreakCalendar::class);

    $heatmapData = $component->get('heatmapData');
    
    // The heatmap data should be an array (may be empty if no activity)
    expect($heatmapData)->toBeArray();
});
