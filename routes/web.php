<?php

use App\Http\Controllers\AdminController;
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
    
    // Routes Admin
    Route::prefix('admin')->name('admin.')->middleware('permission:view_statistics')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Actions bannissement (permission ban_users )
        Route::post('/users/{id}/ban', [AdminController::class, 'banUser'])->name('users.ban');
        Route::post('/users/{id}/unban', [AdminController::class, 'unbanUser'])->name('users.unban');
    });
    
    // Routes User
    Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.dashboard');
});

require __DIR__ . '/auth.php';
