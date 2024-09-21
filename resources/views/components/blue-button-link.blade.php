<a {{ $attributes->merge(['class' => 'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest blue_bg hover:bg-sky-400 focus:bg-sky-400 active:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
