<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_verified === false) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Votre compte n\'a pas encore été vérifié. Veuillez cliquer sur le lien de vérification envoyé par email.');
        }

        return $next($request);
    }
}
