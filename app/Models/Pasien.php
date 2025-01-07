<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_resep',
        'nama',
        'jenis_obat',
        'waktu_mulai',
        'estimasi_waktu_selesai',
        'estimasi',
        'keterangan',
    ];

    protected $casts = [
        'keterangan' => 'string', // Enum handling tetap sebagai string di database
    ];
}
