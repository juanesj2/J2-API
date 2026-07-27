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

        $absolutePath = null;
        if (Storage::disk('local')->exists($path)) {
            $absolutePath = Storage::disk('local')->path($path);
        } elseif (Storage::disk('public')->exists($path)) {
            $absolutePath = Storage::disk('public')->path($path);
        }

        if (!$absolutePath) {
            abort(404, 'File not found');
        }

        // Validación de propiedad estricta para evitar IDOR (Cacheada para velocidad)
        $cacheKey = 'media_access_' . $user->id . '_' . md5($path);
        $isOwner = \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400, function() use ($path, $couple) {
            if (str_starts_with($path, 'love_album/covers/')) {
                return \App\Models\LoveAlbum::where('cover_image', $path)
                                ->where('couple_id', $couple->id)
                                ->exists();
            } else if (str_starts_with($path, 'love_album/')) {
                return \App\Models\LovePhoto::where('image_path', $path)
                                ->where('couple_id', $couple->id)
                                ->exists();
            } else if (str_starts_with($path, 'milestones/')) {
                return \App\Models\CoupleMilestone::where('image_url', $path)
                                ->where('couple_id', $couple->id)
                                ->exists();
            } else if (str_starts_with($path, 'food_places/')) {
                return \App\Models\CoupleFoodPlace::where('image_url', $path)
                                ->where('couple_id', $couple->id)
                                ->exists();
            } else if (str_starts_with($path, 'food_dishes/')) {
                return \App\Models\CoupleFoodDish::where('image_url', $path)
                                ->where('couple_id', $couple->id)
                                ->exists();
            } else if (str_starts_with($path, 'movies/')) {
                return \App\Models\CoupleMovie::where('image_url', $path)
                                ->where('couple_id', $couple->id)
                                ->exists();
            } else {
                return true;
            }
        });

        if (!$isOwner) {
            abort(403, 'No tienes permiso para ver esta imagen.');
        }

        $extension = pathinfo($absolutePath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'mp4' => 'video/mp4',
            'mp3' => 'audio/mpeg',
            'm4a' => 'audio/mp4',
            'wav' => 'audio/wav',
        ];
        $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
        
        if (function_exists('mime_content_type')) {
            $mimeType = @mime_content_type($absolutePath) ?: $mimeType;
        }
        
        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=86400'
        ]);
    }
}
