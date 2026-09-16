<?php

namespace Database\Seeders;

use App\Models\Pengembalian;
use Illuminate\Database\Seeder;

class PengembalianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pengembalian = [
            [
                'peminjaman_id' => 1,
                'tgl_kembali' => '2026-07-04',
                'kondisi_kembali' => 'Lengkap dan Berfungsi Baik',
                'denda' => 0,
                'petugas_id' => 2, 
            ],
            [
                'peminjaman_id' => 2,
                'tgl_kembali' => '2026-07-05',
                'kondisi_kembali' => 'lengkap dan Berfungsi Baik',
                'denda' => 0,
                'petugas_id' => 2, 
            ],
            [
                'peminjaman_id' => 3,
                'tgl_kembali' => '2026-07-09',
                'kondisi_kembali' => 'Lengkap, Casing sedikit Tergores',
                'denda' => 30000,
                'petugas_id' => 2, 
            ],
        ];

        foreach ($pengembalian as $kembali) {
            Pengembalian::create($kembali);
        }
    }
}
