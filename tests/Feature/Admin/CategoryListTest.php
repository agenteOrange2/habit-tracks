<?php

use App\Models\{User, Category, UserStats, UserLevel};
use Livewire\Livewire;
use App\Livewire\Admin\Categories\CategoryList;

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

test('category list component loads successfully', function () {
    Livewire::actingAs($this->user)
        ->test(CategoryList::class)
        ->assertStatus(200);
});

test('category list displays categories', function () {
    $category = Category::create([
        'name' => 'Productividad',
        'slug' => 'productividad',
        'icon' => '💼',
        'color' => '#3B82F6',
        'order' => 1,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(CategoryList::class)
        ->assertSee('Productividad')
        ->assertSee('💼');
});

test('category list shows empty state when no categories', function () {
    Livewire::actingAs($this->user)
        ->test(CategoryList::class)
        ->assertSee('No hay categorías todavía');
});

test('can toggle category active status', function () {
    $category = Category::create([
        'name' => 'Salud',
        'slug' => 'salud',
        'icon' => '🧘',
        'color' => '#10B981',
        'order' => 1,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(CategoryList::class)
        ->call('toggleActive', $category->id)
        ->assertDispatched('notification');
    
    expect($category->fresh()->is_active)->toBeFalse();
});

test('can open delete confirmation modal', function () {
    $category = Category::create([
        'name' => 'Creatividad',
        'slug' => 'creatividad',
        'icon' => '🎨',
        'color' => '#F59E0B',
        'order' => 1,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(CategoryList::class)
        ->call('confirmDelete', $category->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('categoryToDeleteId', $category->id)
        ->assertSet('categoryToDeleteName', $category->name);
});

test('cannot delete category with associated habits', function () {
    $category = Category::create([
        'name' => 'Aprendizaje',
        'slug' => 'aprendizaje',
        'icon' => '📚',
        'color' => '#8B5CF6',
        'order' => 1,
        'is_active' => true,
    ]);
    
    // Create a habit associated with this category
    \App\Models\Habit::create([
        'user_id' => $this->user->id,
        'name' => 'Read Book',
        'category' => \App\Enums\HabitCategory::LEARNING->value,
        'difficulty' => \App\Enums\HabitDifficulty::EASY->value,
        'category_id' => $category->id,
        'frequency' => \App\Enums\HabitFrequency::DAILY->value,
        'is_recurring' => true,
        'is_active' => true,
        'points_reward' => 30,
    ]);

    Livewire::actingAs($this->user)
        ->test(CategoryList::class)
        ->call('confirmDelete', $category->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('deleteError', 'No se puede eliminar porque hay 1 hábito(s) asociado(s)');
});

test('can delete category without associated habits', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'icon' => '🔥',
        'color' => '#EF4444',
        'order' => 1,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(CategoryList::class)
        ->call('confirmDelete', $category->id)
        ->call('delete')
        ->assertDispatched('notification')
        ->assertSet('showDeleteModal', false);
    
    expect(Category::find($category->id))->toBeNull();
});

test('can update category order', function () {
    $category1 = Category::create([
        'name' => 'Category 1',
        'slug' => 'category-1',
        'icon' => '📁',
        'color' => '#3B82F6',
        'order' => 1,
        'is_active' => true,
    ]);
    
    $category2 = Category::create([
        'name' => 'Category 2',
        'slug' => 'category-2',
        'icon' => '📂',
        'color' => '#10B981',
        'order' => 2,
        'is_active' => true,
    ]);
    
    $category3 = Category::create([
        'name' => 'Category 3',
        'slug' => 'category-3',
        'icon' => '📄',
        'color' => '#F59E0B',
        'order' => 3,
        'is_active' => true,
    ]);

    // Reorder: category3, category1, category2
    Livewire::actingAs($this->user)
        ->test(CategoryList::class)
        ->call('updateOrder', [$category3->id, $category1->id, $category2->id])
        ->assertDispatched('notification');
    
    expect($category3->fresh()->order)->toBe(1)
        ->and($category1->fresh()->order)->toBe(2)
        ->and($category2->fresh()->order)->toBe(3);
});

test('updateOrder validates all category IDs exist', function () {
    $category = Category::create([
        'name' => 'Category 1',
        'slug' => 'category-1',
        'icon' => '📁',
        'color' => '#3B82F6',
        'order' => 1,
        'is_active' => true,
    ]);

    // Try to update with invalid ID
    Livewire::actingAs($this->user)
        ->test(CategoryList::class)
        ->call('updateOrder', [$category->id, 9999])
        ->assertDispatched('notification');
    
    // Order should not change
    expect($category->fresh()->order)->toBe(1);
});

test('updateOrder ensures no duplicate order values', function () {
    $category1 = Category::create([
        'name' => 'Category 1',
        'slug' => 'category-1',
        'icon' => '📁',
        'color' => '#3B82F6',
        'order' => 1,
        'is_active' => true,
    ]);
    
    $category2 = Category::create([
        'name' => 'Category 2',
        'slug' => 'category-2',
        'icon' => '📂',
        'color' => '#10B981',
        'order' => 2,
        'is_active' => true,
    ]);

    // Update order normally
    Livewire::actingAs($this->user)
        ->test(CategoryList::class)
        ->call('updateOrder', [$category2->id, $category1->id])
        ->assertDispatched('notification');
    
    // Verify no duplicates
    $orders = Category::pluck('order')->toArray();
    expect(count($orders))->toBe(count(array_unique($orders)));
});
