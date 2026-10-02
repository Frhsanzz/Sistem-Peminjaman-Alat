<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        // Query log aktivitas
        $query = DB::table('log_aktivitas')
            ->leftJoin(
                'peminjaman',
                'log_aktivitas.peminjaman_id',
                '=',
                'peminjaman.id'
            )
            ->leftJoin(
                'users',
                'log_aktivitas.user_id',
                '=',
                'users.id'
            )
            ->select(
                'log_aktivitas.id',
                'users.name as user_name',
                'log_aktivitas.aktivitas',
                'peminjaman.status as status',
                'log_aktivitas.created_at'
            )
            ->orderByDesc('log_aktivitas.created_at');

        // Filter status
        if ($status !== 'all') {
            $query->where('peminjaman.status', $status);
        }

        $logs = $query
            ->paginate(20)
            ->withQueryString();

        // Jumlah berdasarkan status peminjaman
        $counts = [
            'diajukan' => DB::table('peminjaman')
                ->where('status', 'diajukan')
                ->count(),

            'dipinjamkan' => DB::table('peminjaman')
                ->where('status', 'dipinjamkan')
                ->count(),

            'dikembalikan' => DB::table('peminjaman')
                ->where('status', 'dikembalikan')
                ->count(),

            'telat' => DB::table('peminjaman')
                ->where('status', 'telat')
                ->count(),
        ];

        return view(
            'admin.log-aktivitas.index',
            compact('logs', 'counts', 'status')
        );
    }
}