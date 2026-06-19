<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login dan rolenya sesuai dengan route yang dituju
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            // Jika tidak sesuai, kembalikan ke halaman login atau dashboard masing-masing
            return redirect('/'); 
        }

        return $next($request);
    }
}