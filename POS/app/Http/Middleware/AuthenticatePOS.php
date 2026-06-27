<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePOS
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para continuar.');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Tu cuenta está inactiva.');
        }

        if (!in_array($user->role, \App\Models\User::POS_ROLES)) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'No tienes permisos para acceder al POS.');
        }

        return $next($request);
    }
}
