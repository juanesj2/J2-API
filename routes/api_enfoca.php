<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FotografiaController;
use App\Http\Controllers\Api\ComentarioController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\GrupoController;
use App\Http\Controllers\Api\DesafioController;
use App\Http\Controllers\Api\ReporteController;

Route::middleware('auth:sanctum')->group(function () {

    // ----- FOTOGRAFIAS -----
    Route::get('/fotografias', [FotografiaController::class, 'index']);
    Route::get('/fotografias/buscar', [FotografiaController::class, 'buscar']);
    Route::get('/mis-fotos', [FotografiaController::class, 'misFotos']);
    Route::get('/fotografias-usuario/{id}', [FotografiaController::class, 'fotografiasUsuario']);
    Route::get('/fotografias/{id}', [FotografiaController::class, 'show']);
    Route::post('/fotografias', [FotografiaController::class, 'store']);
    Route::delete('/fotografias/{id}', [FotografiaController::class, 'destroy']);
    Route::put('/fotografias/{id}', [FotografiaController::class, 'update']);
    Route::get('/admin/fotografias', [FotografiaController::class, 'adminIndex']); 

    // ----- COMENTARIOS -----
    Route::get('/fotografias/{fotografiaId}/comentarios', [ComentarioController::class, 'index']);
    Route::post('/fotografias/{fotografiaId}/comentarios', [ComentarioController::class, 'store']);
    Route::delete('/comentarios/{id}', [ComentarioController::class, 'destroy']);

    // ----- LIKES -----
    Route::post('/fotografias/{fotografia}/like', [LikeController::class, 'darLike']);
    Route::delete('/fotografias/{fotografia}/like', [LikeController::class, 'quitarLike']);

    // ----- GRUPOS -----
    Route::get('/grupos/mis-grupos', [GrupoController::class, 'misGrupos']);
    Route::post('/grupos/unirse', [GrupoController::class, 'unirse']);
    Route::delete('/grupos/{id}/salir', [GrupoController::class, 'salir']);
    Route::apiResource('grupos', GrupoController::class);

    // ----- DESAFIOS -----
    Route::get('/desafios/mis-desafios', [DesafioController::class, 'misDesafios']);
    Route::apiResource('desafios', DesafioController::class);

    // ----- REPORTES -----
    Route::post('/reportes', [ReporteController::class, 'store']);
    Route::get('/admin/reportes', [ReporteController::class, 'index']); 
    Route::delete('/admin/reportes/{id}', [ReporteController::class, 'destroyByPhoto']); 

});
