<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AtlasController extends Controller
{
    /**
     * Punto de entrada principal para los Tool Calls (Llamadas a Herramientas)
     * generados por el modelo de IA local (Qwen) desde el atlas-gateway.
     */
    public function execute(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'payload' => 'nullable|array'
        ]);

        $action = $request->input('action');
        $payload = $request->input('payload', []);

        // Aquí resolvemos (switch o match) la acción solicitada por la IA
        try {
            switch ($action) {
                case 'save_secret_note':
                    return $this->saveSecretNote($payload);
                
                // Añadir aquí más herramientas futuras:
                // case 'toggle_light':
                // case 'get_inventory':

                default:
                    return response()->json([
                        'success' => false,
                        'message' => "Acción '{$action}' no reconocida por la API."
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error ejecutando la acción '{$action}': " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Herramienta: Guarda una nota (Mockup inicial, luego se conectará al modelo Eloquent real)
     */
    private function saveSecretNote(array $payload)
    {
        $note = $payload['note'] ?? '';
        $timestamp = $payload['timestamp'] ?? now()->toIso8601String();

        if (empty($note)) {
            throw new \Exception("El contenido de la nota no puede estar vacío.");
        }

        // TODO: Crear modelo Note y guardar en DB
        // Note::create(['content' => $note, 'created_at' => $timestamp]);

        return response()->json([
            'success' => true,
            'message' => 'Nota guardada exitosamente en el servidor.',
            'data' => [
                'note' => $note,
                'saved_at' => $timestamp
            ]
        ]);
    }
}
