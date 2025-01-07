<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pasien;
use Carbon\Carbon;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nomor_resep' => 'A001', 'nama' => 'Budi Santoso', 'jenis_obat' => 'jadi', 'waktu_mulai' => Carbon::now()->subMinutes(10), 'keterangan' => 'menunggu'],
            ['nomor_resep' => 'A002', 'nama' => 'Siti Aisyah', 'jenis_obat' => 'racikan', 'waktu_mulai' => Carbon::now()->subMinutes(20), 'keterangan' => 'menunggu'],
            ['nomor_resep' => 'A003', 'nama' => 'Andi Wijaya', 'jenis_obat' => 'jadi', 'waktu_mulai' => Carbon::now()->subMinutes(30), 'keterangan' => 'menunggu'],
            ['nomor_resep' => 'A004', 'nama' => 'Rina Kartika', 'jenis_obat' => 'racikan', 'waktu_mulai' => Carbon::now()->subMinutes(40), 'keterangan' => 'menunggu'],
            ['nomor_resep' => 'A005', 'nama' => 'Dedi Supriyadi', 'jenis_obat' => 'jadi', 'waktu_mulai' => Carbon::now()->subMinutes(50), 'keterangan' => 'menunggu'],
        ];

        foreach ($data as $item) {
            // Calculate the estimated end time based on the jenis_obat
            $startTime = Carbon::parse($item['waktu_mulai']);
            $estimatedEndTime = $item['jenis_obat'] === 'racikan'
                ? $startTime->copy()->addMinutes(60) // Add 60 minutes for "racikan"
                : $startTime->copy()->addMinutes(30); // Add 30 minutes for "jadi"

            // Insert the data into the database
            Pasien::create([
                'nomor_resep' => $item['nomor_resep'],
                'nama' => $item['nama'],
                'jenis_obat' => $item['jenis_obat'],
                'waktu_mulai' => $item['waktu_mulai'],
                'estimasi_waktu_selesai' => $estimatedEndTime,
                'keterangan' => $item['keterangan'],
            ]);
        }
    }
}
