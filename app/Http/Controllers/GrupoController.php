<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class GrupoController extends Controller
{
    /**
     * Display a listing of the user's groups.
     */
    public function index()
    {
        // Obtener los grupos del usuario actual
        $grupos = Auth::user()->grupos()->with('usuarios')->get();
        return inertia('Grupos/Index', ['grupos' => $grupos]);
    }

    /**
     * Show the form for creating a new group.
     */
    public function create()
    {
        return inertia('Grupos/Create');
    }

    /**
     * Store a newly created group in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $grupo = Grupo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'codigo_invitacion' => Str::random(10),
            'creado_por' => Auth::id(),
        ]);

        // Añadir al creador como miembro admin
        $grupo->usuarios()->attach(Auth::id(), ['rol' => 'admin']);

        return redirect()->route('grupos.index')->with('success', 'Grupo creado con éxito.');
    }

    /**
     * Display the specified group.
     */
    public function show($id)
    {
        $grupo = Grupo::with('usuarios')->findOrFail($id);

        // Validar si el usuario pertenece al grupo o es admin global
        $isMember = $grupo->usuarios->contains(Auth::id());
        if (!$isMember && Auth::user()->rol !== 'admin') {
            abort(403, 'No tienes acceso a este grupo.');
        }

        return inertia('Grupos/Show', [
            'grupo' => $grupo,
            'isGlobalAdmin' => Auth::user()->rol === 'admin',
            'currentUserId' => Auth::id()
        ]);
    }

    /**
     * Show the form to join a group with an invitation code.
     */
    public function joinForm()
    {
        return inertia('Grupos/Join');
    }

    /**
     * Join a group using an invitation code.
     */
    public function join(Request $request)
    {
        $request->validate([
            'codigo_invitacion' => 'required|string',
        ]);

        $grupo = Grupo::where('codigo_invitacion', $request->codigo_invitacion)->first();

        if (!$grupo) {
            return back()->with('error', 'Código de invitación no válido.');
        }

        if ($grupo->usuarios()->where('usuario_id', Auth::id())->exists()) {
            return redirect()->route('grupos.show', $grupo->id)->with('info', 'Ya perteneces a este grupo.');
        }

        // Añadir el usuario al grupo
        $grupo->usuarios()->attach(Auth::id(), ['rol' => 'miembro']);

        return redirect()->route('grupos.show', $grupo->id)->with('success', 'Te has unido al grupo con éxito.');
    }

    /**
     * Leave a group.
     */
    public function leave($id)
    {
        $grupo = Grupo::findOrFail($id);
        
        if ($grupo->creado_por === Auth::id()) {
            return back()->with('error', 'El creador no puede salir del grupo, debes eliminarlo.');
        }

        $grupo->usuarios()->detach(Auth::id());

        return redirect()->route('grupos.index')->with('success', 'Has salido del grupo.');
    }

    /**
     * Delete a group.
     */
    public function destroy($id)
    {
        $grupo = Grupo::findOrFail($id);
        
        if ($grupo->creado_por !== Auth::id() && Auth::user()->rol !== 'admin') {
            abort(403, 'No tienes permiso para eliminar este grupo.');
        }

        $grupo->delete();

        return redirect()->route('grupos.index')->with('success', 'Grupo eliminado correctamente.');
    }
}
