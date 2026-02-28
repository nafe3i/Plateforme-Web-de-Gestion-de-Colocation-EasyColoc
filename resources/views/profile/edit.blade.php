<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Profile
        </h2>
    </x-slot>

    <div class="ec-page space-y-6">
        <div class="ec-card p-5 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="ec-card p-5 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="ec-card p-5 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>

