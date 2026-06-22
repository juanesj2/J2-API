<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Http\Resources\GrupoResource;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos = Grupo::with('usuarios')->get();
        return GrupoResource::collection($grupos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $grupo = Grupo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'codigo_invitacion' => \Illuminate\Support\Str::random(10),
            'creado_por' => $request->user()->id,
        ]);

        // Añadir al creador como miembro del grupo automáticamente
        $grupo->usuarios()->attach($request->user()->id, ['rol' => 'admin']);

        return new GrupoResource($grupo);
    }

    public function show($id)
    {
        $grupo = Grupo::with('usuarios')->findOrFail($id);
        return new GrupoResource($grupo);
    }

    public function update(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $grupo->update($validated);

        return new GrupoResource($grupo);
    }

    public function destroy(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);
        
        // Opcional: solo admin del grupo o de la app puede borrarlo.
        if ($grupo->creado_por !== $request->user()->id && $request->user()->rol !== 'admin') {
            return response()->json(['error' => 'No tienes permiso para eliminar este grupo'], 403);
        }

        $grupo->delete();

        return response()->json(['message' => 'Grupo eliminado']);
    }

    // --- Nuevos métodos ---

    public function misGrupos(Request $request)
    {
        $user = $request->user();
        $grupos = $user->grupos()->with('usuarios')->get();
        return GrupoResource::collection($grupos);
    }

    public function unirse(Request $request)
    {
        $request->validate([
            'codigo_invitacion' => 'required|string',
        ]);

        $grupo = Grupo::where('codigo_invitacion', $request->codigo_invitacion)->first();

        if (!$grupo) {
            return response()->json(['error' => 'Código de invitación no válido'], 404);
        }

        $user = $request->user();

        if ($grupo->usuarios()->where('usuario_id', $user->id)->exists()) {
            return response()->json(['message' => 'El usuario ya pertenece a este grupo'], 200);   
        }

        $grupo->usuarios()->attach($user->id, ['rol' => 'miembro']);

        // Refrescar el grupo
        $grupo->load('usuarios');

        return new GrupoResource($grupo);
    }

    public function salir(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);
        $user = $request->user();

        // Opcional: no dejar salir al creador si es el único admin, o traspasar liderato
        if ($grupo->creado_por === $user->id) {
             return response()->json(['error' => 'El creador no puede salir del grupo (debe eliminarlo)'], 403);
        }

        if (!$grupo->usuarios()->where('usuario_id', $user->id)->exists()) {
             return response()->json(['error' => 'No formas parte de este grupo'], 400);
        }

        $grupo->usuarios()->detach($user->id);

        return response()->json(['message' => 'Has salido del grupo']);
    }
}
