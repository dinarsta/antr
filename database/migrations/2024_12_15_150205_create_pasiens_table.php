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
            $table->enum('jenis_obat', ['jadi', 'racikan']);
            $table->datetime('waktu_mulai'); // Tidak nullable
            $table->datetime('estimasi_waktu_selesai'); // Tidak nullable
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
