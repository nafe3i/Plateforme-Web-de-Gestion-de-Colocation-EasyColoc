<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('adminGlobal')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('admin/dashboard', [UserController::class, 'dashboard'])
        ->middleware('role:adminGlobal')
        ->name('admin.dashboard');

    Route::get('/user/dashboard', [UserController::class, 'index'])
        ->middleware('role:user')
        ->name('user.dashboard');
});

require __DIR__ . '/auth.php';
