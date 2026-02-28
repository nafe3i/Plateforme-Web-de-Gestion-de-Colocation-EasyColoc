@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge(['class' => 'w-full rounded-xl border border-slate-300 bg-white/90 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200']) }}
>

