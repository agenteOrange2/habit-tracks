<?php

use App\Livewire\Dashboard\ActiveRewards;
use App\Models\{User, Reward, UserStats};
use App\Enums\RewardCategory;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    UserStats::factory()->for($this->user)->create();
});

test('component renders successfully', function () {
    Livewire::actingAs($this->user)
        ->test(ActiveRewards::class)
        ->assertStatus(200);
});

test('displays active rewards for user', function () {
    $reward1 = Reward::factory()->for($this->user)->create([
        'name' => 'Pizza Night',
        'description' => 'Order your favorite pizza',
        'is_available' => true,
        'cost_points' => 50,
        'category' => RewardCategory::FOOD,
    ]);

    $reward2 = Reward::factory()->for($this->user)->create([
        'name' => 'Movie Time',
        'description' => 'Watch a movie',
        'is_available' => true,
        'cost_points' => 30,
        'category' => RewardCategory::ENTERTAINMENT,
    ]);

    Livewire::actingAs($this->user)
        ->test(ActiveRewards::class)
        ->assertSee('Pizza Night')
        ->assertSee('Movie Time')
        ->assertSee('50 pts')
        ->assertSee('30 pts');
});

test('does not display unavailable rewards', function () {
    Reward::factory()->for($this->user)->create([
        'name' => 'Available Reward',
        'is_available' => true,
    ]);

    Reward::factory()->for($this->user)->create([
        'name' => 'Unavailable Reward',
        'is_available' => false,
    ]);

    Livewire::actingAs($this->user)
        ->test(ActiveRewards::class)
        ->assertSee('Available Reward')
        ->assertDontSee('Unavailable Reward');
});

test('limits display to 3 rewards', function () {
    Reward::factory()->count(5)->for($this->user)->create([
        'is_available' => true,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(ActiveRewards::class);

    expect($component->rewards)->toHaveCount(3);
});

test('orders rewards by cost ascending', function () {
    $reward1 = Reward::factory()->for($this->user)->create([
        'name' => 'Expensive',
        'cost_points' => 100,
        'is_available' => true,
    ]);

    $reward2 = Reward::factory()->for($this->user)->create([
        'name' => 'Cheap',
        'cost_points' => 10,
        'is_available' => true,
    ]);

    $reward3 = Reward::factory()->for($this->user)->create([
        'name' => 'Medium',
        'cost_points' => 50,
        'is_available' => true,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(ActiveRewards::class);

    expect($component->rewards->first()->name)->toBe('Cheap')
        ->and($component->rewards->last()->name)->toBe('Expensive');
});

test('refreshes rewards when rewardClaimed event is dispatched', function () {
    $reward = Reward::factory()->for($this->user)->create([
        'name' => 'Initial Reward',
        'is_available' => true,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(ActiveRewards::class)
        ->assertSee('Initial Reward');

    // Create a new reward
    $newReward = Reward::factory()->for($this->user)->create([
        'name' => 'New Reward',
        'is_available' => true,
    ]);

    // Dispatch the event
    $component->dispatch('rewardClaimed')
        ->assertSee('New Reward');
});

test('displays empty state when no rewards available', function () {
    Livewire::actingAs($this->user)
        ->test(ActiveRewards::class)
        ->assertSee('Crear nueva recompensa')
        ->assertSee('Personaliza tus incentivos');
});

test('displays reward name and icon', function () {
    $reward = Reward::factory()->for($this->user)->create([
        'name' => 'Gaming Session',
        'category' => RewardCategory::GAMING,
        'icon' => '🎮',
        'is_available' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(ActiveRewards::class)
        ->assertSee('Gaming Session')
        ->assertSee('🎮');
});

test('displays progress bar for rewards', function () {
    $this->user->stats->update(['available_points' => 50]);
    
    $reward = Reward::factory()->for($this->user)->create([
        'name' => 'Popular Reward',
        'cost_points' => 100,
        'is_available' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(ActiveRewards::class)
        ->assertSee('Popular Reward')
        ->assertSee('100 pts')
        ->assertSee('Faltan 50'); // Shows points needed
});
