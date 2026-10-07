<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('pengembalian', function (Blueprint $table) {
        $table->unsignedInteger('denda_terlambat')->default(0)->after('denda');
        $table->unsignedInteger('denda_kerusakan')->default(0)->after('denda_terlambat');
        $table->text('catatan_kerusakan')->nullable()->after('denda_kerusakan');
    });
}

public function down(): void
{
    Schema::table('pengembalian', function (Blueprint $table) {
        $table->dropColumn(['denda_terlambat', 'denda_kerusakan', 'catatan_kerusakan']);
    });
}
};
