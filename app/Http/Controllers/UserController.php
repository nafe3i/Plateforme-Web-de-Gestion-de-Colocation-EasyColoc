<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Services\BalanceCalculator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeColocation = $user->activeColocation();

        $dashboard = [
            'total_expenses' => 0.0,
            'my_balance' => 0.0,
            'members_count' => 0,
            'expenses_this_month' => 0,
            'recent_expenses' => collect(),
            'settlements' => [],
            'members' => collect(),
            'category_breakdown' => collect(),
            'category_max' => 0.0,
        ];

        if ($activeColocation) {
            BalanceCalculator::recalculate($activeColocation);

            $dashboard['total_expenses'] = (float) $activeColocation->expenses()->sum('amount');
            $dashboard['expenses_this_month'] = $activeColocation->expenses()
                ->whereYear('date', now()->year)
                ->whereMonth('date', now()->month)
                ->count();
            $dashboard['recent_expenses'] = $activeColocation->expenses()
                ->with(['payer', 'category'])
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->limit(8)
                ->get();
            $dashboard['members'] = $activeColocation->activeMembers()
                ->orderBy('name')
                ->get();
            $dashboard['members_count'] = $dashboard['members']->count();
            $dashboard['settlements'] = BalanceCalculator::getSettlements($activeColocation);

            $membership = Membership::query()
                ->where('colocation_id', $activeColocation->id)
                ->where('user_id', $user->id)
                ->whereNull('left_at')
                ->first();

            $dashboard['my_balance'] = $membership ? (float) $membership->balance : 0.0;

            // Simpler aggregation for readability: group expenses in PHP by category name.
            $dashboard['category_breakdown'] = $activeColocation->expenses()
                ->with('category')
                ->get()
                ->groupBy(fn($expense) => $expense->category?->name ?? 'Sans categorie')
                ->map(fn($items, $name) => (object) [
                    'category_name' => $name,
                    'total' => $items->sum('amount'),
                ])
                ->orderByDesc('total')
                ->get();
            $dashboard['category_max'] = (float) ($dashboard['category_breakdown']->max('total') ?? 0);
        }

        return view('dashboard', compact('user', 'activeColocation', 'dashboard'));
    }
}
