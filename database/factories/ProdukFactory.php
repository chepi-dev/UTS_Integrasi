<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Produk;

class ProdukFactory extends Factory
{
    protected $model = Produk::class;

    public function definition(): array
    {
        $jenis = $this->faker->randomElement(['tahu', 'tempe']);
        $prefix = strtoupper(substr($jenis, 0, 3));

        return [
            'kode_produk' => $prefix . '-' . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'nama_produk' => $this->faker->word() . ' ' . ucfirst($jenis),
            'jenis_produk' => $jenis,
            'harga_satuan' => $this->faker->numberBetween(1000, 20000),
            'jumlah_stok' => $this->faker->numberBetween(0, 100),
            'terakhir_diperbarui' => now(),
        ];
    }
}