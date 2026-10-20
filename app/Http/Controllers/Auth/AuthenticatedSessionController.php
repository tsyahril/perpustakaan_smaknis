<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses login dan mengarahkan pengguna berdasarkan role.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Admin diarahkan ke dashboard Admin.
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Pustakawan, termasuk Guru yang memiliki akses petugas.
        if ($user->isPustakawan()) {
            return redirect()->route('pustakawan.dashboard');
        }

        // Guru biasa dan Siswa.
        return redirect()->route('guru.dashboard');
    }

    /**
     * Mengakhiri sesi login.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
