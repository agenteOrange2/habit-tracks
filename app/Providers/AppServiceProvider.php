<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\{Habit, Reward, JournalEntry, FocusMode, Category, Difficulty};
use App\Policies\{HabitPolicy, RewardPolicy, JournalEntryPolicy, FocusModePolicy, CategoryPolicy, DifficultyPolicy};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }



    protected $policies = [
        Habit::class => HabitPolicy::class,
        Reward::class => RewardPolicy::class,
        JournalEntry::class => JournalEntryPolicy::class,
        FocusMode::class => FocusModePolicy::class,
        Category::class => CategoryPolicy::class,
        Difficulty::class => DifficultyPolicy::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
