<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoritController extends Controller
{
    public function index(Request $request): View
    {
        $bukus = $request->user()
            ->favorits()
            ->with('kategori:id,nama')
            ->orderByPivot('created_at', 'desc')
            ->paginate(15);

        return view('pages.member.favorit', ['bukus' => $bukus]);
    }

    /**
     * Tambah atau lepas favorit. Menjawab JSON untuk tombol hati di kartu buku
     * dan redirect biasa bila JavaScript tidak aktif.
     */
    public function toggle(Request $request, Buku $buku): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        $aktif = $user->favorits()->toggle($buku->id)['attached'] !== [];
        $pesan = $aktif ? 'Ditambahkan ke favorit.' : 'Dihapus dari favorit.';

        if ($request->wantsJson()) {
            return response()->json([
                'aktif' => $aktif,
                'pesan' => $pesan,
                'jumlah' => $user->favorits()->count(),
            ]);
        }

        return back()->with('success', $pesan);
    }
}
