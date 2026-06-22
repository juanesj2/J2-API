<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Achievement;
use App\Models\CoupleAchievement;
use App\Models\CoupleUnlockedHint;
use App\Models\Couple;
use Carbon\Carbon;

class AchievementController extends Controller
{
    private function getCoupleForUser($userId)
    {
        return Couple::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->first();
    }

    /**
     * Get all achievements, unlocked hints and unlocked achievements
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json([
                'achievements' => [],
                'unlocked_achievements' => [],
                'unlocked_hints' => [],
            ]);
        }

        $allAchievements = Achievement::all();
        $unlockedAchievements = CoupleAchievement::where('couple_id', $couple->id)->get();
        $unlockedHints = CoupleUnlockedHint::where('couple_id', $couple->id)->get();

        return response()->json([
            'achievements' => $allAchievements,
            'unlocked_achievements' => $unlockedAchievements,
            'unlocked_hints' => $unlockedHints,
        ]);
    }

    /**
     * Unlock a new achievement
     */
    public function unlock(Request $request)
    {
        $request->validate([
            'achievement_id' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'Not in a couple'], 400);
        }

        // Si el logro ya est desbloqueado, no hacemos nada
        $existing = CoupleAchievement::where('couple_id', $couple->id)
            ->where('achievement_id', $request->achievement_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Achievement already unlocked',
                'achievement' => $existing,
                'newly_unlocked' => false
            ]);
        }

        $achievement = CoupleAchievement::create([
            'couple_id' => $couple->id,
            'achievement_id' => $request->achievement_id,
            'unlocked_at' => now(),
        ]);

        return response()->json([
            'message' => 'Achievement unlocked successfully',
            'achievement' => $achievement,
            'newly_unlocked' => true
        ], 201);
    }

    /**
     * Unlock a hint for an achievement
     */
    public function unlockHint(Request $request)
    {
        $request->validate([
            'achievement_id' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'Not in a couple'], 400);
        }

        // 1. Check if user already unlocked ANY hint today
        $hintsTodayCount = CoupleUnlockedHint::where('user_id', $user->id)
            ->whereDate('unlocked_at', Carbon::today())
            ->count();

        $hasTrophySecret = CoupleAchievement::where('couple_id', $couple->id)
            ->where('achievement_id', 'trophy_secret')
            ->exists();
            
        $allowedHints = $hasTrophySecret ? 2 : 1;

        if ($hintsTodayCount >= $allowedHints) {
            $msg = $hasTrophySecret 
                ? 'Ya has pedido tus 2 pistas de hoy. Vuelve mañana para más.' 
                : 'Ya has pedido una pista hoy. Vuelve mañana para pedir otra.';
                
            return response()->json([
                'success' => false,
                'message' => $msg
            ], 403);
        }

        // 2. Check how many hints exist for this achievement
        $achievement = Achievement::find($request->achievement_id);
        if (!$achievement || !$achievement->hints) {
            return response()->json([
                'success' => false,
                'message' => 'Logro no encontrado o sin pistas.'
            ], 404);
        }
        
        $totalHints = count($achievement->hints);

        // 3. Obtener cuntas pistas se han desbloqueado ya
        $unlockedHints = CoupleUnlockedHint::where('couple_id', $couple->id)
            ->where('achievement_id', $request->achievement_id)
            ->orderBy('hint_index', 'asc')
            ->get();
        $nextHintIndex = $unlockedHints->count();

        if ($nextHintIndex >= $totalHints) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has desbloqueado todas las pistas para este logro.'
            ], 400);
        }

        // 4. Unlock the hint
        $unlockedHint = CoupleUnlockedHint::create([
            'couple_id' => $couple->id,
            'user_id' => $user->id,
            'achievement_id' => $request->achievement_id,
            'hint_index' => $nextHintIndex,
            'unlocked_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pista desbloqueada',
            'hint' => $unlockedHint
        ]);
    }
}
