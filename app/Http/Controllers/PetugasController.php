<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // =========================
    // PERSETUJUAN PEMINJAMAN
    // =========================
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjaman = Peminjaman::with([
            'user',
            'detailPinjam.alat'
        ])
        ->where('status', 'diajukan')
        ->when($search, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->get();

        return view(
            'petugas.peminjaman.index',
            compact('peminjaman', 'search')
        );
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
        ->where('status', 'disetujui')
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
    // SETUJUI PEMINJAMAN
    // =========================
    public function setujuPeminjaman($id)
{
    DB::beginTransaction();

    try {
        $peminjaman = Peminjaman::with('detailPinjam')
            ->findOrFail($id);

        // Cek stok terlebih dahulu
        foreach ($peminjaman->detailPinjam as $detail) {
            $alat = Alat::findOrFail($detail->alat_id);

            if ($alat->stok < $detail->jumlah) {
                throw new \Exception(
                    'Stok alat "' . $alat->nama_alat . '" tidak mencukupi.'
                );
            }
        }

        // Kurangi stok
        foreach ($peminjaman->detailPinjam as $detail) {
            $alat = Alat::findOrFail($detail->alat_id);

            $alat->stok -= $detail->jumlah;
            $alat->save();
        }

        // Ubah status menjadi disetujui
        $peminjaman->update([
            'status' => 'dipinjamkan'
        ]);

        DB::commit();

        return redirect()->back()->with(
            'success',
            'Peminjaman berhasil disetujui dan stok alat dikurangi.'
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
        $peminjaman = Peminjaman::with([
            'user',
            'detailPinjam.alat'
        ])
        ->where('status', 'Dipinjam')
        ->latest()
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
            'denda' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjam')
                ->findOrFail($peminjamanId);

            Pengembalian::create([
                'peminjamanId' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);

            // Ubah status peminjaman menjadi selesai
            $peminjaman->update([
                'status' => 'selesai'
            ]);

            // Kembalikan stok alat
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);

                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();

            return redirect()->back()->with(
                'success',
                'Pengembalian berhasil dicatat dan stok dipulihkan.'
            );

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with(
                'error',
                'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }
}
