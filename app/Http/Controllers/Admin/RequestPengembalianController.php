<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RequestPengembalianController extends Controller
{
    /**
     * Menu "Request Pengembalian" sudah disatukan ke halaman Pengembalian
     * (tab "Pengajuan"). Rute lama tetap dipertahankan agar tautan lama
     * tidak rusak, hanya dialihkan ke tab tersebut.
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route(
            'admin.pengembalian.index',
            array_filter(['tab' => 'pengajuan'] + $request->query())
        );
    }
}
