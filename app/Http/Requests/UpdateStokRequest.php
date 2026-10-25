<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_penyesuaian' => ['required', 'string', 'in:penjualan,restok,koreksi'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ];
    }
}