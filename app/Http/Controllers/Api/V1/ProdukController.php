<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateStokRequest;
use App\Http\Resources\ProdukResource;
use App\Models\Produk;
use App\Exceptions\StokHabisException;
use App\Http\ApiProblem;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index()
    {
        $data = Produk::orderBy('nama_produk')->get();
        return response()->json(ProdukResource::collection($data));
    }

 public function store(StoreProdukRequest $request)
{
    $valid = $request->validated();

    $kode = strtoupper(substr($valid['jenis_produk'], 0, 4)) . '-' . str_pad(
            Produk::where('jenis_produk', $valid['jenis_produk'])->count() + 1,
        3,
        '0',
        STR_PAD_LEFT
    );

    $produk = Produk::create([
        'kode_produk' => $kode,
        'nama_produk' => $valid['nama_produk'],
        'jenis_produk' => $valid['jenis_produk'],
        'harga_satuan' => $valid['harga_satuan'],
        'jumlah_stok' => $valid['stok_awal'],
        'terakhir_diperbarui' => now(),
    ]);

    return response()
        ->json(new ProdukResource($produk), 201)
        ->header('Location', route('api.v1.produk.show', ['kode_produk' => $produk->kode_produk]));
    }

    public function show(string $kode_produk)
    {
        $produk = Produk::find($kode_produk);

        if (!$produk) {
            return ApiProblem::respond(
                404,
                'Produk tidak ditemukan',
                sprintf('Produk dengan kode %s tidak tersedia.', $kode_produk)
            );
        }

        return response()->json(new ProdukResource($produk));
    }

    public function sesuaikanStok(UpdateStokRequest $request, string $kode_produk)
    {
        $produk = Produk::find($kode_produk);

        if (!$produk) {
            return ApiProblem::respond(
                404,
                'Produk tidak ditemukan',
                sprintf('Produk dengan kode %s tidak tersedia.', $kode_produk)
            );
        }

        $valid = $request->validated();
        $jenis = $valid['jenis_penyesuaian'];
        $jumlah = $valid['jumlah'];

        try {
            DB::transaction(function () use (&$produk, $jenis, $jumlah) {
                if ($jenis === 'penjualan') {
                    if ($produk->jumlah_stok < $jumlah) {
                        throw new StokHabisException(
                            sprintf('Stok %s tidak cukup. Sisa: %d, diminta: %d', $produk->nama_produk, $produk->jumlah_stok, $jumlah)
                        );
                    }
                    $produk->jumlah_stok -= $jumlah;
                } elseif ($jenis === 'restok') {
                    $produk->jumlah_stok += $jumlah;
                } elseif ($jenis === 'koreksi') {
                    $produk->jumlah_stok = $jumlah;
                }

                $produk->terakhir_diperbarui = now();
                $produk->save();
            });
        } catch (StokHabisException $e) {
            return ApiProblem::respond(409, 'Stok tidak cukup', $e->getMessage());
        }

        return response()->json(new ProdukResource($produk->fresh()));
    }
}