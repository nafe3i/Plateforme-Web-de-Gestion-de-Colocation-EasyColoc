<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Membership;
use App\Models\User;
use App\Services\BalanceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ColocationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasActiveColocation()) {
            return redirect()->route('colocations.show', $user->activeColocation()->id);
        }

        return view('colocations.index');
    }

    public function create()
    {
        $user = Auth::user();

        if (!$user->can('create_colocation')) {
            abort(403, 'Permission refusee.');
        }

        if ($user->hasActiveColocation()) {
            return redirect()->route('colocations.show', $user->activeColocation()->id)
                ->with('error', 'Vous avez deja une colocation active.');
        }

        return view('colocations.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('create_colocation')) {
            abort(403, 'Permission refusee.');
        }

        if ($user->hasActiveColocation()) {
            return redirect()->back()
                ->with('error', 'Vous avez deja une colocation active.')
                ->withInput();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $colocation = Colocation::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'owner_id' => $user->id,
                'status' => 'active',
            ]);

            Membership::create([
                'user_id' => $user->id,
                'colocation_id' => $colocation->id,
                'role' => 'owner',
                'balance' => 0,
                'manual_adjustment' => 0,
                'joined_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('colocations.show', $colocation->id)
                ->with('success', 'Colocation creee avec succes.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la creation.')
                ->withInput();
        }
    }

    public function show(Colocation $colocation)
    {
        if (!$colocation->hasMember(Auth::user())) {
            abort(403, 'Vous n etes pas membre de cette colocation.');
        }

        BalanceCalculator::recalculate($colocation);
        $colocation->load(['owner', 'activeMembers']);

        return view('colocations.show', compact('colocation'));
    }

    public function edit(Colocation $colocation)
    {
        if (!$colocation->isOwner(Auth::user())) {
            abort(403, 'Seul le proprietaire peut modifier la colocation.');
        }

        if (!$colocation->isActive()) {
            return redirect()->route('colocations.show', $colocation)
                ->with('error', 'La colocation annulee ne peut pas etre modifiee.');
        }

        return view('colocations.edit', compact('colocation'));
    }

    public function update(Request $request, Colocation $colocation)
    {
        if (!$colocation->isOwner(Auth::user())) {
            abort(403, 'Seul le proprietaire peut modifier la colocation.');
        }

        if (!$colocation->isActive()) {
            return redirect()->route('colocations.show', $colocation)
                ->with('error', 'La colocation annulee ne peut pas etre modifiee.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $colocation->update($validated);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation mise a jour avec succes.');
    }

    public function leave(Colocation $colocation)
    {
        $user = Auth::user();

        if (!$colocation->hasMember($user)) {
            abort(403, 'Vous n etes pas membre de cette colocation.');
        }

        if ($colocation->isOwner($user)) {
            return redirect()->back()
                ->with('error', 'En tant que proprietaire, vous devez annuler la colocation.');
        }

        $membership = $this->activeMembership($colocation, $user->id);

        if (!$membership) {
            return redirect()->back()->with('error', 'Membership introuvable.');
        }

        DB::beginTransaction();
        try {
            BalanceCalculator::recalculate($colocation);
            $membership->refresh();
            $balance = (float) $membership->balance;
            $hasDebt = $balance > 0.01;

            $remainingMemberships = $colocation->memberships()
                ->whereNull('left_at')
                ->where('user_id', '!=', $user->id)
                ->get();

            $this->distributeAdjustment($remainingMemberships, $balance);
            $membership->update(['left_at' => now()]);
            BalanceCalculator::recalculate($colocation);

            if ($hasDebt) {
                $user->decrement('reputation');
                $message = "Vous avez quitte la colocation. Reputation -1 (dette " . number_format($balance, 2) . " EUR).";
            } else {
                $user->increment('reputation');
                $message = 'Vous avez quitte la colocation. Reputation +1.';
            }

            DB::commit();

            return redirect()->route('colocations.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors du depart.');
        }
    }

    public function destroy(Colocation $colocation)
    {
        if (!$colocation->isOwner(Auth::user())) {
            abort(403, 'Seul le proprietaire peut annuler la colocation.');
        }

        DB::beginTransaction();
        try {
            BalanceCalculator::recalculate($colocation);
            $activeMemberships = $colocation->memberships()
                ->with('user')
                ->whereNull('left_at')
                ->get();

            foreach ($activeMemberships as $membership) {
                if (!$membership->user) {
                    continue;
                }

                if ((float) $membership->balance > 0.01) {
                    $membership->user->decrement('reputation');
                } else {
                    $membership->user->increment('reputation');
                }
            }

            $colocation->update(['status' => 'cancelled']);
            $colocation->memberships()
                ->whereNull('left_at')
                ->update(['left_at' => now()]);

            DB::commit();

            return redirect()->route('colocations.index')
                ->with('success', 'Colocation annulee. Reputation mise a jour pour les membres.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de l annulation.');
        }
    }

    public function removeMember(Colocation $colocation, User $member)
    {
        if (!$colocation->isOwner(Auth::user())) {
            abort(403, 'Seul le proprietaire peut retirer un membre.');
        }

        if ($colocation->isOwner($member)) {
            return redirect()->back()->with('error', 'Impossible de retirer le proprietaire.');
        }

        $membership = $this->activeMembership($colocation, $member->id);

        if (!$membership) {
            return redirect()->back()->with('error', 'Membre non trouve.');
        }

        DB::beginTransaction();
        try {
            BalanceCalculator::recalculate($colocation);
            $membership->refresh();

            $balance = (float) $membership->balance;
            $hasDebt = $balance > 0.01;

            if ($hasDebt) {
                $ownerMembership = $this->activeMembership($colocation, (int) Auth::id());

                if (!$ownerMembership) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Owner introuvable pour transfert de dette.');
                }

                $ownerMembership->increment('manual_adjustment', $balance);
                $member->decrement('reputation');

                $message = "Membre {$member->name} retire. Dette " . number_format($balance, 2) . " EUR transferee a l owner. Reputation -1.";
            } else {
                $remainingMemberships = $colocation->memberships()
                    ->whereNull('left_at')
                    ->where('user_id', '!=', $member->id)
                    ->get();

                $this->distributeAdjustment($remainingMemberships, $balance);
                $member->increment('reputation');
                $message = "Membre {$member->name} retire. Reputation +1 (aucune dette).";
            }

            $membership->update(['left_at' => now()]);
            BalanceCalculator::recalculate($colocation);

            DB::commit();

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors du retrait du membre.');
        }
    }

    private function activeMembership(Colocation $colocation, int $userId): ?Membership
    {
        return $colocation->memberships()
            ->where('user_id', $userId)
            ->whereNull('left_at')
            ->first();
    }

    private function distributeAdjustment($memberships, float $amount): void
    {
        $memberships = $memberships->values();
        $count = $memberships->count();

        if ($count === 0 || abs($amount) < 0.01) {
            return;
        }

        $baseShare = round($amount / $count, 2);
        $distributed = 0.0;

        foreach ($memberships as $index => $membership) {
            $isLast = $index === $count - 1;
            $share = $isLast ? round($amount - $distributed, 2) : $baseShare;
            $membership->increment('manual_adjustment', $share);
            $distributed += $share;
        }
    }
}
