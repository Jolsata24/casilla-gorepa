<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMtcAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Definir una lista blanca de IPs (Opcional pero recomendado)
        // Estas son IPs de ejemplo, debes pedir las oficiales al MTC
        $allowedIps = ['190.119.xxx.xxx', '200.48.xxx.xxx', '127.0.0.1'];

        if (!in_array($request->ip(), $allowedIps) && app()->environment('production')) {
             return response()->json(['message' => 'Unauthorized IP'], 403);
        }

        // 2. Verificar un Token de Seguridad (Header)
        // El MTC suele enviar un token o firma en los headers.
        // Si no lo tienen, puedes acordar un "Shared Secret".
        $tokenMtc = $request->header('X-MTC-Authorization');
        
        if ($tokenMtc !== env('MTC_SHARED_SECRET')) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }

        return $next($request);
    }
}