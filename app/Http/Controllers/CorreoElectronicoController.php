<?php

namespace App\Http\Controllers;

// Este es el Mailer que vamos a usar
use App\Mail\miCorreoElectronico;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

// Vamos a usan el modelo de fotografia
use App\Models\Fotografia;

class CorreoElectronicoController extends Controller
{
    //**************************************************************/
    //**************************************************************/
    //                Vamos a enviar el correo
    //**************************************************************/
    //**************************************************************/

    // Esta es la funcion que se encarga de enviar el correo
    public function enviarCorreo(Request $request)
    {
        // Obtenemos el usuario logueado
        $usuario = Auth::user();
    
        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'No estas logeado'], 401);
        }
    
        // Obtenemos la fotografía seleccionada
        $fotografia = Fotografia::with(['likes', 'comentarios'])->find($request->fotografia_id);
    
        if (!$fotografia) {
            return response()->json(['success' => false, 'message' => 'Fotografía no encontrada'], 404);
        }
    
        // Estos seran los datos que se enviaran por el correo
        $details = [
            'nombre' => $usuario->name,
            'email' => $usuario->email,
            'fotografia' => $fotografia->direccion_imagen,
            'likes' => $fotografia->likes->count(),
            'comentariosCount' => $fotografia->comentarios ->count(),
            // pluck() se usa para sacar una sola columna de los comentarios en este caso contenido y luego lo convertinos en un array
            'comentarios' => $fotografia->comentarios->pluck('contenido')->toArray(),
        ];
    
        // Enviamos el correo
        Mail::to($usuario->email)->send(new miCorreoElectronico($details));
    
        // Devolvemos un mensaje en formato json
        return response()->json(['success' => true, 'message' => 'Correo enviado con éxito!']);
    }
    
}
