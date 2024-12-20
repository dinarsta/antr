<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AntrianController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('antrian.index');
});


Route::post('/antrian/panggil', [AntrianController::class, 'panggilBerikutnya'])->name('antrian.panggil');


Route::get('/antrian', [AntrianController::class, 'index'])->name('antrian.index');
Route::post('/antrian/panggil', [AntrianController::class, 'panggil'])->name('antrian.panggil');

// Opsional: Route untuk mengisi data dummy
Route::get('/antrian/seed', [AntrianController::class, 'seedDummyData']);
Route::get('/antrian/next', [AntrianController::class, 'panggilBerikutnya'])->name('antrian.next');

Route::post('/antrian/panggil', [AntrianController::class, 'panggil'])->name('antrian.panggil');

Route::get('/', [AntrianController::class, 'index']); // Route to view the queue
Route::post('/panggil-berikutnya', [AntrianController::class, 'panggilBerikutnya']); // Route to call the next patient
Route::post('/panggil', [AntrianController::class, 'panggil']); // Route to call a patient from the queue
Route::post('/seed-dummy-data', [AntrianController::class, 'seedDummyData']); // Route to create dummy data
