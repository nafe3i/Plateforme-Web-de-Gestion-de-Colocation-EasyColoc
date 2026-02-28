<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Mot de passe oublie</h1>
        <p class="mt-1 text-sm text-slate-600">
            Entrez votre email pour recevoir un lien de reinitialisation.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

