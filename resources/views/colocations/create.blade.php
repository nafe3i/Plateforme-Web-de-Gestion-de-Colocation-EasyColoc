<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Creer une colocation</h2>
    </x-slot>

    <div class="ec-page">
        <div class="mx-auto max-w-2xl ec-card p-7">
            @if(session('error'))
                <div class="ec-alert ec-alert-error mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('colocations.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="name" value="Nom de la colocation *" />
                    <x-text-input id="name" type="text" name="name" value="{{ old('name') }}" required />
                    @error('name')
                        <x-input-error class="mt-2" :messages="$message" />
                    @enderror
                </div>

                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea name="description" id="description" rows="4"
                        class="w-full rounded-xl border border-slate-300 bg-white/90 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('description') }}</textarea>
                    @error('description')
                        <x-input-error class="mt-2" :messages="$message" />
                    @enderror
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit"
                        class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-5 py-2.5 text-sm font-bold text-white transition hover:from-blue-500 hover:to-blue-400">
                        Creer
                    </button>
                    <a href="{{ route('colocations.index') }}"
                        class="inline-flex items-center rounded-xl border border-slate-300 bg-white/85 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

