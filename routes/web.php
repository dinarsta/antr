<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AntrianController;

// Route untuk menampilkan halaman antrian
Route::get('/', [AntrianController::class, 'index'])->name('antrian.index');

// Route untuk memperbarui status pasien
Route::post('/update-status/{id}', [AntrianController::class, 'updateStatus'])->name('update.status');

// Route untuk memeriksa status pasien
Route::get('/periksa-status', [AntrianController::class, 'periksaStatusPasien'])->name('periksa.status');

// Route untuk memperbarui estimasi waktu
Route::post('/update-estimasi/{id}', [AntrianController::class, 'updateEstimasi']);
