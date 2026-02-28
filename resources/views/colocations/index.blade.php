<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Colocations</h2>
    </x-slot>

    <div class="ec-page">
        <div class="ec-card p-8">
            <h3 class="text-2xl font-extrabold text-slate-900">Aucune colocation active</h3>
            <p class="mt-2 text-sm text-slate-600">Demarrez une nouvelle colocation ou rejoignez-en une via invitation.</p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                @can('create_colocation')
                    <a href="{{ route('colocations.create') }}"
                        class="group rounded-2xl border border-blue-200 bg-blue-50 p-6 transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="mb-4 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white">
                            +
                        </div>
                        <h4 class="text-lg font-extrabold text-blue-800">Creer une colocation</h4>
                        <p class="mt-2 text-sm text-blue-700">Definir votre espace, inviter vos membres et suivre les depenses.</p>
                    </a>
                @endcan

                @can('join_colocation')
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">
                        <div class="mb-4 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-amber-600 text-white">
                            @
                        </div>
                        <h4 class="text-lg font-extrabold text-amber-800">Rejoindre une colocation</h4>
                        <p class="mt-2 text-sm text-amber-700">
                            Utilisez le lien d invitation recu par email depuis un owner.
                        </p>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>

