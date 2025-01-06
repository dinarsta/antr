<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pasiens', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_resep')->unique();
            $table->string('nama');
            $table->enum('jenis_obat', ['jadi', 'racikan']); // Tetap
            $table->timestamp('waktu_mulai')->nullable(); // Mengganti 'waktu_pemanggilan' menjadi 'waktu_mulai'
            $table->timestamp('estimasi_waktu_selesai')->nullable(); // Tambahan estimasi waktu selesai
            $table->enum('keterangan', ['menunggu', 'selesai'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};
