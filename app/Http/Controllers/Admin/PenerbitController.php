<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenerbitController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $penerbits = Buku::query()
            ->selectRaw('penerbit, COUNT(*) as jumlah_buku')
            ->when($search, fn ($q) => $q->where('penerbit', 'like', "%{$search}%"))
            ->groupBy('penerbit')
            ->orderBy('penerbit')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.penerbit.index', compact('penerbits', 'search'));
    }
}
