<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApresentadorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PerguntaController;
use App\Http\Controllers\ProjecaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Painel de Administração (Protegido por Autenticação)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/igrejas', [AdminController::class, 'criarIgreja'])->name('igrejas.criar');
    Route::post('/usuarios', [AdminController::class, 'salvarUsuario'])->name('usuarios.salvar');
    Route::post('/reimportar-classico', [AdminController::class, 'reimportarClassico'])->name('reimportar');
    Route::get('/exportar-perguntas', [AdminController::class, 'exportarPerguntas'])->name('exportar');
});

// Enciclopédia Bíblica & Regras Clássicas
Route::get('/curiosidades', [GameController::class, 'curiosidades'])->name('curiosidades');

// Modo Individual Clássico (Show do Milhão Bíblico)
Route::prefix('jogar')->name('game.')->group(function () {
    Route::get('/', [GameController::class, 'jogar'])->name('play');
    Route::post('/jogador', [GameController::class, 'definirJogador'])->name('player');
    Route::post('/responder', [GameController::class, 'responder'])->name('answer');
    Route::post('/ajuda', [GameController::class, 'usarAjuda'])->name('lifeline');
    Route::post('/encerrar', [GameController::class, 'encerrar'])->name('finish');
    Route::get('/resultado', [GameController::class, 'resultado'])->name('result');
});

// Modo Apresentador & Salas Ao Vivo
Route::prefix('apresentador')->name('apresentador.')->group(function () {
    Route::get('/', [ApresentadorController::class, 'painel'])->name('painel');
    Route::post('/criar', [ApresentadorController::class, 'criarSala'])->name('criar');
    Route::get('/control/{sala}', [ApresentadorController::class, 'control'])->name('control');
    Route::post('/control/{sala}/ajuda', [ApresentadorController::class, 'usarAjuda'])->name('ajuda');
    Route::post('/control/{sala}/premios', [ApresentadorController::class, 'atualizarPremios'])->name('premios');
    Route::post('/control/{sala}/equipes', [ApresentadorController::class, 'criarEquipe'])->name('equipe.criar');
    Route::post('/control/{sala}/proxima', [ApresentadorController::class, 'proximaPergunta'])->name('proxima');
    Route::post('/control/{sala}/pausar', [ApresentadorController::class, 'pausar'])->name('pausar');
    Route::post('/control/{sala}/retomar', [ApresentadorController::class, 'retomar'])->name('retomar');
    Route::post('/control/{sala}/finalizar', [ApresentadorController::class, 'finalizar'])->name('finalizar');
    Route::get('/control/{sala}/resultado', [ApresentadorController::class, 'resultado'])->name('resultado');
});

// Projeção para Telão / TV
Route::prefix('projecao')->name('projecao.')->group(function () {
    Route::get('/{sala}', [ProjecaoController::class, 'tela'])->name('tela');
});

// Gestão de Perguntas
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

// Entrada e jogo de participantes (multijogador via smartphone)
Route::get('/entrar/{codigo?}', [ParticipantController::class, 'entrarView'])->name('entrar.view');
