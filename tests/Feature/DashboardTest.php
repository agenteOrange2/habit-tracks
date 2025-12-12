<?php

use App\Models\User;
use App\Models\UserStats;
use App\Models\UserLevel;

test('guests are redirected to the login page', function () {
    $this->get('/')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    
    // Create user stats and level
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

    $this->actingAs($user);

    $this->get('/')->assertStatus(200);
});

test('dashboard displays user greeting', function () {
    $user = User::factory()->create(['name' => 'Test User']);
    
    // Create user stats and level
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

    $this->actingAs($user);

    $response = $this->get('/');
    
    $response->assertStatus(200);
    $response->assertSee('Test User', false);
});

test('dashboard includes all integrated components', function () {
    $user = User::factory()->create(['name' => 'Test User']);
    
    // Create user stats and level
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

    $this->actingAs($user);

    $response = $this->get('/');
    
    $response->assertStatus(200);
    
    // Verify all components are present
    $response->assertSeeLivewire('dashboard.stats-cards');
    $response->assertSeeLivewire('dashboard.pomodoro-timer');
    $response->assertSeeLivewire('dashboard.habits-list');
    $response->assertSeeLivewire('dashboard.energy-bar');
    $response->assertSeeLivewire('dashboard.quick-actions');
    $response->assertSeeLivewire('dashboard.weekly-progress');
    $response->assertSeeLivewire('dashboard.streak-calendar');
    $response->assertSeeLivewire('dashboard.recent-achievements');
    $response->assertSeeLivewire('dashboard.active-rewards');
});
test('d
ashboard layout is responsive', function () {
    $user = User::factory()->create();
    
    // Create user stats and level
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

    $this->actingAs($user);

    $response = $this->get('/');
    
    $response->assertStatus(200);
    
    // Verify responsive grid classes are present
    $response->assertSee('grid-cols-1', false); // Mobile stacking
    $response->assertSee('lg:grid-cols-2', false); // Desktop 2-column grid
    $response->assertSee('sm:grid-cols-4', false); // Quick actions responsive grid
});
