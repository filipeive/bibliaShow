<?php

namespace App\Http\Controllers;

use App\Game\Engine\GameEngine;
use Illuminate\Http\Request;

class ProjecaoController extends Controller
{
    public function tela($salaId)
    {
        $engine = session('game_engine_' . $salaId) ? unserialize(session('game_engine_' . $salaId)) : null;

        if (!$engine) {
            return view('projecao.espera', ['salaId' => $salaId]);
        }

        $estado = $engine->getState();

        return view('projecao.tela', [
            'salaId' => $salaId,
            'estado' => $estado['estado'],
            'pergunta' => $estado['pergunta_atual'],
            'pontuacao' => $estado['pontuacao'],
            'tempo_restante' => $estado['tempo_restante'],
            'tempo_esgotado' => $estado['tempo_esgotado'],
            'ranking' => $estado['ranking'],
        ]);
    }
}
