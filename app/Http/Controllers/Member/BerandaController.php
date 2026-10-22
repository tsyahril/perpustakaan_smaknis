<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Services\RekomendasiBuku;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function index(Request $request, RekomendasiBuku $rekomendasi): View
    {
        $bukuBaru = Buku::with('kategori:id,nama')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        // Hanya tampil kalau sudah ada buku yang pernah dipinjam.
        $terpopuler = Buku::with('kategori:id,nama')
            ->denganJumlahDipinjam()
            ->having('dipinjam_count', '>', 0)
            ->orderByDesc('dipinjam_count')
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        $kategoriPilihan = Kategori::withCount('bukus')
            ->having('bukus_count', '>', 0)
            ->orderByDesc('bukus_count')
            ->orderBy('nama')
            ->limit(8)
            ->get();

        $rakKategori = $kategoriPilihan->take(3)->map(fn (Kategori $kategori) => [
            'kategori' => $kategori,
            'bukus' => Buku::with('kategori:id,nama')
                ->where('id_kategori', $kategori->id)
                ->orderByDesc('id')
                ->limit(10)
                ->get(),
        ]);

        return view('pages.member.beranda', [
            'bukuBaru' => $bukuBaru,
            'terpopuler' => $terpopuler,
            'kategoriPilihan' => $kategoriPilihan,
            'rakKategori' => $rakKategori,
            'rekomendasi' => $request->user() ? $rekomendasi->untuk($request->user()) : collect(),
            'terakhirDilihat' => $this->terakhirDilihat($request),
            'statistik' => [
                'judul' => Buku::count(),
                'eksemplar' => (int) Buku::sum('stok'),
                'kategori' => Kategori::count(),
            ],
        ]);
    }

    /**
     * Buku yang baru dibuka pengunjung, diambil dari sesi (diisi KatalogController::show).
     */
    protected function terakhirDilihat(Request $request)
    {
        $ids = $request->session()->get(KatalogController::SESI_DILIHAT, []);

        if ($ids === []) {
            return collect();
        }

        return Buku::with('kategori:id,nama')
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Buku $buku) => array_search($buku->id, $ids, true))
            ->values();
    }
}
