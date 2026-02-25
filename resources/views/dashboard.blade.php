<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Carte Bienvenue --}}
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-black p-6 rounded-2xl shadow-lg">
                <h3 class="text-2xl font-bold">
                    Bienvenue {{ $user->name }} 
                </h3>
                <p class="opacity-90">
                    Heureux de vous revoir sur EasyColoc
                </p>
            </div>

            {{-- Grid principale --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Carte Profil --}}
                <div class="bg-white p-6 rounded-2xl shadow-md">
                    <h4 class="text-lg font-semibold mb-4 border-b pb-2">
                        Informations personnelles
                    </h4>

                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-500 text-sm">Nom</span>
                            <p class="font-semibold">{{ $user->name }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500 text-sm">Email</span>
                            <p class="font-semibold">{{ $user->email }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500 text-sm">Réputation</span>
                            <p class="font-bold 
                                {{ $user->reputation >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $user->reputation }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Carte Roles & Permissions --}}
                <div class="bg-white p-6 rounded-2xl shadow-md">
                    <h4 class="text-lg font-semibold mb-4 border-b pb-2">
                        Rôles & Permissions
                    </h4>

                    {{-- Roles --}}
                    <div class="mb-4">
                        <span class="text-gray-500 text-sm">Rôles</span>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($user->getRoleNames() as $role)
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-semibold">
                                    {{ $role }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Permissions --}}
                    <div>
                        <span class="text-gray-500 text-sm">Permissions</span>
                        <div class="mt-2 grid grid-cols-1 gap-1 max-h-40 overflow-y-auto">
                            @foreach($user->getAllPermissions() as $permission)
                                <span class="text-sm text-gray-700">
                                    ✓ {{ $permission->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            {{-- Actions rapides --}}
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h4 class="text-lg font-semibold mb-4 border-b pb-2">
                    Actions Rapides
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    @can('create_colocation')
                        <a href="#" class="p-4 border rounded-xl hover:shadow-md transition">
                            <p class="font-semibold">Créer une colocation</p>
                            <p class="text-sm text-gray-500">Nouvelle annonce</p>
                        </a>
                    @endcan

                    @can('join_colocation')
                        <a href="#" class="p-4 border rounded-xl hover:shadow-md transition">
                            <p class="font-semibold">Rejoindre une colocation</p>
                            <p class="text-sm text-gray-500">Code invitation</p>
                        </a>
                    @endcan

                    <a href="{{ route('profile.edit') }}" class="p-4 border rounded-xl hover:shadow-md transition">
                        <p class="font-semibold">Modifier profil</p>
                        <p class="text-sm text-gray-500">Paramètres</p>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>