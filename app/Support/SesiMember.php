<?php

namespace App\Support;

use App\Enums\StatusPinjaman;
use App\Models\Anggota;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\PengajuanPeminjaman;
use Illuminate\Support\Collection;

/**
 * Konteks anggota yang sedang membuka katalog: apa yang sudah difavoritkan,
 * isi keranjang, dan pengingat peminjaman. Dibuat satu kali per request oleh
 * middleware BagikanSesiMember lalu dipakai bersama oleh semua tampilan.
 */
class SesiMember
{
    private ?Collection $idFavorit = null;

    private ?Collection $idKeranjang = null;

    private ?Collection $pengingat = null;

    public function __construct(private readonly ?User $user) {}

    public function login(): bool
    {
        return $this->user !== null;
    }

    public function user(): ?User
    {
        return $this->user;
    }

    public function anggota(): ?Anggota
    {
        return $this->user?->anggota;
    }

    public function favorit(int $bukuId): bool
    {
        return $this->idFavorit()->contains($bukuId);
    }

    public function dikeranjang(int $bukuId): bool
    {
        return $this->idKeranjang()->contains($bukuId);
    }

    public function jumlahFavorit(): int
    {
        return $this->idFavorit()->count();
    }

    public function jumlahKeranjang(): int
    {
        return $this->idKeranjang()->count();
    }

    /**
     * Pengingat untuk lonceng di header: buku terlambat, hampir jatuh tempo,
     * serta keputusan pustakawan dalam 7 hari terakhir.
     *
     * @return Collection<int, array{tipe: string, judul: string, detail: string, url: string}>
     */
    public function pengingat(): Collection
    {
        return $this->pengingat ??= $this->susunPengingat();
    }

    private function idFavorit(): Collection
    {
        return $this->idFavorit ??= $this->user
            ? $this->user->favorits()->pluck('bukus.id')
            : collect();
    }

    private function idKeranjang(): Collection
    {
        return $this->idKeranjang ??= $this->user
            ? $this->user->keranjang()->pluck('bukus.id')
            : collect();
    }

    private function susunPengingat(): Collection
    {
        $anggotaId = $this->user?->id_anggota;

        if (! $anggotaId) {
            return collect();
        }

        $baris = Transaksi::query()
            ->with('buku:id,judul')
            ->where('anggota_id', $anggotaId)
            ->where('status', 'pinjam')
            ->where(fn ($q) => $q
                ->where('status_persetujuan', 'disetujui')
                ->orWhere(fn ($ditolak) => $ditolak
                    ->where('status_persetujuan', 'ditolak')
                    ->where(fn ($c) => $c->whereNull('catatan')->orWhere('catatan', '!=', PengajuanPeminjaman::CATATAN_BATAL))
                    ->where('updated_at', '>=', now()->subDays(7))))
            ->get();

        $urutan = ['terlambat' => 0, 'tempo' => 1, 'ditolak' => 2, 'disetujui' => 3];

        return GrupPeminjaman::kelompokkan($baris)
            ->map(fn (GrupPeminjaman $grup) => $this->pengingatDari($grup))
            ->filter()
            ->sortBy(fn (array $p) => $urutan[$p['tipe']])
            ->take(6)
            ->values();
    }

    private function pengingatDari(GrupPeminjaman $grup): ?array
    {
        $judul = $grup->items->first()->buku?->judul ?? 'Buku';
        $judul .= $grup->jumlahBuku() > 1 ? ' dan '.($grup->jumlahBuku() - 1).' buku lain' : '';
        $url = route('pinjaman.show', $grup->key);

        $status = $grup->status();

        if ($status === StatusPinjaman::Terlambat) {
            return ['tipe' => 'terlambat', 'judul' => $judul, 'detail' => 'Terlambat '.$grup->hariTerlambat().' hari, segera kembalikan.', 'url' => $url];
        }

        if ($status === StatusPinjaman::Ditolak) {
            return ['tipe' => 'ditolak', 'judul' => $judul, 'detail' => 'Pengajuan ditolak pustakawan.', 'url' => $url];
        }

        if ($status !== StatusPinjaman::Dipinjam) {
            return null;
        }

        $batas = $grup->batasKembali();

        if ($batas && today()->diffInDays($batas->copy()->startOfDay(), false) <= 2) {
            return ['tipe' => 'tempo', 'judul' => $judul, 'detail' => Format::sisaHari($batas).', siapkan untuk dikembalikan.', 'url' => $url];
        }

        if ($grup->items->max('updated_at')?->gte(now()->subDays(7))) {
            return ['tipe' => 'disetujui', 'judul' => $judul, 'detail' => 'Pengajuan disetujui, ambil bukunya di perpustakaan.', 'url' => $url];
        }

        return null;
    }
}
