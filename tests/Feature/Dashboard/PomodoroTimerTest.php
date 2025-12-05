<?php

use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Dashboard\PomodoroTimer;

test('pomodoro timer component renders successfully', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PomodoroTimer::class)
        ->assertStatus(200)
        ->assertSee('Focus Timer')
        ->assertSee('25:00');
});

test('pomodoro timer starts at 25 minutes', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PomodoroTimer::class)
        ->assertSet('timer', 1500)
        ->assertSet('running', false);
});

test('pomodoro timer toggles running state', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PomodoroTimer::class)
        ->assertSet('running', false)
        ->call('toggleTimer')
        ->assertSet('running', true)
        ->call('toggleTimer')
        ->assertSet('running', false);
});

test('pomodoro timer decrements when running', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PomodoroTimer::class)
        ->set('running', true)
        ->call('tick')
        ->assertSet('timer', 1499);
});

test('pomodoro timer does not decrement when paused', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PomodoroTimer::class)
        ->set('running', false)
        ->call('tick')
        ->assertSet('timer', 1500);
});

test('pomodoro timer stops at zero', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PomodoroTimer::class)
        ->set('timer', 1)
        ->set('running', true)
        ->call('tick')
        ->assertSet('timer', 0)
        ->assertSet('running', false);
});

test('pomodoro timer formats time correctly', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(PomodoroTimer::class);
    
    // Test 25:00
    $component->set('timer', 1500);
    expect($component->instance()->formatTime())->toBe('25:00');
    
    // Test 10:30
    $component->set('timer', 630);
    expect($component->instance()->formatTime())->toBe('10:30');
    
    // Test 00:05
    $component->set('timer', 5);
    expect($component->instance()->formatTime())->toBe('00:05');
    
    // Test 00:00
    $component->set('timer', 0);
    expect($component->instance()->formatTime())->toBe('00:00');
});
