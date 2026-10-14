<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenulisController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $penulis = Buku::query()
            ->selectRaw('penulis, COUNT(*) as jumlah_buku')
            ->when($search, fn ($q) => $q->where('penulis', 'like', "%{$search}%"))
            ->groupBy('penulis')
            ->orderBy('penulis')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.penulis.index', compact('penulis', 'search'));
    }
}
