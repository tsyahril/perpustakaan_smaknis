<?php

namespace App\Services;

use App\Exceptions\PeminjamanDitolakException;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Pengajuan dan pembatalan peminjaman dari sisi anggota.
 *
 * Aturannya sengaja sama dengan form peminjaman di panel admin:
 * stok langsung dikurangi saat diajukan (ditahan), lalu dikembalikan
 * bila pengajuan ditolak atau dibatalkan.
 */
class PengajuanPeminjaman
{
    public const MAKS_BUKU = 5;

    public const MAKS_HARI = 30;

    public const CATATAN_BATAL = 'Dibatalkan oleh peminjam';

    /**
     * Ajukan peminjaman beberapa buku sekaligus dan kembalikan kode peminjamannya.
     *
     * @param  array<int, int>  $bukuIds
     *
     * @throws PeminjamanDitolakException
     */
    public function ajukan(Anggota $anggota, array $bukuIds, CarbonInterface $rencanaKembali): string
    {
        $bukuIds = array_values(array_unique(array_map('intval', $bukuIds)));

        $this->pastikanAnggotaAktif($anggota);

        if ($bukuIds === []) {
            throw new PeminjamanDitolakException('Keranjang masih kosong.');
        }

        if (count($bukuIds) > self::MAKS_BUKU) {
            throw new PeminjamanDitolakException('Satu peminjaman maksimal '.self::MAKS_BUKU.' buku.');
        }

        return DB::transaction(function () use ($anggota, $bukuIds, $rencanaKembali) {
            // Kunci baris berurutan berdasarkan id supaya dua pengajuan
            // yang berebut buku sama tidak saling mengunci (deadlock).
            $bukus = Buku::query()
                ->whereIn('id', $bukuIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($bukus->count() !== count($bukuIds)) {
                throw new PeminjamanDitolakException('Ada buku di keranjang yang sudah tidak ada di katalog.');
            }

            $kode = $this->buatKode();

            foreach ($bukus as $buku) {
                if ($buku->stok <= 0) {
                    throw new PeminjamanDitolakException("Stok buku \"{$buku->judul}\" habis.");
                }

                if ($this->masihDipinjam($anggota, $buku)) {
                    throw new PeminjamanDitolakException("Kamu masih meminjam atau mengajukan \"{$buku->judul}\".");
                }

                Transaksi::create([
                    'anggota_id' => $anggota->id,
                    'buku_id' => $buku->id,
                    'kode_peminjaman' => $kode,
                    'tanggal_pinjam' => today(),
                    'tanggal_kembali_rencana' => $rencanaKembali->toDateString(),
                    'status' => 'pinjam',
                    'status_persetujuan' => 'diajukan',
                ]);

                $buku->decrement('stok');
            }

            return $kode;
        });
    }

    /**
     * Batalkan pengajuan yang belum diproses pustakawan. Riwayatnya tetap
     * disimpan sebagai pengajuan ditolak dengan catatan khusus.
     *
     * @throws PeminjamanDitolakException
     */
    public function batalkan(Anggota $anggota, string $groupKey): int
    {
        return DB::transaction(function () use ($anggota, $groupKey) {
            $items = $this->transaksiGrup($anggota, $groupKey)->lockForUpdate()->get();

            if ($items->isEmpty()) {
                throw new PeminjamanDitolakException('Peminjaman tidak ditemukan.');
            }

            if ($items->contains(fn (Transaksi $t) => $t->status_persetujuan !== 'diajukan')) {
                throw new PeminjamanDitolakException('Hanya pengajuan yang masih menunggu persetujuan yang bisa dibatalkan.');
            }

            foreach ($items as $item) {
                $item->update([
                    'status_persetujuan' => 'ditolak',
                    'catatan' => self::CATATAN_BATAL,
                ]);

                Buku::whereKey($item->buku_id)->increment('stok');
            }

            return $items->count();
        });
    }

    /**
     * Query transaksi satu peminjaman milik anggota. Kunci grup memakai
     * format yang sama dengan Transaksi::group_key ("K:kode" atau "T:id").
     */
    public function transaksiGrup(Anggota $anggota, string $groupKey): Builder
    {
        $query = Transaksi::query()->where('anggota_id', $anggota->id);

        return match (true) {
            str_starts_with($groupKey, 'K:') => $query->where('kode_peminjaman', substr($groupKey, 2)),
            str_starts_with($groupKey, 'T:') => $query->whereKey((int) substr($groupKey, 2)),
            default => $query->whereRaw('1 = 0'),
        };
    }

    /**
     * Apakah anggota masih memegang atau mengajukan buku ini.
     */
    public function masihDipinjam(Anggota $anggota, Buku $buku): bool
    {
        return Transaksi::query()
            ->where('anggota_id', $anggota->id)
            ->where('buku_id', $buku->id)
            ->where('status', 'pinjam')
            ->where('status_persetujuan', '!=', 'ditolak')
            ->exists();
    }

    /**
     * @throws PeminjamanDitolakException
     */
    public function pastikanAnggotaAktif(?Anggota $anggota): void
    {
        if (! $anggota) {
            throw new PeminjamanDitolakException('Akunmu belum terhubung dengan kartu anggota. Hubungi pustakawan untuk menghubungkannya.');
        }

        if ($anggota->status !== 'aktif') {
            throw new PeminjamanDitolakException('Kartu anggotamu sedang nonaktif. Hubungi pustakawan.');
        }
    }

    protected function buatKode(): string
    {
        do {
            $kode = 'PJM-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(5));
        } while (Transaksi::where('kode_peminjaman', $kode)->exists());

        return $kode;
    }
}
