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
                // Add 60 minutes for 'racikan' and 30 minutes for 'jadi'
                $data->estimasi_waktu_selesai = $this->calculateEstimatedEndTime($startTime, $data->jenis_obat);
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
     * Menghitung estimasi waktu selesai berdasarkan jenis obat.
     */
    private function calculateEstimatedEndTime(Carbon $startTime, string $jenisObat): Carbon
    {
        $duration = ($jenisObat === 'racikan') ? 60 : 30;
        return $startTime->addMinutes($duration);
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
