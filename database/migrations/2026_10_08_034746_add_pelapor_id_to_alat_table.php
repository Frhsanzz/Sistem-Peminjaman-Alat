<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('alat', 'pelapor_id')) {
            Schema::table('alat', function (Blueprint $table) {
                $table->unsignedBigInteger('pelapor_id')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('alat', 'pelapor_id')) {
            Schema::table('alat', function (Blueprint $table) {
                $table->dropColumn('pelapor_id');
            });
        }
    }
};