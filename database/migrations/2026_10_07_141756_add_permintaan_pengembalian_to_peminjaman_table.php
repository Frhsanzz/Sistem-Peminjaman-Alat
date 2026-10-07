<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('peminjaman', 'permintaan_pengembalian')) {
            Schema::table('peminjaman', function (Blueprint $table) {
                $table->boolean('permintaan_pengembalian')
                    ->default(false)
                    ->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('peminjaman', 'permintaan_pengembalian')) {
            Schema::table('peminjaman', function (Blueprint $table) {
                $table->dropColumn('permintaan_pengembalian');
            });
        }
    }
};