<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasiensTable extends Migration
{
    public function up(): void
    {
        Schema::create('pasiens', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_resep')->unique();
            $table->string('nama');
            $table->enum('jenis_obat', ['jadi', 'racikan']);
            $table->datetime('waktu_mulai');
            $table->datetime('estimasi_waktu_selesai');
            $table->enum('keterangan', ['menunggu', 'selesai'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
}
