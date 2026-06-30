<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class WebhookController extends Controller
{
    public function revenueCat(Request $request)
    {
        // TODO: Implement Authorization/Secret validation here if configured in RevenueCat
        
        $type = $request->input('event.type');
        $appUserId = $request->input('event.app_user_id'); // e.g. "user_15"
        $expirationAtMs = $request->input('event.expiration_at_ms');

        if (!$appUserId) {
            return response()->json(['status' => 'ignored', 'message' => 'No app_user_id']);
        }

        // Parse the user ID. We passed `user_{id}` from the app.
        $userId = str_replace('user_', '', $appUserId);
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json(['status' => 'ignored', 'message' => 'User not found']);
        }

        // Get the couple
        $couple = \App\Models\Couple::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->first();

        if (!$couple) {
            return response()->json(['status' => 'ignored', 'message' => 'Couple not found']);
        }

        if ($type === 'INITIAL_PURCHASE' || $type === 'RENEWAL') {
            if ($expirationAtMs) {
                $couple->premium_until = Carbon::createFromTimestampMs($expirationAtMs);
                $couple->save();
            }
        } elseif ($type === 'CANCELLATION' || $type === 'EXPIRATION') {
            // Optional: let it expire naturally based on the date, or force it:
            // $couple->premium_until = Carbon::now();
            // $couple->save();
        }

        return response()->json(['status' => 'success']);
    }
}
