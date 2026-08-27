<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // 1. Recibimos la carga útil
        $appSource = $request->input('app', 'desconocida'); // Ej: 'whatsapp', 'telegram', etc.
        $from = $request->input('from');
        $body = $request->input('body');
        $name = $request->input('pushname') ?? 'Usuario';

        Log::info("🤖 Webhook [{$appSource}] -> {$name} ({$from}): {$body}");

        // 2. Lógica dinámica según la app de origen
        $reply = "";

        switch ($appSource) {
            case 'whatsapp':
                $reply = "¡Hola {$name} desde WhatsApp! 🟩 He recibido tu mensaje: '{$body}'";
                break;
            case 'telegram':
                $reply = "¡Hola {$name} desde Telegram! 🟦 He recibido tu mensaje: '{$body}'";
                break;
            default:
                $reply = "¡Hola {$name}! Mensaje recibido desde una app desconocida: '{$body}'";
                break;
        }

        // 3. Le devolvemos la respuesta al Gateway correspondiente
        return response()->json([
            'success' => true,
            'reply' => $reply
        ]);
    }
}
