<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AntrianController;

// Route untuk menampilkan halaman antrian
Route::get('/', [AntrianController::class, 'index'])->name('antrian.index');



Route::post('/update-status/{id}', [AntrianController::class, 'updateStatus'])->name('update.status');
Route::get('/periksa-status', [AntrianController::class, 'periksaStatusPasien'])->name('periksa.status');

Route::post('/update-status/{id}', [AntrianController::class, 'updateStatus']);
