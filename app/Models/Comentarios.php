<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// Auth nos servira para comprobar si el usuario esta logueado
use Illuminate\Support\Facades\Auth;

class Comentarios extends Model
{
    // Desactivamos las marcas de tiempo que usa laravel para que no intente crear 
    // las columnas created_at y updated_at en la base de datos
    public $timestamps = false;

    // Hacemos referencia a la la tabla comentarios de la base de datos
    protected $table = 'comentarios';

    // Estas son las columnas que laravel va a poder modificar de la base de datos
    protected $fillable = ['fotografia_id', 'usuario_id', 'contenido'];

    //**************************************************************/
    //**************************************************************/
    //               Relaciones con la base de datos
    //**************************************************************/
    //**************************************************************/

    // Esta es una relacion con el modelo de fotografia
    public function fotografia()
    {
        // Con belongto() indicamos que un comentario pertenece solo a una fotografia
        return $this->belongsTo(Fotografia::class);
    }

    // Esta es una relacion con el modelo de User
    public function user()
    {
        // Con belongto() indicamos que un comentario pertenece solo a un usuario
        return $this->belongsTo(User::class, 'usuario_id');
    }

    
    //**************************************************************/
    //**************************************************************/
    //                Control crear comentarios
    //**************************************************************/
    //**************************************************************/

    // Esta es la funcion para crear un nuevo comentario
    public static function crearComentario($data)
    {
        // Al igual que antes self es para decirle a laravel en que tabla tiene que crear el comentario
        return self::create([
            // Los datos de $data nos vienen desde el controlador que es donde llamamos a nuestra funcion
            'fotografia_id' => $data['fotografia_id'],
            'usuario_id' => $data['usuario_id'],
            'contenido' => $data['comentario'],
        ]);
    }

    //**************************************************************/
    //**************************************************************/
    //                Control comprobar comentario
    //**************************************************************/
    //**************************************************************/

    // funcion para comprobar si el usuario ha echo algun comentario
    public static function comprobarComentario($fotografiaId)
    {
        // Al usar self estamos diciendole a laravel la tabla donde buscar
        return self::where('fotografia_id', $fotografiaId) // Buscamos en la base de datos la fotografia correspondiente
                    ->where('usuario_id', Auth::id()) // ademas solamente los comentarios realizados por el usuario logueado
                    ->exists(); // Esto nos devolvera true o false dependiendo si a encontrado algo o no
    }


}