@props(['active'])

@php
    $classes = ($active ?? false)
        ? 'block w-full rounded-lg bg-blue-600 px-4 py-2.5 text-start text-sm font-semibold text-white'
        : 'block w-full rounded-lg px-4 py-2.5 text-start text-sm font-semibold text-slate-700 hover:bg-slate-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

