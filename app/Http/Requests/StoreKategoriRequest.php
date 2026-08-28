<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreKategoriRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk penambahan data kategori baru.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_kategori' => 'required|string|max:50',
            'keterangan'    => 'nullable|string|max:150',
        ];
    }

    /**
     * Pesan kustom untuk validasi input.
     */
    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.string'   => 'Nama kategori harus berupa teks.',
            'nama_kategori.max'      => 'Nama kategori maksimal 50 karakter.',
            'keterangan.string'      => 'Keterangan harus berupa teks.',
            'keterangan.max'         => 'Keterangan maksimal 150 karakter.',
        ];
    }

    /**
     * Menangani kegagalan validasi untuk menghasilkan response JSON terstandar.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal, silakan periksa data yang Anda masukkan.',
            'errors'  => $validator->errors()
        ], 422));
    }
}
