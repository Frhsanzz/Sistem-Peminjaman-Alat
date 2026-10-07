<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('pengembalian_detail', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pengembalian_id')->constrained('pengembalian')->cascadeOnDelete();
        $table->foreignId('alat_id')->constrained('alat');
        $table->unsignedInteger('jumlah_dipinjam');
        $table->unsignedInteger('jumlah_rusak')->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('pengembalian_detail');
}
};
