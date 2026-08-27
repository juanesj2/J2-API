<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\BotMessage;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Cache;

class BotController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // 1. Recibimos la carga útil
        $appSource = $request->input('app', 'desconocida');
        $from = $request->input('from');
        $body = $request->input('body');
        $name = $request->input('pushname') ?? 'Usuario';

        Log::info("🤖 Webhook [{$appSource}] -> {$name} ({$from}): {$body}");

        // Guardar mensaje entrante en BD
        BotMessage::create([
            'app_source' => $appSource,
            'phone_number' => $from,
            'contact_name' => $name,
            'body' => $body,
            'is_from_bot' => false,
        ]);

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

        // Revisar si está activada la "Acción Humana"
        $humanActionRequired = Cache::get('bot_human_action', false);

        if ($humanActionRequired) {
            // Guardar como BORRADOR (draft), no se enviará automáticamente
            BotMessage::create([
                'app_source' => $appSource,
                'phone_number' => $from,
                'contact_name' => 'Bot (Borrador)',
                'body' => $reply,
                'is_from_bot' => true,
                'status' => 'draft'
            ]);

            return response()->json(['success' => true]); // No devolvemos 'reply' para evitar envío inmediato
        }

        // Si la Acción Humana está APAGADA, responder y guardar como 'sent'
        BotMessage::create([
            'app_source' => $appSource,
            'phone_number' => $from,
            'contact_name' => 'Bot (Auto)',
            'body' => $reply,
            'is_from_bot' => true,
            'status' => 'sent'
        ]);

        // 3. Le devolvemos la respuesta al Gateway para envío inmediato
        return response()->json([
            'success' => true,
            'reply' => $reply
        ]);
    }

    // Endpoint para el panel web: obtener lista de chats agrupados
    public function getChats(Request $request)
    {
        $appSource = $request->input('app', 'whatsapp');
        
        $chats = BotMessage::where('app_source', $appSource)
            ->select('phone_number', 'contact_name')
            ->selectRaw('MAX(created_at) as last_message_at')
            ->groupBy('phone_number', 'contact_name')
            ->orderByDesc('last_message_at')
            ->get();
            
        return response()->json($chats);
    }

    // Endpoint para el panel web: obtener historial de un chat
    public function getMessages(Request $request, $phone)
    {
        $appSource = $request->input('app', 'whatsapp');
        
        $messages = BotMessage::where('app_source', $appSource)
            ->where('phone_number', $phone)
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json($messages);
    }

    // Endpoint para el panel web: enviar mensaje manualmente (se queda en cola)
    public function sendWebMessage(Request $request)
    {
        $request->validate([
            'app' => 'required',
            'phone_number' => 'required',
            'message' => 'required'
        ]);

        // Guardar en BD como pendiente
        $msg = BotMessage::create([
            'app_source' => $request->app,
            'phone_number' => $request->phone_number,
            'contact_name' => 'Bot (Web)',
            'body' => $request->message,
            'is_from_bot' => true,
            'status' => 'pending' // Importante para el Polling
        ]);

        return response()->json(['success' => true, 'message' => $msg]);
    }

    // Endpoint para el Bot Node.js: Obtener mensajes pendientes
    public function getPendingMessages(Request $request)
    {
        $appSource = $request->input('app', 'whatsapp');

        $pending = BotMessage::where('app_source', $appSource)
            ->where('is_from_bot', true)
            ->where('status', 'pending')
            ->get();

        return response()->json($pending);
    }

    // Endpoint para el Bot Node.js: Marcar como enviados
    public function markMessagesSent(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (!empty($ids)) {
            BotMessage::whereIn('id', $ids)->update(['status' => 'sent']);
        }

        return response()->json(['success' => true]);
    }

    // Endpoint web: Aprobar un borrador para que sea enviado
    public function approveDraft(Request $request)
    {
        $request->validate(['id' => 'required']);
        $msg = BotMessage::find($request->id);
        
        if ($msg && $msg->status === 'draft') {
            $msg->update(['status' => 'pending']); // Pasará a pending para que el Polling lo envíe
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }

    // Endpoint web: Obtener estado de configuración
    public function getSettings()
    {
        return response()->json([
            'human_action' => Cache::get('bot_human_action', false)
        ]);
    }

    // Endpoint web: Cambiar configuración
    public function toggleSettings(Request $request)
    {
        $current = Cache::get('bot_human_action', false);
        Cache::put('bot_human_action', !$current);
        
        return response()->json(['success' => true, 'human_action' => !$current]);
    }

    // Endpoint web: Eliminar un mensaje del historial (o borrar un borrador)
    public function deleteMessage($id)
    {
        $msg = BotMessage::find($id);
        if ($msg) {
            $msg->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
}
