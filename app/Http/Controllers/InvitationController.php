<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public function store(Request $request, Colocation $colocation)
    {
        if (!$colocation->isOwner(Auth::user())) {
            abort(403, 'Seul le proprietaire peut inviter.');
        }

        if (!$colocation->isActive()) {
            return redirect()->back()->with('error', 'Impossible d\'inviter sur une colocation annulee.');
        }

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $existingMember = User::where('email', $validated['email'])
            ->whereHas('memberships', function ($query) use ($colocation) {
                $query->where('colocation_id', $colocation->id)
                    ->whereNull('left_at');
            })->exists();

        if ($existingMember) {
            return redirect()->back()->with('error', 'Cette personne est deja membre.');
        }

        $existingInvitation = Invitation::where('colocation_id', $colocation->id)
            ->where('email', $validated['email'])
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return redirect()->back()->with('error', 'Invitation deja envoyee a cet email.');
        }

        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'invited_by' => Auth::id(),
            'email' => $validated['email'],
        ]);

        $inviteLink = route('invitations.show', $invitation->token);
        $mailWarning = null;

        try {
            Mail::to($invitation->email)->send(new InvitationMail($invitation));
        } catch (\Throwable $e) {
            $mailWarning = ' Invitation creee mais email non envoye (verifiez la config MAIL_*).';
        }

        return redirect()->back()->with(
            'success',
            "Invitation envoyee a {$validated['email']}. Lien: {$inviteLink}{$mailWarning}"
        );
    }

    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isPending()) {
            return view('invitations.already-responded', compact('invitation'));
        }

        return view('invitations.show', compact('invitation'));
    }

    public function accept(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isPending()) {
            return redirect()->route('login')->with('error', 'Invitation deja traitee.');
        }

        if (!Auth::check()) {
            session(['invitation_token' => $token]);
            return redirect()->route('login')->with('info', 'Connectez-vous pour accepter l\'invitation.');
        }

        $user = Auth::user();

        if ($user->email !== $invitation->email) {
            return redirect()->back()->with('error', 'Cette invitation est pour ' . $invitation->email);
        }

        if ($user->hasActiveColocation()) {
            return redirect()->back()->with('error', 'Vous avez deja une colocation active.');
        }

        if (!$invitation->colocation || !$invitation->colocation->isActive()) {
            return redirect()->back()->with('error', 'Cette colocation n\'est plus active.');
        }

        DB::beginTransaction();
        try {
            Membership::create([
                'user_id' => $user->id,
                'colocation_id' => $invitation->colocation_id,
                'role' => 'member',
                'balance' => 0,
                'manual_adjustment' => 0,
                'joined_at' => now(),
            ]);

            $invitation->update(['status' => 'accepted']);
            session()->forget('invitation_token');

            DB::commit();

            return redirect()->route('colocations.show', $invitation->colocation_id)
                ->with('success', 'Vous avez rejoint la colocation.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de l\'acceptation.');
        }
    }

    public function reject(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isPending()) {
            return redirect()->route('login')->with('error', 'Invitation deja traitee.');
        }

        $invitation->update(['status' => 'rejected']);
        session()->forget('invitation_token');

        return redirect()->route('login')->with('success', 'Invitation refusee.');
    }
}
