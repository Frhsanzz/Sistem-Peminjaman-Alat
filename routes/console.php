<?php

use App\Models\Peminjaman;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Tandai peminjaman yang lewat tanggal kembali sebagai "telat".
 * Hanya yang masih berstatus dipinjamkan.
 */
Schedule::call(function () {
    Peminjaman::where('status', 'dipinjamkan')
        ->whereDate('tgl_kembali_plan', '<', today())
        ->update(['status' => 'telat']);
})->daily();