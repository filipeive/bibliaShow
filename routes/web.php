<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ApresentadorController;
use App\Http\Controllers\ProjecaoController;
use App\Http\Controllers\PerguntaController;
use App\Http\Controllers\ParticipantController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('jogar')->name('game.')->group(function () {
    Route::get('/', [GameController::class, 'jogar'])->name('play');
    Route::post('/responder', [GameController::class, 'responder'])->name('answer');
    Route::get('/resultado', [GameController::class, 'resultado'])->name('result');
});

Route::prefix('apresentador')->name('apresentador.')->group(function () {
    Route::get('/', [ApresentadorController::class, 'painel'])->name('painel');
    Route::post('/criar', [ApresentadorController::class, 'criarSala'])->name('criar');
    Route::get('/control/{sala}', [ApresentadorController::class, 'control'])->name('control');
    Route::post('/control/{sala}/proxima', [ApresentadorController::class, 'proximaPergunta'])->name('proxima');
    Route::post('/control/{sala}/pausar', [ApresentadorController::class, 'pausar'])->name('pausar');
    Route::post('/control/{sala}/retomar', [ApresentadorController::class, 'retomar'])->name('retomar');
    Route::post('/control/{sala}/finalizar', [ApresentadorController::class, 'finalizar'])->name('finalizar');
    Route::get('/control/{sala}/resultado', [ApresentadorController::class, 'resultado'])->name('resultado');
});

Route::prefix('projecao')->name('projecao.')->group(function () {
    Route::get('/{sala}', [ProjecaoController::class, 'tela'])->name('tela');
});

Route::prefix('perguntas')->name('perguntas.')->group(function () {
    Route::get('/', [PerguntaController::class, 'index'])->name('index');
    Route::get('/criar', [PerguntaController::class, 'create'])->name('create');
    Route::post('/', [PerguntaController::class, 'store'])->name('store');
    Route::get('/{questao}', [PerguntaController::class, 'show'])->name('show');
    Route::get('/{questao}/editar', [PerguntaController::class, 'edit'])->name('edit');
    Route::put('/{questao}', [PerguntaController::class, 'update'])->name('update');
    Route::delete('/{questao}', [PerguntaController::class, 'destroy'])->name('destroy');
    Route::post('/{questao}/aprovar', [PerguntaController::class, 'aprovar'])->name('aprovar');
    Route::post('/{questao}/rejeitar', [PerguntaController::class, 'rejeitar'])->name('rejeitar');
});

// Participant entry (multijogador)
Route::get('/entrar/{codigo}', [ParticipantController::class, 'entrarView'])->name('entrar.view');
