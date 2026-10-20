@extends('layouts.admin')

@section('title', 'Kelola Anggota')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-100">Kelola Anggota</h1>
            <p class="text-sm text-slate-400">Kelola akun admin, pustakawan, guru, dan siswa. Siswa punya kelas, guru punya bagian/jabatan.</p>
        </div>
        <a href="{{ route('admin.anggota.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Anggota
        </a>
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

    {{-- Ringkasan per role --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3">
        <a href="{{ route('admin.anggota.index') }}"
            class="bg-[#0b1d32] rounded-lg border p-4 hover:border-blue-400 {{ empty($role) ? 'border-blue-600' : 'border-slate-700' }}">
            <p class="text-xs text-slate-400">Total Anggota</p>
            <p class="mt-1 text-2xl font-bold text-slate-100">{{ $counts['total'] ?? 0 }}</p>
        </a>
        @foreach ([['admin', 'Admin'], ['pustakawan', 'Pustakawan'], ['guru', 'Guru'], ['siswa', 'Siswa']] as [$key, $label])
            <a href="{{ route('admin.anggota.index', ['role' => $key]) }}"
                class="bg-[#0b1d32] rounded-lg border p-4 hover:border-blue-400 {{ ($role ?? '') === $key ? 'border-blue-600' : 'border-slate-700' }}">
                <p class="text-xs text-slate-400">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-100">{{ $counts[$key] ?? 0 }}</p>
            </a>
        @endforeach
    </div>

    <div class="bg-[#0b1d32] rounded-lg border border-slate-700 overflow-hidden">
        <form method="GET" action="{{ route('admin.anggota.index') }}" class="flex flex-col md:flex-row gap-2 p-4 border-b border-slate-700/80">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / gmail / kelas..."
                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg text-slate-100 placeholder:text-slate-500 bg-[#071426] border-slate-600 focus:outline-none focus:ring-2 focus:ring-sky-400/30 focus:border-sky-400">
            <select name="role" class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-slate-100 bg-[#071426] border-slate-600 focus:outline-none focus:ring-2 focus:ring-sky-400/30">
                <option value="" class="bg-[#0b1d32] text-slate-100">Semua Role</option>
                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="pustakawan" {{ $role === 'pustakawan' ? 'selected' : '' }}>Pustakawan</option>
                <option value="guru" {{ $role === 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="siswa" {{ $role === 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700">Cari</button>
            @if ($search || $role)
                <a href="{{ route('admin.anggota.index') }}" class="px-4 py-2 text-sm font-semibold bg-slate-800 text-slate-300 rounded-lg hover:bg-slate-700 text-center">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-[#142c47] text-slate-400 text-xs border-b border-slate-700/80">
                        <th class="px-4 py-3 text-left font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Nama</th>
                        <th class="px-4 py-3 text-left font-semibold">Gmail</th>
                        <th class="px-4 py-3 text-left font-semibold">Password</th>
                        <th class="px-4 py-3 text-left font-semibold">Role</th>
                        <th class="px-4 py-3 text-left font-semibold">Kelas / Bagian</th>
                        <th class="px-4 py-3 text-left font-semibold">Kartu Anggota</th>
                        <th class="px-4 py-3 text-center font-semibold">Total Pinjam</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/70">
                    @forelse ($anggotas as $i => $anggota)
                        <tr class="hover:bg-[#142c47]">
                            <td class="px-4 py-3 text-slate-400">{{ $anggotas->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-100">{{ $anggota->name }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-300">{{ $anggota->email }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-100">-terbaca-</span>
                                    <button type="button" onclick="openResetModal({{ $anggota->id }})"
                                        class="px-2 py-1 text-xs font-medium text-slate-300 border border-gray-300 rounded hover:bg-slate-800">Reset</button>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $anggota->role === 'admin' ? 'bg-red-100 text-red-800'
                                        : ($anggota->role === 'pustakawan' ? 'bg-purple-100 text-purple-800'
                                        : ($anggota->role === 'guru' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                                    {{ $anggota->aksesLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-200">
                                {{ $anggota->kelas ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-slate-300">
                                @if ($anggota->anggota)
                                    <p class="font-mono text-xs">{{ $anggota->anggota->nomor_anggota }}</p>
                                    <p class="text-xs text-slate-400">{{ $anggota->anggota->nama }}</p>
                                @else
                                    <span class="text-slate-500">belum terhubung</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($anggota->id_anggota)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $anggota->total_pinjam > 0 ? 'bg-blue-100 text-blue-800' : 'bg-slate-800 text-slate-400' }}">
                                        {{ $anggota->total_pinjam }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.anggota.edit', $anggota) }}" class="text-blue-700 hover:underline text-sm">Edit</a>
                                    <span class="text-gray-200">|</span>
                                    <form action="{{ route('admin.anggota.destroy', $anggota) }}" method="POST" class="inline"
                                        data-confirm="Hapus anggota {{ $anggota->name }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div id="resetModal-{{ $anggota->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
                            <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeResetModal({{ $anggota->id }})"></div>
                            <div class="relative bg-[#0b1d32] rounded-xl border border-slate-700 w-full max-w-md p-5 text-slate-100">
                                <h3 class="font-bold text-slate-100">Reset Password</h3>
                                <p class="text-sm text-slate-400 mt-1">{{ $anggota->name }} ({{ $anggota->email }})</p>
                                <form action="{{ route('admin.anggota.reset-password', $anggota) }}" method="POST" class="mt-4 space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-medium text-slate-300 mb-1">Password Baru</label>
                                        <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg text-slate-100 bg-[#071426] border-slate-600 focus:outline-none focus:ring-2 focus:ring-sky-400/30">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-300 mb-1">Konfirmasi Password</label>
                                        <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi password"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg text-slate-100 bg-[#071426] border-slate-600 focus:outline-none focus:ring-2 focus:ring-sky-400/30">
                                    </div>
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button type="button" onclick="closeResetModal({{ $anggota->id }})"
                                            class="px-4 py-2 text-sm text-slate-300 bg-slate-800 rounded-lg hover:bg-slate-700">Batal</button>
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-slate-400">Belum ada data anggota.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($anggotas->hasPages())
            <div class="p-4 border-t border-slate-700/80">{{ $anggotas->links() }}</div>
        @endif
    </div>
</div>

<script>
    function openResetModal(id) { document.getElementById('resetModal-' + id).classList.remove('hidden'); }
    function closeResetModal(id) { document.getElementById('resetModal-' + id).classList.add('hidden'); }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') document.querySelectorAll('[id^="resetModal-"]').forEach(function (el) { el.classList.add('hidden'); });
    });
</script>
@endsection

