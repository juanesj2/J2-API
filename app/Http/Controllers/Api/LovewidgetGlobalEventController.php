<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LovewidgetGlobalEvent;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LovewidgetGlobalEventController extends Controller
{
    public function getActive(Request $request)
    {
        // Get the most recent active event that hasn't expired
        // Priorities: 1. Targeted event for this user, 2. Global event
        $event = LovewidgetGlobalEvent::where('is_active', true)
            ->where(function ($q) use ($request) {
                $q->whereNull('target_user_id')
                  ->orWhere('target_user_id', $request->user()->id);
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
            })
            ->orderByRaw('target_user_id IS NOT NULL DESC') // Priority to targeted events
            ->latest()
            ->first();

        if (!$event) {
            return response()->json(null);
        }

        return response()->json($event);
    }

    public function store(Request $request)
    {
        if ($request->user()->rol !== 'SuperAdmin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'target_user_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'confetti_enabled' => 'boolean',
            'confetti_colors' => 'nullable|array',
            'emojis_enabled' => 'boolean',
            'emojis_list' => 'nullable|string',
            'top_bar_color' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        // Stop any currently active events of the same type (global vs targeted)
        if (isset($validated['target_user_id']) && $validated['target_user_id']) {
            LovewidgetGlobalEvent::where('is_active', true)
                ->where('target_user_id', $validated['target_user_id'])
                ->update(['is_active' => false]);
        } else {
            LovewidgetGlobalEvent::where('is_active', true)
                ->whereNull('target_user_id')
                ->update(['is_active' => false]);
        }

        $event = new LovewidgetGlobalEvent($validated);
        $event->is_active = true;

        if ($request->has('duration_minutes') && $request->duration_minutes > 0) {
            $event->expires_at = Carbon::now()->addMinutes($request->duration_minutes);
        }

        $event->save();

        // Send Push Notifications
        $fcm = new FcmService();
        $title = $event->title ?: '¡Nuevo Evento!';
        $body = $event->message ?: 'Abre la app para verlo';

        if ($event->target_user_id) {
            // Send only to the target user
            $targetUser = User::find($event->target_user_id);
            if ($targetUser && $targetUser->fcm_token) {
                $fcm->sendToToken(
                    $targetUser->fcm_token,
                    $title,
                    $body,
                    ['type' => 'global_event'],
                    $targetUser->notification_sound ?? 'default'
                );
            }
        } else {
            // Send to all LoveWidget users
            $users = User::where('app', 'love_widget')
                         ->whereNotNull('fcm_token')
                         ->where('fcm_token', '!=', '')
                         ->get();
            
            foreach ($users as $u) {
                $fcm->sendToToken(
                    $u->fcm_token,
                    $title,
                    $body,
                    ['type' => 'global_event'],
                    $u->notification_sound ?? 'default'
                );
            }
        }

        return response()->json($event);
    }

    public function stop(Request $request)
    {
        if ($request->user()->rol !== 'SuperAdmin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        LovewidgetGlobalEvent::where('is_active', true)->update(['is_active' => false]);

        return response()->json(['message' => 'Eventos detenidos.']);
    }

    // =============================================
    // PURCHASE EVENT - Cualquier usuario puede usarlo
    // Siempre va dirigido a la pareja, siempre 24h
    // =============================================
    public function purchaseEvent(Request $request)
    {
        $user = $request->user();

        // Require a couple relationship
        $couple = \App\Models\Couple::where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id)
                    ->first();

        if (!$couple) {
            return response()->json(['error' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        // Determine partner
        $partnerId = $couple->user1_id === $user->id ? $couple->user2_id : $couple->user1_id;
        $partner = User::find($partnerId);

        if (!$partner) {
            return response()->json(['error' => 'No se encontró la pareja.'], 404);
        }

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'message'          => 'required|string',
            'confetti_enabled' => 'boolean',
            'confetti_colors'  => 'nullable|array',
            'emojis_enabled'   => 'boolean',
            'emojis_list'      => 'nullable|string',
            'top_bar_color'    => 'nullable|string',
        ]);

        // Disable any existing targeted event for this partner
        LovewidgetGlobalEvent::where('is_active', true)
            ->where('target_user_id', $partnerId)
            ->update(['is_active' => false]);

        // Always force: directed to partner, exactly 24 hours
        $event = new LovewidgetGlobalEvent($validated);
        $event->is_active       = true;
        $event->target_user_id  = $partnerId;
        $event->expires_at      = Carbon::now()->addHours(24);
        $event->save();

        // Send push notification to partner only
        if ($partner->fcm_token) {
            $fcm = new FcmService();
            $fcm->sendToToken(
                $partner->fcm_token,
                $event->title,
                $event->message,
                ['type' => 'global_event'],
                $partner->notification_sound ?? 'default'
            );
        }

        return response()->json([
            'message' => '¡Evento enviado a tu pareja!',
            'event'   => $event,
        ], 201);
    }
}
