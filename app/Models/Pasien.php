<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pasien extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_resep',
        'nama',
        'jenis_obat',
        'waktu_mulai',
        'estimasi_waktu_selesai',
        'keterangan'
    ];

    /**
     * Automatically calculate estimasi_waktu_selesai before creating the model.
     */
    protected static function booted()
    {
        static::creating(function ($pasien) {
            if ($pasien->waktu_mulai && $pasien->jenis_obat) {
                $waktuMulai = Carbon::parse($pasien->waktu_mulai);
                $estimasiWaktuSelesai = $waktuMulai->copy();

                // Add time based on jenis_obat
                if ($pasien->jenis_obat == 'racikan') {
                    $estimasiWaktuSelesai->addMinutes(60);
                } elseif ($pasien->jenis_obat == 'jadi') {
                    $estimasiWaktuSelesai->addMinutes(30);
                }

                $pasien->estimasi_waktu_selesai = $estimasiWaktuSelesai;
            }
        });
    }
}
