<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BukuController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $idKategori = $request->query('id_kategori');

        $bukus = Buku::query()
            ->with('kategori')
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->where('judul', 'like', "%{$search}%")
                ->orWhere('penulis', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%")))
            ->when($idKategori, fn ($q) => $q->where('id_kategori', $idKategori))
            ->orderBy('judul')
            ->paginate(10)
            ->withQueryString();

        $kategoris = Kategori::orderBy('nama')->get();

        return view('pages.admin.buku.index', compact('bukus', 'kategoris', 'search', 'idKategori'));
    }

    public function create(): View
    {
        $kategoris = Kategori::orderBy('nama')->get();

        return view('pages.admin.buku.create', compact('kategoris'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:150'],
            'penerbit' => ['required', 'string', 'max:150'],
            'isbn' => ['required', 'string', 'max:30', 'unique:bukus,isbn'],
            'tahun_terbit' => ['required', 'integer', 'digits:4', 'min:1000', 'max:' . date('Y')],
            'id_kategori' => ['required', 'exists:kategoris,id'],
            'stok' => ['required', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('covers', 'public');
        }

        Buku::create($validated);

        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(Buku $buku): View
    {
        $buku->load('kategori');

        return view('pages.admin.buku.show', compact('buku'));
    }

    public function edit(Buku $buku): View
    {
        $kategoris = Kategori::orderBy('nama')->get();

        return view('pages.admin.buku.edit', compact('buku', 'kategoris'));
    }

    public function update(Request $request, Buku $buku): RedirectResponse
    {
        $validated = $request->validate([
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:150'],
            'penerbit' => ['required', 'string', 'max:150'],
            'isbn' => ['required', 'string', 'max:30', Rule::unique('bukus', 'isbn')->ignore($buku->id)],
            'tahun_terbit' => ['required', 'integer', 'digits:4', 'min:1000', 'max:' . date('Y')],
            'id_kategori' => ['required', 'exists:kategoris,id'],
            'stok' => ['required', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('cover')) {
            if ($buku->cover) {
                Storage::disk('public')->delete($buku->cover);
            }
            $validated['cover'] = $request->file('cover')->store('covers', 'public');
        } else {
            unset($validated['cover']);
        }

        $buku->update($validated);

        return redirect()->route('admin.buku.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        if ($buku->cover) {
            Storage::disk('public')->delete($buku->cover);
        }

        $buku->delete();

        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil dihapus.');
    }
}
