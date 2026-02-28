<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'banned_users' => User::where('is_banned', true)->count(),
            'active_users' => User::where('is_banned', false)->count(),
            'total_colocations' => Colocation::count(),
            'active_colocations' => Colocation::where('status', 'active')->count(),
            'total_expenses' => Expense::count(),
        ];

        $users = User::with('roles')->paginate(10);
        $user = auth()->user();

        return view('admin.dashboard', compact('stats', 'users', 'user'));
    }

    public function banUser(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous bannir vous-meme.');
        }

        $user->update(['is_banned' => true]);

        return redirect()->back()->with('success', "Utilisateur {$user->name} banni avec succes.");
    }

    public function unbanUser(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_banned' => false]);

        return redirect()->back()->with('success', "Utilisateur {$user->name} debanni avec succes.");
    }
}
