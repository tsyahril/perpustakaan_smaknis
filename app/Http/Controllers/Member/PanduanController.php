<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\DendaSetting;
use App\Services\PengajuanPeminjaman;
use Illuminate\View\View;

class PanduanController extends Controller
{
    public function index(): View
    {
        return view('pages.member.panduan', [
            'setting' => DendaSetting::current(),
            'maksBuku' => PengajuanPeminjaman::MAKS_BUKU,
            'maksHari' => PengajuanPeminjaman::MAKS_HARI,
        ]);
    }
}
