<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hanya admin & guru yang ditandai pustakawan yang boleh mengelola perpustakaan.
 * Guru biasa / siswa dialihkan ke dashboard guru.
 */
class EnsureLibraryAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->canManageLibrary()) {
            // Hindari loop bila rute tujuan juga memakai middleware ini.
            if ($request->routeIs('guru.*')) {
                abort(403, 'Tidak memiliki akses perpustakaan.');
            }

            return redirect()->route('guru.dashboard')
                ->with('error', 'Akun guru biasa tidak memiliki akses kelola perpustakaan.');
        }

        return $next($request);
    }
}
