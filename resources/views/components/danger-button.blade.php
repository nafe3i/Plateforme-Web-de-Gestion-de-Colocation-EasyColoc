<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-red-600 to-rose-600 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-sm transition hover:from-red-500 hover:to-rose-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:scale-[0.99]']) }}>
    {{ $slot }}
</button>

