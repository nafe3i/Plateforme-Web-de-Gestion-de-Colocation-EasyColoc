@php
    $user = Auth::user();
    $activeColocation = $user?->activeColocation();
@endphp

<aside>
    <div class="ec-card mb-4 p-4 lg:hidden">
        <nav class="flex gap-2 overflow-x-auto pb-1">
            <a href="{{ route('dashboard') }}"
                class="shrink-0 rounded-full px-3 py-2 text-xs font-bold {{ request()->routeIs('dashboard') || request()->routeIs('user.dashboard') || request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                Vue d ensemble
            </a>
            @if($activeColocation)
                <a href="{{ route('expenses.index', $activeColocation) }}"
                    class="shrink-0 rounded-full px-3 py-2 text-xs font-bold {{ request()->routeIs('expenses.*') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                    Depenses
                </a>
                <a href="{{ route('colocations.show', $activeColocation) }}"
                    class="shrink-0 rounded-full px-3 py-2 text-xs font-bold {{ request()->routeIs('colocations.show') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                    Membres
                </a>
            @endif
            @if($user && $user->hasRole('adminGlobal'))
                <a href="{{ route('admin.dashboard') }}"
                    class="shrink-0 rounded-full px-3 py-2 text-xs font-bold {{ request()->routeIs('admin.*') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                    Admin
                </a>
            @endif
        </nav>
    </div>

    <div class="ec-sidebar hidden lg:block">
        <p class="px-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Navigation</p>
        <nav class="mt-3 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="ec-sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('user.dashboard') || request()->routeIs('admin.dashboard') ? 'ec-sidebar-link-active' : '' }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12L12 3l9 9" />
                    <path d="M5 10v10h14V10" />
                </svg>
                Vue d ensemble
            </a>

            @if($activeColocation)
                <a href="{{ route('expenses.index', $activeColocation) }}"
                    class="ec-sidebar-link {{ request()->routeIs('expenses.*') ? 'ec-sidebar-link-active' : '' }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16" />
                        <path d="M4 12h16" />
                        <path d="M4 18h10" />
                    </svg>
                    Depenses
                </a>

                <a href="{{ route('colocations.show', $activeColocation) }}"
                    class="ec-sidebar-link {{ request()->routeIs('colocations.show') ? 'ec-sidebar-link-active' : '' }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="8.5" cy="7" r="4" />
                        <path d="M20 8v6" />
                        <path d="M17 11h6" />
                    </svg>
                    Membres
                </a>
            @endif

            <a href="{{ $activeColocation ? route('expenses.index', $activeColocation) : route('colocations.index') }}"
                class="ec-sidebar-link {{ request()->routeIs('expenses.*') || request()->routeIs('colocations.index') ? 'ec-sidebar-link-active' : '' }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19h16" />
                    <path d="M7 15V9" />
                    <path d="M12 15V5" />
                    <path d="M17 15v-3" />
                </svg>
                Statistiques
            </a>

            @if($user && $user->hasRole('adminGlobal'))
                <a href="{{ route('admin.dashboard') }}"
                    class="ec-sidebar-link {{ request()->routeIs('admin.*') ? 'ec-sidebar-link-active' : '' }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 3l8 4v6c0 5-3.4 7.8-8 8-4.6-.2-8-3-8-8V7l8-4z" />
                        <path d="M9 12l2 2 4-4" />
                    </svg>
                    Admin
                </a>
            @endif
        </nav>
    </div>
</aside>

