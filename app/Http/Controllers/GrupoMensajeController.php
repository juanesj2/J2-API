<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrupoMensaje;
use App\Models\Grupo;
use Illuminate\Support\Facades\Auth;

class GrupoMensajeController extends Controller
{
    /**
     * Devuelve los mensajes de un grupo en formato JSON.
     */
    public function index($grupo_id)
    {
        // Validar que el grupo exista y cargar usuarios para verificar membresia
        $grupo = Grupo::with('usuarios')->findOrFail($grupo_id);
        
        $isMember = $grupo->usuarios->contains(Auth::id());
        
        if (!$isMember && Auth::user()->rol !== 'admin') {
            return response()->json(['message' => 'No tienes acceso a los mensajes de este grupo.'], 403);
        }

        // Obtener los mensajes con información básica del usuario
        $mensajes = GrupoMensaje::where('grupo_id', $grupo_id)
            ->with('user:id,name,email,rol')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($mensajes);
    }

    /**
     * Guarda un nuevo mensaje en el grupo.
     */
    public function store(Request $request, $grupo_id)
    {
        // Validar que el grupo exista y el usuario sea miembro
        $grupo = Grupo::with('usuarios')->findOrFail($grupo_id);
        $isMember = $grupo->usuarios->contains(Auth::id());
        
        if (!$isMember && Auth::user()->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos para enviar mensajes a este grupo.'], 403);
        }

        // Validación de datos de entrada
        $request->validate([
            'mensaje' => 'nullable|string',
            'imagen'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Máximo 5MB
        ]);

        if (!$request->mensaje && !$request->hasFile('imagen')) {
            return response()->json(['message' => 'Debes enviar texto o una imagen.'], 422);
        }

        $nombreImagen = null;
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            // Generar nombre seguro y unívoco
            $nombreImagen = time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
            // Mover directamente a la carpeta pública images/chat
            $imagen->move(public_path('images/chat'), $nombreImagen);
        }

        // Crear el mensaje en BD
        $mensaje = GrupoMensaje::create([
            'grupo_id'   => $grupo_id,
            'usuario_id' => Auth::id(),
            'mensaje'    => $request->mensaje,
            'imagen'     => $nombreImagen ? 'chat/' . $nombreImagen : null,
        ]);

        // Recargar con los datos del usuario para devolverlo instataneamente al JS
        $mensaje->load('user:id,name,email,rol');

        return response()->json([
            'message' => 'Mensaje enviado',
            'data'    => $mensaje
        ], 201);
    }
}
