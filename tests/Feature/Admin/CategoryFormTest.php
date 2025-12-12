<?php

use App\Models\{User, Category, UserStats, UserLevel};
use Livewire\Livewire;
use App\Livewire\Admin\Categories\CategoryForm;

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

test('category form component loads successfully', function () {
    Livewire::actingAs($this->user)
        ->test(CategoryForm::class)
        ->assertStatus(200);
});

test('can open create modal', function () {
    Livewire::actingAs($this->user)
        ->test(CategoryForm::class)
        ->call('openCreate')
        ->assertSet('showModal', true)
        ->assertSet('isEditing', false);
});

test('can create new category', function () {
    Livewire::actingAs($this->user)
        ->test(CategoryForm::class)
        ->call('openCreate')
        ->set('name', 'Nueva Categoría')
        ->set('icon', '🎯')
        ->set('color', '#DDEBF1')
        ->call('save')
        ->assertDispatched('categoryCreated')
        ->assertDispatched('notification');
    
    expect(Category::where('name', 'Nueva Categoría')->exists())->toBeTrue();
});

test('validates required fields', function () {
    Livewire::actingAs($this->user)
        ->test(CategoryForm::class)
        ->call('openCreate')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('validates name minimum length', function () {
    Livewire::actingAs($this->user)
        ->test(CategoryForm::class)
        ->call('openCreate')
        ->set('name', 'AB')
        ->call('save')
        ->assertHasErrors(['name' => 'min']);
});

test('validates name uniqueness', function () {
    Category::create([
        'name' => 'Categoría Existente',
        'slug' => 'categoria-existente',
        'icon' => '📁',
        'color' => '#DDEBF1',
        'order' => 1,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(CategoryForm::class)
        ->call('openCreate')
        ->set('name', 'Categoría Existente')
        ->set('icon', '🎯')
        ->set('color', '#DDEBF1')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('can open edit modal with existing category', function () {
    $category = Category::create([
        'name' => 'Categoría Test',
        'slug' => 'categoria-test',
        'icon' => '📚',
        'color' => '#DDEBF1',
        'order' => 1,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(CategoryForm::class)
        ->call('openEdit', $category->id)
        ->assertSet('showModal', true)
        ->assertSet('isEditing', true)
        ->assertSet('name', 'Categoría Test')
        ->assertSet('icon', '📚')
        ->assertSet('color', '#DDEBF1');
});

test('can update existing category', function () {
    $category = Category::create([
        'name' => 'Categoría Original',
        'slug' => 'categoria-original',
        'icon' => '📁',
        'color' => '#DDEBF1',
        'order' => 1,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(CategoryForm::class)
        ->call('openEdit', $category->id)
        ->set('name', 'Categoría Actualizada')
        ->set('icon', '🎨')
        ->set('color', '#F4DFEB')
        ->call('save')
        ->assertDispatched('categoryUpdated')
        ->assertDispatched('notification');
    
    $category->refresh();
    expect($category->name)->toBe('Categoría Actualizada');
    expect($category->icon)->toBe('🎨');
    expect($category->color)->toBe('#F4DFEB');
});

test('can close modal', function () {
    Livewire::actingAs($this->user)
        ->test(CategoryForm::class)
        ->call('openCreate')
        ->assertSet('showModal', true)
        ->call('closeModal')
        ->assertSet('showModal', false);
});

test('resets form when closing modal', function () {
    Livewire::actingAs($this->user)
        ->test(CategoryForm::class)
        ->call('openCreate')
        ->set('name', 'Test')
        ->set('icon', '🎯')
        ->call('closeModal')
        ->assertSet('name', '')
        ->assertSet('icon', '📁');
});
