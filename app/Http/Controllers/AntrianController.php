<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pasien;
use Illuminate\Http\Request;

class AntrianController extends Controller
{
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

    public function updateStatus(Request $request, $id)
    {
        \Log::info('Request Data:', $request->all());
        \Log::info('Pasien ID:', ['id' => $id]);

        $pasien = Pasien::find($id);
        if (!$pasien) {
            return response()->json(['error' => 'Pasien tidak ditemukan'], 404);
        }

        $pasien->status = $request->status;
        $pasien->keterangan = $request->keterangan;
        $pasien->save();

        return response()->json(['success' => true]);
    }



    public function periksaStatusPasien()
    {
        $pasiens = Pasien::where('keterangan', 'menunggu')->get();

        foreach ($pasiens as $pasien) {
            if ($pasien->estimasi_waktu_selesai && Carbon::now()->greaterThanOrEqualTo($pasien->estimasi_waktu_selesai)) {
                $pasien->keterangan = 'selesai';
                $pasien->save();
            }
        }

        return response()->json(['message' => 'Status pasien diperiksa dan diperbarui.']);
    }

}
