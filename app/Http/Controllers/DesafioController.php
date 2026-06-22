<?php

namespace App\Http\Controllers;

// Este es el modelo que usaremos en este controlador
use App\Models\Desafio;

use Illuminate\Http\Request; // Esto nos permitira interactuar con los datos enviados desde un formulario
use Illuminate\Support\Facades\Auth; // Este nos servira para realizar autenticaciones del usuario

class DesafioController extends Controller
{
    
    //**************************************************************/
    //**************************************************************/
    //                Visualizamos los desafios
    //**************************************************************/
    //**************************************************************/

    public function index()
    {
        // 1) Si está autenticado y vetado, lo mando a /vetado
        if (Auth::check() && Auth::user()->estaVetado()) {
            return redirect()->route('vetado');
        }

        // 2) Si no está vetado, muestro normalmente
        $desafios = Desafio::paginate(12);
        
        $user_desafios_ids = [];
        if (Auth::check()) {
            $user_desafios_ids = Auth::user()->desafios->pluck('id')->toArray();
        }

        return inertia('Desafios/Index', [
            'desafios' => $desafios,
            'userDesafiosIds' => $user_desafios_ids
        ]);
    }

    //**************************************************************/
    //**************************************************************/
    //                Visualizamos mis desafios
    //**************************************************************/
    //**************************************************************/

    public function misDesafios()
    {
        $usuario = Auth::user();
        $misDesafios = $usuario->desafios()->paginate(10);

        return inertia('Desafios/MisDesafios', [
            'misDesafios' => $misDesafios
        ]);
    }

}