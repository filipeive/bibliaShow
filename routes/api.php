<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LivroController;

Route::get('/livros/{livro}/capitulos', [LivroController::class, 'capitulos']);
