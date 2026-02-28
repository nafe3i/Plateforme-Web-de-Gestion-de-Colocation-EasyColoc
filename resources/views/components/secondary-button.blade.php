<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white/85 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>

