<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fotografia;
use App\Models\Likes;
use App\Models\Desafio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    //**************************************************************/
    //**************************************************************/
    //                      Control de likes
    //**************************************************************/
    //**************************************************************/

    // Esta es la función que se encarga de dar un nuevo like de alta en la base de datos
    public function darLike(Fotografia $fotografia)
    {
        // Primero comprobamos que el usuario esté logeado y si no lo está devolvemos un mensaje de error
        if (!Auth::check()) {
            return response()->json(['error' => 'No estás logueado'], 401);
        }

        // Mediante la función darLike del modelo damos de alta el like
        Likes::darLike($fotografia->id);

        /******************** DESAFIO ********************/
        // Comprobamos si el usuario ha recibido su primer like en alguna de sus fotos
        $autor = $fotografia->user;

        // Calculamos la suma total de likes recibidos en TODAS las fotos
        $likesTotales = Fotografia::where('usuario_id', $autor->id)
            ->withCount('likes')
            ->get()
            ->sum('likes_count');

        // Si es el primer like que recibe en todas sus fotos
        if ($likesTotales === 1) {
            $desafio = Desafio::where('titulo', 'Me gusta esto')->first();

            if ($desafio && !$autor->desafios->contains($desafio->id)) {
                $autor->desafios()->attach($desafio->id, ['conseguido_en' => now()]);
                $autor->verificarColeccionista(); // Verificamos si el usuario tiene el desafío de coleccionista
            }
        }

        // Comprobamos si alguna de las fotos ha recibido más de 25 likes
        $likesActuales = $fotografia->likes()->count();
        
        if ($likesActuales >= 25) {
            $desafio25Likes = Desafio::where('titulo', 'Popular')->first();
            if ($desafio25Likes && !$autor->desafios->contains($desafio25Likes->id)) {
                // attach() nos permite asociar el desafío al usuario en la tabla pivote
                $autor->desafios()->attach($desafio25Likes->id, ['conseguido_en' => now()]);
                $autor->verificarColeccionista(); // Verificamos si el usuario tiene el desafío de coleccionista
            }
        }

        /******************** FIN DESAFIO ********************/

        // Le devolvemos al cliente estos datos en formato JSON para poder hacer cosas en la app
        return response()->json([
            'liked' => true,
            'likesCount' => $fotografia->likes()->count()
        ]);
    }

    // Esta es la función que se encarga de dar baja un like en la base de datos
    public function quitarLike(Fotografia $fotografia)
    {
        // Primero comprobamos que el usuario esté logeado y si no lo está devolvemos un mensaje de error
        if (!Auth::check()) {
            return response()->json(['error' => 'No estás logueado'], 401);
        }

        // Mediante la función quitarLike del modelo damos de baja el like
        Likes::quitarLike($fotografia->id);

        // Le devolvemos al cliente estos datos en formato JSON
        return response()->json([
            'liked' => false,
            'likesCount' => $fotografia->likes()->count()
        ]);
    }
}
