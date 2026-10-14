@extends('layouts.admin')

@section('title', 'Kelola Anggota')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Kelola Anggota</h1>
            <p class="text-sm text-gray-500">Kelola data admin, guru, dan siswa.</p>
        </div>
        <a href="{{ route('admin.anggota.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Anggota
        </a>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Ringkasan --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
        <a href="{{ route('admin.anggota.index') }}"
            class="bg-white rounded-lg border p-4 hover:border-blue-400 {{ empty($role) ? 'border-blue-600' : 'border-gray-200' }}">
            <p class="text-xs text-gray-500">Total Anggota</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $counts['total'] ?? 0 }}</p>
        </a>
        <a href="{{ route('admin.anggota.index', ['role' => 'admin']) }}"
            class="bg-white rounded-lg border p-4 hover:border-blue-400 {{ ($role ?? '') === 'admin' ? 'border-blue-600' : 'border-gray-200' }}">
            <p class="text-xs text-gray-500">Admin <span class="text-gray-300">· Semua</span></p>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $counts['admin'] ?? 0 }}</p>
        </a>
        <a href="{{ route('admin.anggota.index', ['role' => 'guru']) }}"
            class="bg-white rounded-lg border p-4 hover:border-blue-400 {{ ($role ?? '') === 'guru' ? 'border-blue-600' : 'border-gray-200' }}">
            <p class="text-xs text-gray-500">Guru</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $counts['guru'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $counts['guru_petugas'] ?? 0 }} pustakawan · {{ $counts['guru_biasa'] ?? 0 }} biasa</p>
        </a>
        <a href="{{ route('admin.anggota.index', ['role' => 'siswa']) }}"
            class="bg-white rounded-lg border p-4 hover:border-blue-400 {{ ($role ?? '') === 'siswa' ? 'border-blue-600' : 'border-gray-200' }}">
            <p class="text-xs text-gray-500">Siswa</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $counts['siswa'] ?? 0 }}</p>
        </a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <form method="GET" action="{{ route('admin.anggota.index') }}" class="flex flex-col md:flex-row gap-2 p-4 border-b border-gray-100">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / gmail..."
                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
            <select name="role" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                <option value="">Semua Role</option>
                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="guru" {{ $role === 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="siswa" {{ $role === 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700">Cari</button>
            @if ($search || $role)
                <a href="{{ route('admin.anggota.index') }}" class="px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-center">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b border-gray-100">
                        <th class="px-4 py-3 text-left font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Nama</th>
                        <th class="px-4 py-3 text-left font-semibold">Gmail</th>
                        <th class="px-4 py-3 text-left font-semibold">Password</th>
                        <th class="px-4 py-3 text-left font-semibold">Role</th>
                        <th class="px-4 py-3 text-left font-semibold">Akses</th>
                        <th class="px-4 py-3 text-center font-semibold">Total Pinjam</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($anggotas as $i => $anggota)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400">{{ $anggotas->firstItem() + $i }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $anggota->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $anggota->email }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-800">••••••••</span>
                                    <button type="button" onclick="openResetModal({{ $anggota->id }})"
                                        class="px-2 py-1 text-xs font-medium text-gray-600 border border-gray-300 rounded hover:bg-gray-100">Reset</button>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ ucfirst($anggota->role) }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                @if ($anggota->role === 'admin')
                                    Semua
                                @elseif ($anggota->role === 'guru')
                                    {{ $anggota->is_petugas ? 'Pustakawan' : 'Guru Biasa' }}
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-300">—</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.anggota.edit', $anggota) }}" class="text-blue-700 hover:underline text-sm">Edit</a>
                                    <span class="text-gray-200">|</span>
                                    <form action="{{ route('admin.anggota.destroy', $anggota) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Hapus anggota {{ $anggota->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div id="resetModal-{{ $anggota->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
                            <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeResetModal({{ $anggota->id }})"></div>
                            <div class="relative bg-white rounded-lg border border-gray-200 w-full max-w-md p-5">
                                <h3 class="font-bold text-gray-800">Reset Password</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $anggota->name }} ({{ $anggota->email }})</p>
                                <form action="{{ route('admin.anggota.reset-password', $anggota) }}" method="POST" class="mt-4 space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Password Baru</label>
                                        <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Konfirmasi Password</label>
                                        <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi password"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                                    </div>
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button type="button" onclick="closeResetModal({{ $anggota->id }})"
                                            class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-400">Belum ada data anggota.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($anggotas->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $anggotas->links() }}</div>
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
