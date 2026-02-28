<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Dashboard EasyColoc</h2>
    </x-slot>

    <div class="ec-page">
        <div class="ec-layout">
            @include('layouts.sidebar')

            <div class="space-y-6">
                <div class="ec-header-card p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-extrabold">Bienvenue {{ $user->name }}</h3>
                            <p class="mt-2 text-sm text-blue-100/90">
                                Visualisez vos depenses, soldes et remboursements de maniere claire.
                            </p>
                        </div>
                        <a href="{{ route('profile.edit') }}"
                            class="inline-flex items-center rounded-xl border border-blue-200/60 bg-white/15 px-4 py-2 text-xs font-bold text-white transition hover:bg-white/25">
                            Mon profil
                        </a>
                    </div>
                </div>

                @if(!$activeColocation)
                    <div class="ec-card p-6">
                        <h3 class="text-lg font-extrabold text-slate-900">Aucune colocation active</h3>
                        <p class="mt-2 text-sm text-slate-600">Creer votre espace ou rejoindre une invitation pour commencer.</p>
                        <div class="mt-5 flex flex-wrap gap-3">
                            @can('create_colocation')
                                <a href="{{ route('colocations.create') }}"
                                    class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-500">
                                    Creer une colocation
                                </a>
                            @endcan
                            @can('join_colocation')
                                <a href="{{ route('colocations.index') }}"
                                    class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                    Rejoindre une invitation
                                </a>
                            @endcan
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div class="ec-metric-card">
                            <p class="ec-metric-label">Total depenses</p>
                            <p class="ec-metric-value">{{ number_format($dashboard['total_expenses'], 2) }} EUR</p>
                        </div>
                        <div class="ec-metric-card">
                            <p class="ec-metric-label">Mon solde</p>
                            <p class="ec-metric-value {{ $dashboard['my_balance'] > 0 ? 'text-red-600' : ($dashboard['my_balance'] < 0 ? 'text-emerald-600' : 'text-slate-900') }}">
                                {{ number_format(abs($dashboard['my_balance']), 2) }} EUR
                            </p>
                            <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $dashboard['my_balance'] > 0 ? 'ec-badge-negative' : ($dashboard['my_balance'] < 0 ? 'ec-badge-positive' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                {{ $dashboard['my_balance'] > 0 ? 'Vous devez payer' : ($dashboard['my_balance'] < 0 ? 'Vous devez recevoir' : 'Solde equilibre') }}
                            </span>
                        </div>
                        <div class="ec-metric-card">
                            <p class="ec-metric-label">Membres actifs</p>
                            <p class="ec-metric-value">{{ $dashboard['members_count'] }}</p>
                        </div>
                        <div class="ec-metric-card">
                            <p class="ec-metric-label">Depenses ce mois</p>
                            <p class="ec-metric-value">{{ $dashboard['expenses_this_month'] }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                        <div class="ec-card p-6 xl:col-span-2">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-lg font-extrabold text-slate-900">Depenses recentes</h3>
                                <a href="{{ route('expenses.index', $activeColocation) }}"
                                    class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                                    Voir tout
                                </a>
                            </div>

                            @if($dashboard['recent_expenses']->isEmpty())
                                <p class="mt-4 text-sm text-slate-500">Aucune depense enregistree.</p>
                            @else
                                <div class="mt-4 overflow-x-auto">
                                    <table class="min-w-full divide-y divide-slate-200">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Titre</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Categorie</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Payeur</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Montant</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 bg-white/80">
                                            @foreach($dashboard['recent_expenses'] as $expense)
                                                <tr class="transition hover:bg-slate-50">
                                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $expense->date->format('d/m/Y') }}</td>
                                                    <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ $expense->title }}</td>
                                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $expense->category?->name }}</td>
                                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $expense->payer?->name }}</td>
                                                    <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ number_format($expense->amount, 2) }} EUR</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <div class="ec-card p-6">
                            <h3 class="text-lg font-extrabold text-slate-900">Statistiques categories</h3>
                            @if($dashboard['category_breakdown']->isEmpty())
                                <p class="mt-4 text-sm text-slate-500">Ajoutez des depenses pour afficher un graphique.</p>
                            @else
                                <div class="mt-4 space-y-3">
                                    @foreach($dashboard['category_breakdown'] as $stat)
                                        <div class="space-y-1.5">
                                            <div class="ec-chart-row">
                                                <p class="w-28 shrink-0 truncate text-xs font-semibold text-slate-600">{{ $stat->category_name }}</p>
                                                <div class="h-2 flex-1 rounded-full bg-slate-100">
                                                    <div class="ec-chart-bar"
                                                        style="width: {{ $dashboard['category_max'] > 0 ? round(((float) $stat->total / $dashboard['category_max']) * 100, 1) : 0 }}%"></div>
                                                </div>
                                            </div>
                                            <p class="text-right text-xs font-bold text-slate-700">{{ number_format((float) $stat->total, 2) }} EUR</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                        <div class="ec-card p-6">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-lg font-extrabold text-slate-900">Qui doit quoi a qui</h3>
                                <a href="{{ route('expenses.index', $activeColocation) }}"
                                    class="inline-flex items-center rounded-xl bg-blue-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-blue-500">
                                    Gerer les paiements
                                </a>
                            </div>

                            @if(empty($dashboard['settlements']))
                                <p class="mt-4 text-sm font-semibold text-emerald-700">Aucune dette en attente.</p>
                            @else
                                <div class="mt-4 space-y-3">
                                    @foreach($dashboard['settlements'] as $settlement)
                                        <div class="ec-settlement-card">
                                            <p class="text-sm text-slate-700">
                                                <span class="font-bold text-slate-900">{{ $settlement['from']->name }}</span>
                                                doit
                                                <span class="font-bold text-red-600">{{ number_format($settlement['amount'], 2) }} EUR</span>
                                                a
                                                <span class="font-bold text-emerald-700">{{ $settlement['to']->name }}</span>
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="ec-card p-6">
                            <h3 class="text-lg font-extrabold text-slate-900">Membres et roles</h3>
                            <div class="mt-4 space-y-3">
                                @foreach($dashboard['members'] as $member)
                                    <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white/85 px-4 py-3">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">{{ $member->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $member->email }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $member->pivot->role }}</p>
                                            <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $member->pivot->balance <= 0 ? 'ec-badge-positive' : 'ec-badge-negative' }}">
                                                {{ $member->pivot->balance <= 0 ? 'Doit recevoir' : 'Doit payer' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

