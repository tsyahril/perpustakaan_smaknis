@props(['item'])

<nav aria-label="Jejak halaman" class="wadah pt-5 text-xs text-slate-500">
    <ol class="flex flex-wrap items-center gap-1.5">
        <li><a href="{{ route('beranda') }}" class="hover:text-brand-700">Beranda</a></li>
        @foreach ($item as $label => $url)
            <li class="flex items-center gap-1.5">
                <x-member.ikon nama="chevron-right" kelas="h-3 w-3 text-slate-300" />
                @if ($url)
                    <a href="{{ $url }}" class="hover:text-brand-700">{{ $label }}</a>
                @else
                    <span class="max-w-[16rem] truncate font-medium text-slate-700" aria-current="page">{{ $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
