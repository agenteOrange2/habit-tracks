<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\{Habit, Reward, JournalEntry, FocusMode};
use App\Policies\{HabitPolicy, RewardPolicy, JournalEntryPolicy, FocusModePolicy};

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
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
