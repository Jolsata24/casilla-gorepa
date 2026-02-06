<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si NO está logueado O NO es admin...
        if (!auth()->check() || !auth()->user()->is_admin) {
            // ...lo expulsamos con un Error 403 (Prohibido)
            abort(403, 'ACCESO DENEGADO: Solo personal autorizado del GOREPA.');
        }

        // Si es admin, pase usted.
        return $next($request);
    }
}
