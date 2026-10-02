<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    // Melihat daftar/katalog alat yang tersedia
  public function katalogAlat() { $alat = Alat::with('kategori') ->where('stok', '>', 0) ->get(); $kategoriList = Kategori::pluck('nama_kategori'); return view('peminjam.katalog', compact('alat', 'kategoriList')); }

    public function ajukanPeminjaman(Request $request)
{
    $request->validate([
        'alat_id' => 'required|array|min:1',
        'alat_id.*' => 'exists:alat,id',

        'jumlah' => 'required|array',

        'tanggal_kembali' => 'required|array',

        'tanggal_kembali.*' => 'required|date|after:today',
    ]);

    DB::beginTransaction();

    try {

        // Ambil alat yang dipilih
        $alatIds = $request->alat_id;

        // Gunakan tanggal kembali dari alat pertama yang dipilih
        $tanggalKembali = null;

        foreach ($alatIds as $alatId) {
            if (!empty($request->tanggal_kembali[$alatId])) {
                $tanggalKembali = $request->tanggal_kembali[$alatId];
                break;
            }
        }

        if (!$tanggalKembali) {
            return back()
                ->withInput()
                ->with('error', 'Silakan pilih tanggal kembali.');
        }

        // Buat peminjaman utama
        $peminjaman = Peminjaman::create([
            'user_id' => auth()->id(),
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali_plan' => $tanggalKembali,
            'status' => 'diajukan',
        ]);

        // Simpan detail alat
        foreach ($alatIds as $alatId) {

            $jumlah = (int) ($request->jumlah[$alatId] ?? 1);

            DetailPinjam::create([
                'peminjaman_id' => $peminjaman->id,
                'alat_id' => $alatId,
                'jumlah' => $jumlah,
            ]);
        }

        // Catat aktivitas SEKALI untuk satu pengajuan
        DB::table('log_aktivitas')->insert([
            'user_id' => auth()->id(),
            'peminjaman_id' => $peminjaman->id,
            'aktivitas' => 'Mengajukan peminjaman alat.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::commit();

        return redirect()
            ->route('peminjam.peminjaman')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
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