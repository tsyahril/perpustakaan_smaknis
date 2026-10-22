<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestPeminjamanController extends Controller
{
    /**
     * Menu "Request Peminjaman" sudah disatukan ke halaman Peminjaman
     * (tab "Pengajuan"). Rute lama tetap dipertahankan agar tautan lama
     * tidak rusak, hanya dialihkan ke tab tersebut.
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route(
            'admin.peminjaman.index',
            array_filter(['tab' => 'pengajuan'] + $request->query())
        );
    }

    /**
     * Ambil semua transaksi satu peminjaman dari kunci grup.
     */
    protected function resolveGroup(string $groupKey)
    {
        $query = Transaksi::query()->with(['anggota.kelas', 'buku']);

        if (str_starts_with($groupKey, 'K:')) {
            return $query->where('kode_peminjaman', substr($groupKey, 2))->get();
        }

        return $query->whereKey((int) substr($groupKey, 2))->get();
    }

    public function approve(string $groupKey): RedirectResponse
    {
        $items = $this->resolveGroup($groupKey);

        abort_if($items->isEmpty(), 404);

        if ($items->first()->status_persetujuan !== 'diajukan') {
            return back()->with('error', 'Hanya pengajuan berstatus diajukan yang bisa disetujui.');
        }

        $items->each->update(['status_persetujuan' => 'disetujui']);

        return back()->with('success', 'Pengajuan disetujui (' . $items->count() . ' buku), pindah ke Data Peminjaman.');
    }

    public function reject(Request $request, string $groupKey): RedirectResponse
    {
        $items = $this->resolveGroup($groupKey);

        abort_if($items->isEmpty(), 404);

        if ($items->first()->status_persetujuan !== 'diajukan') {
            return back()->with('error', 'Hanya pengajuan berstatus diajukan yang bisa ditolak.');
        }

        $request->validate(['catatan' => ['nullable', 'string', 'max:500']]);

        DB::transaction(function () use ($items, $request) {
            foreach ($items as $item) {
                $item->update([
                    'status_persetujuan' => 'ditolak',
                    'catatan' => $request->input('catatan'),
                ]);

                // Stok ditahan sejak pengajuan dibuat, jadi dikembalikan bila ditolak.
                $item->buku->increment('stok');
            }
        });

        return back()->with('success', 'Pengajuan ditolak, stok dikembalikan.');
    }
}




