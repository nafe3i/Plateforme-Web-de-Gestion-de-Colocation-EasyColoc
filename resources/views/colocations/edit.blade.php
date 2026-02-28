<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Modifier la colocation</h2>
    </x-slot>

    <div class="ec-page">
        <div class="ec-layout">
            @include('layouts.sidebar')

            <div class="space-y-6">
                @if(session('error'))
                    <div class="ec-alert ec-alert-error">{{ session('error') }}</div>
                @endif

                <div class="ec-card p-6">
                    <h3 class="text-lg font-extrabold text-slate-900">Informations de la colocation</h3>

                    <form method="POST" action="{{ route('colocations.update', $colocation) }}" class="mt-5 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nom</label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name', $colocation->name) }}"
                                required
                                maxlength="255"
                                class="w-full rounded-xl border border-slate-300 bg-white/90 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            @error('name')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="mb-1.5 block text-sm font-semibold text-slate-700">Description</label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                maxlength="1000"
                                class="w-full rounded-xl border border-slate-300 bg-white/90 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('description', $colocation->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap gap-3 pt-2">
                            <button type="submit" class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-500">
                                Enregistrer
                            </button>
                            <a href="{{ route('colocations.show', $colocation) }}"
                                class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
