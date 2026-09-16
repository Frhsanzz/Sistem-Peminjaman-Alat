<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        schema::create('detail_pinjam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjam_id')->constained('peminjaman')->cascadeOnDelete();
            $table->foreigId('alat_id')->constrained('alat')->cascadeOnDelete();
            $table->integer('jumlah')->default(1);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
