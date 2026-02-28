<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Categories</h2>
    </x-slot>

    <div class="ec-page space-y-6">
        @if(session('success'))
            <div class="ec-alert ec-alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="ec-alert ec-alert-error">{{ session('error') }}</div>
        @endif

        <div class="ec-card p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">Gestion des categories</h3>
                    @if($activeColocation)
                        <p class="mt-1 text-sm text-slate-500">Colocation active: {{ $activeColocation->name }}</p>
                    @endif
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                    Retour dashboard
                </a>
            </div>
        </div>

        @if($canManage)
            <div class="ec-card p-6">
                <h3 class="text-base font-extrabold text-slate-900">Ajouter une categorie</h3>
                <form action="{{ route('categories.store') }}" method="POST" class="mt-4 flex flex-wrap gap-3">
                    @csrf
                    <x-text-input type="text" name="name" placeholder="Nom de categorie" required class="flex-1 min-w-[220px]" />
                    <button class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-500">
                        Ajouter
                    </button>
                </form>
                @error('name')
                    <x-input-error class="mt-2" :messages="$message" />
                @enderror
            </div>
        @endif

        <div class="ec-card p-6">
            <h3 class="text-base font-extrabold text-slate-900">Liste des categories</h3>
            <div class="mt-4 space-y-3">
                @foreach($categories as $category)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white/75 p-4">
                        <div class="font-bold text-slate-900">{{ $category->name }}</div>

                        @if($canManage)
                            <div class="flex flex-wrap items-center gap-2">
                                <form action="{{ route('categories.update', $category) }}" method="POST" class="flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <x-text-input type="text" name="name" value="{{ $category->name }}" class="max-w-[190px]" />
                                    <button class="inline-flex items-center rounded-lg bg-amber-500 px-3 py-2 text-xs font-bold text-white transition hover:bg-amber-400">
                                        Modifier
                                    </button>
                                </form>

                                <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                    onsubmit="return confirm('Supprimer cette categorie ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-red-500">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>

