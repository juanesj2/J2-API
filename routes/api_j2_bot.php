<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BotController;

/*
|--------------------------------------------------------------------------
| Rutas del Bot Agnóstico (J2-Bot Gateway)
|--------------------------------------------------------------------------
*/

Route::prefix('bot')->group(function () {
    // Endpoints para el Polling y Webhooks del Gateway Node.js
    Route::post('/webhook', [BotController::class, 'handleWebhook']);
    Route::get('/pending-messages', [BotController::class, 'getPendingMessages']);
    Route::post('/mark-sent', [BotController::class, 'markMessagesSent']);

    // Endpoints protegidos para el Panel Web del Hub
    Route::middleware(['web', \App\Http\Middleware\HubAuthMiddleware::class])->group(function () {
        Route::get('/chats', [BotController::class, 'getChats']);
        Route::get('/messages/{phone}', [BotController::class, 'getMessages']);
        Route::post('/web-send', [BotController::class, 'sendWebMessage']);
        Route::post('/approve-draft', [BotController::class, 'approveDraft']);
        Route::delete('/messages/{id}', [BotController::class, 'deleteMessage']);
        Route::get('/settings', [BotController::class, 'getSettings']);
        Route::post('/settings/toggle', [BotController::class, 'toggleSettings']);
    });
});
