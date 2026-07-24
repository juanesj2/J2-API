<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Couple;
use App\Models\CoupleMessage;
use App\Models\LovePhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class GodModeController extends Controller
{
    private function isAdmin(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->rol, ['admin', 'SuperAdmin'])) {
            // Check fallback for local dev if role is not set
            if ($user && in_array($user->email, ['juanesj2@gmail.com', 'juanstiven@tecon.es'])) {
                return;
            }
            abort(403, 'No tienes poderes divinos.');
        }
    }

    public function getStats(Request $request)
    {
        $this->isAdmin($request);

        $totalUsers = User::where('app', 'love_widget')->count();
        $totalCouples = Couple::count();
        $totalMessages = CoupleMessage::count();
        $totalGifts = CoupleMessage::where('mensaje', 'like', '[GIFT]%')->count();
        
        $activeCouplesToday = Couple::whereDate('last_poke_at', Carbon::today())->count();

        return response()->json([
            'total_users' => $totalUsers,
            'total_couples' => $totalCouples,
            'total_messages' => $totalMessages,
            'total_gifts' => $totalGifts,
            'active_couples_today' => $activeCouplesToday,
            'global_theme' => Cache::get('love_widget_global_theme', 'default')
        ]);
    }

    public function grantPremium(Request $request)
    {
        $this->isAdmin($request);
        $request->validate(['email' => 'required|email']);

        $targetUser = User::where('email', $request->email)->first();
        if (!$targetUser) return response()->json(['error' => 'Usuario no encontrado'], 404);

        $couple = Couple::where('user1_id', $targetUser->id)->orWhere('user2_id', $targetUser->id)->first();
        if (!$couple) return response()->json(['error' => 'Usuario no tiene pareja vinculada'], 404);

        // Otorga premium de por vida (hasta 2099)
        $couple->premium_until = Carbon::create(2099, 12, 31, 23, 59, 59);
        $couple->save();

        return response()->json(['message' => 'Suscripción VIP "Amor Ilimitado" concedida a ' . $targetUser->name]);
    }

    public function grantGifts(Request $request)
    {
        $this->isAdmin($request);
        $request->validate([
            'email' => 'nullable|email', // Optional, defaults to self
            'type' => 'required|string|in:all,teddy,rose,ring,letters',
            'amount' => 'nullable|integer|min:1'
        ]);

        if ($request->email) {
            $user = User::where('email', $request->email)->first();
            if (!$user) return response()->json(['error' => 'Usuario no encontrado'], 404);
        } else {
            $user = $request->user();
        }

        $couple = Couple::where('user1_id', $user->id)->orWhere('user2_id', $user->id)->first();
        if (!$couple) return response()->json(['error' => 'No tienes pareja'], 404);

        $inventory = $couple->inventory ?? [];
        $amount = $request->amount ?: 999;
        
        if ($request->type === 'all') {
            $inventory['gifts'] = ($inventory['gifts'] ?? 0) + $amount;
            $inventory['gift_rose'] = ($inventory['gift_rose'] ?? 0) + $amount;
            $inventory['gift_teddy'] = ($inventory['gift_teddy'] ?? 0) + $amount;
            $inventory['gift_ring'] = ($inventory['gift_ring'] ?? 0) + $amount;
            $inventory['letters'] = ($inventory['letters'] ?? 0) + $amount;
        } else {
            $key = 'gift_' . $request->type;
            if ($request->type === 'letters') $key = 'letters';
            $inventory[$key] = ($inventory[$key] ?? 0) + $amount;
        }
        
        $couple->inventory = $inventory;
        $couple->save();

        return response()->json([
            'message' => 'Magia divina aplicada: +' . $amount . ' regalos añadidos.',
            'inventory' => $inventory
        ]);
    }

    public function setStreak(Request $request)
    {
        $this->isAdmin($request);
        $request->validate(['streak' => 'required|integer|min:0']);

        $user = $request->user();
        $couple = Couple::where('user1_id', $user->id)->orWhere('user2_id', $user->id)->first();
        if (!$couple) return response()->json(['error' => 'No tienes pareja'], 404);

        $couple->current_streak = $request->streak;
        if ($request->streak > $couple->longest_streak) {
            $couple->longest_streak = $request->streak;
        }
        
        // Ajustamos la fecha para que la racha no se rompa inmediatamente (fingimos que enviaron foto ayer)
        if ($request->streak > 0) {
            $couple->last_photo_date = Carbon::yesterday()->format('Y-m-d H:i:s');
            $couple->streak_broken_at = null;
        } else {
            $couple->last_photo_date = null;
        }

        $couple->save();

        return response()->json([
            'message' => 'Dios del Tiempo: Racha actualizada a ' . $request->streak . ' días.',
            'streak' => $couple->current_streak
        ]);
    }

    public function setGlobalTheme(Request $request)
    {
        $this->isAdmin($request);
        $request->validate(['theme' => 'required|string']);

        $theme = $request->theme;
        Cache::put('love_widget_global_theme', $theme, now()->addDays(30)); 

        return response()->json(['message' => 'Temática global forzada a: ' . $theme]);
    }
}
