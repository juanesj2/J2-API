<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HubController;
use App\Http\Middleware\HubAuthMiddleware;

Route::get("/", function () { return response()->json(["message" => "J2 API Running"]); });

// ============================
//           J2 HUB
// ============================
Route::prefix('hub')->group(function () {
    // Rutas públicas del Hub
    Route::get('/login', [HubController::class, 'login'])->name('login');
    Route::post('/login', [HubController::class, 'authenticate']);
    Route::post('/logout', [HubController::class, 'logout'])->name('logout');

    // Rutas protegidas del Hub
    Route::middleware(['web', HubAuthMiddleware::class])->group(function () {
        Route::get('/', [HubController::class, 'index'])->name('hub.index');
        Route::get('/app/{file}', [HubController::class, 'showApp'])->name('hub.app');
        Route::get('/usuarios', [HubController::class, 'users'])->name('hub.users');
        Route::post('/usuarios/{user}/role', [HubController::class, 'toggleRole']);
        Route::delete('/usuarios/{user}', [HubController::class, 'deleteUser']);
        Route::post('/deploy', [HubController::class, 'deploy'])->name('hub.deploy');
    });
});
