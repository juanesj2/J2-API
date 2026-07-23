<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WebhookController;

// ============================
//          TEST
// ============================
Route::get('/test', function () {
    return response()->json([
        'mensaje' => 'API funcionando correctamente',
        'status' => 200
    ]);
});

Route::get('/chivato', function () {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    return response()->json([
        'URL_LEIDA_DEL_ENV' => env('FRONTEND_URL'),
        'ORIGENES_PERMITIDOS' => config('cors.allowed_origins')
    ]);
});

// ============================
//       LOGIN / REGISTER
// ============================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/test/reset-link/{email}', [AuthController::class, 'testResetLink']);

// ============================
//     WEBHOOKS
// ============================
Route::post('/webhooks/revenuecat', [WebhookController::class, 'revenueCat']);

// ============================
//     RUTAS PROTEGIDAS AUTH
// ============================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/usuario', [UserController::class, 'show']);
    Route::put('/usuario', [UserController::class, 'update']);
    Route::delete('/usuario', [UserController::class, 'destroySelf']);
    Route::get('/usuarios/buscar', [UserController::class, 'search']);
    
    // Map Location endpoints
    Route::post('/location', [UserController::class, 'updateLocation']);
    Route::get('/location/partner', [UserController::class, 'getPartnerLocation']);

    // Admin routes (Usuarios)
    Route::get('/admin/usuarios', [UserController::class, 'index']);
    Route::delete('/admin/usuarios/{id}', [UserController::class, 'destroy']);
    Route::put('/admin/usuarios/{id}', [UserController::class, 'updateAdmin']);
    
    // Rutas públicas de usuarios
    Route::get('/usuarios', [UserController::class, 'index']);
});
