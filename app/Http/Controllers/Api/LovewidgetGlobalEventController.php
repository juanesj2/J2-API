<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LovewidgetGlobalEvent;
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
}
