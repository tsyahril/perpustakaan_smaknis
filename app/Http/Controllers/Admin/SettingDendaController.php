<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DendaSetting;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingDendaController extends Controller
{
    /**
     * Halaman "Setting Denda" sudah disatukan ke halaman Denda
     * (tab "Setting"). Rute lama tetap dipertahankan agar tautan lama
     * tidak rusak, hanya dialihkan ke tab tersebut.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.denda.index', ['tab' => 'setting']);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'denda_terlambat_per_hari' => ['required', 'integer', 'min:0', 'max:1000000'],
            'denda_rusak' => ['required', 'integer', 'min:0', 'max:10000000'],
            'denda_hilang' => ['required', 'integer', 'min:0', 'max:10000000'],
        ]);

        $setting = DendaSetting::current();
        $setting->update($validated);
        $setting->refresh();

        // Denda pada transaksi yang sudah dikembalikan tersimpan sebagai
        // hasil hitungan saat itu. Karena tarifnya baru berubah, semua
        // catatan itu dihitung ulang supaya ikut menyesuaikan.
        $terhitung = 0;

        Transaksi::query()
            ->where('status', 'dikembalikan')
            ->where('status_persetujuan', '!=', 'ditolak')
            ->chunkById(200, function ($transaksis) use ($setting, &$terhitung) {
                foreach ($transaksis as $transaksi) {
                    $transaksi->terapkanDenda($setting);
                    $terhitung++;
                }
            });

        $pesan = 'Setting denda berhasil diperbarui.';

        if ($terhitung > 0) {
            $pesan .= " Denda pada {$terhitung} riwayat pengembalian dihitung ulang.";
        }

        return redirect()
            ->route('admin.denda.index', ['tab' => 'setting'])
            ->with('success', $pesan);
    }
}
