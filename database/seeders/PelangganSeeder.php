<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pelanggans = [
            [
                'kode_pelanggan' => 'PLG-0001',
                'nama_pelanggan' => 'Ahmad Fauzi',
                'email'          => 'ahmad.fauzi@gmail.com',
                'nomor_telepon'  => '081234567890',
                'alamat'         => 'Jl. Sudirman No. 45, Jakarta Pusat',
                'status'         => 'aktif',
            ],
            [
                'kode_pelanggan' => 'PLG-0002',
                'nama_pelanggan' => 'Siti Nurhaliza',
                'email'          => 'siti.nurhaliza@yahoo.com',
                'nomor_telepon'  => '082198765432',
                'alamat'         => 'Jl. Malioboro No. 12, Yogyakarta',
                'status'         => 'aktif',
            ],
            [
                'kode_pelanggan' => 'PLG-0003',
                'nama_pelanggan' => 'Budi Santoso',
                'email'          => 'budi.santoso@outlook.com',
                'nomor_telepon'  => '085712349988',
                'alamat'         => 'Jl. Diponegoro No. 88, Surabaya',
                'status'         => 'aktif',
            ],
            [
                'kode_pelanggan' => 'PLG-0004',
                'nama_pelanggan' => 'Dewi Lestari',
                'email'          => 'dewi.lestari@gmail.com',
                'nomor_telepon'  => '081377889900',
                'alamat'         => 'Jl. Gatot Subroto No. 23, Bandung',
                'status'         => 'nonaktif',
            ],
            [
                'kode_pelanggan' => 'PLG-0005',
                'nama_pelanggan' => 'Rian Pratama',
                'email'          => 'rian.pratama@gmail.com',
                'nomor_telepon'  => '089655443322',
                'alamat'         => 'Jl. Pemuda No. 101, Semarang',
                'status'         => 'aktif',
            ],
        ];

        foreach ($pelanggans as $item) {
            Pelanggan::updateOrCreate(
                ['kode_pelanggan' => $item['kode_pelanggan']],
                $item
            );
        }
    }
}