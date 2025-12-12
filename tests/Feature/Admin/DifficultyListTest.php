<?php

use App\Models\{User, Difficulty, UserStats, UserLevel};
use Livewire\Livewire;
use App\Livewire\Admin\Difficulties\DifficultyList;

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

test('difficulty list component loads successfully', function () {
    Livewire::actingAs($this->user)
        ->test(DifficultyList::class)
        ->assertStatus(200);
});

test('difficulty list displays difficulties', function () {
    $difficulty = Difficulty::create([
        'name' => 'Fácil',
        'slug' => 'facil',
        'icon' => '⭐',
        'points' => 10,
        'order' => 1,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(DifficultyList::class)
        ->assertSee('Fácil')
        ->assertSee('⭐')
        ->assertSee('10 pts');
});

test('difficulty list shows empty state when no difficulties', function () {
    Livewire::actingAs($this->user)
        ->test(DifficultyList::class)
        ->assertSee('No hay dificultades todavía');
});

test('can toggle difficulty active status', function () {
    $difficulty = Difficulty::create([
        'name' => 'Medio',
        'slug' => 'medio',
        'icon' => '⭐⭐',
        'points' => 20,
        'order' => 2,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(DifficultyList::class)
        ->call('toggleActive', $difficulty->id)
        ->assertDispatched('notification');
    
    expect($difficulty->fresh()->is_active)->toBeFalse();
});

test('can open delete confirmation modal', function () {
    $difficulty = Difficulty::create([
        'name' => 'Difícil',
        'slug' => 'dificil',
        'icon' => '⭐⭐⭐',
        'points' => 30,
        'order' => 3,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(DifficultyList::class)
        ->call('confirmDelete', $difficulty->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('difficultyToDeleteId', $difficulty->id)
        ->assertSet('difficultyToDeleteName', $difficulty->name);
});

test('cannot delete difficulty with associated habits', function () {
    $difficulty = Difficulty::create([
        'name' => 'Extremo',
        'slug' => 'extremo',
        'icon' => '⭐⭐⭐⭐',
        'points' => 50,
        'order' => 4,
        'is_active' => true,
    ]);
    
    // Create a habit associated with this difficulty
    \App\Models\Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Marathon Training',
        'category' => \App\Enums\HabitCategory::HEALTH->value,
        'difficulty' => \App\Enums\HabitDifficulty::HARD->value,
        'difficulty_id' => $difficulty->id,
        'frequency' => \App\Enums\HabitFrequency::DAILY->value,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 50,
    ]);

    Livewire::actingAs($this->user)
        ->test(DifficultyList::class)
        ->call('confirmDelete', $difficulty->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('deleteError', 'No se puede eliminar porque hay 1 hábito(s) asociado(s)');
});

test('can delete difficulty without associated habits', function () {
    $difficulty = Difficulty::create([
        'name' => 'Test Difficulty',
        'slug' => 'test-difficulty',
        'icon' => '🔥',
        'points' => 100,
        'order' => 5,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(DifficultyList::class)
        ->call('confirmDelete', $difficulty->id)
        ->call('delete')
        ->assertDispatched('notification')
        ->assertSet('showDeleteModal', false);
    
    expect(Difficulty::find($difficulty->id))->toBeNull();
});

test('can update difficulty order', function () {
    $difficulty1 = Difficulty::create([
        'name' => 'Difficulty 1',
        'slug' => 'difficulty-1',
        'icon' => '⭐',
        'points' => 10,
        'order' => 1,
        'is_active' => true,
    ]);
    
    $difficulty2 = Difficulty::create([
        'name' => 'Difficulty 2',
        'slug' => 'difficulty-2',
        'icon' => '⭐⭐',
        'points' => 20,
        'order' => 2,
        'is_active' => true,
    ]);
    
    $difficulty3 = Difficulty::create([
        'name' => 'Difficulty 3',
        'slug' => 'difficulty-3',
        'icon' => '⭐⭐⭐',
        'points' => 30,
        'order' => 3,
        'is_active' => true,
    ]);

    // Reorder: difficulty3, difficulty1, difficulty2
    Livewire::actingAs($this->user)
        ->test(DifficultyList::class)
        ->call('updateOrder', [$difficulty3->id, $difficulty1->id, $difficulty2->id])
        ->assertDispatched('notification');
    
    expect($difficulty3->fresh()->order)->toBe(1)
        ->and($difficulty1->fresh()->order)->toBe(2)
        ->and($difficulty2->fresh()->order)->toBe(3);
});

test('updateOrder validates all difficulty IDs exist', function () {
    $difficulty = Difficulty::create([
        'name' => 'Difficulty 1',
        'slug' => 'difficulty-1',
        'icon' => '⭐',
        'points' => 10,
        'order' => 1,
        'is_active' => true,
    ]);

    // Try to update with invalid ID
    Livewire::actingAs($this->user)
        ->test(DifficultyList::class)
        ->call('updateOrder', [$difficulty->id, 9999])
        ->assertDispatched('notification');
    
    // Order should not change
    expect($difficulty->fresh()->order)->toBe(1);
});

test('updateOrder ensures no duplicate order values', function () {
    $difficulty1 = Difficulty::create([
        'name' => 'Difficulty 1',
        'slug' => 'difficulty-1',
        'icon' => '⭐',
        'points' => 10,
        'order' => 1,
        'is_active' => true,
    ]);
    
    $difficulty2 = Difficulty::create([
        'name' => 'Difficulty 2',
        'slug' => 'difficulty-2',
        'icon' => '⭐⭐',
        'points' => 20,
        'order' => 2,
        'is_active' => true,
    ]);

    // Update order normally
    Livewire::actingAs($this->user)
        ->test(DifficultyList::class)
        ->call('updateOrder', [$difficulty2->id, $difficulty1->id])
        ->assertDispatched('notification');
    
    // Verify no duplicates
    $orders = Difficulty::pluck('order')->toArray();
    expect(count($orders))->toBe(count(array_unique($orders)));
});
