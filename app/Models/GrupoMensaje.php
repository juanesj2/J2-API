<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoMensaje extends Model
{
    protected $fillable = [
        'grupo_id',
        'usuario_id',
        'mensaje',
        'imagen',
    ];

    /**
     * Relación con el Grupo al que pertenece el mensaje
     */
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    /**
     * Relación con el Usuario que envió el mensaje
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
