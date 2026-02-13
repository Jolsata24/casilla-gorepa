<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    protected $fillable = ['user_id', 'nombre', 'color'];

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }
}