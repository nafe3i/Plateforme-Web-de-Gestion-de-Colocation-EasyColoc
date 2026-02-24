<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}

                    <p>Nom : {{ auth()->user()->name }}</p>

                    <p>Rôles :</p>
                    <ul>
                        @foreach(auth()->user()->getRoleNames() as $role)
                            <li>{{ $role }}</li>
                        @endforeach
                    </ul>

                    <p>Permissions :</p>
                    <ul>
                        @foreach(auth()->user()->getAllPermissions() as $permission)
                            <li>{{ $permission->name }}</li>
                        @endforeach
                    </ul>
                    @if(Auth::user()->hasRole('adminGlobal'))
                        <p> Total des utilisateurs {{$stats['total_users'] }}</p>
                        <p> nbr utilisateurs banner{{$stats['banned_users'] }}</p>
                        <p> nbrutilisateurs active {{$stats['active_users'] }}</p>
                        <h2>tous les utilisateurs</h2>
                        @foreach ($users as $us)
                            <p>nom: {{  $us->name}}</p>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>