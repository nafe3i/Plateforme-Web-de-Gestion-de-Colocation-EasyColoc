<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EasyColoc') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="ec-shell min-h-screen flex items-center justify-center px-4 py-10">
            <div class="w-full max-w-md">
                <a href="/" class="inline-flex items-center gap-3 mb-6">
                    <x-application-logo class="h-10" />
                </a>

                <div class="ec-card p-7 sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>

