<?php
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('love-album')->group(function () {
    Route::post('/pair', [\App\Http\Controllers\Api\LoveAlbumController::class, 'pair']);
    Route::post('/unpair', [\App\Http\Controllers\Api\LoveAlbumController::class, 'unpair']);
    Route::get('/info', [\App\Http\Controllers\Api\LoveAlbumController::class, 'getCoupleInfo']);
    Route::put('/info', [\App\Http\Controllers\Api\LoveAlbumController::class, 'updateCoupleInfo']);
    Route::post('/poke', [\App\Http\Controllers\Api\LoveAlbumController::class, 'poke']);
    Route::post('/remind-streak', [\App\Http\Controllers\Api\LoveAlbumController::class, 'remindStreak']);
    Route::post('/custom-notification', [\App\Http\Controllers\Api\LoveAlbumController::class, 'customNotification']);
    Route::post('/save-fcm-token', [\App\Http\Controllers\Api\LoveAlbumController::class, 'saveFcmToken']);
    Route::post('/avatar', [\App\Http\Controllers\Api\LoveAlbumController::class, 'uploadAvatar']);
    Route::get('/roulette', [\App\Http\Controllers\Api\LoveAlbumController::class, 'getRouletteOptions']);
    Route::post('/roulette', [\App\Http\Controllers\Api\LoveAlbumController::class, 'updateRouletteOptions']);
    
    // Timeline y Planes (Sustituye a Hitos y Deseos)
    Route::get('/plans', [\App\Http\Controllers\Api\CouplePlanController::class, 'index']);
    Route::post('/plans', [\App\Http\Controllers\Api\CouplePlanController::class, 'store']);
    Route::put('/plans/{id}', [\App\Http\Controllers\Api\CouplePlanController::class, 'update']);
    Route::delete('/plans/{id}', [\App\Http\Controllers\Api\CouplePlanController::class, 'destroy']);

    // Buzón Secreto (Cartitas de Amor)
    Route::get('/secret-notes', [\App\Http\Controllers\Api\SecretNoteController::class, 'index']);
    Route::post('/secret-notes', [\App\Http\Controllers\Api\SecretNoteController::class, 'store']);
    Route::delete('/secret-notes/{id}', [\App\Http\Controllers\Api\SecretNoteController::class, 'destroy']);

    // Widget Extras
    Route::get('/widget/food-places', [\App\Http\Controllers\Api\WidgetController::class, 'getFoodPlaces']);
    Route::post('/widget/food-places', [\App\Http\Controllers\Api\WidgetController::class, 'addFoodPlace']);
    Route::put('/widget/food-places/{id}', [\App\Http\Controllers\Api\WidgetController::class, 'updateFoodPlace']);
    Route::delete('/widget/food-places/{id}', [\App\Http\Controllers\Api\WidgetController::class, 'deleteFoodPlace']);

    Route::post('/widget/food-places/{placeId}/dishes', [\App\Http\Controllers\Api\WidgetController::class, 'addFoodDish']);
    Route::post('/widget/food-places/{placeId}/dishes/{dishId}', [\App\Http\Controllers\Api\WidgetController::class, 'updateFoodDish']);
    Route::delete('/widget/food-places/{placeId}/dishes/{dishId}', [\App\Http\Controllers\Api\WidgetController::class, 'deleteFoodDish']);

    Route::get('/widget/movies', [\App\Http\Controllers\Api\WidgetController::class, 'getMovies']);
    Route::post('/widget/movies', [\App\Http\Controllers\Api\WidgetController::class, 'addMovie']);
    Route::put('/widget/movies/{id}', [\App\Http\Controllers\Api\WidgetController::class, 'updateMovie']);
    Route::delete('/widget/movies/{id}', [\App\Http\Controllers\Api\WidgetController::class, 'deleteMovie']);
    
    // Preguntas (Minijuego)
    Route::get('/questions', [\App\Http\Controllers\Api\LoveAlbumController::class, 'getQuestions']);
    Route::post('/questions/{id}/answer', [\App\Http\Controllers\Api\LoveAlbumController::class, 'answerQuestion']);
    
    // Álbumes Personalizados (Colecciones)
    Route::get('/albums', [\App\Http\Controllers\Api\LoveAlbumController::class, 'getAlbums']);
    Route::post('/albums', [\App\Http\Controllers\Api\LoveAlbumController::class, 'createAlbum']);
    Route::put('/albums/{id}', [\App\Http\Controllers\Api\LoveAlbumController::class, 'updateAlbum']);
    Route::post('/albums/{id}/cover', [\App\Http\Controllers\Api\LoveAlbumController::class, 'updateAlbumCover']);
    Route::post('/albums/{id}/photos', [\App\Http\Controllers\Api\LoveAlbumController::class, 'assignPhotosToAlbum']);
    Route::post('/albums/photos/remove', [\App\Http\Controllers\Api\LoveAlbumController::class, 'removePhotosFromAlbum']);
    Route::delete('/albums/{id}', [\App\Http\Controllers\Api\LoveAlbumController::class, 'deleteAlbum']);

    // Fotos
    Route::get('/photos', [\App\Http\Controllers\Api\LoveAlbumController::class, 'index']);
    Route::post('/photos', [\App\Http\Controllers\Api\LoveAlbumController::class, 'store']);
    Route::get('/photos/{id}', [\App\Http\Controllers\Api\LoveAlbumController::class, 'show']);
    Route::get('/photos/{id}/download', [\App\Http\Controllers\Api\LoveAlbumController::class, 'download']);
    Route::delete('/photos/{id}', [\App\Http\Controllers\Api\LoveAlbumController::class, 'destroy']);
    
    // Reacciones a Fotos
    Route::post('/photos/{id}/reactions', [\App\Http\Controllers\Api\LoveAlbumController::class, 'react']);
    
    // Chat Privado
    Route::get('/chat', [\App\Http\Controllers\Api\CoupleChatController::class, 'index']);
    Route::post('/chat', [\App\Http\Controllers\Api\CoupleChatController::class, 'store']);
    Route::post('/chat/delivered', [\App\Http\Controllers\Api\CoupleChatController::class, 'markDelivered']);
    Route::put('/chat/{id}', [\App\Http\Controllers\Api\CoupleChatController::class, 'update']);
    Route::delete('/chat/{id}', [\App\Http\Controllers\Api\CoupleChatController::class, 'destroy']);
    Route::post('/chat/{id}/react', [\App\Http\Controllers\Api\CoupleChatController::class, 'react']);
    
    // Achievements
    Route::get('/achievements', [\App\Http\Controllers\Api\AchievementController::class, 'index']);
    Route::post('/achievements/unlock', [\App\Http\Controllers\Api\AchievementController::class, 'unlock']);
    Route::post('/achievements/hints/unlock', [\App\Http\Controllers\Api\AchievementController::class, 'unlockHint']);
    
    // Minijuegos
    Route::get('/games/progress', [\App\Http\Controllers\Api\GameController::class, 'getGameProgress']);
    Route::get('/games/swipe/categories', [\App\Http\Controllers\Api\GameController::class, 'getSwipeCategories']);
    Route::get('/games/swipe/cards', [\App\Http\Controllers\Api\GameController::class, 'getSwipeCards']);
    Route::get('/games/swipe/all', [\App\Http\Controllers\Api\GameController::class, 'getAllSwipeCards']);
    Route::post('/games/swipe/answer', [\App\Http\Controllers\Api\GameController::class, 'answerSwipe']);
    Route::get('/games/swipe/stats', [\App\Http\Controllers\Api\GameController::class, 'getSwipeStats']);

    Route::get('/games/drawing/categories', [\App\Http\Controllers\Api\GameController::class, 'getDrawingCategories']);
    Route::get('/games/drawing/prompt', [\App\Http\Controllers\Api\GameController::class, 'getDrawingPrompt']);
    Route::get('/games/drawing/all', [\App\Http\Controllers\Api\GameController::class, 'getAllDrawingPrompts']);
    Route::post('/games/drawing/upload', [\App\Http\Controllers\Api\GameController::class, 'uploadDrawing']);
    Route::get('/games/drawing/{promptId}/result', [\App\Http\Controllers\Api\GameController::class, 'getDrawingResult']);
});
