<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BotController;

/*
|--------------------------------------------------------------------------
| Rutas del Bot Agnóstico (J2-Bot Gateway)
|--------------------------------------------------------------------------
*/

Route::prefix('bot')->group(function () {
    // Ruta principal que escucha los mensajes que le manda Node.js
    Route::post('/webhook', [BotController::class, 'handleWebhook']);

    // Endpoints para el Panel Web
    Route::get('/chats', [BotController::class, 'getChats']);
    Route::get('/messages/{phone}', [BotController::class, 'getMessages']);
    Route::post('/web-send', [BotController::class, 'sendWebMessage']);

    // Endpoints para el Polling del Bot Node.js
    Route::get('/pending-messages', [BotController::class, 'getPendingMessages']);
    Route::post('/mark-sent', [BotController::class, 'markMessagesSent']);
});
