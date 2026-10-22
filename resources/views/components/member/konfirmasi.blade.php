@props(['action', 'judul', 'pesan', 'tombol' => 'Ya, lanjutkan', 'metode' => 'POST'])

{{-- Pembungkus tombol berbahaya: klik pemicu membuka dialog, lalu form dikirim setelah dikonfirmasi. --}}
<div x-data="{ buka: false }" class="inline-block" @keydown.escape.window="buka = false">
    <span @click="buka = true">{{ $slot }}</span>

    <form method="POST" action="{{ $action }}" x-ref="form" class="hidden">
        @csrf
        @if ($metode !== 'POST')
            @method($metode)
        @endif
    </form>

    <template x-teleport="body">
        <div x-show="buka" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div x-show="buka" x-transition.opacity class="absolute inset-0 bg-slate-900/50" @click="buka = false"></div>
            <div x-show="buka" x-transition class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-red-50 text-red-600">
                    <x-member.ikon nama="alert-circle" kelas="h-6 w-6" />
                </span>
                <h3 class="mt-4 text-base font-bold text-slate-900">{{ $judul }}</h3>
                <p class="mt-1.5 text-sm text-slate-500">{{ $pesan }}</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn-soft" @click="buka = false">Batal</button>
                    <button type="button" class="btn-bahaya" @click="$refs.form.submit()">{{ $tombol }}</button>
                </div>
            </div>
        </div>
    </template>
</div>
