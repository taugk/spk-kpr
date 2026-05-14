<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            // Redirect berdasarkan URL yang diakses
            if ($request->is('debitur/*')) {
                return redirect()->route('debitur.login')->with('error', 'Silakan login sebagai debitur terlebih dahulu.');
            }
            
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Jika tidak ada parameter role, berarti hanya cek sudah login
        if (empty($roles)) {
            return $next($request);
        }

        // Cek apakah role user sesuai dengan yang diizinkan
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}