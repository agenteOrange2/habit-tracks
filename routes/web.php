<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', App\Livewire\Dashboard\Index::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/welcome', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Placeholder routes for quick actions (to be implemented)
    Route::get('/habits/create', function () {
        return redirect()->route('dashboard')->with('info', 'Función de crear hábito próximamente disponible');
    })->name('habits.create');

    Route::get('/pomodoro', function () {
        return redirect()->route('dashboard')->with('info', 'Función de Pomodoro próximamente disponible');
    })->name('pomodoro.index');

    Route::get('/rewards', function () {
        return redirect()->route('dashboard')->with('info', 'Función de recompensas próximamente disponible');
    })->name('rewards.index');

    Route::get('/journal/create', function () {
        return redirect()->route('dashboard')->with('info', 'Función de diario próximamente disponible');
    })->name('journal.create');
});
