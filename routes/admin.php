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
Route::get('/journal', App\Livewire\Journal\JournalList::class)
    ->name('admin.journal.index');
Route::get('/journal/create', App\Livewire\Journal\JournalEditor::class)
    ->name('admin.journal.create');
Route::get('/journal/{entry}/edit', App\Livewire\Journal\JournalEditor::class)
    ->name('admin.journal.edit');

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

// Calendar System
Route::get('/calendar', App\Livewire\Calendar\CalendarView::class)
    ->name('admin.calendar.index');
Route::get('/calendar/create', App\Livewire\Calendar\EventEditor::class)
    ->name('admin.calendar.create');
Route::get('/calendar/{event}/edit', App\Livewire\Calendar\EventEditor::class)
    ->name('admin.calendar.edit');
Route::get('/calendar/settings', App\Livewire\Calendar\CalendarSettings::class)
    ->name('admin.calendar.settings');
Route::get('/calendar/google/connect', function () {
    // Check if Google credentials are configured
    if (empty(config('google.client_id')) || empty(config('google.client_secret'))) {
        return redirect()->route('admin.calendar.settings')
            ->with('error', 'Las credenciales de Google Calendar no están configuradas. Configura GOOGLE_CLIENT_ID y GOOGLE_CLIENT_SECRET en tu archivo .env');
    }
    
    $googleService = app(\App\Services\GoogleCalendarService::class);
    return redirect()->away($googleService->getAuthUrl());
})->name('admin.calendar.google.connect');
