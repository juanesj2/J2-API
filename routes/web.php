<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HubController;
use App\Http\Controllers\HubDbController;
use App\Http\Middleware\HubAuthMiddleware;

Route::get("/", function () { return redirect('/hub'); });

// ============================
//     RECUPERAR CONTRASEÑA
// ============================
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::post('/reset-password', [\App\Http\Controllers\Api\AuthController::class, 'resetPassword'])->name('password.update');

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

        // Hub Features
        Route::get('/logs', [HubController::class, 'logs'])->name('hub.logs');
        Route::post('/logs/clear', [HubController::class, 'clearLogs'])->name('hub.logs.clear');
        
        Route::post('/profile/update', [HubController::class, 'updateProfile'])->name('hub.profile.update');

        // Env Editor
        Route::get('/env', [HubController::class, 'envEditor'])->name('hub.env');
        Route::post('/env/verify', [HubController::class, 'verifyEnvPassword'])->name('hub.env.verify');
        Route::post('/env/update', [HubController::class, 'updateEnv'])->name('hub.env.update');
        Route::post('/env/extend', [HubController::class, 'extendEnvSession'])->name('hub.env.extend');

        // Database Viewer
        Route::get('/db', [HubDbController::class, 'index'])->name('hub.db.index');
        Route::post('/db/verify', [HubDbController::class, 'unlockDb'])->name('hub.db.verify');
        Route::post('/db/extend', [HubDbController::class, 'extendSession'])->name('hub.db.extend');
        Route::post('/db/sql', [HubDbController::class, 'executeSql'])->name('hub.db.sql');
        Route::get('/db/{table}', [HubDbController::class, 'show'])->name('hub.db.show');
        Route::post('/db/{table}/insert', [HubDbController::class, 'insertRow'])->name('hub.db.insert');
        Route::put('/db/{table}/{id}', [HubDbController::class, 'updateRow'])->name('hub.db.update');
        Route::delete('/db/{table}/{id}', [HubDbController::class, 'deleteRow'])->name('hub.db.delete');
    });
});
