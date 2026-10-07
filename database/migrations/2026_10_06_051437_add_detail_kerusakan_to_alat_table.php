<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            // Aman dijalankan walau kolom sudah ada
            if (! Schema::hasColumn('alat', 'jumlah_rusak')) {
                $table->unsignedInteger('jumlah_rusak')->default(0)->after('stok');
            }
            if (! Schema::hasColumn('alat', 'keterangan_rusak')) {
                $table->text('keterangan_rusak')->nullable();
            }
            if (! Schema::hasColumn('alat', 'tanggal_rusak')) {
                $table->date('tanggal_rusak')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            // jumlah_rusak sengaja tidak di-drop karena dipakai fitur lain
            $table->dropColumn(['keterangan_rusak', 'tanggal_rusak']);
        });
    }
};