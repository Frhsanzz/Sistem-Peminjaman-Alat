<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckActiveUser
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Admin tetap dapat masuk agar bisa mengaktifkan user kembali
        if ($user->role !== 'admin' && !$user->is_active) {

            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Akun Anda telah dinonaktifkan oleh admin.'
                );
        }

        return $next($request);
    }
}