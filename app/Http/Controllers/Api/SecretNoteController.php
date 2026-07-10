<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Couple;
use App\Models\SecretNote;

class SecretNoteController extends Controller
{
    private function getCoupleForUser($userId)
    {
        return Couple::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->first();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'Not in a couple'], 400);
        }

        // Obtener todas las cartitas de amor de la pareja, ordenadas por más recientes
        $notes = SecretNote::where('couple_id', $couple->id)
            ->with('user:id,name,avatar_url')
            ->orderBy('created_at', 'desc')
            ->get();

        // Marcar como leídas las que no son del usuario actual
        foreach ($notes as $note) {
            if ($note->user_id !== $user->id && !$note->is_read) {
                $note->is_read = true;
                $note->save();
            }
        }

        return response()->json([
            'notes' => $notes
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'Not in a couple'], 400);
        }

        $note = SecretNote::create([
            'couple_id' => $couple->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Secret note created successfully',
            'note' => $note->load('user:id,name,avatar_url')
        ], 201);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $note = SecretNote::where('id', $id)->where('user_id', $user->id)->first();

        if (!$note) {
            return response()->json(['message' => 'Note not found or unauthorized'], 404);
        }

        $note->delete();

        return response()->json(['message' => 'Note deleted successfully']);
    }
}
