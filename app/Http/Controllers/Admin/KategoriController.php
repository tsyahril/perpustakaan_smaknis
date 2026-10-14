<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $kategoris = Kategori::query()
            ->withCount('bukus')
            ->when($search, fn ($q) => $q->where('nama', 'like', "%{$search}%"))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.kategori.index', compact('kategoris', 'search'));
    }

    public function create(): View
    {
        return view('pages.admin.kategori.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kategoris,nama'],
        ]);

        Kategori::create($validated);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori): View
    {
        return view('pages.admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kategoris,nama,' . $kategori->id],
        ]);

        $kategori->update($validated);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->bukus()->exists()) {
            return back()->with('error', "Kategori {$kategori->nama} masih dipakai {$kategori->bukus()->count()} buku, tidak bisa dihapus.");
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
