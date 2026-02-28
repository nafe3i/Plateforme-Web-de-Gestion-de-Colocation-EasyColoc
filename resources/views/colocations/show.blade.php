<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">{{ $colocation->name }}</h2>
    </x-slot>

    <div class="ec-page">
        <div class="ec-layout">
            @include('layouts.sidebar')

            <div class="space-y-6">
                @if(session('success'))
                    <div class="ec-alert ec-alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="ec-alert ec-alert-error">{{ session('error') }}</div>
                @endif

                <div class="ec-card p-6">
                    <h3 class="text-lg font-extrabold text-slate-900">Informations</h3>
                    <p class="mt-2 text-slate-700">{{ $colocation->description }}</p>
                    <p class="mt-2 text-sm text-slate-500">
                        Creee par {{ $colocation->owner->name }} le {{ $colocation->created_at->format('d/m/Y') }}
                    </p>
                </div>

                <div class="ec-card p-6">
                    <h3 class="text-lg font-extrabold text-slate-900">Membres ({{ $colocation->activeMembers->count() }})</h3>

                    <div class="mt-4 space-y-3">
                        @foreach($colocation->activeMembers as $member)
                            <div class="rounded-xl border border-slate-200 bg-white/85 p-4 transition hover:shadow-sm">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $member->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $member->email }}</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-600">
                                            Reputation: <span class="{{ $member->reputation >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $member->reputation }}</span>
                                        </p>

                                        @if((float) $member->pivot->balance !== 0.0)
                                            <p class="mt-1 text-xs font-semibold {{ $member->pivot->balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                                Balance: {{ number_format($member->pivot->balance, 2) }} EUR
                                                {{ $member->pivot->balance > 0 ? '(doit)' : '(credit)' }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2">
                                        @if($member->pivot->role === 'owner')
                                            <span class="ec-pill">Owner</span>
                                        @else
                                            <span class="ec-pill">Member</span>
                                            @if($colocation->isOwner(auth()->user()))
                                                <form action="{{ route('colocations.removeMember', [$colocation, $member]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-red-500"
                                                        onclick="return confirm('Retirer {{ $member->name }} de la colocation ?')">
                                                        Retirer
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($colocation->isOwner(auth()->user()))
                    <div class="ec-card p-6">
                        <h3 class="text-lg font-extrabold text-slate-900">Inviter un membre</h3>
                        <form action="{{ route('invitations.store', $colocation) }}" method="POST" class="mt-4 flex flex-wrap gap-3">
                            @csrf
                            <input type="email" name="email" placeholder="Email de la personne a inviter" required
                                class="flex-1 rounded-xl border border-slate-300 bg-white/90 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <button type="submit" class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-blue-500">
                                Envoyer l invitation
                            </button>
                        </form>
                    </div>
                @endif

                <div class="ec-card p-6">
                    <h3 class="text-lg font-extrabold text-slate-900">Actions</h3>

                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('expenses.index', $colocation) }}"
                            class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-500">
                            Voir les depenses
                        </a>

                        @if(auth()->user()->hasRole('adminGlobal') || $colocation->isOwner(auth()->user()))
                            <a href="{{ route('categories.index') }}"
                                class="inline-flex items-center rounded-xl bg-slate-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-600">
                                Gerer les categories
                            </a>
                        @endif

                        @if($colocation->isOwner(auth()->user()))
                            <a href="{{ route('colocations.edit', $colocation) }}"
                                class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                Modifier la colocation
                            </a>
                            <form action="{{ route('colocations.destroy', $colocation) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-red-500"
                                    onclick="return confirm('Etes-vous sur de vouloir annuler cette colocation ?')">
                                    Annuler la colocation
                                </button>
                            </form>
                        @else
                            <form action="{{ route('colocations.leave', $colocation) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-orange-500"
                                    onclick="return confirm('Etes-vous sur de vouloir quitter cette colocation ?')">
                                    Quitter la colocation
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

