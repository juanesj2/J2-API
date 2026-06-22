<?php

namespace App\Http\Controllers;

// Este es el modelo que usaremos en este controlador
use App\Models\Grupo;

use Illuminate\Http\Request; // Esto nos permitira interactuar con los datos enviados desde un formulario
use Illuminate\Support\Facades\Auth; // Este nos servira para realizar autenticaciones del usuario

class GruposController extends Controller
{
    
    //**************************************************************/
    //**************************************************************/
    //                Index de Grupos
    //**************************************************************/
    //**************************************************************/

    public function index()
    {
        // 1) Si está autenticado y vetado, lo mando a /vetado
        if (Auth::check() && Auth::user()->estaVetado()) {
            return redirect()->route('vetado');
        }

        // 2) Si no está vetado, muestro el index_opciones
        return view('grupos.index_opciones');

    }

}