<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-sm transition hover:from-blue-500 hover:to-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:scale-[0.99]']) }}>
    {{ $slot }}
</button>

