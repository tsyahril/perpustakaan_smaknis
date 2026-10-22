<div x-data class="pointer-events-none fixed inset-x-0 top-4 z-[60] flex flex-col items-center gap-2 px-4 sm:items-end sm:pr-6" role="status" aria-live="polite">
    <template x-for="t in $store.member.toasts" :key="t.id">
        <div x-transition.opacity.duration.200ms
             class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border bg-white px-4 py-3 shadow-kartu-naik"
             :class="t.tipe === 'galat' ? 'border-red-200' : 'border-emerald-200'">
            <span class="mt-0.5 shrink-0" :class="t.tipe === 'galat' ? 'text-red-500' : 'text-emerald-500'">
                <x-member.ikon nama="check-circle" kelas="h-5 w-5" x-show="t.tipe !== 'galat'" />
                <x-member.ikon nama="alert-circle" kelas="h-5 w-5" x-show="t.tipe === 'galat'" x-cloak />
            </span>
            <p class="flex-1 text-sm font-medium text-slate-700" x-text="t.pesan"></p>
            <button type="button" class="text-slate-400 hover:text-slate-600" @click="$store.member.tutup(t.id)" aria-label="Tutup">
                <x-member.ikon nama="x" kelas="h-4 w-4" />
            </button>
        </div>
    </template>
</div>
