<?php

namespace App\Http\Middleware;

use App\Support\SesiMember;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membagikan $sesi (SesiMember) ke semua tampilan halaman anggota.
 * Objeknya dibuat baru di tiap request sehingga tidak ada data basi.
 */
class BagikanSesiMember
{
    public function handle(Request $request, Closure $next): Response
    {
        View::share('sesi', new SesiMember($request->user()));

        return $next($request);
    }
}
