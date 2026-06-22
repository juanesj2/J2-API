<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Likes extends Model
{

    // Desactivar las marcas de tiempo que usa Laravel para que no intente crear
    // las columnas created_at y updated_at en la base de datos
    public $timestamps = false;

    // Hacemos referencia a la la tabla likes de la base de datos
    protected $table = 'likes';

    // Estas son las columnas que laravel va a poder modificar de la base de datos
    protected $fillable = ['usuario_id', 'fotografia_id'];

    //**************************************************************/
    //**************************************************************/
    //               Relaciones con la base de datos
    //**************************************************************/
    //**************************************************************/

    // Relación con el modelo Fotografia
    public function fotografia()
    {
        // Usamos belongsto() porque un like solo puede pertenecer a una foto
        return $this->belongsTo(Fotografia::class, 'fotografia_id');
    }

    // Relación con el modelo User
    public function user()
    {
        // Usamos belongsto() porque un like solo puede pertenecer a un usuario
        return $this->belongsTo(User::class, 'usuario_id');
    }

    //**************************************************************/
    //**************************************************************/
    //                    Comprobacion de like
    //**************************************************************/
    //**************************************************************/

    // Método para verificar si el usuario autenticado ha dado like
    public static function comprobarLike($fotografiaId)
    {
        // Filtramos los datos para saber si el usuario loguado a dado algun like en la foto seleccionada
        return self::where('fotografia_id', $fotografiaId)
                    ->where('usuario_id', Auth::id())
                    ->exists();
    }

    //**************************************************************/
    //**************************************************************/
    //                Control para dar/quitar likes
    //**************************************************************/
    //**************************************************************/

    // Funcion para dar like
    public static function darLike($fotografiaId)
    {
        // Lo primer oque comprobamos es si el usuario esta loguado
        if (Auth::check()) {
            $usuarioId = Auth::id(); // cogemos el id del usuaio logeado y lo metemos en $usuarioId
            // Comprobamos si el usaio a dado like
            if (!self::comprobarLike($fotografiaId)) {
                // Cramos el like si no esta dado
                self::create(['usuario_id' => $usuarioId, 'fotografia_id' => $fotografiaId]);
            }
        }
    }

    // Funcion para quitar like
    public static function quitarLike($fotografiaId)
    {
        if (Auth::check()) {
            $usuarioId = Auth::id();
            if (self::comprobarLike($fotografiaId)) {
                self::where('fotografia_id', $fotografiaId)
                    ->where('usuario_id', $usuarioId)
                    ->delete();
            }
        }
    }
}