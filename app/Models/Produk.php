<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'kode_produk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_produk',      // <-- TAMBAHKAN INI
        'nama_produk',
        'jenis_produk',
        'harga_satuan',
        'jumlah_stok',
        'terakhir_diperbarui',
    ];

    protected $casts = [
        'harga_satuan' => 'integer',
        'jumlah_stok' => 'integer',
        'terakhir_diperbarui' => 'datetime',
    ];

    public function getTersediaAttribute(): bool
    {
        return $this->jumlah_stok > 0;
    }
}