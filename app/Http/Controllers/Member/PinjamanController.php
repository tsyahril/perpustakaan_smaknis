<?php

namespace App\Http\Controllers\Member;

use App\Enums\StatusPinjaman;
use App\Exceptions\PeminjamanDitolakException;
use App\Http\Controllers\Controller;
use App\Models\DendaSetting;
use App\Models\Transaksi;
use App\Services\PengajuanPeminjaman;
use App\Support\GrupPeminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class PinjamanController extends Controller
{
    private const PER_HALAMAN = 8;

    /**
     * Tab pada halaman Pinjaman Saya dan status yang dicakupnya.
     */
    private const TAB = [
        'semua' => ['label' => 'Semua', 'status' => null],
        'menunggu' => ['label' => 'Menunggu', 'status' => [StatusPinjaman::Menunggu]],
        'dipinjam' => ['label' => 'Dipinjam', 'status' => [StatusPinjaman::Dipinjam, StatusPinjaman::Terlambat]],
        'dikembalikan' => ['label' => 'Dikembalikan', 'status' => [StatusPinjaman::Dikembalikan]],
        'ditolak' => ['label' => 'Ditolak', 'status' => [StatusPinjaman::Ditolak, StatusPinjaman::Dibatalkan]],
    ];

    public function __construct(private readonly PengajuanPeminjaman $layanan) {}

    public function index(Request $request): View
    {
        $anggota = $request->user()->anggota;

        $grups = $anggota
            ? GrupPeminjaman::kelompokkan(
                Transaksi::with('buku.kategori')->where('anggota_id', $anggota->id)->get()
            )
            : collect();

        $tab = array_key_exists($request->query('status'), self::TAB) ? $request->query('status') : 'semua';

        $jumlah = collect(self::TAB)->map(fn (array $t) => $t['status'] === null
            ? $grups->count()
            : $grups->filter(fn (GrupPeminjaman $g) => in_array($g->status(), $t['status'], true))->count());

        $tampil = $grups->when(
            self::TAB[$tab]['status'],
            fn ($koleksi, $status) => $koleksi->filter(fn (GrupPeminjaman $g) => in_array($g->status(), $status, true))
        )->values();

        $halaman = max(1, (int) $request->query('page', 1));

        return view('pages.member.pinjaman.index', [
            'anggota' => $anggota,
            'grups' => new LengthAwarePaginator(
                $tampil->forPage($halaman, self::PER_HALAMAN)->values(),
                $tampil->count(),
                self::PER_HALAMAN,
                $halaman,
                ['path' => $request->url(), 'query' => $request->query()],
            ),
            'tab' => $tab,
            'daftarTab' => self::TAB,
            'jumlah' => $jumlah,
            'setting' => DendaSetting::current(),
        ]);
    }

    public function show(Request $request, string $groupKey): View
    {
        $anggota = $request->user()->anggota;

        abort_unless($anggota, 404);

        $items = $this->layanan->transaksiGrup($anggota, $groupKey)->with('buku.kategori')->get();

        abort_if($items->isEmpty(), 404);

        return view('pages.member.pinjaman.show', [
            'grup' => GrupPeminjaman::kelompokkan($items)->first(),
            'setting' => DendaSetting::current(),
        ]);
    }

    public function batal(Request $request, string $groupKey): RedirectResponse
    {
        $anggota = $request->user()->anggota;

        abort_unless($anggota && $this->layanan->transaksiGrup($anggota, $groupKey)->exists(), 404);

        try {
            $jumlah = $this->layanan->batalkan($anggota, $groupKey);
        } catch (PeminjamanDitolakException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('pinjaman.index', ['status' => 'ditolak'])
            ->with('success', "Pengajuan dibatalkan, {$jumlah} buku dikembalikan ke katalog.");
    }
}
