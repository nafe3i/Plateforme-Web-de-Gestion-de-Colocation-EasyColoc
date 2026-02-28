<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Depenses - {{ $colocation->name }}</h2>
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
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <form method="GET" action="{{ route('expenses.index', $colocation) }}" class="flex items-end gap-3">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Filtrer par mois</label>
                                <select name="month" class="rounded-xl border border-slate-300 bg-white/90 px-3 py-2 text-sm">
                                    <option value="all" {{ $month === 'all' ? 'selected' : '' }}>Tous les mois</option>
                                    @foreach($months as $m)
                                        <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-500">
                                Filtrer
                            </button>
                        </form>

                        <div class="flex gap-3">
                            <a href="{{ route('colocations.show', $colocation) }}"
                                class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                Retour colocation
                            </a>
                            <a href="{{ route('expenses.create', $colocation) }}"
                                class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-500">
                                Ajouter depense
                            </a>
                        </div>
                    </div>
                </div>

                <div class="ec-card p-6">
                    <h3 class="text-lg font-extrabold text-slate-900">Historique des depenses</h3>

                    @if($expenses->isEmpty())
                        <p class="mt-3 text-slate-500">Aucune depense pour ce filtre.</p>
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
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white/80">
                                    @foreach($expenses as $expense)
                                        <tr class="transition hover:bg-slate-50">
                                            <td class="px-4 py-3 text-sm text-slate-700">{{ $expense->date->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ $expense->title }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-700">{{ $expense->category?->name }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-700">{{ $expense->payer?->name }}</td>
                                            <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ number_format($expense->amount, 2) }} EUR</td>
                                            <td class="px-4 py-3 text-sm">
                                                <form action="{{ route('expenses.destroy', [$colocation, $expense]) }}" method="POST"
                                                    onsubmit="return confirm('Supprimer cette depense ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-lg bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 transition hover:bg-red-100">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="ec-card p-6">
                    <h3 class="text-lg font-extrabold text-slate-900">Qui doit a qui</h3>

                    @if(empty($settlements))
                        <p class="mt-3 font-semibold text-emerald-700">Aucun remboursement en attente.</p>
                    @else
                        <div class="mt-4 space-y-3">
                            @foreach($settlements as $settlement)
                                <div class="ec-settlement-card flex flex-wrap items-center justify-between gap-3">
                                    <p class="text-sm text-slate-700">
                                        <span class="font-bold text-slate-900">{{ $settlement['from']->name }}</span>
                                        doit
                                        <span class="font-bold text-red-600">{{ number_format($settlement['amount'], 2) }} EUR</span>
                                        a
                                        <span class="font-bold text-emerald-700">{{ $settlement['to']->name }}</span>
                                    </p>

                                    <form action="{{ route('settlements.pay', $colocation) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="from_user_id" value="{{ $settlement['from_id'] }}">
                                        <input type="hidden" name="to_user_id" value="{{ $settlement['to_id'] }}">
                                        <input type="hidden" name="amount" value="{{ $settlement['amount'] }}">
                                        <button class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white transition hover:-translate-y-0.5 hover:bg-emerald-500">
                                            Marquer paye
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

