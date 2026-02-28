<x-guest-layout>
    <div class="text-center">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Invitation deja traitee</h2>

        @if($invitation->status === 'accepted')
            <p class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                Cette invitation a deja ete acceptee.
            </p>
        @else
            <p class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                Cette invitation a deja ete refusee.
            </p>
        @endif

        <a href="{{ route('login') }}"
            class="mt-6 inline-flex items-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-blue-500">
            Retour a la connexion
        </a>
    </div>
</x-guest-layout>

