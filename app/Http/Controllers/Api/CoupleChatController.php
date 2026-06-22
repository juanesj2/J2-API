<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Couple;
use App\Models\CoupleMessage;
use Illuminate\Support\Facades\Auth;

class CoupleChatController extends Controller
{
    private function getCoupleForUser($userId)
    {
        return Couple::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->first();
    }

    public function index()
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $messages = CoupleMessage::where('couple_id', $couple->id)
            ->with(['user:id,name', 'photo'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $request->validate([
            'mensaje' => 'required|string',
            'love_photo_id' => 'nullable|exists:love_photos,id',
            'reply_to' => 'nullable|array'
        ]);

        $message = CoupleMessage::create([
            'couple_id' => $couple->id,
            'user_id' => $user->id,
            'mensaje' => $request->mensaje,
            'love_photo_id' => $request->love_photo_id,
            'reply_to' => $request->reply_to,
        ]);

        return response()->json([
            'message' => 'Mensaje enviado',
            'chat_message' => $message->load(['user:id,name', 'photo'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $message = CoupleMessage::find($id);

        if (!$message) {
            return response()->json(['message' => 'Mensaje no encontrado.'], 404);
        }

        if ($message->user_id !== $user->id) {
            return response()->json(['message' => 'No puedes editar un mensaje que no es tuyo.'], 403);
        }

        $request->validate([
            'mensaje' => 'required|string',
        ]);

        $message->update([
            'mensaje' => $request->mensaje,
            'is_edited' => true
        ]);

        return response()->json([
            'message' => 'Mensaje actualizado',
            'chat_message' => $message->load(['user:id,name', 'photo'])
        ], 200);
    }

    public function react(Request $request, $id)
    {
        $user = Auth::user();
        $message = CoupleMessage::find($id);

        if (!$message) {
            return response()->json(['message' => 'Mensaje no encontrado.'], 404);
        }

        $request->validate([
            'reaction' => 'required|string'
        ]);

        $reactions = $message->reactions ?? [];
        $reaction = $request->reaction;

        // Si el usuario ya reaccionó con este emoji, lo quitamos
        $existingIndex = null;
        foreach ($reactions as $index => $r) {
            if ($r['user_id'] === $user->id && $r['reaction'] === $reaction) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            unset($reactions[$existingIndex]);
            // Reindexar array
            $reactions = array_values($reactions);
        } else {
            $reactions[] = [
                'user_id' => $user->id,
                'reaction' => $reaction,
                'user_name' => $user->name,
                'timestamp' => now()->toIso8601String()
            ];
        }

        $message->update(['reactions' => $reactions]);

        return response()->json([
            'message' => 'Reacción actualizada',
            'chat_message' => $message->load(['user:id,name', 'photo'])
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $message = CoupleMessage::find($id);

        if (!$message) {
            return response()->json(['message' => 'Mensaje no encontrado.'], 404);
        }

        if ($message->user_id !== $user->id) {
            return response()->json(['message' => 'No puedes eliminar un mensaje que no es tuyo.'], 403);
        }

        $message->delete();

        return response()->json([
            'message' => 'Mensaje eliminado'
        ], 200);
    }
}
