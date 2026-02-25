<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        // if
        // (!Auth()->user()->can('view_admin_dashboard')) {
        //     abort(403);
        // }
        $stats = [
            'total_users' => User::count(),
            'banned_users' => User::where('is_banned', true)->count(),
            'active_users' => User::where('is_banned', false)->count(),
        ];
        $users = User::with('roles')->paginate(10);
        return view('admin.dashboard', compact('stats', 'users'));
    }
    public function banUser($id)
    {
        // if (!auth()->user()->can('ban_users')) {
        //     abort(403, 'Unauthorized action.');
        // }
        // $this->authorise('ban_users');
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot ban yourself.');
        }
        $user->update(['is_banned' => true]);
        return redirect()->back()->with('success', 'User banned successfully.');
    }
    public function unbanUser($id)
    {
        // if (!auth()->user()->can('ban_users')) {
        //     abort(403, 'Action non autorisée');
        // }

        $user = User::findOrFail($id);
        $user->update(['is_banned' => false]);

        return redirect()->back()->with('success', "Utilisateur {$user->name} débanni avec succès");
    }
}
