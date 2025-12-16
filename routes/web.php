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

// Debug route - remove after testing
Route::get('/debug-livewire', function () {
    return response()->json([
        'status' => 'ok',
        'livewire_installed' => class_exists(\Livewire\Livewire::class),
        'app_url' => config('app.url'),
        'asset_url' => config('app.asset_url'),
        'session_driver' => config('session.driver'),
        'csrf_token' => csrf_token(),
        'php_version' => PHP_VERSION,
    ]);
});

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



