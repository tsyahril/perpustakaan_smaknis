<?php

namespace App\View\Components\Member;

use App\Models\Kategori;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

class Header extends Component
{
    /** @var Collection<int, Kategori> */
    public Collection $kategoris;

    /** @var Collection<int, Kategori> */
    public Collection $kategoriTeratas;

    public function __construct()
    {
        $this->kategoris = Kategori::withCount('bukus')->orderBy('nama')->get();
        $this->kategoriTeratas = $this->kategoris
            ->where('bukus_count', '>', 0)
            ->whereNotIn('nama', ['Fiksi', 'Komik', 'Pelajaran', 'Biografi', 'Non-Fiksi'])
            ->sortByDesc('bukus_count')
            ->take(5)
            ->values();
    }

    public function render(): View
    {
        return view('components.member.header');
    }
}
