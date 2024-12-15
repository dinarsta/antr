<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $fillable = ['nomor_antrian', 'nama', 'jenis_obat', 'waktu_pemanggilan'];

    protected $casts = [
        'waktu_pemanggilan' => 'datetime',
    ];
}
