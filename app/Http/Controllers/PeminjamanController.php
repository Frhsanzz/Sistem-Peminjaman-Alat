<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * KATALOG ALAT
     */
    public function katalogAlat()
    {
        $alat = Alat::with('kategori')
            ->whereRaw('(stok - jumlah_rusak) > 0')
            ->get();

        return view('peminjam.katalog', compact('alat'));
    }

    /**
     * AJUKAN PEMINJAMAN DARI KATALOG
     */
   public function ajukanPeminjaman(Request $request)
{
    $request->validate([
        'alat_id'           => ['required', 'array', 'min:1'],
        'alat_id.*'         => ['required', 'exists:alat,id'],
        'jumlah'            => ['nullable', 'array'],
        'jumlah.*'          => ['nullable', 'integer', 'min:1'],
        'tanggal_kembali'   => ['nullable', 'array'],
        'tanggal_kembali.*' => ['nullable', 'date', 'after_or_equal:today'],
    ], [
        'alat_id.required' => 'Centang "Pilih" pada minimal satu alat.',
        'alat_id.min'      => 'Centang "Pilih" pada minimal satu alat.',
    ]);

    // Setiap alat yang dipilih wajib punya tanggal kembali
    foreach ($request->alat_id as $alatId) {
        if (empty($request->tanggal_kembali[$alatId])) {
            return back()
                ->withInput()
                ->with('error', 'Isi tanggal kembali untuk setiap alat yang dipilih.');
        }
    }

    DB::beginTransaction();

    try {
        // Kelompokkan alat berdasarkan tanggal kembali
        $perTanggal = collect($request->alat_id)
            ->groupBy(fn ($id) => $request->tanggal_kembali[$id]);

        foreach ($perTanggal as $tanggal => $daftarAlatId) {

            // STATUS = DIAJUKAN, stok BELUM dikurangi
            $peminjaman = Peminjaman::create([
                'user_id'          => auth()->id(),
                'tgl_pinjam'       => now()->toDateString(),
                'tgl_kembali_plan' => $tanggal,
                'status'           => 'diajukan',
            ]);

            foreach ($daftarAlatId as $alatId) {
                $jumlah = (int) ($request->jumlah[$alatId] ?? 1);
                $alat   = Alat::findOrFail($alatId);

                $stokBaik = (int) $alat->stok - (int) $alat->jumlah_rusak;

                if ($jumlah > $stokBaik) {
                    throw new \Exception("Stok {$alat->nama_alat} tidak mencukupi.");
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id'       => $alatId,
                    'jumlah'        => $jumlah,
                ]);
            }
        }

        DB::commit();

        return redirect()
            ->route('peminjam.peminjaman')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim dan menunggu persetujuan petugas.');

    } catch (\Throwable $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->with('error', 'Pengajuan gagal: ' . $e->getMessage());
    }
}

    /**
     * RIWAYAT PEMINJAMAN
     */
    public function riwayatPeminjaman()
    {
        $peminjaman = Peminjaman::with([
            'detailPinjam.alat'
        ])
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

        return view('peminjam.riwayat', compact('peminjaman'));
    }

    /**
     * AJUKAN PENGEMBALIAN
     */
    public function ajukanPengembalian($id)
    {
        $peminjaman = Peminjaman::with('detailPinjam')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if (!in_array($peminjaman->status, [
            'dipinjamkan',
            'telat'
        ])) {
            return back()->with(
                'error',
                'Peminjaman belum dapat diajukan untuk pengembalian.'
            );
        }

        /*
         * Jika menggunakan kolom permintaan_pengembalian
         */
        $peminjaman->permintaan_pengembalian = true;
        $peminjaman->save();

        return back()->with(
            'success',
            'Permintaan pengembalian berhasil dikirim ke petugas.'
        );
    }

    /**
     * BATALKAN PENGAJUAN
     */
    public function cancelPeminjaman($id)
    {
        $peminjaman = Peminjaman::where('user_id', auth()->id())
            ->findOrFail($id);

        if ($peminjaman->status !== 'diajukan') {
            return back()->with(
                'error',
                'Peminjaman yang sudah diproses tidak dapat dibatalkan.'
            );
        }

        $peminjaman->detailPinjam()->delete();
        $peminjaman->delete();

        return back()->with(
            'success',
            'Pengajuan peminjaman berhasil dibatalkan.'
        );
    }
}