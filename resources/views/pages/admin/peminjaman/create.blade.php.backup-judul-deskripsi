@extends('layouts.admin')

@section('title', 'Tambah Peminjaman')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Pengajuan Peminjaman</h1>
        <p class="text-sm text-gray-500">Pilih peminjam, lalu pilih buku.</p>
    </div>

    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('error'))
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <form id="formPinjam" action="{{ route('admin.peminjaman.store') }}" method="POST" class="bg-white rounded-lg border border-gray-200 p-6 space-y-5">
        @csrf

        {{-- 1. Peminjam: ketik nama, role & kelas terisi otomatis --}}
        <div class="relative" id="anggotaPicker">
            <label class="block text-sm font-medium text-gray-700 mb-1">Peminjam <span class="text-red-600">*</span></label>
            <input type="text" id="anggotaKeyword" autocomplete="off" placeholder="Ketik nama anggota..."
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            <input type="hidden" name="anggota_id" id="anggotaId" value="{{ old('anggota_id') }}">
            <div id="anggotaDropdown" class="hidden absolute z-10 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg"></div>
            <div id="anggotaAuto" class="hidden mt-2 flex flex-wrap items-center gap-2 text-xs">
                <span id="roleBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-blue-100 text-blue-800"></span>
                <span id="kelasInfo" class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-gray-100 text-gray-600"></span>
            </div>
            @error('anggota_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- 2. Buku --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Buku (maks 5) <span class="text-red-600">*</span></label>
                <button type="button" id="tambahBuku" class="px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100">+ Tambah</button>
            </div>
            <div id="daftarBuku" class="space-y-2"></div>
            @error('buku_ids')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pinjam <span class="text-red-600">*</span></label>
                <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali Rencana (opsional)</label>
                <input type="date" name="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana') }}" id="tanggalRencanaUmum"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>
        </div>

        <label class="flex items-start gap-2 text-sm text-gray-600 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <input type="checkbox" name="ajukan_persetujuan" value="1" checked class="mt-1">
            <span>Buat sebagai <strong>pengajuan</strong> yang harus disetujui pustakawan dulu (stok langsung ditahan). Lepas centang untuk langsung disetujui.</span>
        </label>

        <div class="flex gap-3 pt-2 border-t border-gray-100">
            <button type="submit" class="px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">
                Simpan Pengajuan
            </button>
            <a href="{{ route('admin.peminjaman.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    const ANGGOTAS = {!! $anggotasJson !!};
    const BUKUS = {!! $bukusJson !!};

    const anggotaKeyword = document.getElementById('anggotaKeyword');
    const anggotaId = document.getElementById('anggotaId');
    const anggotaDropdown = document.getElementById('anggotaDropdown');
    const anggotaAuto = document.getElementById('anggotaAuto');
    const roleBadge = document.getElementById('roleBadge');
    const kelasInfo = document.getElementById('kelasInfo');
    const daftarBuku = document.getElementById('daftarBuku');
    const rencanaUmum = document.getElementById('tanggalRencanaUmum');

    function tampilOtomatis(a) {
        if (!a) {
            anggotaAuto.classList.add('hidden');
            return;
        }
        roleBadge.textContent = a.role === 'siswa' ? 'Siswa' : 'Guru';
        if (a.role === 'siswa') {
            kelasInfo.textContent = a.kelas_nama || '-';
            kelasInfo.classList.remove('hidden');
        } else {
            kelasInfo.classList.add('hidden');
        }
        anggotaAuto.classList.remove('hidden');
    }

    function renderAnggotaDropdown() {
        const q = anggotaKeyword.value.toLowerCase().trim();
        const list = ANGGOTAS.filter(a =>
            !q || (a.nama + ' ' + a.nomor).toLowerCase().includes(q)
        ).slice(0, 20);
        anggotaDropdown.innerHTML = '';
        if (!list.length) {
            anggotaDropdown.innerHTML = '<p class="px-3 py-2 text-xs text-gray-400">Tidak ada anggota yang cocok.</p>';
        } else {
            list.forEach(a => {
                const b = document.createElement('button');
                b.type = 'button';
                b.className = 'flex w-full items-center justify-between gap-2 px-3 py-2 text-sm text-left text-gray-700 hover:bg-blue-50';
                b.innerHTML = `<span class="font-medium">${a.nama}</span><span class="text-xs text-gray-400">${a.role === 'siswa' ? 'Siswa' : 'Guru'}</span>`;
                b.addEventListener('click', () => {
                    anggotaId.value = a.id;
                    anggotaKeyword.value = a.nama;
                    tampilOtomatis(a);
                    anggotaDropdown.classList.add('hidden');
                });
                anggotaDropdown.appendChild(b);
            });
        }
        anggotaDropdown.classList.remove('hidden');
    }

    // Kembalikan pilihan lama bila validasi gagal.
    (function pulihkanAnggota() {
        const lama = anggotaId.value;
        if (!lama) return;
        const a = ANGGOTAS.find(x => String(x.id) === String(lama));
        if (a) {
            anggotaKeyword.value = a.nama;
            tampilOtomatis(a);
        }
    })();

    anggotaKeyword.addEventListener('input', () => { anggotaId.value = ''; tampilOtomatis(null); renderAnggotaDropdown(); });
    anggotaKeyword.addEventListener('focus', renderAnggotaDropdown);
    document.addEventListener('click', e => {
        if (!document.getElementById('anggotaPicker').contains(e.target)) anggotaDropdown.classList.add('hidden');
    });

    function opsiBuku(selectedId = '') {
        const wrap = document.createElement('div');
        wrap.className = 'flex gap-2 items-start buku-baris';
        wrap.innerHTML = `
            <div class="relative flex-1">
                <input type="text" autocomplete="off" placeholder="Ketik judul buku..."
                    class="bukuKeyword w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                <input type="hidden" name="buku_ids[]" class="bukuId" value="${selectedId}">
                <div class="bukuDropdown hidden absolute z-10 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg"></div>
                <div class="detailBox hidden mt-1.5 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600">
                    <div class="flex items-center gap-2.5">
                        <span class="detailCover shrink-0"></span>
                        <span class="min-w-0 flex-1 space-y-0.5">
                            <span class="detailPenulis block font-medium text-gray-800"></span>
                            <span class="flex items-center justify-between gap-2"><span class="detailIsbn text-gray-400"></span><span class="detailStok"></span></span>
                        </span>
                    </div>
                </div>
                <p class="stokInfo mt-1 text-xs text-gray-500"></p>
                <label class="mt-2 block text-[11px] text-gray-500">Rencana kembali buku ini (kosong = tidak ada rencana)</label>
                <input type="date" class="rencanaPerBuku mt-1 w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600" name="">
            </div>
            <button type="button" class="hapusBuku px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100">Hapus</button>
        `;
        daftarBuku.appendChild(wrap);

        const keyword = wrap.querySelector('.bukuKeyword');
        const hiddenId = wrap.querySelector('.bukuId');
        const dropdown = wrap.querySelector('.bukuDropdown');
        const stokInfo = wrap.querySelector('.stokInfo');
        const detailBox = wrap.querySelector('.detailBox');
        const detailCover = wrap.querySelector('.detailCover');
        const detailPenulis = wrap.querySelector('.detailPenulis');
        const detailIsbn = wrap.querySelector('.detailIsbn');
        const detailStok = wrap.querySelector('.detailStok');
        const rencanaInput = wrap.querySelector('.rencanaPerBuku');
        rencanaInput.value = rencanaUmum ? rencanaUmum.value : '';

        function esc(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        }
        function coverHtml(b, cls) {
            if (b.cover) {
                return `<img src="${esc(b.cover)}" alt="" loading="lazy" class="${cls} rounded border border-gray-200 object-cover" onerror="this.remove()">`;
            }
            const inisial = esc((b.judul || '?').trim().charAt(0).toUpperCase() || '?');
            return `<span class="${cls} flex items-center justify-center rounded border border-gray-200 bg-blue-100 text-blue-700 font-bold">${inisial}</span>`;
        }

        function bukuTerpilih() {
            return BUKUS.find(b => String(b.id) === String(hiddenId.value));
        }
        function detailBuku(b) {
            const bagian = [b.penulis, b.penerbit ? b.penerbit + (b.tahun ? ' (' + b.tahun + ')' : '') : (b.tahun || '')].filter(Boolean).join(' • ');
            return { bagian, isbn: b.isbn || '-' };
        }
        function updateStok() {
            const b = bukuTerpilih();
            if (!b) { detailBox.classList.add('hidden'); stokInfo.textContent = ''; return; }
            const d = detailBuku(b);
            detailCover.innerHTML = coverHtml(b, 'w-9 h-12');
            detailPenulis.textContent = d.bagian || '-';
            detailIsbn.textContent = 'ISBN: ' + d.isbn;
            detailStok.textContent = 'Stok: ' + b.stok;
            detailStok.className = 'inline-flex items-center px-2 py-0.5 rounded-full font-semibold ' + (b.stok > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600');
            detailBox.classList.remove('hidden');
            stokInfo.textContent = '';
        }
        function normalisasi(s) {
            return (s || '').toLowerCase().replace(/\s+/g, ' ').trim();
        }
        function haystack(b) {
            return normalisasi([b.judul, b.penulis, b.penerbit, b.isbn, b.tahun, b.kategori].filter(Boolean).join(' '));
        }
        function skor(b, tokens) {
            const judul = normalisasi(b.judul);
            let nilai = 0;
            tokens.forEach(t => {
                if (judul.startsWith(t)) nilai += 3;
                else if (judul.includes(t)) nilai += 2;
                else if (normalisasi(b.penulis).includes(t)) nilai += 1;
                else nilai += 0.5;
            });
            return nilai;
        }
        function render() {
            const q = normalisasi(keyword.value);
            const tokens = q.split(' ').filter(Boolean);
            let list = BUKUS.filter(b => {
                if (!tokens.length) return true;
                const h = haystack(b);
                return tokens.every(t => h.includes(t));
            });
            list = list.map(b => ({ b, s: tokens.length ? skor(b, tokens) : 0 }))
                .sort((x, y) => (y.s - x.s) || (y.b.stok - x.b.stok) || x.b.judul.localeCompare(y.b.judul))
                .slice(0, 20)
                .map(x => x.b);
            dropdown.innerHTML = '';
            if (!list.length) {
                dropdown.innerHTML = '<p class="px-3 py-2 text-xs text-gray-400">Tidak ada buku yang cocok.</p>';
            } else {
                list.forEach(b => {
                    const d = detailBuku(b);
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'block w-full px-3 py-2 text-left hover:bg-blue-50';
                    btn.innerHTML = `<span class="flex items-center gap-2.5"><span class="shrink-0">${coverHtml(b, 'w-8 h-11 text-sm')}</span><span class="min-w-0 flex-1"><span class="flex items-center justify-between gap-2"><span class="truncate font-medium text-sm text-gray-800">${esc(b.judul)}</span><span class="shrink-0 text-xs px-2 py-0.5 rounded-full font-semibold ${b.stok > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}">Stok: ${b.stok}</span></span><span class="block text-xs text-gray-400 truncate">${esc(d.bagian || '')}</span></span></span>`;
                    btn.addEventListener('click', () => {
                        const sudahDipilih = Array.from(document.querySelectorAll('.bukuId')).some(el => el !== hiddenId && el.value === String(b.id));
                        if (sudahDipilih) {
                            alert('Buku ini sudah ada di daftar.');
                            return;
                        }
                        hiddenId.value = b.id;
                        keyword.value = b.judul;
                        rencanaInput.name = 'tanggal_kembali_rencana_per_buku[' + b.id + ']';
                        dropdown.classList.add('hidden');
                        updateStok();
                    });
                    dropdown.appendChild(btn);
                });
            }
            dropdown.classList.remove('hidden');
        }

        keyword.addEventListener('input', () => { hiddenId.value = ''; rencanaInput.name = ''; detailBox.classList.add('hidden'); stokInfo.textContent = ''; render(); });
        keyword.addEventListener('focus', render);
        wrap.querySelector('.hapusBuku').addEventListener('click', () => {
            if (daftarBuku.children.length > 1) wrap.remove();
        });
        document.addEventListener('click', e => {
            if (!wrap.contains(e.target)) dropdown.classList.add('hidden');
        });
        updateStok();
    }

    document.getElementById('tambahBuku').addEventListener('click', () => {
        if (daftarBuku.children.length >= 5) {
            alert('Maksimal 5 buku per pengajuan.');
            return;
        }
        opsiBuku();
    });

    opsiBuku();

    // Ubah tanggal rencana umum => terisi ke semua buku (tiap buku bisa dikosongkan).
    if (rencanaUmum) {
        rencanaUmum.addEventListener('change', () => {
            document.querySelectorAll('.rencanaPerBuku').forEach(input => {
                input.value = rencanaUmum.value;
            });
        });
    }

    document.getElementById('formPinjam').addEventListener('submit', e => {
        if (!anggotaId.value) {
            e.preventDefault();
            alert('Pilih peminjam dari daftar.');
            anggotaKeyword.focus();
            return;
        }
        const ids = Array.from(document.querySelectorAll('.bukuId')).map(el => el.value).filter(Boolean);
        if (!ids.length) {
            e.preventDefault();
            alert('Pilih minimal 1 buku.');
            return;
        }
        const kosong = BUKUS.filter(b => ids.includes(String(b.id)) && b.stok <= 0);
        if (kosong.length) {
            e.preventDefault();
            alert(`Stok habis: ${kosong.map(b => b.judul).join(', ')}`);
        }
    });
</script>
@endsection
