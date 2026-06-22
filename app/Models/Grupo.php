<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Grupo extends Model
{
    // Hacemos referencia a la tabla desafios de la base de datos
    protected $table = 'grupo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo_invitacion',
        'ubicacion_icono',
        'creado_por',
    ];

    //**************************************************************/
    //**************************************************************/
    //               Relaciones con la base de datos
    //**************************************************************/
    //**************************************************************/

    // Esta es una relacion con el modelo de User
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'grupo_usuarios', 'grupo_id', 'usuario_id')
                    ->withTimestamps()
                    ->withPivot('rol');
    }

    /**
     * Relación: Un grupo tiene muchos mensajes
     */
    public function mensajes()
    {
        return $this->hasMany(GrupoMensaje::class, 'grupo_id');
    }
}
