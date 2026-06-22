<?php

namespace App\Http\Controllers;

// Estos seran los modelos que usaremos en este controlador
use App\Models\Comentarios;
use App\Models\Fotografia;
use App\Models\Desafio;

use Illuminate\Http\Request; // Esto nos permitira interactuar con los datos enviados desde un formulario
use Illuminate\Support\Facades\Auth; // Este nos servira para realizar autenticaciones del usuario

class ComentariosController extends Controller
{
    //**************************************************************/
    //**************************************************************/
    //                Visualizamos los comentarios
    //**************************************************************/
    //**************************************************************/

    // Funcion para mostrar la vista de comentarios
    public function index(Request $request)
    {
        // La funcion check() se usa para comprobar si el usuario esta logueado
        if (Auth::check()) {
            // Obtener el ID de la fotografía desde la solicitud
            $fotografiaId = $request->input('fotografia_id');

            // Obtener la instancia de la fotografía con datos extra
            $fotografia = Fotografia::with(['user', 'likes' => function ($query) {
                if (Auth::check()) {
                    $query->where('usuario_id', Auth::id());
                }
            }])
            ->withCount(['likes', 'comentarios'])
            ->findOrFail($fotografiaId);

            // Obtener los comentarios
            $comentarios = Comentarios::where('fotografia_id', $fotografiaId)
                            ->with('user')
                            ->orderBy('created_at', 'desc')
                            ->get();

            return inertia('Fotografias/Comentar', [
                'fotografia' => $fotografia,
                'comentariosInitial' => $comentarios
            ]);
        } else {
            // Si el usuario no está logueado, redirige a la vista principal.
            return redirect('/');
        }
    }


    //**************************************************************/
    //**************************************************************/
    //                Control crear comentarios
    //**************************************************************/
    //**************************************************************/

    // Funcion para almacenar un nuevo comentario
    public function store(Request $request)
    {
        // Usamos validate para comprobar que los datos cumplan los requisitos
        $request->validate([
            'fotografia_id' => 'required|exists:fotografias,id',
            'comentario' => 'required|string|max:255',
        ]);

        // Creamos y guardamos el comentario usando la funcion crearComentario que esta en el modelo
        Comentarios::crearComentario([
            'fotografia_id' => $request->fotografia_id, // usando el $request estamos sacando la informacion que se envia en el formulario
            'usuario_id' => Auth::id(),
            'comentario' => $request->comentario,
        ]);


        /******************** DESAFIO ********************/
        // Comprobamos si el usuario a hecho 10 comentarios en fotos de otros usuarios
        $autor = Auth::user();

        // Calculamos la suma total de comentarios que ha hecho en las fotos de otros usuarios
        $comentariosEnFotosDeOtros = \App\Models\Comentarios::where('usuario_id', $autor->id)
            ->whereHas('fotografia', function ($query) use ($autor) {
                $query->where('usuario_id', '!=', $autor->id);
            })
            ->count();

        if ($comentariosEnFotosDeOtros >= 10) {
            $desafio = Desafio::where('titulo', 'Social')->first();

            if ($desafio && !$autor->desafios->contains($desafio->id)) {
                $autor->desafios()->attach($desafio->id, ['conseguido_en' => now()]);
                $user->verificarColeccionista(); // Verificamos si el usuario tiene el desafio de coleccionista
            }
        }

        /******************** FIN DESAFIO ********************/

        // Redirigimos de nuevo a la vista pero con un mensaje de exito
        return redirect()->route('comentarios.index', ['fotografia_id' => $request->fotografia_id])
                         ->with('success', 'Comentario añadido con éxito.');
    }


    //**************************************************************/
    //**************************************************************/
    //                Control comprobar comentario
    //**************************************************************/
    //**************************************************************/

    // En esta funcion comprobamos si el usuario a comentado o no en la foto seleccionada
    public function comprobarComentario(Request $request)
    {
        // Usando la funcion del controlador lo comprobamos
        $comentado = Comentarios::comprobarComentario($request->fotografia_id);

        // Devolvemos al cliente la informacion en tipo json
        return response()->json(['comentado' => $comentado]);
    }

    //**************************************************************/
    //**************************************************************/
    //                   Devolver los comentarios
    //**************************************************************/
    //**************************************************************/

    // Con esta funcion obtenemos todos los comentarios que tiene una fotografia
    public function getComentarios($id)
    {
        // Usamos el modelo para conectar a la base de datos y buscar los comentarios de esa fotografia
        // Al usar el with estamos recuperando de forma anticipada los datos de los usuarios y asi se ahorra tiempo de carga
        // Con el el get() estamos ejecutando la consulta 
        $comentarios = Comentarios::where('fotografia_id', $id)->with('user')->get();

        // devolvemos al cliente los datos en formato json
        return response()-> json($comentarios);
    }

    //**************************************************************/
    //**************************************************************/
    //                   Eliminar un comentario
    //**************************************************************/
    //**************************************************************/

    // Asi es como eliminas un comentario seleccionado
    public function destroy($comentarioId)
    {
        $comentario = Comentarios::find($comentarioId);
    
        if ($comentario) {
            // Verificamos si el usuario actual es el creador del comentario o es un administrador
            if ($comentario->usuario_id !== Auth::id() && Auth::user()->rol !== 'admin') {
                return response()->json(['message' => 'No estás autorizado para eliminar este comentario.'], 403);
            }

            $comentario->delete(); // Eliminamos el comentario con delete()
            // Devolvemos al cliente el mensaje existoso y un codigo de exito
            return response()->json(['message' => 'Comentario eliminado con éxito.'], 200);
        }
        
        // Si por lo que sea no se encuentra el comentario entonces devolvemos el mensaje y el codigo 404
        return response()->json(['message' => 'Comentario no encontrado.'], 404);
    }
}