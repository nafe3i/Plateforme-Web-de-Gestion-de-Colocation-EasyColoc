@php
    $user = Auth::user();
    $activeColocation = $user?->activeColocation();
    $canManageCategories = $user && ($user->hasRole('adminGlobal') || ($activeColocation && $activeColocation->isOwner($user)));
@endphp

<nav x-data="{ open: false }" class="relative z-20 border-b border-slate-200/80 bg-white/85 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="shrink-0">
                    <x-application-logo class="h-9" />
                </a>

                <div class="hidden sm:flex items-center gap-2">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('user.dashboard') || request()->routeIs('admin.dashboard')">
                        Dashboard
                    </x-nav-link>

                    @if($activeColocation)
                        <x-nav-link :href="route('colocations.show', $activeColocation)" :active="request()->routeIs('colocations.show')">
                            Ma Colocation
                        </x-nav-link>
                        <x-nav-link :href="route('expenses.index', $activeColocation)" :active="request()->routeIs('expenses.*')">
                            Depenses
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('colocations.index')" :active="request()->routeIs('colocations.index') || request()->routeIs('colocations.create')">
                            Colocations
                        </x-nav-link>
                    @endif

                    @if($canManageCategories)
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                            Categories
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-xs font-extrabold text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                            <span>{{ $user->name }}</span>
                            <svg class="h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-200 bg-white/90 sm:hidden">
        <div class="space-y-1 px-4 py-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('user.dashboard') || request()->routeIs('admin.dashboard')">
                Dashboard
            </x-responsive-nav-link>

            @if($activeColocation)
                <x-responsive-nav-link :href="route('colocations.show', $activeColocation)" :active="request()->routeIs('colocations.show')">
                    Ma Colocation
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('expenses.index', $activeColocation)" :active="request()->routeIs('expenses.*')">
                    Depenses
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('colocations.index')" :active="request()->routeIs('colocations.index') || request()->routeIs('colocations.create')">
                    Colocations
                </x-responsive-nav-link>
            @endif

            @if($canManageCategories)
                <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                    Categories
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="border-t border-slate-200 px-4 py-4">
            <div class="font-semibold text-slate-800">{{ $user->name }}</div>
            <div class="text-sm text-slate-500">{{ $user->email }}</div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profile
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

