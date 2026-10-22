<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Services\PengajuanPeminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KatalogController extends Controller
{
    public const SESI_DILIHAT = 'terakhir_dilihat';

    public const PER_HALAMAN = 20;

    public const URUTAN = [
        'relevan' => 'Paling sesuai',
        'terbaru' => 'Terbaru ditambahkan',
        'populer' => 'Paling sering dipinjam',
        'judul_az' => 'Judul A-Z',
        'judul_za' => 'Judul Z-A',
        'tahun_terbaru' => 'Tahun terbit terbaru',
        'tahun_terlama' => 'Tahun terbit terlama',
        'stok' => 'Stok terbanyak',
    ];

    public function index(Request $request): View
    {
        $filter = $this->filter($request);

        $query = Buku::query()
            ->with('kategori:id,nama')
            ->cari($filter['q'])
            ->when($filter['kategori'], fn ($q, $id) => $q->where('id_kategori', $id))
            ->when($filter['penerbit'], fn ($q, $daftar) => $q->whereIn('penerbit', $daftar))
            ->when($filter['penulis'], fn ($q, $penulis) => $q->where('penulis', $penulis))
            ->when($filter['tahun_dari'], fn ($q, $tahun) => $q->where('tahun_terbit', '>=', $tahun))
            ->when($filter['tahun_sampai'], fn ($q, $tahun) => $q->where('tahun_terbit', '<=', $tahun))
            ->when($filter['tersedia'], fn ($q) => $q->tersedia());

        if ($filter['urut'] === 'populer') {
            $query->denganJumlahDipinjam();
        }

        $bukus = $query
            ->urutkan($filter['urut'], $filter['q'])
            ->paginate(self::PER_HALAMAN)
            ->withQueryString();

        $kategoris = Kategori::withCount('bukus')->orderBy('nama')->get();

        return view('pages.member.katalog.index', [
            'bukus' => $bukus,
            'filter' => $filter,
            'kategoris' => $kategoris,
            'kategoriAktif' => $filter['kategori'] ? $kategoris->firstWhere('id', $filter['kategori']) : null,
            'kategoriTeratas' => $kategoris->sortByDesc('bukus_count')->take(8)->values(),
            'penerbits' => $this->daftarPenerbit(),
            'chips' => $this->chipFilter($request, $filter, $kategoris),
            'urutan' => Arr::except(self::URUTAN, $filter['q'] === '' ? ['relevan'] : []),
        ]);
    }

    public function show(Request $request, Buku $buku): View
    {
        $buku->load('kategori');

        $serupa = fn () => Buku::with('kategori:id,nama')->whereKeyNot($buku->id)->orderByDesc('id')->limit(10);

        $anggota = $request->user()?->anggota;

        $pinjamanAktif = $anggota
            ? Transaksi::where('anggota_id', $anggota->id)
                ->where('buku_id', $buku->id)
                ->where('status', 'pinjam')
                ->where('status_persetujuan', '!=', 'ditolak')
                ->latest('id')
                ->first()
            : null;

        $this->catatDilihat($request, $buku);

        return view('pages.member.katalog.show', [
            'buku' => $buku,
            'terkait' => $serupa()->where('id_kategori', $buku->id_kategori)->get(),
            'karyaPenulis' => $serupa()->where('penulis', $buku->penulis)->get(),
            'jumlahDipinjam' => $buku->transaksis()->where('status_persetujuan', '!=', 'ditolak')->count(),
            'pinjamanAktif' => $pinjamanAktif,
            'maksBuku' => PengajuanPeminjaman::MAKS_BUKU,
        ]);
    }

    /**
     * Saran pencarian untuk kotak cari di header.
     */
    public function saran(Request $request): JsonResponse
    {
        $kata = is_string($request->query('q')) ? trim($request->query('q')) : '';

        if (mb_strlen($kata) < 2) {
            return response()->json(['buku' => [], 'kategori' => []]);
        }

        $buku = Buku::with('kategori:id,nama')
            ->cari($kata)
            ->urutkan('relevan', $kata)
            ->limit(6)
            ->get()
            ->map(fn (Buku $b) => [
                'judul' => $b->judul,
                'penulis' => $b->penulis,
                'cover' => $b->coverUrl(),
                'tersedia' => $b->stok > 0,
                'url' => route('katalog.show', $b),
            ]);

        $pola = '%'.addcslashes($kata, '%_\\').'%';

        $kategori = Kategori::where('nama', 'like', $pola)
            ->orderBy('nama')
            ->limit(3)
            ->get()
            ->map(fn (Kategori $k) => [
                'nama' => $k->nama,
                'url' => route('katalog.index', ['kategori' => $k->id]),
            ]);

        return response()->json(['buku' => $buku, 'kategori' => $kategori]);
    }

    /**
     * Normalkan parameter query supaya aman dipakai langsung di query builder.
     *
     * @return array{q: string, kategori: ?int, penerbit: array<int, string>, penulis: ?string, tahun_dari: ?int, tahun_sampai: ?int, tersedia: bool, urut: string}
     */
    protected function filter(Request $request): array
    {
        $kata = is_string($request->query('q')) ? Str::limit(trim($request->query('q')), 100, '') : '';
        $urut = is_string($request->query('urut')) ? $request->query('urut') : '';

        if (! array_key_exists($urut, self::URUTAN) || ($urut === 'relevan' && $kata === '')) {
            $urut = $kata !== '' ? 'relevan' : 'terbaru';
        }

        $tahun = fn (string $kunci) => ($t = (int) $request->query($kunci)) >= 1000 && $t <= 9999 ? $t : null;

        $penulis = $request->query('penulis');

        return [
            'q' => $kata,
            'kategori' => $request->integer('kategori') ?: null,
            'penerbit' => collect(Arr::wrap($request->query('penerbit')))
                ->filter(fn ($p) => is_string($p) && $p !== '')
                ->unique()
                ->take(10)
                ->values()
                ->all(),
            'penulis' => is_string($penulis) && $penulis !== '' ? Str::limit($penulis, 150, '') : null,
            'tahun_dari' => $tahun('tahun_dari'),
            'tahun_sampai' => $tahun('tahun_sampai'),
            'tersedia' => $request->boolean('tersedia'),
            'urut' => $urut,
        ];
    }

    /**
     * Penerbit yang punya buku, dari yang paling banyak.
     */
    protected function daftarPenerbit()
    {
        return Buku::query()
            ->select('penerbit', DB::raw('COUNT(*) as total'))
            ->groupBy('penerbit')
            ->orderByDesc('total')
            ->orderBy('penerbit')
            ->limit(30)
            ->get();
    }

    /**
     * Chip filter aktif beserta tautan untuk melepasnya satu per satu.
     *
     * @return array<int, array{label: string, url: string}>
     */
    protected function chipFilter(Request $request, array $filter, $kategoris): array
    {
        $tanpa = fn (string ...$kunci) => route('katalog.index', Arr::except($request->query(), [...$kunci, 'page']));

        $chips = [];

        if ($filter['q'] !== '') {
            $chips[] = ['label' => 'Kata kunci: '.$filter['q'], 'url' => $tanpa('q')];
        }

        if ($filter['kategori']) {
            $nama = $kategoris->firstWhere('id', $filter['kategori'])?->nama ?? 'Kategori';
            $chips[] = ['label' => $nama, 'url' => $tanpa('kategori')];
        }

        foreach ($filter['penerbit'] as $penerbit) {
            $sisa = array_values(array_diff($filter['penerbit'], [$penerbit]));
            $chips[] = [
                'label' => $penerbit,
                'url' => route('katalog.index', array_merge(Arr::except($request->query(), ['penerbit', 'page']), $sisa ? ['penerbit' => $sisa] : [])),
            ];
        }

        if ($filter['penulis']) {
            $chips[] = ['label' => 'Penulis: '.$filter['penulis'], 'url' => $tanpa('penulis')];
        }

        if ($filter['tahun_dari'] || $filter['tahun_sampai']) {
            $chips[] = [
                'label' => 'Terbit '.($filter['tahun_dari'] ?? '...').' - '.($filter['tahun_sampai'] ?? '...'),
                'url' => $tanpa('tahun_dari', 'tahun_sampai'),
            ];
        }

        if ($filter['tersedia']) {
            $chips[] = ['label' => 'Hanya yang tersedia', 'url' => $tanpa('tersedia')];
        }

        return $chips;
    }

    /**
     * Simpan 10 buku terakhir yang dibuka, yang terbaru di depan.
     */
    protected function catatDilihat(Request $request, Buku $buku): void
    {
        $ids = array_values(array_unique([$buku->id, ...$request->session()->get(self::SESI_DILIHAT, [])]));

        $request->session()->put(self::SESI_DILIHAT, array_slice($ids, 0, 10));
    }
}
