<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LivroController;
use App\Http\Controllers\ParticipantController;

Route::get('/livros/{livro}/capitulos', [LivroController::class, 'capitulos']);

// Participant multijogador routes
Route::prefix('salas')->name('salas.')->group(function () {
    Route::post('/entrar', [ParticipantController::class, 'entrar'])->name('entrar');
    Route::get('/{sala}', [ParticipantController::class, 'mostrar'])->name('mostrar');
    Route::post('/{sala}/responder', [ParticipantController::class, 'responder'])->name('responder');
    Route::delete('/{sala}/participantes/{participante}', [ParticipantController::class, 'sair'])->name('sair');
});
