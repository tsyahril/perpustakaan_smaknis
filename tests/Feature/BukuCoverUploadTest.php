<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BukuCoverUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_cover_masuk_saat_tambah_buku(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $kategori = Kategori::create(['nama' => 'Fiksi']);

        $response = $this->actingAs($admin)->post(route('admin.buku.store'), [
            'cover' => UploadedFile::fake()->image('sampul.jpg'),
            'judul' => 'Buku Tes Cover',
            'penulis' => 'Penulis Tes',
            'penerbit' => 'Penerbit Tes',
            'isbn' => '9780000000001',
            'tahun_terbit' => 2020,
            'id_kategori' => $kategori->id,
            'stok' => 5,
            'deskripsi' => 'Deskripsi tes.',
        ]);

        $response->assertRedirect(route('admin.buku.index'));

        $buku = \App\Models\Buku::where('isbn', '9780000000001')->first();
        $this->assertNotNull($buku);
        $this->assertNotNull($buku->cover, 'kolom cover NULL di database');
        Storage::disk('public')->assertExists($buku->cover);
    }
}
