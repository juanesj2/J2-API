<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getAllUsers(Request $request)
    {
        if ($request->user()->rol !== 'SuperAdmin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $users = User::select('id', 'name', 'email')->get();
        return response()->json($users);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Admin check should ideally be in middleware, but double check here or just return all
        if ($request->user()->rol !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $users = User::paginate(20);
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the authenticated user.
     */
    public function show(Request $request)
    {
        $user = clone $request->user();

        // Si el usuario es antiguo y no tiene código, se lo generamos
        if (empty($user->pairing_code)) {
            $user->pairing_code = strtoupper(substr(uniqid(), -6));
            $user->save();
        }

        $user->verificarTodosLosDesafios();
        return new UserResource($user);
    }

    /**
     * Update the authenticated user.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|confirmed|min:8',
        ]);

        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return new UserResource($user);
    }

    /**
     * Update user details by Admin (Role, Veto).
     */
    public function updateAdmin(Request $request, string $id)
    {
        if ($request->user()->rol !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $user = User::findOrFail($id);

        $request->validate([
             'rol' => 'sometimes|in:usuario,admin',
             'vetado' => 'sometimes|boolean',
             'fecha_veto' => 'sometimes|nullable|date',
        ]);

        if ($request->has('rol')) {
            $user->rol = $request->rol;
        }

        if ($request->has('vetado')) {
            if ($request->vetado) {
                // Si mandan fecha de veto específica, la usamos
                if ($request->has('fecha_veto') && $request->fecha_veto != null) {
                    $user->vetado_hasta = \Carbon\Carbon::parse($request->fecha_veto)->endOfDay();
                } else {
                    // Limite máximo seguro para columnas TIMESTAMP en MySQL (Problema del año 2038)
                    $user->vetado_hasta = \Carbon\Carbon::create(2037, 12, 31, 23, 59, 59);
                }
            } else {
                // Si le quitan el veto
                $user->vetado_hasta = null;
            }
        }

        $user->save();

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        if ($request->user()->rol !== 'admin') {
           return response()->json(['error' => 'No autorizado'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    /**
     * Remove the authenticated user's own account.
     */
    public function destroySelf(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = clone $request->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password' => ['La contraseña proporcionada es incorrecta.'],
            ]);
        }

        // Logout the user and delete the account
        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Cuenta eliminada correctamente']);
    }

    /**
     * Search users by name.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        $query = $request->input('query');

        $users = User::where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        return UserResource::collection($users);
    }

    public function updateLocation(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'is_sharing_location' => 'sometimes|boolean'
            ]);

            $user = $request->user();
            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->location_updated_at = now();
            
            if ($request->has('is_sharing_location')) {
                $user->is_sharing_location = $request->is_sharing_location;
            }
            
            $user->save();

            return response()->json(['message' => 'Ubicación actualizada correctamente']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    public function getPartnerLocation(Request $request)
    {
        try {
            $user = $request->user();
            
            $couple = \App\Models\Couple::where('user1_id', $user->id)
                ->orWhere('user2_id', $user->id)
                ->first();

            if (!$couple) {
                return response()->json(['error' => 'No tienes pareja asignada'], 404);
            }

            $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;
            $partner = User::find($partnerId);

            if (!$partner) {
                return response()->json(['error' => 'Pareja no encontrada'], 404);
            }

            if (!$partner->is_sharing_location || !$partner->latitude || !$partner->longitude) {
                 return response()->json(['error' => 'La pareja no está compartiendo su ubicación'], 403);
            }

            return response()->json([
                'latitude' => (float) $partner->latitude,
                'longitude' => (float) $partner->longitude,
                'updated_at' => $partner->location_updated_at,
                'name' => $partner->name,
                'avatar' => $partner->avatar_url
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }
}
