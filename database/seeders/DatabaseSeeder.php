<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\PasienSeeder; // Tambahkan ini untuk memanggil PasienSeeder

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan seeder untuk tabel pasien
        $this->call(PasienSeeder::class);
    }
}
