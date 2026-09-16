<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class UpdateGambarAlatSeeder extends Seeder
{
    /**
     * Jalankan seeder ini dengan:
     *   php artisan db:seed --class=UpdateGambarAlatSeeder
     *
     * Pastikan file gambar sudah ada di storage/app/public/alat/
     * sebelum menjalankan seeder ini.
     */
    public function run(): void
    {
        // 1. Mouse
        Alat::where('nama_alat', 'like', '%mouse%')
            ->update(['gambar' => 'mouse.jpg']);

        // 2. Adapter HDMI to VGA
        Alat::where('nama_alat', 'like', '%HDMI to VGA%')
            ->update(['gambar' => 'adapter-hdmi-to-vga.jpg']);

        // 3. Mini PC Intel NUC 11
        Alat::where('nama_alat', 'like', '%NUC 11%')
            ->update(['gambar' => 'mini-pc-intel-nuc-11.jpg']);

        // 4. Tang Crimping RJ45/RJ11
        Alat::where('nama_alat', 'like', '%Tang Crimping%')
            ->update(['gambar' => 'tang-crimping-rj45-rj11.jpg']);

        // 5. Tool Kit Elektronik MAXPOWER
        // Kalau alat ini belum ada di tabel, updateOrCreate akan membuat baris baru.
        // Sesuaikan kategori_id & stok sesuai kebutuhanmu.
        $kategoriPerkakas = Kategori::where('nama_kategori', 'like', '%Perkakas%')->first();

        Alat::updateOrCreate(
            ['nama_alat' => 'Tool Kit Elektronik MAXPOWER'],
            [
                'kategori_id'    => $kategoriPerkakas?->id,
                'status_kondisi' => 'Baik',
                'stok'           => 5,
                'gambar'         => 'tool-kit-elektronik-maxpower.jpg',
            ]
        );

        $this->command->info('Gambar alat berhasil diperbarui.');
    }
}