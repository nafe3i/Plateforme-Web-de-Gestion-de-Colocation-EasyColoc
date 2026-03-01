<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ExpenseController;

Route::get('/', function () {
    return view('welcome');
});

// Routes publiques invitations
Route::get('/invitations/{token}', [InvitationController::class, 'show'])
    ->name('invitations.show');
Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])
    ->name('invitations.accept');
Route::post('/invitations/{token}/reject', [InvitationController::class, 'reject'])
    ->name('invitations.reject');

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
        Route::post('/users/{id}/ban', [AdminController::class, 'banUser'])
            ->middleware('permission:ban_users')
            ->name('users.ban');
        Route::post('/users/{id}/unban', [AdminController::class, 'unbanUser'])
            ->middleware('permission:ban_users')
            ->name('users.unban');
    });

    // Routes User
    Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.dashboard');

    // Routes Colocations
    Route::resource('colocations', ColocationController::class);
    Route::post('/colocations/{colocation}/leave', [ColocationController::class, 'leave'])
        ->name('colocations.leave');
    Route::delete('/colocations/{colocation}/members/{member}', [ColocationController::class, 'removeMember'])
        ->name('colocations.removeMember');
    Route::post('/colocations/{colocation}/invite', [InvitationController::class, 'store'])
        ->name('invitations.store');

    // Routes Dépenses
    Route::get('/colocations/{colocation}/expenses', [ExpenseController::class, 'index'])
        ->name('expenses.index');
    Route::get('/colocations/{colocation}/expenses/create', [ExpenseController::class, 'create'])
        ->name('expenses.create');
    Route::post('/colocations/{colocation}/expenses', [ExpenseController::class, 'store'])
        ->name('expenses.store');
    Route::delete('/colocations/{colocation}/expenses/{expense}', [ExpenseController::class, 'destroy'])
        ->name('expenses.destroy');
});

require __DIR__ . '/auth.php';
