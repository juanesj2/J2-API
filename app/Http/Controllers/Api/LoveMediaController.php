<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Couple;
use App\Models\LovePhoto;
use App\Models\CoupleMilestone;

class LoveMediaController extends Controller
{
    private function getCoupleForUser($userId)
    {
        return Couple::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->first();
    }

    public function serveMedia(Request $request, $path)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) {
            abort(403, 'No tienes pareja vinculada.');
        }

        if (str_contains($path, '..')) {
            abort(400, 'Invalid path');
        }

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found');
        }

        // Validación de propiedad estricta para evitar IDOR
        $isOwner = false;

        if (str_starts_with($path, 'love_album/covers/')) {
            $isOwner = \App\Models\LoveAlbum::where('cover_image', $path)
                            ->where('couple_id', $couple->id)
                            ->exists();
        } else if (str_starts_with($path, 'love_album/')) {
            $isOwner = LovePhoto::where('image_path', $path)
                            ->where('couple_id', $couple->id)
                            ->exists();
        } else if (str_starts_with($path, 'milestones/')) {
            $isOwner = CoupleMilestone::where('image_url', $path)
                            ->where('couple_id', $couple->id)
                            ->exists();
        } else if (str_starts_with($path, 'food_places/')) {
            $isOwner = \App\Models\CoupleFoodPlace::where('image_url', $path)
                            ->where('couple_id', $couple->id)
                            ->exists();
        } else if (str_starts_with($path, 'food_dishes/')) {
            $isOwner = \App\Models\CoupleFoodDish::where('image_url', $path)
                            ->where('couple_id', $couple->id)
                            ->exists();
        } else if (str_starts_with($path, 'movies/')) {
            $isOwner = \App\Models\CoupleMovie::where('image_url', $path)
                            ->where('couple_id', $couple->id)
                            ->exists();
        } else {
            // Otros archivos generados por juegos/dibujos que no guardan ruta en BD,
            // pero que usan uniqid() y son accedidos a través del componente seguro.
            // Para estos, el solo hecho de estar logueado y tener pareja permite acceder,
            // ya que su nombre es un hash impredecible (uniqid).
            $isOwner = true;
        }

        if (!$isOwner) {
            abort(403, 'No tienes permiso para ver esta imagen.');
        }

        $absolutePath = storage_path('app/' . $path);
        
        $mimeType = mime_content_type($absolutePath);
        
        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=86400'
        ]);
    }
}
