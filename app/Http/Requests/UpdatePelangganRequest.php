<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdatePelangganRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Mendapatkan ID pelanggan dari route parameter
        $pelangganId = $this->route('pelanggan');

        return [
            'kode_pelanggan' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('pelanggans', 'kode_pelanggan')->ignore($pelangganId),
            ],
            'nama_pelanggan' => 'sometimes|required|string|max:100',
            'email'          => 'nullable|email|max:100',
            'nomor_telepon'  => 'nullable|string|max:20',
            'alamat'         => 'nullable|string',
            'status'         => 'sometimes|required|in:aktif,nonaktif',
        ];
    }

    /**
     * Pesan kustom untuk validasi.
     */
    public function messages(): array
    {
        return [
            'kode_pelanggan.required' => 'Kode pelanggan wajib diisi.',
            'kode_pelanggan.unique' => 'Kode pelanggan sudah digunakan.',
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
            'nama_pelanggan.max' => 'Nama pelanggan maksimal 100 karakter.',
            'email.email' => 'Format email tidak valid.',
            'status.in' => 'Status harus aktif atau nonaktif.',
        ];
    }

    /**
     * Handle failed validation to return consistent JSON response.
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
