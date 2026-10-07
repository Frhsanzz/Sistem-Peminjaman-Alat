<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    /**
     * Menampilkan daftar pengembalian
     */
    public function index(Request $request)
    {
        $keyword = $request->q;

        $pengembalian = Pengembalian::with([
                'peminjaman.user',
                'peminjaman.detailPinjam.alat',
                'details',
                'petugas',
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
    }


    /**
     * Form tambah pengembalian
     */
    public function create(Request $request)
    {
        $daftar = Peminjaman::with('user')
            ->whereIn('status', ['dipinjamkan', 'telat'])
            ->doesntHave('pengembalian')
            ->latest()
            ->get();

        $dipilih = $request->peminjaman_id
            ? Peminjaman::with('user', 'detailPinjam.alat')
                ->find($request->peminjaman_id)
            : null;

        return view(
            'admin.pengembalian.create',
            compact('daftar', 'dipilih')
        );
    }


    /**
     * Menyimpan data pengembalian
     */
    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id'     => 'required|exists:peminjaman,id',
            'tgl_kembali'       => 'required|date',

            'items'             => 'nullable|array',
            'items.*'           => 'nullable|integer|min:0',

            // Denda diinput manual
            'denda_terlambat'   => 'nullable|integer|min:0',
            'denda_kerusakan'   => 'nullable|integer|min:0',

            'catatan_kerusakan' => 'nullable|string|max:500',
        ]);

        $peminjaman = Peminjaman::with([
            'detailPinjam.alat',
            'pengembalian'
        ])->findOrFail($request->peminjaman_id);

        dd([
    'denda_terlambat' => $request->denda_terlambat,
    'denda_kerusakan' => $request->denda_kerusakan,
    ]);

        // Cegah pengembalian dua kali
        if ($peminjaman->pengembalian) {
            return back()
                ->withInput()
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        DB::transaction(function () use ($request, $peminjaman) {

            $totalRusak = 0;
            $totalPinjam = 0;
            $baris = [];

            /*
             * Menghitung jumlah barang rusak
             */
            foreach ($peminjaman->detailPinjam as $d) {

                $rusak = (int) $request->input(
                    "items.{$d->id}",
                    0
                );

                // Jangan sampai jumlah rusak lebih besar
                // dari jumlah yang dipinjam
                $rusak = max(
                    0,
                    min($rusak, $d->jumlah)
                );

                $totalRusak += $rusak;
                $totalPinjam += $d->jumlah;

                $baris[] = [
                    $d,
                    $rusak
                ];
            }


            /*
             * Menentukan kondisi pengembalian
             */
            $kondisi = $totalRusak === 0
                ? 'baik'
                : (
                    $totalRusak >= $totalPinjam
                        ? 'rusak'
                        : 'sebagian_rusak'
                );


            /*
             * DENDA DIINPUT MANUAL
             */

            $dendaTelat = (int) (
                $request->denda_terlambat ?? 0
            );

            $dendaRusak = (int) (
                $request->denda_kerusakan ?? 0
            );

            /*
             * Total denda
             */
            $totalDenda = $dendaTelat + $dendaRusak;


            /*
             * Simpan pengembalian
             */
            $pengembalian = Pengembalian::create([
                'peminjaman_id'     => $peminjaman->id,
                'tgl_kembali'       => $request->tgl_kembali,

                'kondisi_kembali'   => $kondisi,

                'denda'             => $totalDenda,
                'denda_terlambat'   => $dendaTelat,
                'denda_kerusakan'   => $dendaRusak,

                'catatan_kerusakan' => $request->catatan_kerusakan,

                'petugas_id'        => auth()->id(),
            ]);


            /*
             * Simpan detail pengembalian
             * dan kembalikan stok alat
             */
            foreach ($baris as [$d, $rusak]) {

                $pengembalian->details()->create([
                    'alat_id'         => $d->alat_id,
                    'jumlah_dipinjam' => $d->jumlah,
                    'jumlah_rusak'    => $rusak,
                ]);


                /*
                 * STOK ALAT BERTAMBAH
                 */
                if ($d->alat) {

                    $d->alat->stok += $d->jumlah;

                    $d->alat->jumlah_rusak += $rusak;

                    $d->alat->save();
                }
            }


            /*
             * Status peminjaman berubah menjadi dikembalikan
             *
             * Data peminjaman TIDAK dihapus.
             */
            $peminjaman->update([
                'status' => 'dikembalikan'
            ]);
        });


        return redirect()
            ->route('admin.pengembalian.index')
            ->with(
                'success',
                'Data pengembalian berhasil ditambahkan.'
            );
    }


    /**
     * Menampilkan detail pengembalian
     */
    public function show($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.detailPinjam.alat',
            'details',
            'petugas',
        ])->findOrFail($id);

        return view(
            'admin.pengembalian.show',
            compact('pengembalian')
        );
    }


    /**
     * Form edit pengembalian
     */
    public function edit($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.detailPinjam.alat',
            'details',
            'petugas',
        ])->findOrFail($id);

        return view(
            'admin.pengembalian.edit',
            compact('pengembalian')
        );
    }


    /**
     * Update data pengembalian
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_kembali'       => 'required|date',

            // Denda tetap manual
            'denda_terlambat'   => 'nullable|integer|min:0',
            'denda_kerusakan'   => 'nullable|integer|min:0',

            'catatan_kerusakan' => 'nullable|string|max:500',
        ]);


        $pengembalian = Pengembalian::with('peminjaman')
            ->findOrFail($id);


        /*
         * Denda diambil dari input Admin/Petugas
         */
        $dendaTelat = (int) (
            $request->denda_terlambat ?? 0
        );

        $dendaRusak = (int) (
            $request->denda_kerusakan ?? 0
        );


        /*
         * Total denda
         */
        $totalDenda = $dendaTelat + $dendaRusak;


        /*
         * Update data
         */
        $pengembalian->update([
            'tgl_kembali'       => $request->tgl_kembali,

            'denda_terlambat'   => $dendaTelat,
            'denda_kerusakan'   => $dendaRusak,

            'denda'             => $totalDenda,

            'catatan_kerusakan' => $request->catatan_kerusakan,
        ]);


        return redirect()
            ->route('admin.pengembalian.index')
            ->with(
                'success',
                'Data pengembalian berhasil diperbarui.'
            );
    }


    /**
     * Menghapus data pengembalian
     */
    public function destroy($id)
    {
        $pengembalian = Pengembalian::with([
            'details.alat',
            'peminjaman'
        ])->findOrFail($id);


        DB::transaction(function () use ($pengembalian) {

            /*
             * Jika data pengembalian dihapus,
             * stok yang sebelumnya ditambahkan
             * harus dikurangi kembali.
             */
            foreach ($pengembalian->details as $d) {

                if ($d->alat) {

                    $d->alat->stok = max(
                        0,
                        $d->alat->stok - $d->jumlah_dipinjam
                    );

                    $d->alat->jumlah_rusak = max(
                        0,
                        $d->alat->jumlah_rusak - $d->jumlah_rusak
                    );

                    $d->alat->save();
                }
            }


            /*
             * Kembalikan status peminjaman
             */
            if ($pengembalian->peminjaman) {

                $pengembalian->peminjaman->update([
                    'status' => 'dipinjamkan'
                ]);
            }


            /*
             * Hapus data pengembalian
             */
            $pengembalian->delete();
        });


        return redirect()
            ->route('admin.pengembalian.index')
            ->with(
                'success',
                'Data pengembalian berhasil dihapus.'
            );
    }
}