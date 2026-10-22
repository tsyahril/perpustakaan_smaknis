<?php

namespace App\Support;

use App\Enums\StatusPinjaman;
use App\Models\DendaSetting;
use App\Models\Transaksi;
use App\Services\PengajuanPeminjaman;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Satu peminjaman di mata anggota: semua transaksi dengan kode_peminjaman
 * yang sama (atau satu transaksi lama yang belum punya kode).
 */
final class GrupPeminjaman
{
    /**
     * @param  Collection<int, Transaksi>  $items
     */
    public function __construct(
        public readonly string $key,
        public readonly ?string $kode,
        public readonly Collection $items,
    ) {}

    /**
     * Kelompokkan transaksi menjadi daftar peminjaman, yang terbaru di atas.
     *
     * @param  Collection<int, Transaksi>  $transaksis
     * @return Collection<int, self>
     */
    public static function kelompokkan(Collection $transaksis): Collection
    {
        return $transaksis
            ->groupBy(fn (Transaksi $t) => $t->group_key)
            ->map(fn (Collection $items, string $key) => new self(
                $key,
                $items->first()->kode_peminjaman,
                $items->sortBy('id')->values(),
            ))
            ->sortByDesc(fn (self $grup) => sprintf('%s-%010d', $grup->tanggalPinjam()?->format('Ymd'), $grup->items->max('id')))
            ->values();
    }

    public function status(): StatusPinjaman
    {
        if ($this->items->every(fn (Transaksi $t) => $t->status_persetujuan === 'ditolak')) {
            return $this->items->first()->catatan === PengajuanPeminjaman::CATATAN_BATAL
                ? StatusPinjaman::Dibatalkan
                : StatusPinjaman::Ditolak;
        }

        if ($this->items->contains(fn (Transaksi $t) => $t->status_persetujuan === 'diajukan')) {
            return StatusPinjaman::Menunggu;
        }

        $aktif = $this->aktif();

        if ($aktif->isNotEmpty()) {
            return $aktif->contains(fn (Transaksi $t) => $t->isTelat())
                ? StatusPinjaman::Terlambat
                : StatusPinjaman::Dipinjam;
        }

        return StatusPinjaman::Dikembalikan;
    }

    /**
     * Buku yang sudah disetujui dan masih dipegang peminjam.
     *
     * @return Collection<int, Transaksi>
     */
    public function aktif(): Collection
    {
        return $this->items->filter(
            fn (Transaksi $t) => $t->status === 'pinjam' && $t->status_persetujuan === 'disetujui'
        );
    }

    public function jumlahBuku(): int
    {
        return $this->items->count();
    }

    public function tanggalPinjam(): ?CarbonInterface
    {
        return $this->items->min('tanggal_pinjam');
    }

    /**
     * Batas kembali terdekat dari buku yang masih dipegang (atau rencana awal
     * bila semuanya sudah kembali / belum disetujui).
     */
    public function batasKembali(): ?CarbonInterface
    {
        $sumber = $this->aktif()->isNotEmpty() ? $this->aktif() : $this->items;

        return $sumber->pluck('tanggal_kembali_rencana')->filter()->min();
    }

    public function hariTerlambat(): int
    {
        return (int) $this->aktif()->max(fn (Transaksi $t) => $t->hitungHariTerlambat());
    }

    /**
     * Total denda: nilai tersimpan untuk buku yang sudah kembali,
     * perkiraan berjalan untuk buku yang masih terlambat.
     */
    public function denda(?DendaSetting $setting = null): int
    {
        return (int) $this->items->sum(fn (Transaksi $t) => $this->dendaItem($t, $setting));
    }

    /**
     * Bagian denda yang belum dibayar.
     */
    public function dendaBelumLunas(?DendaSetting $setting = null): int
    {
        return (int) $this->items
            ->reject(fn (Transaksi $t) => $t->status === 'dikembalikan' && $t->denda_dibayar)
            ->sum(fn (Transaksi $t) => $this->dendaItem($t, $setting));
    }

    public function dendaItem(Transaksi $t, ?DendaSetting $setting = null): int
    {
        if ($t->status_persetujuan !== 'disetujui') {
            return 0;
        }

        return $t->status === 'dikembalikan'
            ? (int) $t->denda
            : $t->hitungDenda(null, null, $setting)['total'];
    }
}
