<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->string('kode_produk')->primary();
            $table->string('nama_produk');
            $table->string('jenis_produk'); // tahu atau tempe
            $table->integer('harga_satuan');
            $table->integer('jumlah_stok')->default(0);
            $table->timestamp('terakhir_diperbarui')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};