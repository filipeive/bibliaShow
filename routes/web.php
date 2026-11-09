<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('jogar')->name('game.')->group(function () {
    Route::get('/', [GameController::class, 'jogar'])->name('play');
    Route::post('/responder', [GameController::class, 'responder'])->name('answer');
    Route::get('/resultado', [GameController::class, 'resultado'])->name('result');
});
