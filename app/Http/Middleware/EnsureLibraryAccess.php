<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLibraryAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Silakan login terlebih dahulu.');
        }

        // Admin selalu memiliki akses penuh ke fitur perpustakaan.
        // Pustakawan mendapat akses sesuai fungsi role yang tersedia.
        if ($user->canManageLibrary()) {
            return $next($request);
        }

        abort(403, 'Akun ini tidak memiliki akses ke fitur perpustakaan.');
    }
}
