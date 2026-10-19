<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'anggota_id',
        'buku_id',
        'kode_peminjaman',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'denda',
        'denda_terlambat',
        'denda_kondisi',
        'hari_terlambat',
        'kondisi',
        'status',
        'status_persetujuan',
        'catatan',
        'denda_dibayar',
        'denda_dibayar_pada',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date',
        'denda_dibayar' => 'boolean',
        'denda_dibayar_pada' => 'datetime',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    /**
     * Hari terlambat dihitung otomatis: selisih hari aktual (atau hari ini
     * bila belum kembali) terhadap rencana. Tidak pernah negatif.
     */
    public function hitungHariTerlambat(?Carbon $pembanding = null): int
    {
        // Buku tanpa tanggal rencana tidak punya batas keterlambatan.
        if (! $this->tanggal_kembali_rencana) {
            return 0;
        }

        $rencana = $this->tanggal_kembali_rencana instanceof Carbon
            ? $this->tanggal_kembali_rencana
            : Carbon::parse($this->tanggal_kembali_rencana);

        $aktual = $pembanding
            ?? ($this->tanggal_kembali_aktual ? Carbon::parse($this->tanggal_kembali_aktual) : Carbon::today());

        if ($aktual->lte($rencana)) {
            return 0;
        }

        return (int) $rencana->diffInDays($aktual);
    }

    public function isTelat(): bool
    {
        return $this->hitungHariTerlambat() > 0;
    }

    /**
     * Hitung rincian denda berdasarkan setting aktif.
     * return [hari, denda_terlambat, denda_kondisi, total]
     */
    public function hitungDenda(?string $kondisi = null, ?Carbon $aktual = null, ?DendaSetting $setting = null): array
    {
        $setting ??= DendaSetting::current();
        $hari = $this->hitungHariTerlambat($aktual);
        $dendaTerlambat = $hari * (int) $setting->denda_terlambat_per_hari;

        $kondisi = $kondisi ?? $this->kondisi;
        $dendaKondisi = match ($kondisi) {
            'rusak' => (int) $setting->denda_rusak,
            'hilang' => (int) $setting->denda_hilang,
            default => 0,
        };

        return [
            'hari' => $hari,
            'denda_terlambat' => $dendaTerlambat,
            'denda_kondisi' => $dendaKondisi,
            'total' => $dendaTerlambat + $dendaKondisi,
        ];
    }

    /**
     * Hitung ulang denda dari tarif yang sedang berlaku lalu simpan.
     * Dipakai setelah tarif di Setting Denda berubah, supaya data denda
     * yang sudah tersimpan ikut menyesuaikan.
     */
    public function terapkanDenda(?DendaSetting $setting = null): self
    {
        $rincian = $this->hitungDenda(null, null, $setting);

        $this->forceFill([
            'hari_terlambat' => $rincian['hari'],
            'denda_terlambat' => $rincian['denda_terlambat'],
            'denda_kondisi' => $rincian['denda_kondisi'],
            'denda' => $rincian['total'],
        ])->save();

        return $this;
    }

    /**
     * Hitung denda lama (kompatibilitas): total dari rincian.
     */
    public function calculateDenda(): int
    {
        return $this->hitungDenda()['total'];
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status_persetujuan', 'disetujui');
    }

    /**
     * Label tanggal rencana untuk ditampilkan (kosong = belum ditentukan).
     */
    public function tanggalRencanaLabel(): string
    {
        return $this->tanggal_kembali_rencana?->format('d/m/Y') ?? '-';
    }

    /**
     * Kunci pengelompokan transaksi menjadi satu peminjaman.
     * Bila kode_peminjaman kosong (data lama), tiap baris dianggap sendiri.
     */
    public function getGroupKeyAttribute(): string
    {
        return $this->kode_peminjaman ? 'K:' . $this->kode_peminjaman : 'T:' . $this->id;
    }

    /**
     * Denda dianggap lunas bila tidak ada denda atau sudah ditandai dibayar.
     */
    public function isDendaLunas(): bool
    {
        return $this->denda <= 0 || (bool) $this->denda_dibayar;
    }

    /**
     * Ambil semua transaksi yang satu peminjaman (satu kode_peminjaman).
     */
    public function grupPeminjaman()
    {
        if ($this->kode_peminjaman) {
            return Transaksi::query()
                ->with(['anggota.kelas', 'buku'])
                ->where('kode_peminjaman', $this->kode_peminjaman)
                ->get();
        }

        return Transaksi::query()
            ->with(['anggota.kelas', 'buku'])
            ->whereKey($this->getKey())
            ->get();
    }
}