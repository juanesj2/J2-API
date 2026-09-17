<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AtlasController;
use App\Http\Middleware\AtlasAuthMiddleware;

/*
|--------------------------------------------------------------------------
| Rutas del Ecosistema ATLAS (IA & Domótica Híbrida)
|--------------------------------------------------------------------------
*/

Route::prefix('atlas')->middleware([AtlasAuthMiddleware::class])->group(function () {
    // Endpoint principal para ejecutar los "Tool Calls" de la IA
    Route::post('/execute', [AtlasController::class, 'execute']);
});
