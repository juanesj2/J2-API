<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Fotografia extends Model
{

    // Desactivamos las marcas de tiempo que usa laravel para que no intente crear 
    // las columnas created_at y updated_at en la base de datos
    public $timestamps = false;

    // Campos que se pueden rellenar de forma masiva
    protected $fillable = [
        'titulo',
        'descripcion',
        'vetada',
    ];

    // Hacemos referencia a la la tabla fotografias de la base de datos
    protected $table = 'fotografias';

    //**************************************************************/
    //**************************************************************/
    //               Relaciones con la base de datos
    //**************************************************************/
    //**************************************************************/

    // Esta es una relacion con el modelo de User
    public function user()
    {
        // Con esto estamos haciendo una relacion hacia el modelo User mediante la columna usuario_id
        // Usamos belongto() porque una foto solo puede pertenecer a un usuario
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Esta es una relacion con el modelo de likes
    public function likes()
    {
        // Con esto estamos haciendo una relacion hacia el modelo Likes mediante la columna fotografia_id
        // Con hasMany() indicamos que una fotografia puede tener muchos likes
        return $this->hasMany(Likes::class, 'fotografia_id');
    }

    // Esta es una relacion con el modelo de comentarios
    public function comentarios()
    {
        // Con esto estamos haciendo una relacion hacia el modelo comentarios mediante la columna fotografia_id
        // Con hasMany() indicamos que una fotografia puede tener muchos comentarios
        return $this->hasMany(Comentarios::class, 'fotografia_id');
    }

    //**************************************************************/
    //**************************************************************/
    //                    Conteo de los likes
    //**************************************************************/
    //**************************************************************/

    // Funcion para contar los likes
    public function likesCount()
    {
        return $this->likes()->count();
    }

    //**************************************************************/
    //**************************************************************/
    //                    Conteo de los comentarios
    //**************************************************************/
    //**************************************************************/

    // Funcion para contar los comentarios
    public function comentariosCount()
    {
        return $this->comentarios()->count();
    }
}