<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comentarios;
use App\Models\Fotografia;
use App\Http\Resources\ComentarioResource;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($fotografiaId)
    {
        $comentarios = Comentarios::where('fotografia_id', $fotografiaId)
            ->with('user')
            ->orderBy('id', 'desc')
            ->get();

        return ComentarioResource::collection($comentarios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $fotografiaId)
    {
        $request->validate([
            'contenido' => 'required|string|max:1000',
        ]);

        $fotografia = Fotografia::findOrFail($fotografiaId);

        $comentario = Comentarios::create([
            'fotografia_id' => $fotografia->id,
            'usuario_id' => Auth::id(),
            'contenido' => $request->contenido,
        ]);

        return new ComentarioResource($comentario);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comentario = Comentarios::findOrFail($id);

        if ($comentario->usuario_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $comentario->delete();

        return response()->json(['message' => 'Comentario eliminado']);
    }
}
