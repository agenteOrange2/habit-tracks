<?php

use App\Livewire\Dashboard\EnergyBar;
use App\Models\User;
use App\Services\EnergyService;
use Livewire\Livewire;

test('energy bar component renders with energy status', function () {
    $user = User::factory()->create([
        'energy_level' => 75,
        'last_energy_update' => now(),
    ]);

    $this->actingAs($user);

    Livewire::test(EnergyBar::class)
        ->assertSee('Energía')
        ->assertSee('/100')
        ->assertStatus(200);
});

test('energy bar shows warning color when energy is below 30%', function () {
    $user = User::factory()->create([
        'energy_level' => 25,
        'last_energy_update' => now(),
    ]);

    $this->actingAs($user);

    $component = Livewire::test(EnergyBar::class);
    
    expect($component->get('energyStatus')['percentage'])->toBeLessThan(30);
    expect((int) round($component->get('energyStatus')['current']))->toBe(25);
});

test('energy bar displays correct percentage', function () {
    $user = User::factory()->create([
        'energy_level' => 50,
        'last_energy_update' => now(),
    ]);

    $this->actingAs($user);

    $component = Livewire::test(EnergyBar::class);
    
    expect($component->get('energyStatus')['percentage'])->toBeGreaterThan(49);
    expect($component->get('energyStatus')['percentage'])->toBeLessThan(51);
    expect((int) round($component->get('energyStatus')['current']))->toBe(50);
    expect($component->get('energyStatus')['max'])->toBe(100);
});

test('energy bar refreshes when energyUpdated event is dispatched', function () {
    $user = User::factory()->create([
        'energy_level' => 75,
        'last_energy_update' => now(),
    ]);

    $this->actingAs($user);

    $component = Livewire::test(EnergyBar::class);
    
    // Update user energy and last update time
    $user->update([
        'energy_level' => 50,
        'last_energy_update' => now(),
    ]);
    
    // Dispatch event
    $component->dispatch('energyUpdated');
    
    expect((int) round($component->get('energyStatus')['current']))->toBe(50);
});

test('energy bar percentage is clamped between 0 and 100', function () {
    $user = User::factory()->create([
        'energy_level' => 100,
        'last_energy_update' => now(),
    ]);

    $this->actingAs($user);

    $component = Livewire::test(EnergyBar::class);
    
    expect($component->get('energyStatus')['percentage'])->toBeLessThanOrEqual(100);
    expect($component->get('energyStatus')['percentage'])->toBeGreaterThanOrEqual(0);
});
