<section>
    <header>
        <h2 class="text-lg font-extrabold text-slate-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            {{ __("Update your account name and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification" class="ml-1 font-bold underline">
                        {{ __('Re-send verification email') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-semibold text-emerald-700">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 1800)"
                    class="text-sm font-semibold text-slate-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

