<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
        $aktivitasTerbaru = collect();

        if (auth()->check() && auth()->user()->role === 'admin') {
            $aktivitasTerbaru = \Illuminate\Support\Facades\DB::table('log_aktivitas')
                ->leftJoin('users', 'log_aktivitas.user_id', '=', 'users.id')
                ->select(
                    'log_aktivitas.aktivitas',
                    'log_aktivitas.created_at',
                    'users.name as nama_user',
                    'users.role as role_user'
                )
                ->orderByDesc('log_aktivitas.created_at')
                ->limit(10)
                ->get();
        }

        $view->with('aktivitasTerbaru', $aktivitasTerbaru);
    });
}
}
