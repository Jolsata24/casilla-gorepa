<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// --- IMPORTACIONES FALTANTES ---
use Illuminate\Support\Facades\Event; // Para usar Event::listen
use Illuminate\Auth\Events\Login;     // Para detectar el evento Login
use App\Models\Bitacora;              // Para guardar en la base de datos
// -------------------------------

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
        // Escuchar el evento de Login exitoso
        Event::listen(Login::class, function ($event) {
            Bitacora::create([
                'user_id'    => $event->user->id,
                'accion'     => 'LOGIN_EXITOSO',
                'ip_address' => request()->ip(),
                'detalles'   => 'El usuario inició sesión en el sistema.',
            ]);
        });
    }
}