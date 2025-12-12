<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\Category;
use App\Models\Difficulty;
use App\Enums\HabitCategory;
use App\Enums\HabitDifficulty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HabitSettingsMigrationService
{
    /**
     * Migrate all habits from enum-based categories to database categories
     *
     * @return array Migration results with counts
     */
    public function migrateCategories(): array
    {
        $migrated = 0;
        $skipped = 0;
        $errors = [];

        try {
            DB::beginTransaction();

            // Get all habits that have a category enum but no category_id
            $habits = Habit::whereNotNull('category')
                ->whereNull('category_id')
                ->get();

            foreach ($habits as $habit) {
                try {
                    // Get the enum value
                    $categoryEnum = $habit->category;
                    
                    if (!$categoryEnum instanceof HabitCategory) {
                        $skipped++;
                        continue;
                    }

                    // Find the corresponding category in the database by slug
                    $category = Category::where('slug', $categoryEnum->value)->first();

                    if (!$category) {
                        $errors[] = "Category not found for habit {$habit->id}: {$categoryEnum->value}";
                        continue;
                    }

                    // Update the habit with the category_id
                    $habit->category_id = $category->id;
                    $habit->save();

                    $migrated++;
                } catch (\Exception $e) {
                    $errors[] = "Error migrating habit {$habit->id}: {$e->getMessage()}";
                    Log::error("Category migration error for habit {$habit->id}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'migrated' => $migrated,
                'skipped' => $skipped,
                'errors' => $errors,
                'total' => $habits->count()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'migrated' => 0,
                'skipped' => 0,
                'errors' => [$e->getMessage()],
                'total' => 0
            ];
        }
    }

    /**
     * Migrate all habits from enum-based difficulties to database difficulties
     *
     * @return array Migration results with counts
     */
    public function migrateDifficulties(): array
    {
        $migrated = 0;
        $skipped = 0;
        $errors = [];

        try {
            DB::beginTransaction();

            // Get all habits that have a difficulty enum but no difficulty_id
            $habits = Habit::whereNotNull('difficulty')
                ->whereNull('difficulty_id')
                ->get();

            foreach ($habits as $habit) {
                try {
                    // Get the enum value
                    $difficultyEnum = $habit->difficulty;
                    
                    if (!$difficultyEnum instanceof HabitDifficulty) {
                        $skipped++;
                        continue;
                    }

                    // Find the corresponding difficulty in the database by slug
                    $difficulty = Difficulty::where('slug', $difficultyEnum->value)->first();

                    if (!$difficulty) {
                        $errors[] = "Difficulty not found for habit {$habit->id}: {$difficultyEnum->value}";
                        continue;
                    }

                    // Update the habit with the difficulty_id
                    $habit->difficulty_id = $difficulty->id;
                    $habit->save();

                    $migrated++;
                } catch (\Exception $e) {
                    $errors[] = "Error migrating habit {$habit->id}: {$e->getMessage()}";
                    Log::error("Difficulty migration error for habit {$habit->id}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'migrated' => $migrated,
                'skipped' => $skipped,
                'errors' => $errors,
                'total' => $habits->count()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Difficulty migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'migrated' => 0,
                'skipped' => 0,
                'errors' => [$e->getMessage()],
                'total' => 0
            ];
        }
    }

    /**
     * Migrate both categories and difficulties for all habits
     *
     * @return array Combined migration results
     */
    public function migrateAll(): array
    {
        $categoryResults = $this->migrateCategories();
        $difficultyResults = $this->migrateDifficulties();

        return [
            'categories' => $categoryResults,
            'difficulties' => $difficultyResults,
            'overall_success' => $categoryResults['success'] && $difficultyResults['success']
        ];
    }

    /**
     * Verify the integrity of migrated data
     *
     * @return array Verification results
     */
    public function verifyIntegrity(): array
    {
        $issues = [];
        $stats = [
            'total_habits' => 0,
            'habits_with_category_id' => 0,
            'habits_with_difficulty_id' => 0,
            'habits_missing_category_id' => 0,
            'habits_missing_difficulty_id' => 0,
            'orphaned_category_references' => 0,
            'orphaned_difficulty_references' => 0,
        ];

        try {
            // Count total habits
            $stats['total_habits'] = Habit::count();

            // Count habits with category_id
            $stats['habits_with_category_id'] = Habit::whereNotNull('category_id')->count();

            // Count habits with difficulty_id
            $stats['habits_with_difficulty_id'] = Habit::whereNotNull('difficulty_id')->count();

            // Find habits that should have category_id but don't
            $habitsWithoutCategoryId = Habit::whereNotNull('category')
                ->whereNull('category_id')
                ->get();
            
            $stats['habits_missing_category_id'] = $habitsWithoutCategoryId->count();
            
            foreach ($habitsWithoutCategoryId as $habit) {
                $issues[] = "Habit {$habit->id} ('{$habit->name}') has category enum but no category_id";
            }

            // Find habits that should have difficulty_id but don't
            $habitsWithoutDifficultyId = Habit::whereNotNull('difficulty')
                ->whereNull('difficulty_id')
                ->get();
            
            $stats['habits_missing_difficulty_id'] = $habitsWithoutDifficultyId->count();
            
            foreach ($habitsWithoutDifficultyId as $habit) {
                $issues[] = "Habit {$habit->id} ('{$habit->name}') has difficulty enum but no difficulty_id";
            }

            // Find habits with category_id that points to non-existent category
            $habitsWithInvalidCategory = Habit::whereNotNull('category_id')
                ->whereDoesntHave('categoryRelation')
                ->get();
            
            $stats['orphaned_category_references'] = $habitsWithInvalidCategory->count();
            
            foreach ($habitsWithInvalidCategory as $habit) {
                $issues[] = "Habit {$habit->id} ('{$habit->name}') has invalid category_id: {$habit->category_id}";
            }

            // Find habits with difficulty_id that points to non-existent difficulty
            $habitsWithInvalidDifficulty = Habit::whereNotNull('difficulty_id')
                ->whereDoesntHave('difficultyRelation')
                ->get();
            
            $stats['orphaned_difficulty_references'] = $habitsWithInvalidDifficulty->count();
            
            foreach ($habitsWithInvalidDifficulty as $habit) {
                $issues[] = "Habit {$habit->id} ('{$habit->name}') has invalid difficulty_id: {$habit->difficulty_id}";
            }

            $isValid = empty($issues);

            return [
                'valid' => $isValid,
                'stats' => $stats,
                'issues' => $issues,
                'message' => $isValid 
                    ? 'All habits have valid category and difficulty relationships' 
                    : 'Found ' . count($issues) . ' integrity issues'
            ];
        } catch (\Exception $e) {
            Log::error('Integrity verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'valid' => false,
                'stats' => $stats,
                'issues' => ['Verification failed: ' . $e->getMessage()],
                'message' => 'Verification process encountered an error'
            ];
        }
    }
}
