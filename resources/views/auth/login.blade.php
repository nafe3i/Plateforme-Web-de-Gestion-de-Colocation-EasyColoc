<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Connexion</h1>
        <p class="mt-1 text-sm text-slate-600">Accedez a votre espace EasyColoc.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-700 shadow-sm focus:ring-blue-500" name="remember">
            <span>{{ __('Remember me') }}</span>
        </label>

        <div class="flex items-center justify-between pt-2">
            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-blue-700 hover:text-blue-600" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

