<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PelangganResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'kode_pelanggan' => $this->kode_pelanggan,
            'nama_pelanggan' => $this->nama_pelanggan,
            'email'          => $this->email ?? '-',
            'nomor_telepon'  => $this->nomor_telepon ?? '-',
            'alamat'         => $this->alamat ?? '-',
            'status'         => $this->status,
            'created_at'     => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'     => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
