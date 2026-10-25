<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kode_produk' => $this->kode_produk,
            'nama_produk' => $this->nama_produk,
            'jenis_produk' => $this->jenis_produk,
            'harga_satuan' => $this->harga_satuan,
            'jumlah_stok' => $this->jumlah_stok,
            'terakhir_diperbarui' => $this->terakhir_diperbarui?->toISOString(),
            'tersedia' => $this->tersedia,
        ];
    }
}