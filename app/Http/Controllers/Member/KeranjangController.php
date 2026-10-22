<?php

namespace App\Http\Controllers\Member;

use App\Exceptions\PeminjamanDitolakException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\CheckoutRequest;
use App\Models\Buku;
use App\Models\DendaSetting;
use App\Models\Transaksi;
use App\Services\PengajuanPeminjaman;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeranjangController extends Controller
{
    public function __construct(private readonly PengajuanPeminjaman $layanan) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $anggota = $user->anggota;

        $items = $user->keranjang()
            ->with('kategori:id,nama')
            ->orderByPivot('created_at', 'desc')
            ->get();

        $sedangDipinjam = $anggota
            ? Transaksi::where('anggota_id', $anggota->id)
                ->where('status', 'pinjam')
                ->where('status_persetujuan', '!=', 'ditolak')
                ->pluck('buku_id')
            : collect();

        // Alasan sebuah buku tidak bisa diajukan, dikunci per id buku.
        $masalah = $items->mapWithKeys(fn (Buku $buku) => [
            $buku->id => match (true) {
                $buku->stok <= 0 => 'Stok habis, hapus dari keranjang untuk melanjutkan.',
                $sedangDipinjam->contains($buku->id) => 'Kamu masih meminjam atau mengajukan buku ini.',
                default => null,
            },
        ])->filter();

        $kartuBermasalah = ! $anggota || $anggota->status !== 'aktif';

        return view('pages.member.keranjang.index', [
            'items' => $items,
            'anggota' => $anggota,
            'masalah' => $masalah,
            'kartuBermasalah' => $kartuBermasalah,
            'bisaDiajukan' => ! $kartuBermasalah && $items->isNotEmpty() && $masalah->isEmpty(),
            'setting' => DendaSetting::current(),
            'maksBuku' => PengajuanPeminjaman::MAKS_BUKU,
            'maksHari' => PengajuanPeminjaman::MAKS_HARI,
        ]);
    }

    /**
     * Masukkan buku ke keranjang. Dengan langsung=1 pengguna dibawa
     * ke halaman keranjang (tombol "Pinjam Sekarang").
     */
    public function tambah(Request $request, Buku $buku): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        try {
            $this->layanan->pastikanAnggotaAktif($user->anggota);

            if (! $user->keranjang()->whereKey($buku->id)->exists()) {
                if ($buku->stok <= 0) {
                    throw new PeminjamanDitolakException('Stok buku ini sedang habis.');
                }

                if ($this->layanan->masihDipinjam($user->anggota, $buku)) {
                    throw new PeminjamanDitolakException('Kamu masih meminjam atau mengajukan buku ini.');
                }

                if ($user->keranjang()->count() >= PengajuanPeminjaman::MAKS_BUKU) {
                    throw new PeminjamanDitolakException('Keranjang penuh, satu peminjaman maksimal '.PengajuanPeminjaman::MAKS_BUKU.' buku.');
                }

                $user->keranjang()->attach($buku->id);
            }
        } catch (PeminjamanDitolakException $e) {
            return $this->balas($request, false, $e->getMessage());
        }

        return $this->balas($request, true, 'Ditambahkan ke keranjang pinjam.', $request->boolean('langsung'));
    }

    public function hapus(Request $request, Buku $buku): RedirectResponse
    {
        $request->user()->keranjang()->detach($buku->id);

        return back()->with('success', 'Buku dihapus dari keranjang.');
    }

    public function kosongkan(Request $request): RedirectResponse
    {
        $request->user()->keranjang()->detach();

        return back()->with('success', 'Keranjang dikosongkan.');
    }

    public function checkout(CheckoutRequest $request): RedirectResponse
    {
        $user = $request->user();
        $bukuIds = $user->keranjang()->pluck('bukus.id')->all();

        try {
            $kode = $this->layanan->ajukan(
                $user->anggota,
                $bukuIds,
                Carbon::parse($request->validated('tanggal_kembali')),
            );
        } catch (PeminjamanDitolakException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $user->keranjang()->detach($bukuIds);

        return redirect()
            ->route('pinjaman.show', 'K:'.$kode)
            ->with('success', 'Pengajuan terkirim. Tunggu persetujuan pustakawan.');
    }

    private function balas(Request $request, bool $berhasil, string $pesan, bool $langsung = false): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return response()->json([
                'ok' => $berhasil,
                'pesan' => $pesan,
                'jumlah' => $request->user()->keranjang()->count(),
                'url_keranjang' => route('keranjang.index'),
            ], $berhasil ? 200 : 422);
        }

        if (! $berhasil) {
            return back()->with('error', $pesan);
        }

        return $langsung
            ? redirect()->route('keranjang.index')->with('success', $pesan)
            : back()->with('success', $pesan);
    }
}
