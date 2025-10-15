<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Pastikan user sudah login
    if (! Auth::check()) {
        return redirect('/login'); 
    }

    $user = Auth::user();
    
    // 2. Lakukan pengecekan role
    // Peran user yang sedang login harus ada di dalam array $roles yang diperlukan
    // Asumsi kolom role di tabel user Anda bernama 'role'
    if (! in_array($user->role, $roles)) {
        // Jika peran user TIDAK diizinkan, kembalikan error atau redirect
        return redirect('/home')->with('error', 'Anda tidak memiliki akses ke halaman Admin.');
        // ATAU
        // abort(403, 'Akses Ditolak');
    }

    // 3. Jika peran sesuai, lanjutkan permintaan
    return $next($request);
    }
}
