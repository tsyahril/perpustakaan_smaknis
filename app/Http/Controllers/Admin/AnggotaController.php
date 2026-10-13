<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnggotaController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $role = $request->query('role');

        $anggotas = User::query()
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->when($role && in_array($role, ['admin', 'guru', 'siswa']), fn ($q) => $q->where('role', $role))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'guru' => User::where('role', 'guru')->count(),
            'guru_petugas' => User::where('role', 'guru')->where('is_petugas', true)->count(),
            'guru_biasa' => User::where('role', 'guru')->where(fn ($q) => $q->where('is_petugas', false)->orWhereNull('is_petugas'))->count(),
            'siswa' => User::where('role', 'siswa')->count(),
        ];

        return view('pages.admin.anggota.index', compact('anggotas', 'search', 'role', 'counts'));
    }

    public function create(): View
    {
        return view('pages.admin.anggota.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa'])],
            'is_petugas' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            // Hanya role guru yang memakai flag petugas, role lain selalu false.
            'is_petugas' => $validated['role'] === 'guru' ? (bool) ($validated['is_petugas'] ?? false) : false,
        ]);

        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(User $anggota): View
    {
        return view('pages.admin.anggota.edit', compact('anggota'));
    }

    public function update(Request $request, User $anggota): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($anggota->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa'])],
            'is_petugas' => ['nullable', 'boolean'],
        ]);

        $anggota->name = $validated['name'];
        $anggota->email = $validated['email'];
        $anggota->role = $validated['role'];
        $anggota->is_petugas = $validated['role'] === 'guru' ? (bool) ($validated['is_petugas'] ?? false) : false;

        if (! empty($validated['password'])) {
            $anggota->password = Hash::make($validated['password']);
        }

        $anggota->save();

        return redirect()->route('admin.anggota.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(User $anggota): RedirectResponse
    {
        // Cegah admin menghapus akunnya sendiri.
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
}
