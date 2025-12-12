<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Difficulty;
use App\Models\Habit;
use App\Livewire\Habits\CreateHabit;
use App\Livewire\Habits\EditHabit;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('create habit form loads active categories from database', function () {
    // Create some active and inactive categories
    $activeCategory = Category::factory()->create(['is_active' => true, 'order' => 1]);
    $inactiveCategory = Category::factory()->create(['is_active' => false, 'order' => 2]);
    
    Livewire::actingAs($this->user)
        ->test(CreateHabit::class)
        ->assertStatus(200)
        ->assertSee($activeCategory->name)
        ->assertDontSee($inactiveCategory->name);
});

test('create habit form loads active difficulties from database', function () {
    // Create some active and inactive difficulties
    $activeDifficulty = Difficulty::factory()->create(['is_active' => true, 'order' => 1]);
    $inactiveDifficulty = Difficulty::factory()->create(['is_active' => false, 'order' => 2]);
    
    Livewire::actingAs($this->user)
        ->test(CreateHabit::class)
        ->assertStatus(200)
        ->assertSee($activeDifficulty->name)
        ->assertDontSee($inactiveDifficulty->name);
});

test('create habit saves with category_id and difficulty_id', function () {
    $category = Category::factory()->create(['is_active' => true, 'slug' => 'test-category']);
    $difficulty = Difficulty::factory()->create(['is_active' => true, 'points' => 50, 'slug' => 'test-difficulty']);
    
    Livewire::actingAs($this->user)
        ->test(CreateHabit::class)
        ->set('name', 'Test Habit')
        ->set('category_id', $category->id)
        ->set('difficulty_id', $difficulty->id)
        ->set('frequency', 'daily')
        ->call('save')
        ->assertRedirect(route('admin.habits.index'));
    
    $habit = Habit::where('name', 'Test Habit')->first();
    expect($habit)->not->toBeNull()
        ->and($habit->category_id)->toBe($category->id)
        ->and($habit->difficulty_id)->toBe($difficulty->id)
        ->and($habit->points_reward)->toBe(50);
});

test('edit habit form loads active categories and includes inactive if currently selected', function () {
    $activeCategory = Category::factory()->create(['is_active' => true, 'order' => 1]);
    $inactiveCategory = Category::factory()->create(['is_active' => false, 'order' => 2]);
    
    // Create habit with inactive category
    $habit = Habit::factory()->for($this->user)->create([
        'category_id' => $inactiveCategory->id,
        'difficulty_id' => Difficulty::factory()->create()->id,
    ]);
    
    Livewire::actingAs($this->user)
        ->test(EditHabit::class, ['habit' => $habit])
        ->assertStatus(200)
        ->assertSee($activeCategory->name)
        ->assertSee($inactiveCategory->name)
        ->assertSee('Obsoleta'); // Should mark inactive as deprecated
});

test('edit habit form loads active difficulties and includes inactive if currently selected', function () {
    $activeDifficulty = Difficulty::factory()->create(['is_active' => true, 'order' => 1]);
    $inactiveDifficulty = Difficulty::factory()->create(['is_active' => false, 'order' => 2]);
    
    // Create habit with inactive difficulty
    $habit = Habit::factory()->for($this->user)->create([
        'category_id' => Category::factory()->create()->id,
        'difficulty_id' => $inactiveDifficulty->id,
    ]);
    
    Livewire::actingAs($this->user)
        ->test(EditHabit::class, ['habit' => $habit])
        ->assertStatus(200)
        ->assertSee($activeDifficulty->name)
        ->assertSee($inactiveDifficulty->name)
        ->assertSee('Obsoleta'); // Should mark inactive as deprecated
});

test('edit habit updates with new category_id and difficulty_id', function () {
    $oldCategory = Category::factory()->create(['is_active' => true]);
    $oldDifficulty = Difficulty::factory()->create(['is_active' => true, 'points' => 30]);
    
    $habit = Habit::factory()->for($this->user)->create([
        'category_id' => $oldCategory->id,
        'difficulty_id' => $oldDifficulty->id,
    ]);
    
    $newCategory = Category::factory()->create(['is_active' => true]);
    $newDifficulty = Difficulty::factory()->create(['is_active' => true, 'points' => 60]);
    
    Livewire::actingAs($this->user)
        ->test(EditHabit::class, ['habit' => $habit])
        ->set('category_id', $newCategory->id)
        ->set('difficulty_id', $newDifficulty->id)
        ->call('update')
        ->assertRedirect(route('admin.habits.index'));
    
    $habit->refresh();
    expect($habit->category_id)->toBe($newCategory->id)
        ->and($habit->difficulty_id)->toBe($newDifficulty->id)
        ->and($habit->points_reward)->toBe(60);
});

test('categories are ordered by order field in create form', function () {
    $category1 = Category::factory()->create(['is_active' => true, 'order' => 3, 'name' => 'Third']);
    $category2 = Category::factory()->create(['is_active' => true, 'order' => 1, 'name' => 'First']);
    $category3 = Category::factory()->create(['is_active' => true, 'order' => 2, 'name' => 'Second']);
    
    $component = Livewire::actingAs($this->user)
        ->test(CreateHabit::class);
    
    $categories = $component->viewData('categories');
    expect($categories[0]->name)->toBe('First')
        ->and($categories[1]->name)->toBe('Second')
        ->and($categories[2]->name)->toBe('Third');
});

test('difficulties are ordered by order field in create form', function () {
    $diff1 = Difficulty::factory()->create(['is_active' => true, 'order' => 3, 'name' => 'Third']);
    $diff2 = Difficulty::factory()->create(['is_active' => true, 'order' => 1, 'name' => 'First']);
    $diff3 = Difficulty::factory()->create(['is_active' => true, 'order' => 2, 'name' => 'Second']);
    
    $component = Livewire::actingAs($this->user)
        ->test(CreateHabit::class);
    
    $difficulties = $component->viewData('difficulties');
    expect($difficulties[0]->name)->toBe('First')
        ->and($difficulties[1]->name)->toBe('Second')
        ->and($difficulties[2]->name)->toBe('Third');
});
