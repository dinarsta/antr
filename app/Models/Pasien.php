<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nomor_resep',
        'jenis_obat',
        'waktu_mulai',
        'estimasi_waktu_selesai',
        'keterangan',
    ];

    protected $casts = [
        'keterangan' => 'string', // Enum handling tetap sebagai string di database
    ];
}
