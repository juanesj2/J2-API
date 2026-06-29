<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CouplePlan;
use Illuminate\Support\Facades\Auth;

class CouplePlanController extends Controller
{
    private function getCoupleForUser($userId)
    {
        return \App\Models\Couple::where('user1_id', $userId)
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

        $plans = CouplePlan::where('couple_id', $couple->id)
            ->orderBy('target_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($plans);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string',
            'status' => 'nullable|string',
            'target_date' => 'nullable|date',
            'description' => 'nullable|string',
            'dynamic_data' => 'nullable|array'
        ]);

        $plan = CouplePlan::create([
            'couple_id' => $couple->id,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category ?? 'other',
            'status' => $request->status ?? 'idea',
            'target_date' => $request->target_date,
            'dynamic_data' => $request->dynamic_data,
        ]);

        return response()->json($plan, 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $plan = CouplePlan::where('id', $id)->where('couple_id', $couple->id)->firstOrFail();

        $request->validate([
            'title' => 'nullable|string|max:255',
            'category' => 'nullable|string',
            'status' => 'nullable|string',
            'target_date' => 'nullable|date',
            'description' => 'nullable|string',
            'dynamic_data' => 'nullable|array',
            'linked_album_id' => 'nullable|integer'
        ]);

        $plan->update($request->only([
            'title', 'description', 'category', 'status', 'target_date', 'dynamic_data', 'linked_album_id'
        ]));

        return response()->json($plan);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $plan = CouplePlan::where('id', $id)->where('couple_id', $couple->id)->firstOrFail();
        $plan->delete();

        return response()->json(['message' => 'Plan eliminado']);
    }
}
