<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('pages.guru.dashboard', [
            'user' => $user,
            'totalBuku' => Buku::count(),
        ]);
    }
}
