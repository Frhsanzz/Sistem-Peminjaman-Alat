<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // =========================
    // PERSETUJUAN PEMINJAMAN
    // =========================
    public function indexPeminjaman(Request $request)
{
    $query = Peminjaman::with([
        'user',
        'detailPinjam.alat'
    ])
    ->where('status', 'diajukan');

    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    $peminjaman = $query
        ->latest()
        ->get();

    return view('petugas.peminjaman.index', compact('peminjaman'));
}

    // =========================
// CEK LAPORAN
// =========================
public function cekLaporan(Request $request)
{
    $dari = $request->input(
        'dari',
        now()->startOfMonth()->format('Y-m-d')
    );

    $sampai = $request->input(
        'sampai',
        now()->format('Y-m-d')
    );

    // Data peminjaman pada periode yang dipilih
    $query = Peminjaman::with([
        'user',
        'pengembalian',
        'detailPinjam.alat'
    ])
    ->whereDate('tgl_pinjam', '>=', $dari)
    ->whereDate('tgl_pinjam', '<=', $sampai);

    // Total pengajuan
    $totalPeminjaman = (clone $query)->count();

    // Total disetujui
    $totalDisetujui = (clone $query)
        ->whereIn('status', ['dipinjamkan', 'telat', 'dikembalikan'])
        ->count();

    // Total ditolak
    $totalDitolak = (clone $query)
        ->where('status', 'ditolak')
        ->count();

    // Total selesai
    $totalSelesai = (clone $query)
        ->whereIn('status', ['selesai', 'dikembalikan'])
        ->count();

    // Total denda
    $totalDenda = (clone $query)
        ->get()
        ->sum(function ($peminjaman) {
            return $peminjaman->pengembalian->denda ?? 0;
        });

    // Alat paling sering dipinjam
    $alatPalingSering = DetailPinjam::with('alat')
        ->whereHas('peminjaman', function ($q) use ($dari, $sampai) {
            $q->whereDate('tgl_pinjam', '>=', $dari)
              ->whereDate('tgl_pinjam', '<=', $sampai);
        })
        ->get()
        ->groupBy('alat_id')
        ->map(function ($details) {
            $detail = $details->first();

            return (object) [
                'nama_alat' => $detail->alat->nama_alat ?? '-',
                'total_dipinjam' => $details->sum('jumlah'),
            ];
        })
        ->sortByDesc('total_dipinjam')
        ->take(5);

    // Riwayat peminjaman
    $riwayat = (clone $query)
        ->latest('tgl_pinjam')
        ->paginate(10)
        ->withQueryString();

    $ringkasan = [
        'total_peminjaman' => $totalPeminjaman,
        'total_disetujui' => $totalDisetujui,
        'total_ditolak' => $totalDitolak,
        'total_selesai' => $totalSelesai,
        'total_denda' => $totalDenda,
    ];

    return view('petugas.cetaklaporan.index', compact(
        'dari',
        'sampai',
        'ringkasan',
        'alatPalingSering',
        'riwayat'
    ));
}
// =========================
// CETAK LAPORAN (HALAMAN PRINT)
// =========================
public function printLaporan(Request $request)
{
    $dari = $request->input('dari', now()->startOfMonth()->format('Y-m-d'));
    $sampai = $request->input('sampai', now()->format('Y-m-d'));

    $query = Peminjaman::with([
        'user',
        'pengembalian',
        'detailPinjam.alat'
    ])
    ->whereDate('tgl_pinjam', '>=', $dari)
    ->whereDate('tgl_pinjam', '<=', $sampai);

    $ringkasan = [
        'total_peminjaman' => (clone $query)->count(),
        'total_disetujui'  => (clone $query)->whereIn('status', ['dipinjamkan', 'telat', 'dikembalikan'])->count(),
        'total_ditolak'    => (clone $query)->where('status', 'ditolak')->count(),
        'total_selesai'    => (clone $query)->whereIn('status', ['selesai', 'dikembalikan'])->count(),
        'total_denda'      => (clone $query)->get()->sum(function ($p) {
            return $p->pengembalian->denda ?? 0;
        }),
    ];

    $alatPalingSering = DetailPinjam::with('alat')
        ->whereHas('peminjaman', function ($q) use ($dari, $sampai) {
            $q->whereDate('tgl_pinjam', '>=', $dari)
              ->whereDate('tgl_pinjam', '<=', $sampai);
        })
        ->get()
        ->groupBy('alat_id')
        ->map(function ($details) {
            return (object) [
                'nama_alat'      => $details->first()->alat->nama_alat ?? '-',
                'total_dipinjam' => $details->sum('jumlah'),
            ];
        })
        ->sortByDesc('total_dipinjam')
        ->take(5)
        ->values();

    // Semua data, tanpa paginate
    $riwayat = (clone $query)->latest('tgl_pinjam')->get();

    return view('petugas.cetaklaporan.print', compact(
        'dari',
        'sampai',
        'ringkasan',
        'alatPalingSering',
        'riwayat'
    ));
}


    // =========================
    // SETUJUI PEMINJAMAN
    // =========================
   
public function setujuPeminjaman($id)
{
    DB::beginTransaction();

    try {

        $peminjaman = Peminjaman::with('detailPinjam.alat')
            ->lockForUpdate()
            ->findOrFail($id);

        if ($peminjaman->status !== 'diajukan') {
            DB::rollBack();
            return back()->with(
                'error',
                'Peminjaman ini sudah diproses.'
            );
        }

        foreach ($peminjaman->detailPinjam as $detail) {

            $alat = Alat::lockForUpdate()
                ->findOrFail($detail->alat_id);

            $stokBaik = (int) $alat->stok - (int) $alat->jumlah_rusak;

            if ($stokBaik < $detail->jumlah) {
                throw new \Exception(
                    "Stok {$alat->nama_alat} tidak mencukupi."
                );
            }

            /*
             * BARU DI SINI STOK BERKURANG
             */
            $alat->stok -= $detail->jumlah;
            $alat->save();
        }

        $peminjaman->update([
            'status' => 'dipinjamkan',
        ]);

        DB::commit();

        return back()->with(
            'success',
            'Peminjaman berhasil disetujui.'
        );

    } catch (\Throwable $e) {

        DB::rollBack();

        return back()->with(
            'error',
            'Gagal menyetujui peminjaman: ' . $e->getMessage()
        );
    }
}

 
// =========================
// TOLAK PEMINJAMAN
// =========================
public function tolakPeminjaman($id)
{
    DB::beginTransaction();

    try {
        $peminjaman = Peminjaman::findOrFail($id);

        // Pastikan hanya pengajuan yang bisa ditolak
        if ($peminjaman->status !== 'diajukan') {
            return redirect()->back()->with(
                'error',
                'Peminjaman ini sudah diproses dan tidak dapat ditolak lagi.'
            );
        }

        // Ubah status menjadi ditolak
        $peminjaman->update([
            'status' => 'ditolak'
        ]);

        // Catat aktivitas
        DB::table('log_aktivitas')->insert([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menolak peminjaman alat.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::commit();

        return redirect()->back()->with(
            'success',
            'Peminjaman berhasil ditolak.'
        );

    } catch (\Exception $e) {
        DB::rollback();

        return redirect()->back()->with(
            'error',
            'Terjadi kesalahan: ' . $e->getMessage()
        );
    }
}


// =========================
// PEMANTAUAN PENGEMBALIAN
// =========================
public function indexPengembalian()
{
    $hariIni = now('Asia/Jakarta')->startOfDay();

    // Tandai menjadi telat hanya jika:
    // 1. Masih dipinjamkan
    // 2. Tanggal rencana kembali sudah lewat hari ini
    Peminjaman::where('status', 'dipinjamkan')
        ->whereDate('tgl_kembali_plan', '<', $hariIni->toDateString())
        ->update([
            'status' => 'telat'
        ]);

    $peminjaman = Peminjaman::with([
        'user',
        'detailPinjam.alat',
        'pengembalian',
    ])
    ->whereIn('status', ['dipinjamkan', 'telat'])
    ->orderByDesc('permintaan_pengembalian')
    ->orderBy('tgl_kembali_plan')
    ->get();

    return view(
        'petugas.pengembalian.index',
        compact('peminjaman')
    );
}

// =========================
// PROSES PENGEMBALIAN
// =========================

public function prosesPengembalian(Request $request, $peminjamanId)
{
    $request->validate([
        'kondisi_kembali' => 'required|string',

        'denda' => [
            'nullable',
            'numeric',
            'min:0'
        ],

        'jumlah_rusak' => [
            'required',
            'array'
        ],

        'jumlah_rusak.*' => [
            'required',
            'integer',
            'min:0'
        ],

        'keterangan_rusak' => [
            'nullable',
            'array'
        ],

        'keterangan_rusak.*' => [
            'nullable',
            'string',
            'max:500'
        ],
    ]);

    DB::beginTransaction();

    try {

        /*
         * Ambil peminjaman dan kunci row
         * agar tidak diproses dua kali.
         */
        $peminjaman = Peminjaman::with('detailPinjam.alat')
            ->lockForUpdate()
            ->findOrFail($peminjamanId);


        /*
         * Hanya peminjaman aktif yang boleh dikembalikan.
         */
        if (!in_array($peminjaman->status, [
            'dipinjamkan',
            'telat'
        ])) {

            DB::rollBack();

            return back()->with(
                'error',
                'Peminjaman ini sudah dikembalikan atau tidak dapat diproses.'
            );
        }


        /*
         * Pastikan belum pernah dikembalikan.
         */
        if ($peminjaman->pengembalian) {

            DB::rollBack();

            return back()->with(
                'error',
                'Peminjaman ini sudah memiliki data pengembalian.'
            );
        }


        /*
         * SIMPAN DATA PENGEMBALIAN
         */
        $pengembalian = Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => now(),
            'kondisi_kembali' => $request->kondisi_kembali,
            'denda' => (int) ($request->denda ?? 0),
            'petugas_id' => auth()->id(),
        ]);


        /*
         * PROSES SETIAP ALAT
         */
        foreach ($peminjaman->detailPinjam as $detail) {

            $alat = Alat::lockForUpdate()
                ->findOrFail($detail->alat_id);

            $jumlahDipinjam = (int) $detail->jumlah;

            $jumlahRusak = (int) (
                $request->jumlah_rusak[$detail->alat_id] ?? 0
            );


            /*
             * Validasi jumlah rusak
             */
            if ($jumlahRusak > $jumlahDipinjam) {

                throw new \Exception(
                    "Jumlah rusak {$alat->nama_alat} "
                    . "tidak boleh lebih dari "
                    . "{$jumlahDipinjam} unit."
                );
            }


            /*
             * Hitung alat yang masih baik
             */
            $jumlahBaik = $jumlahDipinjam - $jumlahRusak;


            /*
             * ALAT BAIK KEMBALI KE STOK
             */
            if ($jumlahBaik > 0) {

                $alat->stok += $jumlahDipinjam;
            }


            /*
             * ALAT RUSAK MASUK JUMLAH_RUSAK
             */
            if ($jumlahRusak > 0) {

                $alat->jumlah_rusak += $jumlahRusak;


                $keterangan = trim(
                    (string) (
                        $request->keterangan_rusak[$detail->alat_id]
                        ?? ''
                    )
                );


                if ($keterangan !== '') {

                    $alat->keterangan_rusak =
                        $alat->keterangan_rusak
                        ? $alat->keterangan_rusak
                            . "\n"
                            . $keterangan
                        : $keterangan;
                }


                $alat->tanggal_rusak = now()->toDateString();
                $alat->pelapor_id = auth()->id();
            }


            /*
             * UPDATE KONDISI ALAT
             */
            $alat->syncKondisi();


            /*
             * SIMPAN DETAIL PENGEMBALIAN
             */
            $pengembalian->details()->create([
                'alat_id' => $alat->id,
                'jumlah_dipinjam' => $jumlahDipinjam,
                'jumlah_rusak' => $jumlahRusak,
            ]);


            /*
             * RIWAYAT KERUSAKAN
             */
            if ($jumlahRusak > 0) {

                $alat->riwayat()->create([
                    'keterangan' =>
                        "Dilaporkan rusak saat pengembalian "
                        . "({$jumlahRusak} unit)",
                ]);
            }
        }


        /*
         * PEMINJAMAN SELESAI
         */
        $peminjaman->update([
            'status' => 'dikembalikan',
            'permintaan_pengembalian' => false,
        ]);


        /*
         * LOG AKTIVITAS
         */
        DB::table('log_aktivitas')->insert([
            'user_id' => auth()->id(),
            'peminjaman_id' => $peminjaman->id,
            'aktivitas' => 'Memproses pengembalian alat.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        DB::commit();

        return back()->with(
            'success',
            'Pengembalian berhasil diproses. Stok alat telah diperbarui.'
        );

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with(
            'error',
            'Pengembalian gagal diproses: ' . $e->getMessage()
        );
    }
}


}