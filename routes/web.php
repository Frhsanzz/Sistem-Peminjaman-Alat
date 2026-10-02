<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamanController;
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

        Route::post('/alat', [AdminController::class, 'storeAlat'])
            ->name('alat.store');

        Route::get('/alat/{id}/edit', [AdminController::class, 'editAlat'])
            ->name('alat.edit');

        Route::put('/alat/{alat}', [AdminController::class, 'updateAlat'])
            ->name('alat.update');

        Route::delete('/alat/{id}', [AdminController::class, 'destroyAlat'])
            ->name('alat.destroy');


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

// Daftar pengembalian
Route::get('/pengembalian', function () {

    $keyword = request('q');

    $pengembalian = Pengembalian::with([
        'peminjaman.user',
        'petugas'
    ])
    ->when($keyword, function ($query) use ($keyword) {
        $query->whereHas('peminjaman.user', function ($q) use ($keyword) {
            $q->where('name', 'like', '%' . $keyword . '%');
        });
    })
    ->latest()
    ->paginate(10)
    ->withQueryString();

    return view(
        'admin.pengembalian.index',
        compact('pengembalian', 'keyword')
    );

})->name('pengembalian.index');


// =====================================================
// TAMBAH PENGEMBALIAN
// =====================================================

Route::get('/pengembalian/create', function () {

    $peminjaman = \App\Models\Peminjaman::with('user')
        ->where('status', 'dipinjamkan')
        ->latest()
        ->get();

    $petugas = User::whereIn('role', ['petugas', 'admin'])
        ->orderBy('name')
        ->get();

    return view(
        'admin.pengembalian.create',
        compact('peminjaman', 'petugas')
    );

})->name('pengembalian.create');


// Simpan pengembalian
Route::post('/pengembalian', function (Request $request) {

    $request->validate([
    'peminjaman_id' => 'required|exists:peminjaman,id',
    'tgl_kembali' => 'required|date',
    'kondisi_kembali' => 'required|string',
    'denda' => 'required|integer|min:0',
    'petugas_id' => 'required|exists:users,id',
]);

    Pengembalian::create([
        'peminjaman_id' => $request->peminjaman_id,
        'tgl_kembali' => $request->tgl_kembali,
        'kondisi_kembali' => $request->kondisi_kembali,
        'denda' => $request->denda,
        'petugas_id' => $request->petugas_id,
    ]);

    return redirect()
        ->route('admin.pengembalian.index')
        ->with('success', 'Data pengembalian berhasil ditambahkan.');

})->name('pengembalian.store');


// =====================================================
// EDIT PENGEMBALIAN
// =====================================================

Route::get('/pengembalian/{id}/edit', function ($id) {

    $pengembalian = Pengembalian::with([
        'peminjaman.user',
        'petugas'
    ])->findOrFail($id);

    $petugas = User::whereIn('role', ['petugas', 'admin'])
        ->orderBy('name')
        ->get();

    return view(
        'admin.pengembalian.edit',
        compact('pengembalian', 'petugas')
    );

})->name('pengembalian.edit');


// Update pengembalian
Route::put('/pengembalian/{id}', function (Request $request, $id) {

    $request->validate([
        'tgl_kembali' => 'required|date',
        'kondisi_kembali' => 'required|string',
        'denda' => 'required|integer|min:0',
        'petugas_id' => 'required|exists:users,id',
    ]);

    $pengembalian = Pengembalian::findOrFail($id);

    $pengembalian->update([
        'tgl_kembali' => $request->tgl_kembali,
        'kondisi_kembali' => $request->kondisi_kembali,
        'denda' => $request->denda,
        'petugas_id' => $request->petugas_id,
    ]);

    return redirect()
        ->route('admin.pengembalian.index')
        ->with('success', 'Data pengembalian berhasil diperbarui.');

})->name('pengembalian.update');


// =====================================================
// HAPUS PENGEMBALIAN
// =====================================================

Route::delete('/pengembalian/{id}', function ($id) {

    $pengembalian = Pengembalian::findOrFail($id);

    $pengembalian->delete();

    return redirect()
        ->route('admin.pengembalian.index')
        ->with('success', 'Data pengembalian berhasil dihapus.');

})->name('pengembalian.destroy');

 Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])
    ->name('log-aktivitas');
    });


// =====================================================
// PETUGAS
// =====================================================
Route::middleware(['auth', 'check.active', 'role:petugas'])->group(function () {
    // route petugas


        // Persetujuan Peminjaman
        Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])
            ->name('peminjaman.index');

        // Setujui Peminjaman
        Route::post('/peminjaman/{id}/setujui', [PetugasController::class, 'setujuPeminjaman'])
            ->name('peminjam.setujui');

        // Tolak Peminjaman
        Route::post('/peminjaman/{id}/tolak', [PetugasController::class, 'tolakPeminjaman'])
            ->name('peminjam.tolak');

        // Pemantauan Pengembalian
        Route::get('/pengembalian', [PetugasController::class, 'indexPengembalian'])
            ->name('pengembalian.index');

        // Proses Pengembalian
        Route::post('/pengembalian/{id}', [PetugasController::class, 'prosesPengembalian'])
            ->name('pengembalian.proses');

        Route::get('/cetaklaporan', [PetugasController::class, 'cekLaporan'])
            ->name('cetaklaporan.index');
            
        Route::get('/cetaklaporan/print', [PetugasController::class, 'printLaporan'])
            ->name('cetaklaporan.print');
    });



Route::middleware(['auth', 'check.active', 'role:peminjam'])->group(function () {
    // route peminjam


        // Dashboard
        Route::get('/dashboard', [PeminjamanController::class, 'dashboard'])
            ->name('dashboard');

        // Katalog
        Route::get('/katalog', [PeminjamanController::class, 'katalogAlat'])
            ->name('katalog');

        // Ajukan peminjaman
        Route::post('/peminjaman/ajukan', [PeminjamanController::class, 'ajukanPeminjaman'])
            ->name('peminjaman.ajukan');

        // Peminjaman saya
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