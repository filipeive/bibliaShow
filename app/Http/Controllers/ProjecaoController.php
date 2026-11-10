<?php

namespace App\Http\Controllers;

use App\Game\Engine\GameEngine;
use App\Models\Jogo;
use App\Models\Sala;

class ProjecaoController extends Controller
{
    public function tela($salaId)
    {
        $sala = Sala::with('participantes.equipe')->findOrFail($salaId);

        $jogo = Jogo::where('sala_id', $salaId)->latest()->first();
        $engine = null;

        if ($jogo && $jogo->dados_motor) {
            $unserialized = @unserialize($jogo->dados_motor);
            if ($unserialized instanceof GameEngine) {
                $engine = $unserialized;
            }
        }

        if (!$engine && session('game_engine_' . $salaId)) {
            $unserialized = @unserialize(session('game_engine_' . $salaId));
            if ($unserialized instanceof GameEngine) {
                $engine = $unserialized;
            }
        }

        if (!$engine) {
            return view('projecao.espera', ['salaId' => $salaId, 'sala' => $sala]);
        }

        $estado = $engine->getState();

        $participantes = $sala->participantes()
            ->conectados()
            ->with('equipe')
            ->orderByDesc('pontuacao_total')
            ->get();

        $ranking = $participantes->map(fn ($p) => [
            'id' => $p->id,
            'nome' => $p->nome,
            'equipe' => $p->equipe?->nome,
            'pontuacao' => $p->pontuacao_total,
        ])->values()->all();

        $pontuacaoTotal = max((int) $estado['pontuacao'], (int) $participantes->sum('pontuacao_total'));

        return view('projecao.tela', [
            'sala' => $sala,
            'estado' => $estado['estado'],
            'pergunta' => $estado['pergunta_atual'],
            'numero_pergunta' => $estado['numero_pergunta'] ?? 1,
            'total_perguntas' => $estado['total_perguntas'] ?? 24,
            'fase_atual' => $estado['fase_atual'] ?? 1,
            'placar_milhao' => $estado['placar_milhao'] ?? ['errar' => 0, 'parar' => 0, 'acertar' => 10, 'valor_pergunta' => 10, 'fase' => 1],
            'ajudas_motor' => $estado['ajudas'] ?? [],
            'pontuacao' => $pontuacaoTotal,
            'tempo_restante' => $estado['tempo_restante'],
            'tempo_esgotado' => $estado['tempo_esgotado'],
            'ranking' => $ranking,
        ]);
    }
}
