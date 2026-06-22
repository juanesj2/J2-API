<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Desafio;
use App\Http\Resources\DesafioResource;

class DesafioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $desafios = Desafio::paginate(20);
        return DesafioResource::collection($desafios);
    }

    /**
     * Devuelve los desafíos ya conseguidos por el usuario autenticado.
     */
    public function misDesafios(Request $request)
    {
        $usuario = $request->user();
        if (!$usuario) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        // Cargamos la relación pivot 'conseguido_en'
        $misDesafios = $usuario->desafios()->get();
        return DesafioResource::collection($misDesafios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }
}
