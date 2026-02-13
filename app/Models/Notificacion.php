<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // IMPORTANTE: Para la papelera
use App\Models\User;
use App\Models\Etiqueta;

class Notificacion extends Model
{
    use HasFactory, SoftDeletes; // Agregamos SoftDeletes aquí

    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'asunto',
        'mensaje',
        'ruta_archivo_pdf',
        'fecha_lectura',
        'ip_lectura',
        'mtc_id',       
        'leido_en_mtc', 
        
        // NUEVOS CAMPOS (Para el diseño Gmail)
        'etiqueta_id',  // ID de la carpeta personalizada
        'es_destacado', // Booleano para la estrella de "Importante"
    ];

    /**
     * Casteamos 'es_destacado' a booleano para que PHP lo trate como true/false
     */
    protected $casts = [
        'es_destacado' => 'boolean',
        'fecha_lectura' => 'datetime',
    ];

    /**
     * Relación: Una notificación pertenece a un Usuario (Ciudadano)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Una notificación pertenece a una Etiqueta (Carpeta)
     * Si es NULL, está en la bandeja principal "Recibidos".
     */
    public function etiqueta()
    {
        return $this->belongsTo(Etiqueta::class);
    }

    /**
     * Atributo para verificar si está leído de forma sencilla
     */
    public function getEstaLeidoAttribute()
    {
        return !is_null($this->fecha_lectura);
    }
}