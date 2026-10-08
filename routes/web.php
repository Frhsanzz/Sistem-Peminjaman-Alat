<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\AuthController;
use App\Models\Pengembalian;
use App\Models\User;


// =====================================================
// ROUTE UMUM
// =====================================================

Route::get('/', function () {
    return redirect('/login');
});


// =====================================================
// LOGIN
// =====================================================

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');


// Dashboard umum
Route::get('/dashboard', function () {

    $role = auth()->user()->role;

    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($role === 'petugas') {
        return redirect()->route('petugas.peminjaman.index');
    }

    if ($role === 'peminjam') {
        return redirect()->route('peminjam.katalog');
    }

    abort(403, 'Role tidak dikenali.');

})->middleware('auth')->name('dashboard');

// =====================================================
// ADMIN
// =====================================================

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // =================================================
        // DASHBOARD ADMIN
        // =================================================

        Route::get('/dashboard', [AdminController::class, 'index'])
            ->name('dashboard');

       


        // =================================================
        // CRUD ALAT
        // =================================================

        Route::get('/alat', [AdminController::class, 'indexAlat'])
            ->name('alat.index');

        Route::get('/alat/create', [AdminController::class, 'createAlat'])
            ->name('alat.create');

        Route::get('/alat/{id}', [AdminController::class, 'showAlat'])
        ->whereNumber('id')
        ->name('alat.show');

        Route::post('/alat', [AdminController::class, 'storeAlat'])
            ->name('alat.store');

        Route::get('/alat/{id}/edit', [AdminController::class, 'editAlat'])
            ->name('alat.edit');

        Route::put('/alat/{alat}', [AdminController::class, 'updateAlat'])
            ->name('alat.update');

        Route::delete('/alat/{id}', [AdminController::class, 'destroyAlat'])
            ->name('alat.destroy');

        Route::post('/alat/{id}/perbaiki', [AdminController::class, 'perbaikiAlat'])
            ->whereNumber('id')
            ->name('alat.perbaiki');
        // =================================================
        // CRUD USER
        // =================================================

        Route::get('/users', [AdminController::class, 'indexUser'])
            ->name('user.index');

        Route::get('/users/create', [AdminController::class, 'createUser'])
            ->name('user.create');

        Route::post('/users', [AdminController::class, 'storeUser'])
            ->name('user.store');

        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])
            ->name('user.edit');

        Route::put('/users/{id}', [AdminController::class, 'updateUser'])
            ->name('user.update');

        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])
            ->name('user.destroy');

        Route::patch('/users/{id}/toggle-status', [AdminController::class, 'toggleStatusUser'])
            ->name('user.toggle-status');
   


        // =================================================
        // CRUD KATEGORI
        // =================================================

        Route::get('/kategori', [AdminController::class, 'indexKategori'])
            ->name('kategori.index');

        Route::get('/kategori/create', [AdminController::class, 'createKategori'])
            ->name('kategori.create');

        Route::post('/kategori', [AdminController::class, 'storeKategori'])
            ->name('kategori.store');

        Route::get('/kategori/{id}/edit', [AdminController::class, 'editKategori'])
            ->name('kategori.edit');

        Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])
            ->name('kategori.update');

        Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])
            ->name('kategori.destroy');
            


// =================================================
// PEMINJAMAN
// =================================================

Route::get('/peminjaman', [AdminController::class, 'indexPeminjaman'])
    ->name('peminjaman.index');

Route::get('/peminjaman/create', [AdminController::class, 'createPeminjaman'])
    ->name('peminjaman.create');

Route::post('/peminjaman', [AdminController::class, 'storePeminjaman'])
    ->name('peminjaman.store');

Route::get('/peminjaman/{id}/edit', [AdminController::class, 'editPeminjaman'])
    ->name('peminjaman.edit');

Route::put('/peminjaman/{id}', [AdminController::class, 'updatePeminjaman'])
    ->name('peminjaman.update');

Route::delete('/peminjaman/{id}', [AdminController::class, 'destroyPeminjaman'])
    ->name('peminjaman.destroy');

        // =====================================================
// PENGEMBALIAN ADMIN
// =====================================================

Route::get('/pengembalian', [PengembalianController::class, 'index'])
    ->name('pengembalian.index');

Route::get('/pengembalian/create', [PengembalianController::class, 'create'])
    ->name('pengembalian.create');

Route::post('/pengembalian', [PengembalianController::class, 'store'])
    ->name('pengembalian.store');

Route::get('/pengembalian/{id}', [PengembalianController::class, 'show'])
    ->name('pengembalian.show');

Route::get('/pengembalian/{id}/edit', [PengembalianController::class, 'edit'])
    ->name('pengembalian.edit');

Route::put('/pengembalian/{id}', [PengembalianController::class, 'update'])
    ->name('pengembalian.update');

Route::delete('/pengembalian/{id}', [PengembalianController::class, 'destroy'])
    ->name('pengembalian.destroy');

// =====================================================
// LOG AKTIVITAS ADMIN
// =====================================================

Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])
    ->name('log-aktivitas');
    });    

// =====================================================
// PETUGAS
// =====================================================
Route::middleware(['auth', 'check.active', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {

        // Persetujuan Peminjaman
        Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])
            ->name('peminjaman.index');

        Route::post('/peminjaman/{id}/setujui', [PetugasController::class, 'setujuPeminjaman'])
            ->name('peminjaman.setujui');
        //
        Route::post('/peminjaman/{id}/tolak', [PetugasController::class, 'tolakPeminjaman'])
            ->name('peminjaman.tolak');

        // Pemantauan Pengembalian
        Route::get('/pengembalian', [PetugasController::class, 'indexPengembalian'])
            ->name('pengembalian.index');
        
        Route::get('/pengembalian/{id}/form', [PetugasController::class, 'formPengembalian'])
            ->name('pengembalian.form');

        // Proses Pengembalian
        Route::post('/pengembalian/{id}', [PetugasController::class, 'prosesPengembalian'])
            ->name('pengembalian.proses');

        Route::get('/cetaklaporan', [PetugasController::class, 'cekLaporan'])
            ->name('cetaklaporan.index');
            
        Route::get('/cetaklaporan/print', [PetugasController::class, 'printLaporan'])
            ->name('cetaklaporan.print');
    });



Route::middleware(['auth', 'check.active', 'role:peminjam'])
    ->prefix('peminjam')
    ->name('peminjam.')
    ->group(function () {

    // Katalog alat
    Route::get('/katalog', [PeminjamanController::class, 'katalogAlat'])
        ->name('katalog');

    // Ajukan peminjaman dari katalog
    Route::post('/peminjaman/ajukan', [PeminjamanController::class, 'ajukanPeminjaman'])
        ->name('peminjaman.ajukan');

    // Riwayat peminjaman saya
    Route::get('/peminjaman', [PeminjamanController::class, 'riwayatPeminjaman'])
        ->name('peminjaman');

    // Batalkan pengajuan
    Route::delete('/peminjaman/{id}/cancel', [PeminjamanController::class, 'cancelPeminjaman'])
        ->name('peminjaman.cancel');

    // Ajukan pengembalian
    Route::post('/peminjaman/{id}/kembalikan', [PeminjamanController::class, 'ajukanPengembalian'])
        ->name('peminjaman.kembalikan');
});
// =====================================================
// GUEST / BELUM LOGIN
// =====================================================

Route::middleware(['guest'])->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');

});