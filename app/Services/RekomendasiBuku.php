<?php

namespace App\Services;

use App\Models\Buku;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Rekomendasi sederhana: buku tersedia dari kategori yang pernah dipinjam
 * atau difavoritkan akun, di luar buku yang sudah dikenal akun tersebut.
 */
class RekomendasiBuku
{
    /**
     * @return Collection<int, Buku>
     */
    public function untuk(User $user, int $batas = 12): Collection
    {
        $dikenal = $user->favorits()->pluck('bukus.id')
            ->merge($user->keranjang()->pluck('bukus.id'))
            ->merge($user->id_anggota
                ? Transaksi::where('anggota_id', $user->id_anggota)->pluck('buku_id')
                : collect())
            ->unique()
            ->values();

        if ($dikenal->isEmpty()) {
            return collect();
        }

        $kategoriIds = Buku::whereIn('id', $dikenal)->pluck('id_kategori')->unique();

        return Buku::with('kategori:id,nama')
            ->whereIn('id_kategori', $kategoriIds)
            ->whereNotIn('id', $dikenal)
            ->tersedia()
            ->orderByDesc('id')
            ->limit($batas)
            ->get();
    }
}
