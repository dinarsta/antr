<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AntrianController;




Route::get('/', [AntrianController::class, 'index']);
Route::post('/antrian/panggil-berikutnya', [AntrianController::class, 'panggilBerikutnya']);
Route::put('/antrian/update-status', [AntrianController::class, 'updateStatus']);
Route::put('/pasien/{id}/update-status', [AntrianController::class, 'updateStatusById']);

Route::post('/update-status/{id}', [AntrianController::class, 'updateStatusById']);
Route::post('/update-status/{id}', [AntrianController::class, 'updateStatusToSelesai']);
Route::post('/update-status/{id}', [AntrianController::class, 'updateStatusById']);
Route::post('/update-status/{id}', [AntrianController::class, 'updateStatusById']);


Route::post('/update-status/{id}', [AntrianController::class, 'updateStatus']);
