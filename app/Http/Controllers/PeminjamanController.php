<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    // Melihat daftar/katalog alat yang tersedia
  public function katalogAlat() { $alat = Alat::with('kategori') ->where('stok', '>', 0) ->get(); $kategoriList = Kategori::pluck('nama_kategori'); return view('peminjam.katalog', compact('alat', 'kategoriList')); }

    public function ajukanPeminjaman(Request $request)
{
    $request->validate([
        'tanggal_kembali' => 'required|date|after:today',
        'alat_id' => 'required|array|min:1',
        'alat_id.*' => 'exists:alat,id',
        'jumlah' => 'required|array',
    ]);

    DB::beginTransaction();

    try {

        // Buat data utama peminjaman
        $peminjaman = Peminjaman::create([
            'user_id' => auth()->id(),
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => $request->tanggal_kembali,
            'status' => 'diajukan',
        ]);

        // Simpan setiap alat yang dipilih
        foreach ($request->alat_id as $alatId) {

            DetailPinjam::create([
                'peminjaman_id' => $peminjaman->id,
                'alat_id' => $alatId,
                'jumlah' => $request->jumlah[$alatId] ?? 1,
            ]);

        }

        DB::commit();

        return redirect()
            ->route('peminjam.peminjaman')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.');

    } catch (\Exception $e) {

        DB::rollBack();

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
    }
    }

    // Melihat riwayat peminjaman user yang sedang login
    public function riwayatPeminjaman()
{
    $peminjaman = Peminjaman::with('detailPinjam.alat')
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

    return view('peminjam.index', compact('peminjaman'));
}
}