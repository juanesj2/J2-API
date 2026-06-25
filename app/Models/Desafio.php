<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Desafio extends Model
{
    use HasFactory;

    protected $table = 'enfoca_desafios';

    //**************************************************************/
    //**************************************************************/
    //               Relaciones con la base de datos
    //**************************************************************/
    //**************************************************************/

    // Esta es una relacion con el modelo de User
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'enfoca_desafio_usuario', 'desafio_id', 'usuario_id')
                    ->withTimestamps()
                    // Pivot es una tabla intermedia que relaciona dos tablas muchos a muchos 
                    // que laravel crea automaticamente y con whichPivot podemos acceder a los campos de esa tabla
                    ->withPivot('created_at');
    }

}
