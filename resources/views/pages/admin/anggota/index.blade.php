@extends('layouts.admin')

@section('title', 'Kelola Anggota')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-[#2D251E]">Kelola Anggota</h1>
            <p class="text-sm text-[#8B7E71]">Kelola akun admin, pustakawan, guru, dan siswa. Siswa punya kelas, guru punya bagian/jabatan.</p>
        </div>
        <a href="{{ route('admin.anggota.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-sepia-700 text-white text-sm font-semibold rounded-lg hover:bg-sepia-800">
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
            class="bg-[#FFFFFF] rounded-lg border p-4 hover:border-sepia-400 {{ empty($role) ? 'border-sepia-600' : 'border-[#E8E1D7]' }}">
            <p class="text-xs text-[#8B7E71]">Total Anggota</p>
            <p class="mt-1 text-2xl font-bold text-[#2D251E]">{{ $counts['total'] ?? 0 }}</p>
        </a>
        @foreach ([['admin', 'Admin'], ['pustakawan', 'Pustakawan'], ['guru', 'Guru'], ['siswa', 'Siswa']] as [$key, $label])
            <a href="{{ route('admin.anggota.index', ['role' => $key]) }}"
                class="bg-[#FFFFFF] rounded-lg border p-4 hover:border-sepia-400 {{ ($role ?? '') === $key ? 'border-sepia-600' : 'border-[#E8E1D7]' }}">
                <p class="text-xs text-[#8B7E71]">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold text-[#2D251E]">{{ $counts[$key] ?? 0 }}</p>
            </a>
        @endforeach
    </div>

    <div class="bg-[#FFFFFF] rounded-lg border border-[#E8E1D7] overflow-hidden">
        <form method="GET" action="{{ route('admin.anggota.index') }}" class="flex flex-col md:flex-row gap-2 p-4 border-b border-[#E8E1D7]/80">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / gmail / kelas..."
                class="flex-1 px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg text-[#2D251E] placeholder:text-[#97897B] bg-[#FFFFFF] border-[#B9AE9E] focus:outline-none focus:ring-2 focus:ring-sepia-600/30 focus:border-sepia-400">
            <select name="role" class="px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg text-[#2D251E] bg-[#FFFFFF] border-[#B9AE9E] focus:outline-none focus:ring-2 focus:ring-sepia-600/30">
                <option value="" class="bg-[#FFFFFF] text-[#2D251E]">Semua Role</option>
                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="pustakawan" {{ $role === 'pustakawan' ? 'selected' : '' }}>Pustakawan</option>
                <option value="guru" {{ $role === 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="siswa" {{ $role === 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-[#FFFFFF] text-[#2D251E] rounded-lg hover:bg-[#F0EAE1]">Cari</button>
            @if ($search || $role)
                <a href="{{ route('admin.anggota.index') }}" class="px-4 py-2 text-sm font-semibold bg-[#FFFFFF] text-[#6B5E52] rounded-lg hover:bg-[#F0EAE1] text-center">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-[#F7F4EF] text-[#8B7E71] text-xs border-b border-[#E8E1D7]/80">
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
                <tbody class="divide-y divide-[#E8E1D7]/70">
                    @forelse ($anggotas as $i => $anggota)
                        <tr class="hover:bg-[#F7F4EF]">
                            <td class="px-4 py-3 text-[#8B7E71]">{{ $anggotas->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-[#2D251E]">{{ $anggota->name }}</p>
                            </td>
                            <td class="px-4 py-3 text-[#6B5E52]">{{ $anggota->email }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-[#2D251E]">-terbaca-</span>
                                    <button type="button" onclick="openResetModal({{ $anggota->id }})"
                                        class="px-2 py-1 text-xs font-medium text-[#6B5E52] border border-[#DDD5C9] rounded hover:bg-[#FFFFFF]">Reset</button>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $anggota->role === 'admin' ? 'bg-red-100 text-red-800'
                                        : ($anggota->role === 'pustakawan' ? 'bg-sepia-100 text-sepia-800'
                                        : ($anggota->role === 'guru' ? 'bg-sepia-100 text-sepia-800' : 'bg-green-100 text-green-800')) }}">
                                    {{ $anggota->aksesLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-[#3D342C]">
                                {{ $anggota->kelas ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-[#6B5E52]">
                                @if ($anggota->anggota)
                                    <p class="font-mono text-xs">{{ $anggota->anggota->nomor_anggota }}</p>
                                    <p class="text-xs text-[#8B7E71]">{{ $anggota->anggota->nama }}</p>
                                @else
                                    <span class="text-[#97897B]">belum terhubung</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($anggota->id_anggota)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $anggota->total_pinjam > 0 ? 'bg-sepia-100 text-sepia-800' : 'bg-[#FFFFFF] text-[#8B7E71]' }}">
                                        {{ $anggota->total_pinjam }}
                                    </span>
                                @else
                                    <span class="text-xs text-[#97897B]">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.anggota.edit', $anggota) }}" class="text-sepia-700 hover:underline text-sm">Edit</a>
                                    <span class="text-[#3D342C]">|</span>
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
                            <div class="relative bg-[#FFFFFF] rounded-xl border border-[#E8E1D7] w-full max-w-md p-5 text-[#2D251E]">
                                <h3 class="font-bold text-[#2D251E]">Reset Password</h3>
                                <p class="text-sm text-[#8B7E71] mt-1">{{ $anggota->name }} ({{ $anggota->email }})</p>
                                <form action="{{ route('admin.anggota.reset-password', $anggota) }}" method="POST" class="mt-4 space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-medium text-[#6B5E52] mb-1">Password Baru</label>
                                        <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter"
                                            class="w-full px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg text-[#2D251E] bg-[#FFFFFF] border-[#B9AE9E] focus:outline-none focus:ring-2 focus:ring-sepia-600/30">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-[#6B5E52] mb-1">Konfirmasi Password</label>
                                        <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi password"
                                            class="w-full px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg text-[#2D251E] bg-[#FFFFFF] border-[#B9AE9E] focus:outline-none focus:ring-2 focus:ring-sepia-600/30">
                                    </div>
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button type="button" onclick="closeResetModal({{ $anggota->id }})"
                                            class="px-4 py-2 text-sm text-[#6B5E52] bg-[#FFFFFF] rounded-lg hover:bg-[#F0EAE1]">Batal</button>
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-semibold text-white bg-sepia-700 rounded-lg hover:bg-sepia-800">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-[#8B7E71]">Belum ada data anggota.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($anggotas->hasPages())
            <div class="p-4 border-t border-[#E8E1D7]/80">{{ $anggotas->links() }}</div>
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

