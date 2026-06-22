<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

// ============================
//          TEST
// ============================
Route::get('/test', function () {
    return response()->json([
        'mensaje' => 'API funcionando correctamente',
        'status' => 200
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
//     RUTAS PROTEGIDAS AUTH
// ============================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/usuario', [UserController::class, 'show']);
    Route::put('/usuario', [UserController::class, 'update']);
    Route::get('/usuarios/buscar', [UserController::class, 'search']);

    // Admin routes (Usuarios)
    Route::get('/admin/usuarios', [UserController::class, 'index']);
    Route::delete('/admin/usuarios/{id}', [UserController::class, 'destroy']);
    Route::put('/admin/usuarios/{id}', [UserController::class, 'updateAdmin']);
    
    // Rutas públicas de usuarios
    Route::get('/usuarios', [UserController::class, 'index']);
});
