<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\Request;

class AntrianController extends Controller
{
    /**
     * Menampilkan daftar antrian.
     */
    public function index()
    {
        // Ambil semua data pasien, urutkan berdasarkan nomor antrian
        $antrian = Pasien::orderBy('nomor_antrian')->get();

        return view('antrian.index', compact('antrian'));
    }


    /**
     * Memanggil pasien berikutnya dalam antrian.
     */



     public function panggilBerikutnya()
     {
         // Ambil pasien pertama yang belum dipanggil dan memiliki jenis obat 'racikan'
         $pasien = Pasien::whereNull('waktu_pemanggilan')
                          ->where('jenis_obat', 'racikan')
                          ->orderBy('nomor_antrian')
                          ->first();

         // Jika tidak ada pasien dengan jenis obat 'racikan', ambil pasien dengan jenis obat lainnya
         if (!$pasien) {
             $pasien = Pasien::whereNull('waktu_pemanggilan')
                              ->where('jenis_obat', '!=', 'racikan')
                              ->orderBy('nomor_antrian')
                              ->first();
         }

         if ($pasien) {
             // Tandai pasien sebagai dipanggil
             $pasien->waktu_pemanggilan = now();
             $pasien->save();

             return response()->json([
                 'success' => true,
                 'pasien' => [
                     'id' => $pasien->id,
                     'nomor_antrian' => $pasien->nomor_antrian,
                     'nama' => $pasien->nama,
                     'jenis_obat' => $pasien->jenis_obat,
                     'waktu_pemanggilan' => $pasien->waktu_pemanggilan->format('H:i:s'),
                 ],
             ]);
         }

         return response()->json([
             'success' => false,
             'message' => 'Tidak ada pasien dalam antrian.',
         ]);
     }



     public function panggil(Request $request)
     {
         // Ambil pasien pertama yang belum dipanggil
         $pasien = Pasien::whereNull('waktu_pemanggilan')->orderBy('nomor_antrian')->first();

         if ($pasien) {
             // Tandai pasien sebagai dipanggil
             $pasien->waktu_pemanggilan = now();
             $pasien->save();

             return response()->json([
                 'success' => true,
                 'pasien' => [
                     'id' => $pasien->id,
                     'nomor_antrian' => $pasien->nomor_antrian,
                     'nama' => $pasien->nama,
                     'jenis_obat' => $pasien->jenis_obat,
                 ],
             ]);
         }

         return response()->json([
             'success' => false,
             'message' => 'Tidak ada pasien dalam antrian.',
         ]);
     }

    /**
     * Membuat data pasien dummy menggunakan seeder.
     * (Opsional: Fungsi ini hanya untuk pengembangan jika data perlu di-reset.)
     */
    public function seedDummyData()
    {
        $data = [
            ['nomor_antrian' => 1, 'nama' => 'Budi Santoso', 'jenis_obat' => 'jadi'],
            ['nomor_antrian' => 2, 'nama' => 'Siti Aisyah', 'jenis_obat' => 'racikan'],
            ['nomor_antrian' => 3, 'nama' => 'Andi Wijaya', 'jenis_obat' => 'jadi'],
            ['nomor_antrian' => 4, 'nama' => 'Rina Kartika', 'jenis_obat' => 'racikan'],
            ['nomor_antrian' => 5, 'nama' => 'Dedi Supriyadi', 'jenis_obat' => 'jadi'],
        ];


        foreach ($data as $item) {
            Pasien::create($item);
        }

        return response()->json(['success' => true, 'message' => 'Dummy data berhasil dibuat.']);
    }
}
