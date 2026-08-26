<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BotController;

/*
|--------------------------------------------------------------------------
| Rutas del Bot Agnóstico (J2-Bot Gateway)
|--------------------------------------------------------------------------
*/

Route::prefix('bot')->group(function () {
    // Ruta principal que escucha los mensajes que le manda Node.js
    Route::post('/webhook', [BotController::class, 'handleWebhook']);
});
