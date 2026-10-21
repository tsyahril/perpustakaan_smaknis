<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-sepia-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sepia-800 focus:bg-sepia-800 active:bg-sepia-900 focus:outline-none focus:ring-2 focus:ring-sepia-600 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
