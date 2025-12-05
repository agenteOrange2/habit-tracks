<?php

use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Dashboard\QuickActions;

test('quick actions component renders successfully', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(QuickActions::class)
        ->assertStatus(200)
        ->assertSee('Acciones Rápidas');
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
    expect($component->get('actions')[0]['route'])->toBe('habits.create');
    expect($component->get('actions')[1]['route'])->toBe('pomodoro.index');
    expect($component->get('actions')[2]['route'])->toBe('rewards.index');
    expect($component->get('actions')[3]['route'])->toBe('journal.create');
});
