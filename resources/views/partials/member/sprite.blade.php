{{-- Ikon yang dipakai berulang di tiap kartu buku, dirujuk lewat <use> supaya HTML tidak membengkak. --}}
<svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; width: 0; height: 0; overflow: hidden" aria-hidden="true" focusable="false">
    @foreach (['heart', 'cart', 'check'] as $ikon)
        <symbol id="i-{{ $ikon }}" viewBox="0 0 24 24">{!! \App\Support\Ikon::markup($ikon) !!}</symbol>
    @endforeach
</svg>
