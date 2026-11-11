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
            $unserialized = GameEngine::desserializarSeguro($jogo->dados_motor);
            if ($unserialized instanceof GameEngine) {
                $engine = $unserialized;
            }
        }

        if (!$engine && session('game_engine_' . $salaId)) {
            $unserialized = GameEngine::desserializarSeguro(session('game_engine_' . $salaId));
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

        $respostasRodada = [];
        $curQ = $estado['pergunta_atual'] ?? null;
        if ($curQ && !empty($curQ['id']) && $jogo) {
            $todasRespostas = \App\Models\Resposta::where('jogo_id', $jogo->id)
                ->where('questao_id', $curQ['id'])
                ->get();
            $letras = ['A', 'B', 'C', 'D'];
            $opcoesQ = is_string($curQ['opcoes'] ?? []) ? json_decode($curQ['opcoes'], true) : ($curQ['opcoes'] ?? []);

            foreach ($todasRespostas as $r) {
                $letra = null;
                $idxEscolhido = null;
                foreach ($opcoesQ as $idx => $opTxt) {
                    if (mb_strtolower(trim((string) $opTxt), 'UTF-8') === mb_strtolower(trim((string) $r->resposta), 'UTF-8')) {
                        $idxEscolhido = (int) $idx;
                        $letra = $letras[$idx] ?? (string) ($idx + 1);
                        break;
                    }
                }
                if ($letra === null && is_numeric($r->resposta)) {
                    $idxNum = (int) $r->resposta;
                    $idxEscolhido = $idxNum;
                    $letra = $letras[$idxNum] ?? '?';
                }

                $respostasRodada[$r->participante_id] = [
                    'participante_id' => $r->participante_id,
                    'resposta' => $r->resposta,
                    'letra' => $letra ?? '?',
                    'indice' => $idxEscolhido,
                    'correta' => (bool) $r->correta,
                    'tempo' => $r->tempo_resposta,
                ];
            }
        }

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
            'participantes' => $participantes,
            'respostas_rodada' => $respostasRodada,
            'ranking' => $ranking,
        ]);
    }
}
