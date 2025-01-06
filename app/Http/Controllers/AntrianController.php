<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pasien;
use Illuminate\Http\Request;

class AntrianController extends Controller
{
    /**
     * Menampilkan halaman antrian pasien.
     */
    public function index()
    {
        $pasien = Pasien::all();
        foreach ($pasien as $data) {
            if ($data->waktu_mulai) {
                $startTime = Carbon::parse($data->waktu_mulai);
                $data->estimasi_waktu_selesai = $data->jenis_obat === 'racikan'
                    ? $startTime->addMinutes(60)
                    : $startTime->addMinutes(30);
            }
        }
        return view('antrian.index', compact('pasien'));
    }

    /**
     * Mengupdate status pasien.
     */

     public function updateStatus($id)
{
    // Cari data berdasarkan ID
    $order = Order::find($id); // Ganti 'Order' dengan nama model Anda

    if ($order) {
        $order->keterangan = 'selesai'; // Update kolom 'keterangan'
        $order->save(); // Simpan perubahan

        return response()->json([
            'message' => 'Status berhasil diperbarui menjadi selesai.'
        ]);
    }

    return response()->json([
        'message' => 'Data tidak ditemukan.'
    ], 404);
}

    /**
     * Memeriksa status pasien dan memperbarui keterangan.
     */

     public function periksaStatusPasien()
     {
         \Log::info("Memulai pengecekan status pasien...");

         // Ambil semua pasien dengan keterangan 'menunggu'
         $pasiens = Pasien::where('keterangan', 'menunggu')->get();

         foreach ($pasiens as $pasien) {
             \Log::info("Memeriksa pasien ID {$pasien->id}, Estimasi Waktu Selesai: {$pasien->estimasi_waktu_selesai}");

             // Periksa apakah estimasi waktu selesai sudah terlewati
             if ($pasien->estimasi_waktu_selesai && Carbon::now()->greaterThanOrEqualTo($pasien->estimasi_waktu_selesai)) {
                 $pasien->keterangan = 'selesai';
                 $pasien->save();

                 if ($pasien->wasChanged('keterangan')) {
                     \Log::info("Keterangan pasien ID {$pasien->id} berhasil diperbarui menjadi 'selesai'.");
                 } else {
                     \Log::error("Keterangan pasien ID {$pasien->id} gagal diperbarui.");
                 }
             }
         }

         return response()->json(['message' => 'Status pasien diperiksa dan diperbarui.']);
     }

    public function store(Request $request)
{
    $request->validate([
        'nomor_resep' => 'required|unique:pasiens',
        'nama' => 'required',
        'jenis_obat' => 'required|in:jadi,racikan',
        'waktu_mulai' => 'required|date',
        'estimasi_waktu_selesai' => 'required|date|after:waktu_mulai',
    ]);

    Pasien::create($request->all());
}

}
