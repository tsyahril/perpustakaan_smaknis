<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Dilempar saat aturan peminjaman tidak terpenuhi (stok habis, buku sudah
 * dipinjam, kartu anggota nonaktif, dan sebagainya). Pesannya aman
 * ditampilkan langsung ke anggota.
 */
class PeminjamanDitolakException extends RuntimeException {}
