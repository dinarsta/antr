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
            ['nomor_antrian' => 1, 'nama' => 'Budi Santoso', 'jenis_obat' => 'jadi'],
            ['nomor_antrian' => 2, 'nama' => 'Siti Aisyah', 'jenis_obat' => 'racikan'],
            ['nomor_antrian' => 3, 'nama' => 'Andi Wijaya', 'jenis_obat' => 'jadi'],
            ['nomor_antrian' => 4, 'nama' => 'Rina Kartika', 'jenis_obat' => 'racikan'],
            ['nomor_antrian' => 5, 'nama' => 'Dedi Supriyadi', 'jenis_obat' => 'jadi'],
        ];


        foreach ($data as $item) {
            Pasien::create($item);
        }
    }
}
