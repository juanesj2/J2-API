<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HubController;
use App\Http\Controllers\HubDbController;
use App\Http\Middleware\HubAuthMiddleware;

Route::get("/", function () { return redirect('/hub'); });

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
        Route::get('/app/{file}/export', [HubController::class, 'exportCollection'])->name('hub.app.export');
        Route::get('/usuarios', [HubController::class, 'users'])->name('hub.users');
        Route::post('/usuarios/{user}/role', [HubController::class, 'toggleRole']);
        Route::delete('/usuarios/{user}', [HubController::class, 'deleteUser']);
        Route::post('/deploy', [HubController::class, 'deploy'])->name('hub.deploy');

        // Logs
        Route::get('/logs', [HubController::class, 'logs'])->name('hub.logs');
        Route::post('/logs/clear', [HubController::class, 'clearLogs'])->name('hub.logs.clear');

        // Env Editor
        Route::get('/env', [HubController::class, 'envEditor'])->name('hub.env');
        Route::post('/env/verify', [HubController::class, 'verifyEnvPassword'])->name('hub.env.verify');
        Route::post('/env/update', [HubController::class, 'updateEnv'])->name('hub.env.update');

        // Database Viewer
        Route::get('/db', [HubDbController::class, 'index'])->name('hub.db.index');
        Route::get('/db/{table}', [HubDbController::class, 'show'])->name('hub.db.show');
    });
});
