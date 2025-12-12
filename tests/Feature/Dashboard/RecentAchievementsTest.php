<?php

use App\Models\{User, Achievement, UserStats, UserLevel};
use Livewire\Livewire;
use App\Livewire\Dashboard\RecentAchievements;

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

test('recent achievements component loads successfully', function () {
    Livewire::actingAs($this->user)
        ->test(RecentAchievements::class)
        ->assertStatus(200);
});

test('displays recent achievements with details', function () {
    $achievement = Achievement::create([
        'name' => 'First Steps',
        'description' => 'Complete your first habit',
        'icon' => '🎯',
        'category' => 'habits',
        'requirement_type' => 'habits_completed',
        'requirement_value' => 1,
        'points_reward' => 50,
        'is_secret' => false,
    ]);
    
    // Unlock the achievement
    $this->user->achievements()->attach($achievement->id, [
        'unlocked_at' => now(),
        'progress' => 1,
    ]);

    Livewire::actingAs($this->user)
        ->test(RecentAchievements::class)
        ->assertSee('Logros Recientes')
        ->assertSee('First Steps')
        ->assertSee('Complete your first habit')
        ->assertSee('+50 XP');
});

test('displays multiple recent achievements in order', function () {
    $achievement1 = Achievement::create([
        'name' => 'First Achievement',
        'description' => 'First one',
        'icon' => '🎯',
        'category' => 'habits',
        'requirement_type' => 'habits_completed',
        'requirement_value' => 1,
        'points_reward' => 50,
        'is_secret' => false,
    ]);
    
    $achievement2 = Achievement::create([
        'name' => 'Second Achievement',
        'description' => 'Second one',
        'icon' => '🏆',
        'category' => 'habits',
        'requirement_type' => 'habits_completed',
        'requirement_value' => 5,
        'points_reward' => 100,
        'is_secret' => false,
    ]);
    
    // Unlock achievements at different times
    $this->user->achievements()->attach($achievement1->id, [
        'unlocked_at' => now()->subDays(2),
        'progress' => 1,
    ]);
    
    $this->user->achievements()->attach($achievement2->id, [
        'unlocked_at' => now()->subDay(),
        'progress' => 5,
    ]);

    Livewire::actingAs($this->user)
        ->test(RecentAchievements::class)
        ->assertSee('Second Achievement')
        ->assertSee('First Achievement');
});

test('limits display to 3 most recent achievements', function () {
    // Create 5 achievements
    for ($i = 1; $i <= 5; $i++) {
        $achievement = Achievement::create([
            'name' => "Achievement $i",
            'description' => "Description $i",
            'icon' => '🎯',
            'category' => 'habits',
            'requirement_type' => 'habits_completed',
            'requirement_value' => $i,
            'points_reward' => 50 * $i,
            'is_secret' => false,
        ]);
        
        $this->user->achievements()->attach($achievement->id, [
            'unlocked_at' => now()->subDays(5 - $i),
            'progress' => $i,
        ]);
    }

    $component = Livewire::actingAs($this->user)
        ->test(RecentAchievements::class);
    
    expect($component->get('achievements')->count())->toBe(3);
    
    // Should see the 3 most recent
    $component->assertSee('Achievement 5')
        ->assertSee('Achievement 4')
        ->assertSee('Achievement 3')
        ->assertDontSee('Achievement 1');
});

test('shows empty state when no achievements unlocked', function () {
    Livewire::actingAs($this->user)
        ->test(RecentAchievements::class)
        ->assertSee('Aún no has desbloqueado logros');
});

test('refreshes achievements when achievementUnlocked event is dispatched', function () {
    $achievement = Achievement::create([
        'name' => 'New Achievement',
        'description' => 'Just unlocked',
        'icon' => '🎯',
        'category' => 'habits',
        'requirement_type' => 'habits_completed',
        'requirement_value' => 1,
        'points_reward' => 50,
        'is_secret' => false,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(RecentAchievements::class);
    
    expect($component->get('achievements')->count())->toBe(0);
    
    // Unlock the achievement
    $this->user->achievements()->attach($achievement->id, [
        'unlocked_at' => now(),
        'progress' => 1,
    ]);
    
    // Dispatch the event
    $component->dispatch('achievementUnlocked');
    
    expect($component->get('achievements')->count())->toBe(1);
});

test('only shows unlocked achievements', function () {
    $unlockedAchievement = Achievement::create([
        'name' => 'Unlocked Achievement',
        'description' => 'This is unlocked',
        'icon' => '🎯',
        'category' => 'habits',
        'requirement_type' => 'habits_completed',
        'requirement_value' => 1,
        'points_reward' => 50,
        'is_secret' => false,
    ]);
    
    $lockedAchievement = Achievement::create([
        'name' => 'Locked Achievement',
        'description' => 'This is locked',
        'icon' => '🔒',
        'category' => 'habits',
        'requirement_type' => 'habits_completed',
        'requirement_value' => 10,
        'points_reward' => 100,
        'is_secret' => false,
    ]);
    
    // Unlock only the first achievement
    $this->user->achievements()->attach($unlockedAchievement->id, [
        'unlocked_at' => now(),
        'progress' => 1,
    ]);
    
    // Attach but don't unlock the second
    $this->user->achievements()->attach($lockedAchievement->id, [
        'unlocked_at' => null,
        'progress' => 5,
    ]);

    Livewire::actingAs($this->user)
        ->test(RecentAchievements::class)
        ->assertSee('Unlocked Achievement')
        ->assertDontSee('Locked Achievement');
});
