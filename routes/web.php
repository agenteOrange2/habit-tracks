<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| These routes are accessible to all users, regardless of authentication status.
|
*/

// Welcome page - accessible to all users
Route::get('/welcome', function () {
    return view('welcome');
})->name('home');

// Root route - shows welcome for guests, redirects to admin dashboard for authenticated users
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Google Calendar OAuth Callback
|--------------------------------------------------------------------------
|
| This route handles the OAuth callback from Google Calendar.
| It must be outside the admin prefix to match the redirect URI.
|
*/
Route::get('/calendar/google/callback', function () {
    $code = request()->get('code');
    
    if (!$code) {
        return redirect()->route('admin.calendar.settings')
            ->with('error', 'Error al conectar con Google Calendar');
    }
    
    try {
        $googleService = app(\App\Services\GoogleCalendarService::class);
        $googleService->handleCallback(auth()->user(), $code);
        
        return redirect()->route('admin.calendar.settings')
            ->with('message', 'Google Calendar conectado correctamente');
    } catch (\Exception $e) {
        return redirect()->route('admin.calendar.settings')
            ->with('error', 'Error al conectar: ' . $e->getMessage());
    }
})->middleware(['auth'])->name('calendar.google.callback');

