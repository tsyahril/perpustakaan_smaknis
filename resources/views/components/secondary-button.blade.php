<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-[#DDD5C9] rounded-md font-semibold text-xs text-[#4A4038] uppercase tracking-widest shadow-sm hover:bg-[#FAF9F7] focus:outline-none focus:ring-2 focus:ring-sepia-600 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
