<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('log_aktivitas', function (Blueprint $table) {
        $table->foreignId('peminjaman_id')->nullable()->after('id')->constrained('peminjaman')->nullOnDelete();
    });
}
};
