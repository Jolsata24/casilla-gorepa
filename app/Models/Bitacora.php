<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacoras';

    protected $fillable = [
        'user_id',
        'accion',       // Ej: LOGIN, DESCARGA_PDF, INTENTO_FALLIDO
        'ip_address',
        'detalles',     // Información extra (JSON o texto)
    ];

    /**
     * Relación: Cada registro de bitácora pertenece a un usuario (o es null si es anónimo)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper estático para registrar eventos rápidamente desde cualquier lugar
     */
    public static function registrar($accion, $detalles = null)
    {
        return self::create([
            'user_id'    => auth()->id(), // Detecta automáticamente al usuario actual
            'accion'     => $accion,
            'ip_address' => request()->ip(),
            'detalles'   => $detalles,
        ]);
    }
}