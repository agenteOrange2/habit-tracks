<?php

use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Dashboard\QuickActions;

test('quick actions component renders successfully', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(QuickActions::class)
        ->assertStatus(200)
        ->assertSee('Nuevo Hábito');
});

test('quick actions displays all four actions', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(QuickActions::class)
        ->assertSee('Nuevo Hábito')
        ->assertSee('Pomodoro')
        ->assertSee('Recompensas')
        ->assertSee('Diario');
});

test('quick actions displays correct icons', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(QuickActions::class)
        ->assertSee('➕')
        ->assertSee('🍅')
        ->assertSee('🎁')
        ->assertSee('📝');
});

test('quick actions has correct route configuration', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(QuickActions::class);

    expect($component->get('actions'))->toHaveCount(4);
    expect($component->get('actions')[0]['route'])->toBe('admin.habits.create');
    expect($component->get('actions')[1]['route'])->toBe('admin.pomodoro');
    expect($component->get('actions')[2]['route'])->toBe('rewards.index');
    expect($component->get('actions')[3]['route'])->toBe('admin.journal.create');
});

test('quick actions uses notion color classes for dark mode support', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(QuickActions::class);

    // Verify that each action has a color assigned
    expect($component->get('actions')[0]['color'])->toBe('blue');
    expect($component->get('actions')[1]['color'])->toBe('red');
    expect($component->get('actions')[2]['color'])->toBe('purple');
    expect($component->get('actions')[3]['color'])->toBe('green');
    
    // Verify the view contains the notion color classes
    $component->assertSeeHtml('bg-notion-blue')
        ->assertSeeHtml('bg-notion-red')
        ->assertSeeHtml('bg-notion-purple')
        ->assertSeeHtml('bg-notion-green');
});
