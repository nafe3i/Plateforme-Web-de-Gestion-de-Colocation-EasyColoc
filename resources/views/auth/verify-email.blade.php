<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Verification email</h1>
        <p class="mt-1 text-sm text-slate-600">
            Confirmez votre adresse email pour activer votre compte.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="ec-alert ec-alert-success mb-4">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-semibold text-slate-600 transition hover:text-slate-900">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>

