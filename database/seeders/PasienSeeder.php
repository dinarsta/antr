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

        ];

        foreach ($data as $item) {
            // Calculate the estimated end time based on the jenis_obat
            $startTime = Carbon::parse($item['waktu_mulai']);
            $estimatedEndTime = $item['jenis_obat'] === 'racikan'
                ? $startTime->copy()->addMinutes(60) // Add 60 minutes for "racikan"
                : $startTime->copy()->addMinutes(30); // Add 30 minutes for "jadi"

            // Format estimated time for `estimasi`
            $currentTime = Carbon::now();
            $remainingTime = $estimatedEndTime->diffInSeconds($currentTime, false); // Calculate remaining time

            $estimasi = $remainingTime > 0
                ? gmdate('H:i:s', $remainingTime) // Convert seconds to H:i:s format
                : '00:00:00'; // If time has passed

            // Insert the data into the database
            Pasien::create([
                'nomor_resep' => $item['nomor_resep'],
                'nama' => $item['nama'],
                'jenis_obat' => $item['jenis_obat'],
                'waktu_mulai' => $item['waktu_mulai'],
                'estimasi_waktu_selesai' => $estimatedEndTime,
                'estimasi' => $estimasi,
                'keterangan' => $item['keterangan'],
            ]);
        }
    }
}
