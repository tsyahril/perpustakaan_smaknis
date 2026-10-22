<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kelas;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnggotaController extends Controller
{
    public const BAGIAN_GURU = [
        'Guru Kelas',
        'Guru BK',
        'Staf',
        'Kepala Sekolah',
        'Tata Usaha',
    ];

    public function index(Request $request): View
    {
        $search = $request->query('search');
        $role = $request->query('role');

        $anggotas = User::query()
            ->select('users.*')
            ->with('anggota')
            ->selectSub(
                Transaksi::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('transaksis.anggota_id', 'users.id_anggota')
                    ->where('transaksis.status_persetujuan', '!=', 'ditolak'),
                'total_pinjam'
            )
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->where('name', "like", "%{$search}%")
                ->orWhere('username', "like", "%{$search}%")
                ->orWhere('email', "like", "%{$search}%")
                ->orWhere('kelas', "like", "%{$search}%")))
            ->when($role && in_array($role, User::ROLES), fn ($q) => $q->where('role', $role))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'pustakawan' => User::where('role', 'pustakawan')->count(),
            'guru' => User::where('role', 'guru')->count(),
            'siswa' => User::where('role', 'siswa')->count(),
        ];

        return view('pages.admin.anggota.index', compact('anggotas', 'search', 'role', 'counts'));
    }

    public function create(): View
    {
        return view('pages.admin.anggota.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $user = new User();
        $user->name = $validated['name'];
        $user->username = Str::lower(trim($validated['username']));
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->kelas = $this->kelasUntuk($validated['role'], $validated['kelas'] ?? null);
        $user->id_anggota = $this->cariKartuAnggota($request->input('id_anggota'), $validated['name']);
        $user->save();

        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(User $anggota): View
    {
        return view('pages.admin.anggota.edit', array_merge($this->formData(), ['anggota' => $anggota]));
    }

    public function update(Request $request, User $anggota): RedirectResponse
    {
        $validated = $request->validate($this->rules($anggota));

        $anggota->name = $validated['name'];
        $anggota->username = Str::lower(trim($validated['username']));
        $anggota->email = $validated['email'];
        $anggota->role = $validated['role'];
        $anggota->kelas = $this->kelasUntuk($validated['role'], $validated['kelas'] ?? null);
        $anggota->id_anggota = $this->cariKartuAnggota($request->input('id_anggota'), $validated['name']);

        if (! empty($validated['password'])) {
            $anggota->password = Hash::make($validated['password']);
        }

        $anggota->save();

        return redirect()->route('admin.anggota.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(User $anggota): RedirectResponse
    {
        if ($anggota->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $anggota->delete();

        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil dihapus.');
    }

    public function resetPassword(Request $request, User $anggota): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $anggota->password = Hash::make($validated['password']);
        $anggota->save();

        return back()->with('success', "Password {$anggota->name} berhasil direset.");
    }

    public function toggleAkses(User $anggota): RedirectResponse
    {
        if (!$anggota->isGuru()) {
            return back()->with('error', 'Toggle akses hanya tersedia untuk guru.');
        }

        $anggota->is_petugas = !$anggota->is_petugas;
        $anggota->save();

        $status = $anggota->is_petugas ? 'Pustakawan' : 'Guru Biasa';
        return back()->with('success', "Akses {$anggota->name} diubah menjadi {$status}.");
    }

    protected function rules(?User $anggota = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:255', 'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($anggota?->id),
            ],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($anggota?->id),
            ],
            'password' => [$anggota ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(User::ROLES)],
            'kelas' => ['nullable', 'required_if:role,siswa', 'required_if:role,guru', 'string', 'max:60'],
            'id_anggota' => ['nullable'],
        ];
    }

    protected function formData(): array
    {
        return [
            'daftarKelas' => Kelas::orderBy('nama')->pluck('nama'),
            'bagianGuru' => self::BAGIAN_GURU,
            'daftarAnggota' => Anggota::orderBy('nama')->get(['id', 'nama', 'nomor_anggota']),
        ];
    }

    protected function kelasUntuk(string $role, ?string $kelas): ?string
    {
        return in_array($role, ['siswa', 'guru'], true) ? ($kelas ?: null) : null;
    }

    protected function cariKartuAnggota($idAnggota, string $nama): ?int
    {
        if (filled($idAnggota) && $idAnggota !== 'auto') {
            return (int) $idAnggota;
        }

        $match = Anggota::where('nama', $nama)->orderBy('id')->first();

        return $match?->id;
    }
}

