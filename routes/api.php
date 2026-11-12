<?php

use App\Http\Controllers\Api\FlutterApiController;
use App\Http\Controllers\Api\LivroController;
use App\Http\Controllers\ParticipantController;
use Illuminate\Support\Facades\Route;

Route::get('/livros/{livro}/capitulos', [LivroController::class, 'capitulos']);

// ==========================================
// API RESTful v1 (Pronta para App Flutter)
// ==========================================
Route::prefix('v1')->group(function () {
    Route::get('/meta', [FlutterApiController::class, 'meta']);
    Route::get('/ranking', [FlutterApiController::class, 'ranking']);
    Route::get('/salas/{codigo}', [FlutterApiController::class, 'consultarSalaAoVivo']);

    // Jogo Solo / Individual
    Route::prefix('game')->group(function () {
        Route::post('/start', [FlutterApiController::class, 'startGame']);
        Route::post('/answer', [FlutterApiController::class, 'answer']);
        Route::post('/lifeline', [FlutterApiController::class, 'lifeline']);
        Route::post('/stop', [FlutterApiController::class, 'stopGame']);
        Route::get('/status', [FlutterApiController::class, 'status']);
    });
});

// Participant multijogador routes
Route::prefix('salas')->name('salas.')->group(function () {
    Route::post('/entrar', [ParticipantController::class, 'entrar'])->name('entrar');
    Route::get('/{sala}', [ParticipantController::class, 'mostrar'])->name('mostrar');
    Route::post('/{sala}/responder', [ParticipantController::class, 'responder'])->name('responder');
    Route::post('/{sala}/ajuda', [ParticipantController::class, 'usarAjuda'])->name('ajuda');
    Route::post('/{sala}/participantes/{participante}/equipe', [ParticipantController::class, 'juntarEquipe'])->name('juntar-equipe');
    Route::delete('/{sala}/participantes/{participante}', [ParticipantController::class, 'sair'])->name('sair');
});
