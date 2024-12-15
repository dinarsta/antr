<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pasien;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nomor_antrian' => 1, 'nama' => 'John Doe', 'jenis_obat' => 'jadi'],
            ['nomor_antrian' => 2, 'nama' => 'Jane Smith', 'jenis_obat' => 'racikan'],
            ['nomor_antrian' => 3, 'nama' => 'Alice Brown', 'jenis_obat' => 'jadi'],
            ['nomor_antrian' => 4, 'nama' => 'Bob Johnson', 'jenis_obat' => 'racikan'],
            ['nomor_antrian' => 5, 'nama' => 'Charlie Davis', 'jenis_obat' => 'jadi'],
        ];

        foreach ($data as $item) {
            Pasien::create($item);
        }
    }
}
