<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateRMS
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta está inactiva.']);
        }

        return $next($request);
    }
}
