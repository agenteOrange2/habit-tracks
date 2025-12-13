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



