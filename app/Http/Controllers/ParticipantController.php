<?php

namespace App\Http\Controllers;

use App\Events\ParticipanteEntrou;
use App\Events\RankingAtualizado;
use App\Events\RespostaEnviada;
use App\Game\Engine\GameEngine;
use App\Game\Engine\Scoring\StandardScoring;
use App\Models\Equipe;
use App\Models\Jogo;
use App\Models\Participante;
use App\Models\Questao;
use App\Models\Resposta;
use App\Models\Sala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ParticipantController extends Controller
{
    public function entrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_acesso' => 'required|string|size:6',
            'nome' => 'required|string|max:100',
            'equipe_id' => 'nullable|exists:equipes,id',
            'equipe_nome' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $sala = Sala::where('codigo_acesso', strtoupper(trim($validated['codigo_acesso'])))
            ->where('status', '!=', 'terminada')
            ->first();

        if (!$sala) {
            return response()->json(['error' => 'Sala não encontrada ou já encerrada.'], 404);
        }

        $equipeId = $validated['equipe_id'] ?? null;
        if (!$equipeId && !empty($validated['equipe_nome'])) {
            $equipe = Equipe::firstOrCreate(
                [
                    'sala_id' => $sala->id,
                    'nome' => trim($validated['equipe_nome']),
                ],
                [
                    'cor' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
                ]
            );
            $equipeId = $equipe->id;
        }

        $participanteCodigo = Str::upper(Str::random(6));

        $participante = Participante::create([
            'sala_id' => $sala->id,
            'equipe_id' => $equipeId,
            'nome' => trim($validated['nome']),
            'codigo_acesso' => $participanteCodigo,
            'conectado' => true,
            'pontuacao_total' => 0,
        ]);

        $participante->load('equipe');

        $this->tentarBroadcast(new ParticipanteEntrou($sala->id, [
            'id' => $participante->id,
            'nome' => $participante->nome,
            'equipe' => $participante->equipe?->nome,
            'codigo_acesso' => $participante->codigo_acesso,
        ]));

        return response()->json([
            'sala' => [
                'id' => $sala->id,
                'nome' => $sala->nome,
                'codigo_acesso' => $sala->codigo_acesso,
                'status' => $sala->status,
            ],
            'participante' => [
                'id' => $participante->id,
                'nome' => $participante->nome,
                'codigo_acesso' => $participante->codigo_acesso,
                'equipe_id' => $participante->equipe_id,
                'equipe_nome' => $participante->equipe?->nome,
                'pontuacao_total' => 0,
            ],
        ]);
    }

    public function entrarView(?string $codigo = null)
    {
        $sala = null;
        if ($codigo) {
            $sala = Sala::where('codigo_acesso', strtoupper(trim($codigo)))
                ->where('status', '!=', 'terminada')
                ->first();

            if (!$sala) {
                abort(404, 'Sala não encontrada ou encerrada.');
            }
        }

        return view('participante.entrar', compact('sala', 'codigo'));
    }

    public function mostrar(Request $request, Sala $sala)
    {
        $participantes = $sala->participantes()->conectados()->with('equipe')->orderByDesc('pontuacao_total')->get();
        $equipes = $sala->equipes()->get();

        $jogo = Jogo::where('sala_id', $sala->id)->latest()->first();
        $estadoJogo = $sala->status === 'terminada' ? 'terminado' : ($jogo?->estado ?? 'aguardando');
        $perguntaPublica = null;
        $numeroPergunta = 1;
        $totalPerguntas = 12;
        $tempoRestante = 30;

        if ($jogo && $jogo->dados_motor) {
            $engine = @unserialize($jogo->dados_motor);
            if ($engine instanceof GameEngine) {
                $st = $engine->getState();
                $estadoJogo = $st['estado'];
                $numeroPergunta = $st['numero_pergunta'] ?? 1;
                $totalPerguntas = $st['total_perguntas'] ?? 12;
                $tempoRestante = $st['tempo_restante'] ?? 30;

                if (!empty($st['pergunta_atual'])) {
                    $q = $st['pergunta_atual'];
                    $opcoes = $q['opcoes'] ?? [];
                    if (is_string($opcoes)) {
                        $opcoes = json_decode($opcoes, true) ?: [];
                    }

                    $perguntaPublica = [
                        'id' => $q['id'] ?? null,
                        'texto' => $q['texto'] ?? '',
                        'opcoes' => $opcoes,
                        'livro_nome' => $q['livro_nome'] ?? null,
                        'dificuldade_nome' => $q['dificuldade_nome'] ?? 'Iniciante',
                        'dificuldade_nivel' => $q['dificuldade_nivel'] ?? 1,
                        'categoria_nome' => $q['categoria_nome'] ?? null,
                    ];
                }
            }
        }

        $jaRespondeu = false;
        if ($request->filled('participante_id') && $jogo && $perguntaPublica && !empty($perguntaPublica['id'])) {
            $jaRespondeu = Resposta::where('jogo_id', $jogo->id)
                ->where('questao_id', $perguntaPublica['id'])
                ->where('participante_id', $request->integer('participante_id'))
                ->exists();
        }

        $ranking = $participantes->map(fn ($p) => [
            'id' => $p->id,
            'nome' => $p->nome,
            'equipe' => $p->equipe?->nome,
            'pontuacao' => $p->pontuacao_total,
        ])->values()->all();

        return response()->json([
            'sala' => [
                'id' => $sala->id,
                'nome' => $sala->nome,
                'codigo_acesso' => $sala->codigo_acesso,
                'status' => $sala->status,
            ],
            'estado' => $estadoJogo,
            'numero_pergunta' => $numeroPergunta,
            'total_perguntas' => $totalPerguntas,
            'tempo_restante' => $tempoRestante,
            'pergunta_atual' => $perguntaPublica,
            'ja_respondeu' => $jaRespondeu,
            'participantes' => $participantes,
            'equipes' => $equipes,
            'ranking' => $ranking,
        ]);
    }

    public function responder(Request $request, Sala $sala)
    {
        $validator = Validator::make($request->all(), [
            'participante_id' => 'required|exists:participantes,id',
            'questao_id' => 'required|exists:questoes,id',
            'resposta' => 'required|string',
            'tempo_resposta' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $participante = Participante::find($validated['participante_id']);
        if (!$participante || (int) $participante->sala_id !== (int) $sala->id) {
            return response()->json(['error' => 'Participante não encontrado nesta sala.'], 404);
        }

        $jogo = $sala->jogoAtivo()->first();
        if (!$jogo) {
            $jogo = Jogo::create([
                'sala_id' => $sala->id,
                'estado' => 'em_curso',
                'pontuacao_total' => 0,
            ]);
        }

        $jaRespondeu = Resposta::where('jogo_id', $jogo->id)
            ->where('questao_id', $validated['questao_id'])
            ->where('participante_id', $participante->id)
            ->exists();

        if ($jaRespondeu) {
            return response()->json(['error' => 'Você já respondeu esta pergunta!'], 409);
        }

        $questao = Questao::findOrFail($validated['questao_id']);
        $opcoes = is_array($questao->opcoes) ? $questao->opcoes : (json_decode((string) $questao->opcoes, true) ?: []);

        $respostaTexto = trim($validated['resposta']);
        if (is_numeric($respostaTexto) && isset($opcoes[(int) $respostaTexto])) {
            $respostaTexto = (string) $opcoes[(int) $respostaTexto];
        }

        $acertou = mb_strtolower(trim($respostaTexto), 'UTF-8') === mb_strtolower(trim($questao->resposta_correta), 'UTF-8');
        $tempo = (int) $validated['tempo_resposta'];

        $scoring = new StandardScoring();
        $pontos = $scoring->calcularPontos($acertou, $tempo, 30, 0);

        Resposta::create([
            'jogo_id' => $jogo->id,
            'questao_id' => $validated['questao_id'],
            'participante_id' => $participante->id,
            'equipe_id' => $participante->equipe_id,
            'resposta' => $respostaTexto,
            'correta' => $acertou,
            'tempo_resposta' => $tempo,
            'pontos_ganhos' => $pontos,
        ]);

        if ($acertou) {
            $participante->increment('pontuacao_total', $pontos);
            $participante->refresh();

            if ($participante->equipe_id) {
                $participante->equipe()->increment('pontuacao_total', $pontos);
            }

            $jogo->increment('pontuacao_total', $pontos);
        }

        $this->tentarBroadcast(new RespostaEnviada(
            $sala->id,
            $participante->id,
            $participante->nome,
            (int) $validated['questao_id'],
            $acertou,
            $pontos,
            $tempo
        ));

        $ranking = $sala->participantes()
            ->conectados()
            ->with('equipe')
            ->orderByDesc('pontuacao_total')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nome' => $p->nome,
                    'equipe' => $p->equipe?->nome,
                    'pontuacao' => $p->pontuacao_total,
                ];
            });

        $this->tentarBroadcast(new RankingAtualizado($sala->id, $ranking->values()->all()));

        return response()->json([
            'acertou' => $acertou,
            'pontos' => $pontos,
            'resposta_dada' => $respostaTexto,
            'resposta_correta' => $questao->resposta_correta,
            'referencia_biblica' => $questao->referencia_biblica,
            'explicacao' => $questao->explicacao,
            'participante' => [
                'pontuacao_total' => $participante->pontuacao_total,
            ],
        ]);
    }

    public function sair(Request $request, Sala $sala, Participante $participante)
    {
        if ((int) $participante->sala_id !== (int) $sala->id) {
            return response()->json(['error' => 'Participante não encontrado nesta sala.'], 404);
        }

        $participante->update(['conectado' => false]);

        $this->tentarBroadcast(new ParticipanteEntrou($sala->id, [
            'id' => $participante->id,
            'nome' => $participante->nome,
            'saiu' => true,
        ]));

        return response()->json(['success' => true]);
    }

    private function tentarBroadcast(object $evento): void
    {
        try {
            broadcast($evento);
        } catch (\Throwable $e) {
            // Ignora caso Reverb não esteja ativo
        }
    }
}
