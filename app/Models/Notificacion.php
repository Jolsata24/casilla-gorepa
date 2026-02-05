<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    // Permitimos que estos campos se llenen
    protected $fillable = [
        'user_id',
        'asunto',
        'mensaje',
        'ruta_archivo_pdf',
        'fecha_lectura',
        'ip_lectura'
    ];

    // Helper para saber si ya fue leído
    public function getEstaLeidoAttribute()
    {
        return !is_null($this->fecha_lectura);
    }
}