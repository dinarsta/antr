<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pasien;
use Illuminate\Http\Request;

class AntrianController extends Controller
{
    /**
     * Menampilkan daftar antrian.
     */
    public function index()
    {
        // Mengambil semua data pasien
        $pasien = Pasien::all();

        // Hitung estimasi_waktu_selesai berdasarkan jenis_obat
        foreach ($pasien as $data) {
            if ($data->waktu_mulai) {
                // Menghitung estimasi waktu selesai
                $startTime = \Carbon\Carbon::parse($data->waktu_mulai);
                if ($data->jenis_obat == 'racikan') {
                    $data->estimasi_waktu_selesai = $startTime->addMinutes(60); // 60 menit untuk racikan
                } else {
                    $data->estimasi_waktu_selesai = $startTime->addMinutes(30); // 30 menit untuk jadi
                }
            }
        }

        // Mengirim data ke view
        return view('antrian.index', compact('pasien'));
    }


    /**
     * Memanggil pasien berikutnya dalam antrian.
     */
    public function panggilBerikutnya()
    {
        // Ambil pasien pertama yang belum dipanggil dan memiliki jenis obat 'racikan'
        $pasien = Pasien::whereNull('waktu_mulai')
                        ->where('jenis_obat', 'racikan')
                        ->orderBy('nomor_resep')
                        ->first();

        // Jika tidak ada pasien dengan jenis obat 'racikan', ambil pasien dengan jenis obat lainnya
        if (!$pasien) {
            $pasien = Pasien::whereNull('waktu_mulai')
                            ->where('jenis_obat', '!=', 'racikan')
                            ->orderBy('nomor_resep')
                            ->first();
        }

        if ($pasien) {
            // Tandai pasien sebagai dipanggil
            $pasien->waktu_mulai = now();

            // Tentukan estimasi waktu selesai berdasarkan jenis obat
            $pasien->estimasi_waktu_selesai = $pasien->jenis_obat === 'racikan'
                ? now()->addMinutes(60) // Estimasi 60 menit untuk racikan
                : now()->addMinutes(30); // Estimasi 30 menit untuk jadi

            $pasien->save();

            return response()->json([
                'success' => true,
                'pasien' => [
                    'id' => $pasien->id,
                    'nomor_resep' => $pasien->nomor_resep,
                    'nama' => $pasien->nama,
                    'jenis_obat' => $pasien->jenis_obat,
                    'waktu_mulai' => $pasien->waktu_mulai->format('H:i:s'),
                    'estimasi_waktu_selesai' => $pasien->estimasi_waktu_selesai->format('H:i:s'),
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada pasien dalam antrian.',
        ]);
    }

    /**
     * Update status pasien berdasarkan nomor resep.
     */

     public function updateStatus(Request $request, $id)
{
    $pasien = Pasien::find($id);
    if (!$pasien) {
        return response()->json(['error' => 'Pasien tidak ditemukan'], 404);
    }

    // Update status dan keterangan
    $pasien->status = $request->status;
    $pasien->keterangan = $request->keterangan;
    $pasien->estimasi_waktu_selesai = $request->estimasi_selesai;
    $pasien->save();

    return response()->json(['success' => true]);
}


    /**
     * Update status pasien menjadi selesai berdasarkan ID.
     */
    public function updateStatusToSelesai(Request $request, $id)
    {
        // Validasi data
        $validated = $request->validate([
            'status' => 'required|string|in:Menunggu,Selesai',
            'estimasi_waktu_selesai' => 'nullable|date',
        ]);

        // Cari pasien berdasarkan ID
        $pasien = Pasien::find($id);

        if ($pasien) {
            $pasien->status = $validated['status'];

            if (isset($validated['estimasi_waktu_selesai'])) {
                $pasien->estimasi_waktu_selesai = Carbon::parse($validated['estimasi_waktu_selesai']);
            }

            $pasien->save();

            return response()->json(['success' => true, 'message' => 'Status pasien berhasil diperbarui.']);
        }

        return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan.']);
    }
}
