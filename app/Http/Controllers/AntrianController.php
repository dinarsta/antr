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
     * Mengupdate status pasien berdasarkan ID.
     */
    public function updateStatus($id, Request $request)
    {
        \Log::info("Memperbarui status pasien ID: $id");
        $pasien = Pasien::find($id);

        if ($pasien) {
            $pasien->keterangan = $request->input('keterangan', 'selesai');
            $pasien->save();

            return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
        }

        \Log::error("Pasien dengan ID $id tidak ditemukan.");
        return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan.'], 404);
    }

    /**
     * Memeriksa status pasien dan memperbarui jika waktu selesai telah terlampaui.
     */
    public function periksaStatusPasien()
    {
        \Log::info("Memulai pengecekan status pasien...");
        $pasiens = Pasien::where('keterangan', 'menunggu')->get();

        foreach ($pasiens as $pasien) {
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
}
