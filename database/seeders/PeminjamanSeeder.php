<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $peminjaman = [
            [
                'user_id' => 3,
                'tgl_pinjam' => '2026-07-01',
                'tgl_kembali_plan' => '2026-07-04',
                'status' => 'dikembalikan',
            ],
            [
                'user_id' => 4,
                'tgl_pinjam' => '2026-07-02',
                'tgl_kembali_plan' => '2026-07-05',
                'status' => 'dikembalikan',
            ],
            [
                'user_id' => 5,
                'tgl_pinjam' => '2026-07-03',
                'tgl_kembali_plan' => '2026-07-06',
                'status' => 'telat',
            ],
            [
                'user_id' => 3,
                'tgl_pinjam' => '2026-07-08',
                'tgl_kembali_plan' => '2026-07-11',
                'status' => 'dipinjamkan',
            ],
            [
                'user_id' => 4,
                'tgl_pinjam' => '2026-07-09',
                'tgl_kembali_plan' => '2026-07-12',
                'status' => 'diajukan',
            ],
        ];

        foreach ($peminjaman as $pinjam) {
            Peminjaman::create($pinjam);
        }
    }
}
