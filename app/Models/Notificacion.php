<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Importamos el modelo User

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'asunto',
        'mensaje',
        'ruta_archivo_pdf',
        'fecha_lectura',
        'ip_lectura',
    ];

    /**
     * Relación: Una notificación pertenece a un Usuario (Ciudadano)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Atributo para verificar si está leído de forma sencilla
     */
    public function getEstaLeidoAttribute()
    {
        return !is_null($this->fecha_lectura);
    }
}