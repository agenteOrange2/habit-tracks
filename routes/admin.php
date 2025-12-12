<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| All routes in this file are automatically prefixed with /admin and
| protected with ['auth', 'verified'] middleware as configured in
| bootstrap/app.php. These routes handle the main application functionality.
|
*/

// Dashboard
Route::get('/dashboard', App\Livewire\Dashboard\Index::class)
    ->name('admin.dashboard');

// Habits Management
Route::get('/habits', App\Livewire\Habits\HabitList::class)
    ->name('admin.habits.index');
Route::get('/habits/create', App\Livewire\Habits\CreateHabit::class)
    ->name('admin.habits.create');
Route::get('/habits/{habit}/edit', App\Livewire\Habits\EditHabit::class)
    ->name('admin.habits.edit');
Route::get('/habits/{habit}/stats', App\Livewire\Habits\HabitStats::class)
    ->name('admin.habits.stats');

// Categories Management
Route::get('/categories', App\Livewire\Admin\Categories\CategoryList::class)
    ->name('admin.categories.index');

// Difficulties Management
Route::get('/difficulties', App\Livewire\Admin\Difficulties\DifficultyList::class)
    ->name('admin.difficulties.index');

// Settings
Route::redirect('/settings', '/admin/settings/profile');

Route::get('/settings/profile', Profile::class)
    ->name('admin.settings.profile');
Route::get('/settings/password', Password::class)
    ->name('admin.settings.password');
Route::get('/settings/appearance', Appearance::class)
    ->name('admin.settings.appearance');
Route::get('/settings/two-factor', TwoFactor::class)
    ->middleware(
        when(
            Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
            ['password.confirm'],
            [],
        ),
    )
    ->name('admin.settings.two-factor');

// Pomodoro Timer
Route::get('/pomodoro', App\Livewire\Pomodoro\PomodoroTimer::class)
    ->name('admin.pomodoro');

// Rewards System
Route::get('/rewards', App\Livewire\Rewards\RewardShop::class)
    ->name('rewards.index');
Route::get('/rewards/create', App\Livewire\Rewards\CreateReward::class)
    ->name('rewards.create');
Route::get('/rewards/{reward}/edit', App\Livewire\Rewards\EditReward::class)
    ->name('rewards.edit');
Route::get('/rewards/history', App\Livewire\Rewards\RewardHistory::class)
    ->name('rewards.history');
Route::get('/rewards/stats', App\Livewire\Rewards\RewardStats::class)
    ->name('rewards.stats');

// Journal
Route::get('/journal/create', function () {
    return redirect()->route('admin.dashboard')->with('info', 'Función de diario próximamente disponible');
})->name('admin.journal.create');

// Notes System
Route::get('/notes', App\Livewire\Notes\NotesList::class)
    ->name('notes.index');
Route::get('/notes/create', App\Livewire\Notes\NoteEditor::class)
    ->name('notes.create');
Route::get('/notes/trash', App\Livewire\Notes\TrashNotes::class)
    ->name('notes.trash');
Route::get('/notes/{note}/edit', App\Livewire\Notes\NoteEditor::class)
    ->name('notes.edit');
Route::get('/notes/folders', App\Livewire\Notes\FolderManager::class)
    ->name('notes.folders');
Route::get('/notes/tags', App\Livewire\Notes\TagManager::class)
    ->name('notes.tags');
