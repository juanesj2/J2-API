<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'app' => 'nullable|string|in:enfoca,love_widget',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'app' => $request->app ?? 'enfoca',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function googleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'uid' => 'required|string',
            'id_token' => 'nullable|string',
        ]);

        $email = strtolower(trim($request->email));

        // Si se proporciona id_token, verificar con Google
        if ($request->filled('id_token')) {
            try {
                $response = \Illuminate\Support\Facades\Http::get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $request->id_token
                ]);

                if (!$response->successful() || strtolower($response->json('email')) !== $email) {
                    return response()->json(['error' => 'Token de Google inválido o no coincide con el correo.'], 401);
                }
            } catch (\Throwable $e) {
                return response()->json(['error' => 'Error al verificar token con Google.'], 500);
            }
        }

        $user = User::where('email', $email)->first();

        // Blindaje: proteger cuentas administrativas de suplantación sin id_token
        if ($user && in_array($user->rol, ['admin', 'SuperAdmin']) && !$request->filled('id_token')) {
            return response()->json(['error' => 'Las cuentas de administrador requieren verificación de token de Google.'], 403);
        }

        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make($request->uid . '_google_auth'),
                'app' => 'love_widget',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(\Illuminate\Support\Str::random(60));

                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Si es API, en teoría no deberíamos redirigir, pero como estamos llamando a esto desde una vista HTML:
            if ($request->wantsJson()) {
                return response()->json(['message' => __($status)]);
            }
            return redirect()->route('login')->with('status', __($status));
        }

        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
        
        return back()->withErrors(['email' => [__($status)]]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
