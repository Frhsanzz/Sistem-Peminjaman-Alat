<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('alat', function (Blueprint $table) {
        $table->unsignedInteger('jumlah_rusak')->default(0)->after('stok');
    });
}

public function down(): void
{
    Schema::table('alat', function (Blueprint $table) {
        $table->dropColumn('jumlah_rusak');
    });
}
};
