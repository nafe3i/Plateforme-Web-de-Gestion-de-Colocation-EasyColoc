<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Ajouter une depense - {{ $colocation->name }}</h2>
    </x-slot>

    <div class="ec-page">
        <div class="mx-auto max-w-3xl ec-card p-7">
            @if(session('error'))
                <div class="ec-alert ec-alert-error mb-4">{{ session('error') }}</div>
            @endif

            <form action="{{ route('expenses.store', $colocation) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <x-input-label value="Titre" />
                    <x-text-input type="text" name="title" value="{{ old('title') }}" required />
                    @error('title')
                        <x-input-error :messages="$message" class="mt-2" />
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Montant" />
                        <x-text-input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required />
                        @error('amount')
                            <x-input-error :messages="$message" class="mt-2" />
                        @enderror
                    </div>

                    <div>
                        <x-input-label value="Date" />
                        <x-text-input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required />
                        @error('date')
                            <x-input-error :messages="$message" class="mt-2" />
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Categorie" />
                        <select name="category_id" required class="w-full rounded-xl border border-slate-300 bg-white/90 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Selectionner</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <x-input-error :messages="$message" class="mt-2" />
                        @enderror
                    </div>

                    <div>
                        <x-input-label value="Paye par" />
                        <select name="paid_by" required class="w-full rounded-xl border border-slate-300 bg-white/90 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Selectionner</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ old('paid_by', auth()->id()) == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('paid_by')
                            <x-input-error :messages="$message" class="mt-2" />
                        @enderror
                    </div>
                </div>

                <div>
                    <x-input-label value="Description (optionnel)" />
                    <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-300 bg-white/90 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('description') }}</textarea>
                    @error('description')
                        <x-input-error :messages="$message" class="mt-2" />
                    @enderror
                </div>

                <div class="flex gap-3 pt-1">
                    <button class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-500">
                        Enregistrer
                    </button>
                    <a href="{{ route('expenses.index', $colocation) }}"
                        class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

