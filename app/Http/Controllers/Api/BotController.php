<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // 1. Recibimos la carga útil desde nuestro Gateway en Node.js
        $from = $request->input('from');
        $body = $request->input('body');
        $name = $request->input('pushname') ?? 'Usuario';

        Log::info("🤖 Webhook J2-Bot -> {$name} ({$from}): {$body}");

        // 2. Aquí es donde conectaremos la IA (Gemini).
        // Por ahora, simulamos la respuesta del "Cerebro".
        $reply = "¡Hola {$name}! Tu mensaje ha viajado de WhatsApp a Node, y de Node a Laravel. He recibido: '{$body}'";

        // 3. Le devolvemos la respuesta al Gateway para que la envíe por WhatsApp
        return response()->json([
            'success' => true,
            'reply' => $reply
        ]);
    }
}
