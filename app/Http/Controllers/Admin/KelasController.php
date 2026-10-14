<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $daftarKelas = Kelas::query()
            ->withCount('anggotas')
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->where('nama', 'like', "%{$search}%")
                ->orWhere('jurusan', 'like', "%{$search}%")))
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.kelas.index', ['kelas' => $daftarKelas, 'search' => $search]);
    }

    public function create(): View
    {
        return view('pages.admin.kelas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kelas,nama'],
            'tingkat' => ['required', 'string', 'max:10'],
            'jurusan' => ['nullable', 'string', 'max:50'],
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas): View
    {
        return view('pages.admin.kelas.edit', compact('kelas'));
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kelas,nama,' . $kelas->id],
            'tingkat' => ['required', 'string', 'max:10'],
            'jurusan' => ['nullable', 'string', 'max:50'],
        ]);

        $kelas->update($validated);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->anggotas()->exists()) {
            return back()->with('error', "Kelas {$kelas->nama} masih dipakai {$kelas->anggotas()->count()} anggota, tidak bisa dihapus.");
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
