<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Reporte extends Model
{

    // Hacemos referencia a la tabla reportes de la base de datos
    protected $table = 'reportes';

    // Estas son las columnas que laravel va a poder modificar de la base de datos
    protected $fillable = [
        'usuario_id',
        'foto_id',
        'motivo',
    ];

    // Esta es una relacion con el modelo de User
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Esta es una relacion con el modelo de Fotografia
    public function foto()
    {
        return $this->belongsTo(Fotografia::class, 'foto_id');
    }
}
