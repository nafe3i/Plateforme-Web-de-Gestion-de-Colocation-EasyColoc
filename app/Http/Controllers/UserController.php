<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    //
    public function index()
    {
        // dd(auth()->user()->roles());
        // die;
        $user = Auth::user();
        return view('dashboard', compact('user'));
    }
    public function dashboard()
    {
        // dd(Auth()->user()->hasRole('adminGlobal'));
        // die;
        $user = Auth::user();
        // return view('dashboard', compact(var_name: 'user'));
        $stats = [
            'total_users' => User::count(),
            'banned_users' => User::where('is_banned', true)->count(),
            'active_users' => User::where('is_banned', false)->count(),
        ];
        $users= User::with('roles')->get();

        return view('dashboard', compact('user', 'stats','users'));
    }
}
