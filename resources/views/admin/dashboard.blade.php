<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Admin Dashboard</h2>
    </x-slot>

    <div class="ec-page">
        <div class="ec-layout">
            @include('layouts.sidebar')

            <div class="space-y-6">
                @if(session('success'))
                    <div class="ec-alert ec-alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="ec-alert ec-alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-6">
                    <div class="ec-metric-card">
                        <p class="ec-metric-label">Total utilisateurs</p>
                        <p class="ec-metric-value">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="ec-metric-card">
                        <p class="ec-metric-label">Utilisateurs actifs</p>
                        <p class="ec-metric-value text-emerald-600">{{ $stats['active_users'] }}</p>
                    </div>
                    <div class="ec-metric-card">
                        <p class="ec-metric-label">Utilisateurs bannis</p>
                        <p class="ec-metric-value text-red-600">{{ $stats['banned_users'] }}</p>
                    </div>
                    <div class="ec-metric-card">
                        <p class="ec-metric-label">Total colocations</p>
                        <p class="ec-metric-value text-blue-600">{{ $stats['total_colocations'] }}</p>
                    </div>
                    <div class="ec-metric-card">
                        <p class="ec-metric-label">Colocations actives</p>
                        <p class="ec-metric-value text-sky-600">{{ $stats['active_colocations'] }}</p>
                    </div>
                    <div class="ec-metric-card">
                        <p class="ec-metric-label">Total depenses</p>
                        <p class="ec-metric-value text-indigo-600">{{ $stats['total_expenses'] }}</p>
                    </div>
                </div>

                <div class="ec-card p-6">
                    <h3 class="text-lg font-extrabold text-slate-900">Gestion des utilisateurs</h3>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nom</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Roles</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Reputation</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Statut</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white/80">
                                @foreach($users as $u)
                                    <tr class="transition hover:bg-slate-50">
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $u->id }}</td>
                                        <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ $u->name }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600">{{ $u->email }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($u->roles as $role)
                                                    <span class="ec-pill">{{ $role->name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-bold {{ $u->reputation >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $u->reputation }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($u->is_banned)
                                                <span class="ec-badge-negative inline-flex rounded-full px-2.5 py-1 text-xs font-bold">Banni</span>
                                            @else
                                                <span class="ec-badge-positive inline-flex rounded-full px-2.5 py-1 text-xs font-bold">Actif</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold">
                                            @if($u->id !== auth()->id())
                                                @if($u->is_banned)
                                                    <form method="POST" action="{{ route('admin.users.unban', $u->id) }}">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-bold text-white transition hover:bg-emerald-500">
                                                            Debannir
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.users.ban', $u->id) }}">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg bg-red-600 px-2.5 py-1 text-xs font-bold text-white transition hover:bg-red-500"
                                                            onclick="return confirm('Confirmer le bannissement de {{ $u->name }} ?')">
                                                            Bannir
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <span class="text-slate-400">Vous</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

