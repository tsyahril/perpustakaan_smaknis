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

        if ($user->canManageLibrary()) {
            return $next($request);
        }

        // Redirect guru to guru.dashboard saat akses admin.dashboard
        if ($user->isGuru() && $request->route()->getName() === 'admin.dashboard') {
            return redirect()->route('guru.dashboard');
        }

        abort(403, 'Akun ini tidak memiliki akses ke fitur perpustakaan.');
    }
}
