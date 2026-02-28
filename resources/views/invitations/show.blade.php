<x-guest-layout>
    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Invitation a rejoindre une colocation</h2>

        <div class="mt-5 rounded-xl border border-blue-200 bg-blue-50 p-4">
            <p class="text-sm text-blue-800">
                <span class="font-bold">{{ $invitation->inviter->name }}</span> vous invite a rejoindre:
            </p>
            <p class="mt-1 text-xl font-extrabold text-blue-900">{{ $invitation->colocation->name }}</p>
            @if($invitation->colocation->description)
                <p class="mt-2 text-sm text-blue-800">{{ $invitation->colocation->description }}</p>
            @endif
        </div>

        <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Invitation pour</p>
            <p class="mt-1 font-bold text-slate-900">{{ $invitation->email }}</p>
        </div>

        @if(session('error'))
            <div class="ec-alert ec-alert-error mt-4">{{ session('error') }}</div>
        @endif

        @auth
            @if(auth()->user()->email === $invitation->email)
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <form action="{{ route('invitations.accept', $invitation->token) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-500">
                            Accepter
                        </button>
                    </form>

                    <form action="{{ route('invitations.reject', $invitation->token) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-red-500"
                            onclick="return confirm('Confirmer le refus de cette invitation ?')">
                            Refuser
                        </button>
                    </form>
                </div>
            @else
                <div class="ec-alert ec-alert-error mt-6">
                    Cette invitation est pour {{ $invitation->email }}, mais vous etes connecte avec {{ auth()->user()->email }}.
                </div>
            @endif
        @else
            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                Vous devez etre connecte pour repondre a l invitation.
                <a href="{{ route('login') }}" class="ml-2 font-bold text-amber-900 underline">Se connecter</a>
            </div>
        @endauth
    </div>
</x-guest-layout>

