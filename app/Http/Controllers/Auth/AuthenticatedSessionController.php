<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Guru biasa / siswa tidak punya akses kelola -> ke dashboard guru.
        // Abaikan url.intended ke /admin agar tidak mental bolak-balik.
        $intended = session()->pull('url.intended', null);
        if ($user->canManageLibrary()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        if ($intended && ! str_starts_with(parse_url($intended, PHP_URL_PATH) ?: '', '/admin')) {
            return redirect()->to($intended);
        }

        return redirect()->route('guru.dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
