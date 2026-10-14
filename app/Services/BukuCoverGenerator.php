<?php

namespace App\Services;

use App\Models\Buku;
use Illuminate\Support\Facades\Storage;

class BukuCoverGenerator
{
    /**
     * Buat cover dummy 400x600 JPEG terkompresi untuk buku.
     * Mengembalikan path relatif terhadap disk public (cth: covers/abc123.jpg).
     */
    public static function generate(Buku $buku): string
    {
        $width = 400;
        $height = 600;

        $palet = [
            [30, 64, 175], [153, 27, 27], [20, 83, 45], [88, 28, 135],
            [124, 45, 18], [12, 74, 110], [63, 63, 70], [69, 26, 20],
        ];
        $bg = $palet[$buku->id % count($palet)];

        $img = imagecreatetruecolor($width, $height);
        imagefill($img, 0, 0, imagecolorallocate($img, $bg[0], $bg[1], $bg[2]));

        // Garis bingkai tipis.
        $frame = imagecolorallocate($img, 255, 255, 255);
        imagerectangle($img, 14, 14, $width - 15, $height - 15, $frame);

        $white = imagecolorallocate($img, 255, 255, 255);
        $dim = imagecolorallocate($img, 220, 220, 220);

        // Judul di tengah, dibungkus per ~22 karakter (font bawaan GD).
        $lines = self::wrap(strtoupper($buku->judul), 22);
        $font = 5;
        $lineHeight = imagefontheight($font) + 8;
        $totalHeight = count($lines) * $lineHeight;
        $y = (int) (($height - $totalHeight) / 2) - 30;

        foreach ($lines as $line) {
            $lineWidth = imagefontwidth($font) * strlen($line);
            imagestring($img, $font, (int) (($width - $lineWidth) / 2), $y, $line, $white);
            $y += $lineHeight;
        }

        // Penulis di bawah judul.
        $penulis = self::truncate($buku->penulis, 30);
        $pw = imagefontwidth(4) * strlen($penulis);
        imagestring($img, 4, (int) (($width - $pw) / 2), $y + 14, $penulis, $dim);

        // Label kecil di bawah.
        $label = 'PERPUSTAKAAN SMAKNIS';
        $lw = imagefontwidth(2) * strlen($label);
        imagestring($img, 2, (int) (($width - $lw) / 2), $height - 52, $label, $dim);

        $filename = 'covers/dummy-' . $buku->id . '-' . substr(md5($buku->isbn), 0, 8) . '.jpg';
        $fullPath = Storage::disk('public')->path($filename);

        @mkdir(dirname($fullPath), 0755, true);
        // Kualitas 60: ringan (umumnya < 40KB) tapi teks tetap terbaca.
        imagejpeg($img, $fullPath, 60);
        imagedestroy($img);

        return $filename;
    }

    private static function wrap(string $text, int $perLine): array
    {
        $words = preg_split('/\s+/', $text);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            if (strlen($current . ' ' . $word) <= $perLine) {
                $current = trim($current . ' ' . $word);
            } else {
                if ($current !== '') {
                    $lines[] = $current;
                }
                $current = $word;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return array_slice($lines, 0, 8);
    }

    private static function truncate(string $text, int $max): string
    {
        return strlen($text) > $max ? substr($text, 0, $max - 3) . '...' : $text;
    }
}
