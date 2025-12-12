<?php

use App\Models\{User, Difficulty, UserStats, UserLevel};
use Livewire\Livewire;
use App\Livewire\Admin\Difficulties\DifficultyForm;

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

test('difficulty form component loads successfully', function () {
    Livewire::actingAs($this->user)
        ->test(DifficultyForm::class)
        ->assertStatus(200);
});

test('can open create form', function () {
    Livewire::actingAs($this->user)
        ->test(DifficultyForm::class)
        ->call('openCreate')
        ->assertSet('showModal', true)
        ->assertSet('isEditing', false)
        ->assertSet('name', '')
        ->assertSet('icon', '⭐')
        ->assertSet('points', 10);
});

test('can create new difficulty', function () {
    Livewire::actingAs($this->user)
        ->test(DifficultyForm::class)
        ->call('openCreate')
        ->set('name', 'Muy Fácil')
        ->set('icon', '⭐')
        ->set('points', 5)
        ->call('save')
        ->assertDispatched('difficultyCreated')
        ->assertDispatched('notification')
        ->assertSet('showModal', false);
    
    expect(Difficulty::where('name', 'Muy Fácil')->exists())->toBeTrue();
});

test('can open edit form', function () {
    $difficulty = Difficulty::create([
        'name' => 'Medio',
        'slug' => 'medio',
        'icon' => '⭐⭐',
        'points' => 20,
        'order' => 1,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(DifficultyForm::class)
        ->call('openEdit', $difficulty->id)
        ->assertSet('showModal', true)
        ->assertSet('isEditing', true)
        ->assertSet('name', 'Medio')
        ->assertSet('icon', '⭐⭐')
        ->assertSet('points', 20);
});

test('can update existing difficulty', function () {
    $difficulty = Difficulty::create([
        'name' => 'Difícil',
        'slug' => 'dificil',
        'icon' => '⭐⭐⭐',
        'points' => 30,
        'order' => 1,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(DifficultyForm::class)
        ->call('openEdit', $difficulty->id)
        ->set('name', 'Muy Difícil')
        ->set('points', 40)
        ->call('save')
        ->assertDispatched('difficultyUpdated')
        ->assertDispatched('notification');
    
    expect($difficulty->fresh()->name)->toBe('Muy Difícil');
    expect($difficulty->fresh()->points)->toBe(40);
});

test('validates required fields', function () {
    Livewire::actingAs($this->user)
        ->test(DifficultyForm::class)
        ->call('openCreate')
        ->set('name', '')
        ->set('icon', '')
        ->set('points', '')
        ->call('save')
        ->assertHasErrors(['name', 'icon', 'points']);
});

test('validates name minimum length', function () {
    Livewire::actingAs($this->user)
        ->test(DifficultyForm::class)
        ->call('openCreate')
        ->set('name', 'AB')
        ->call('save')
        ->assertHasErrors(['name' => 'min']);
});

test('validates name uniqueness', function () {
    Difficulty::create([
        'name' => 'Extremo',
        'slug' => 'extremo',
        'icon' => '🔥',
        'points' => 50,
        'order' => 1,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(DifficultyForm::class)
        ->call('openCreate')
        ->set('name', 'Extremo')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('validates points range', function () {
    Livewire::actingAs($this->user)
        ->test(DifficultyForm::class)
        ->call('openCreate')
        ->set('name', 'Test')
        ->set('points', 0)
        ->call('save')
        ->assertHasErrors(['points' => 'min']);
    
    Livewire::actingAs($this->user)
        ->test(DifficultyForm::class)
        ->call('openCreate')
        ->set('name', 'Test')
        ->set('points', 1001)
        ->call('save')
        ->assertHasErrors(['points' => 'max']);
});

test('can close modal', function () {
    Livewire::actingAs($this->user)
        ->test(DifficultyForm::class)
        ->call('openCreate')
        ->assertSet('showModal', true)
        ->call('closeModal')
        ->assertSet('showModal', false);
});
