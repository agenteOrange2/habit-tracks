<?php

use App\Models\User;
use App\Models\Habit;
use App\Models\Category;
use App\Models\Difficulty;

test('habit can have a category relationship', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'icon' => '📚',
        'color' => '#3B82F6',
        'order' => 1,
        'is_active' => true,
    ]);

    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $categoryRelation = $habit->category()->first();
    expect($categoryRelation)->not->toBeNull()
        ->and($categoryRelation->name)->toBe('Test Category')
        ->and($categoryRelation->icon)->toBe('📚');
});

test('habit can have a difficulty relationship', function () {
    $user = User::factory()->create();
    $difficulty = Difficulty::create([
        'name' => 'Easy',
        'slug' => 'easy',
        'points' => 10,
        'icon' => '⭐',
        'order' => 1,
        'is_active' => true,
    ]);

    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'difficulty_id' => $difficulty->id,
    ]);

    $difficultyRelation = $habit->difficulty()->first();
    expect($difficultyRelation)->not->toBeNull()
        ->and($difficultyRelation->name)->toBe('Easy')
        ->and($difficultyRelation->points)->toBe(10);
});

test('getCategoryName returns name from relationship', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Productivity',
        'slug' => 'productivity',
        'icon' => '💼',
        'color' => '#3B82F6',
        'order' => 1,
        'is_active' => true,
    ]);

    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    expect($habit->getCategoryName())->toBe('Productivity');
});

test('getDifficultyPoints returns points from relationship', function () {
    $user = User::factory()->create();
    $difficulty = Difficulty::create([
        'name' => 'Hard',
        'slug' => 'hard',
        'points' => 50,
        'icon' => '🔥',
        'order' => 3,
        'is_active' => true,
    ]);

    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'difficulty_id' => $difficulty->id,
    ]);

    expect($habit->getDifficultyPoints())->toBe(50);
});

test('isUsingDynamicSettings returns true when using new system', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Health',
        'slug' => 'health',
        'icon' => '💪',
        'color' => '#10B981',
        'order' => 2,
        'is_active' => true,
    ]);
    $difficulty = Difficulty::create([
        'name' => 'Medium',
        'slug' => 'medium',
        'points' => 25,
        'icon' => '⚡',
        'order' => 2,
        'is_active' => true,
    ]);

    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'difficulty_id' => $difficulty->id,
    ]);

    expect($habit->isUsingDynamicSettings())->toBeTrue();
});

test('isUsingDynamicSettings returns false when not using new system', function () {
    $user = User::factory()->create();

    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'category_id' => null,
        'difficulty_id' => null,
    ]);

    expect($habit->isUsingDynamicSettings())->toBeFalse();
});

test('getCategoryIcon returns icon from relationship', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Learning',
        'slug' => 'learning',
        'icon' => '🎓',
        'color' => '#8B5CF6',
        'order' => 3,
        'is_active' => true,
    ]);

    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    expect($habit->getCategoryIcon())->toBe('🎓');
});

test('getCategoryColor returns color from relationship', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Finance',
        'slug' => 'finance',
        'icon' => '💰',
        'color' => '#F59E0B',
        'order' => 4,
        'is_active' => true,
    ]);

    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    expect($habit->getCategoryColor())->toBe('#F59E0B');
});

test('getDifficultyIcon returns icon from relationship', function () {
    $user = User::factory()->create();
    $difficulty = Difficulty::create([
        'name' => 'Expert',
        'slug' => 'expert',
        'points' => 100,
        'icon' => '💎',
        'order' => 4,
        'is_active' => true,
    ]);

    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'difficulty_id' => $difficulty->id,
    ]);

    expect($habit->getDifficultyIcon())->toBe('💎');
});
