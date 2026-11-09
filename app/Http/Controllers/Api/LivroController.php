<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Capitulo;
use App\Models\LivroBiblico;
use Illuminate\Http\Request;

class LivroController extends Controller
{
    public function capitulos(LivroBiblico $livro)
    {
        $capitulos = Capitulo::where('livro_id', $livro->id)
            ->orderBy('numero')
            ->get(['id', 'numero']);

        return response()->json($capitulos);
    }
}
