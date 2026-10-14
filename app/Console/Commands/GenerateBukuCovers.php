<?php

namespace App\Console\Commands;

use App\Models\Buku;
use App\Services\BukuCoverGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateBukuCovers extends Command
{
    protected $signature = 'buku:covers';

    protected $description = 'Generate cover dummy terkompresi untuk buku yang belum punya cover';

    public function handle(): int
    {
        $bukus = Buku::whereNull('cover')->get();

        if ($bukus->isEmpty()) {
            $this->info('Semua buku sudah punya cover.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($bukus->count());
        $bar->start();

        $bytes = 0;
        foreach ($bukus as $buku) {
            $path = BukuCoverGenerator::generate($buku);
            $buku->cover = $path;
            $buku->save();
            $bytes += Storage::disk('public')->size($path);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info(sprintf(
            '%d cover dibuat, rata-rata %.1f KB per file.',
            $bukus->count(),
            ($bytes / $bukus->count()) / 1024
        ));

        return self::SUCCESS;
    }
}
