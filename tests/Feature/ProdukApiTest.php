<?php

namespace Tests\Feature;

use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdukApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_produk(): void
    {
        Produk::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/produk');
        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_tambah_produk_baru(): void
    {
        $payload = [
            'nama_produk' => 'Tahu Putih',
            'jenis_produk' => 'tahu',
            'harga_satuan' => 5000,
            'stok_awal' => 50,
        ];

        $response = $this->postJson('/api/v1/produk', $payload);
        $response->assertStatus(201);
        $response->assertHeader('Location');
        $response->assertJson([
            'kode_produk' => 'TAHU-001',
            'nama_produk' => 'Tahu Putih',
            'jenis_produk' => 'tahu',
            'harga_satuan' => 5000,
            'jumlah_stok' => 50,
            'tersedia' => true,
        ]);
    }

    public function test_validasi_gagal_tanpa_field_wajib(): void
    {
        $response = $this->postJson('/api/v1/produk', []);
        $response->assertStatus(422);
        $response->assertHeader('Content-Type', 'application/problem+json');
    }

    public function test_detail_produk(): void
    {
        $produk = Produk::factory()->create(['kode_produk' => 'TEMPE-001']);
        $response = $this->getJson('/api/v1/produk/TEMPE-001');
        $response->assertStatus(200);
        $response->assertJson(['kode_produk' => 'TEMPE-001']);
    }

    public function test_404_produk_tidak_ditemukan(): void
    {
        $response = $this->getJson('/api/v1/produk/XXXX-999');
        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/problem+json');
    }

    public function test_restok_stok(): void
    {
        Produk::factory()->create(['kode_produk' => 'TAHU-001', 'jumlah_stok' => 10]);

        $response = $this->postJson('/api/v1/produk/TAHU-001/penyesuaian-stok', [
            'jenis_penyesuaian' => 'restok',
            'jumlah' => 20,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['jumlah_stok' => 30]);
        $this->assertDatabaseHas('produk', ['kode_produk' => 'TAHU-001', 'jumlah_stok' => 30]);
    }

    public function test_penjualan_stok(): void
    {
        Produk::factory()->create(['kode_produk' => 'TAHU-001', 'jumlah_stok' => 10]);

        $response = $this->postJson('/api/v1/produk/TAHU-001/penyesuaian-stok', [
            'jenis_penyesuaian' => 'penjualan',
            'jumlah' => 4,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['jumlah_stok' => 6]);
    }

    public function test_409_stok_tidak_cukup(): void
    {
        Produk::factory()->create(['kode_produk' => 'TAHU-001', 'jumlah_stok' => 2]);

        $response = $this->postJson('/api/v1/produk/TAHU-001/penyesuaian-stok', [
            'jenis_penyesuaian' => 'penjualan',
            'jumlah' => 10,
        ]);

        $response->assertStatus(409);
        $response->assertHeader('Content-Type', 'application/problem+json');
    }

    public function test_404_penyesuaian_stok_produk_tidak_ditemukan(): void
    {
        $response = $this->postJson('/api/v1/produk/XXXX-999/penyesuaian-stok', [
            'jenis_penyesuaian' => 'penjualan',
            'jumlah' => 1,
        ]);
        $response->assertStatus(404);
    }
}