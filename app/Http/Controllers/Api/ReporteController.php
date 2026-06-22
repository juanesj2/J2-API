<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reporte;
use App\Http\Resources\ReporteResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reportes = Reporte::with('foto')->get();
        return response()->json(['data' => $reportes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'foto_id' => 'required|exists:fotografias,id',
            'motivo' => 'required|string|max:255',
        ]);

        $existe = Reporte::where('foto_id', $request->foto_id)
            ->where('usuario_id', Auth::id())
            ->exists();

        if ($existe) {
            return response()->json(['error' => 'Ya has reportado esta fotografía previamente.'], 409);
        }

        $reporte = Reporte::create([
            'usuario_id' => Auth::id(),
            'foto_id' => $request->foto_id,
            'motivo' => $request->motivo,
        ]);

        return new ReporteResource($reporte);
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
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        if ($request->user()->rol !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $reporte = Reporte::findOrFail($id);
        $reporte->delete();

        return response()->json(['message' => 'Reporte eliminado correctamente']);
    }

    /**
     * Eliminar todos los reportes de una foto especifica (Indultar).
     */
    public function destroyByPhoto(Request $request, string $fotoId)
    {
        if ($request->user()->rol !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Eliminar todos los reportes con foto_id = $fotoId
        Reporte::where('foto_id', $fotoId)->delete();

        return response()->json(['message' => 'Reportes eliminados correctamente']);
    }
}
