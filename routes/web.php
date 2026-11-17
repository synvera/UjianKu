<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaUjianController;
use App\Http\Controllers\SoalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/cek-nilai', [AdminController::class, 'cekNilai'])->name('cek-nilai');

        Route::get('/mapel', [AdminController::class, 'daftarMapel'])->name('mapel.index');
        Route::post('/mapel', [AdminController::class, 'storeMapel'])->name('mapel.store');
        Route::delete('/mapel/{mapel}', [AdminController::class, 'destroyMapel'])->name('mapel.destroy');
        Route::post('/mapel/{mapel}/generate-kode', [AdminController::class, 'generateKode'])->name('mapel.generate-kode');

        Route::get('/mapel/{mapel}/soal', [SoalController::class, 'index'])->name('soal.index');
        Route::post('/mapel/{mapel}/soal', [SoalController::class, 'store'])->name('soal.store');
        Route::post('/mapel/{mapel}/soal/import-txt', [SoalController::class, 'importTxt'])->name('soal.import-txt');
        Route::post('/soal/upload-gambar', [SoalController::class, 'uploadGambar'])->name('soal.upload-gambar');
        Route::get('/soal/{soal}/edit', [SoalController::class, 'edit'])->name('soal.edit');
        Route::put('/soal/{soal}', [SoalController::class, 'update'])->name('soal.update');
        Route::delete('/soal/{soal}', [SoalController::class, 'destroy'])->name('soal.destroy');
        
    });
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/kode', [SiswaUjianController::class, 'showKodeForm'])->name('kode');
        Route::post('/kode', [SiswaUjianController::class, 'verifikasiKode'])->name('kode.verifikasi');
        Route::get('/ujian/{ujianSiswa}', [SiswaUjianController::class, 'halamanUjian'])->name('ujian');
        Route::post('/ujian/{ujianSiswa}/simpan-jawaban', [SiswaUjianController::class, 'simpanJawaban'])->name('ujian.simpan-jawaban');
        Route::post('/ujian/{ujianSiswa}', [SiswaUjianController::class, 'submitUjian'])->name('ujian.submit');
        Route::get('/hasil/{ujianSiswa}', [SiswaUjianController::class, 'halamanHasil'])->name('hasil');
    });
});