<?php

namespace App\Http\Controllers;

use App\Events\ParticipanteEntrou;
use App\Events\RankingAtualizado;
use App\Events\RespostaEnviada;
use App\Game\Engine\GameEngine;
use App\Models\Equipe;
use App\Models\Jogo;
use App\Models\Participante;
use App\Models\Questao;
use App\Models\Resposta;
use App\Models\Sala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $sala = Sala::where('codigo_acesso', strtoupper(trim($validated['codigo_acesso'])))->first();

        if (! $sala) {
            return response()->json(['error' => 'Sala não encontrada. Verifique o PIN digitado.'], 404);
        }

        $equipeId = $validated['equipe_id'] ?? null;
        if (! $equipeId && ! empty($validated['equipe_nome'])) {
            $equipe = Equipe::firstOrCreate(
                [
                    'sala_id' => $sala->id,
                    'nome' => trim($validated['equipe_nome']),
                ],
                [
                    'cor' => '#'.str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
                ]
            );
            $equipeId = $equipe->id;
        }

        $participanteCodigo = Str::upper(Str::random(6));

        // Reutilizar participante se já existe na sala com mesmo nome e reconectar
        $participante = Participante::where('sala_id', $sala->id)
            ->where('nome', trim($validated['nome']))
            ->first();

        if ($participante) {
            $participante->update([
                'conectado' => true,
                'equipe_id' => $equipeId ?: $participante->equipe_id,
            ]);
        } else {
            $participante = Participante::create([
                'sala_id' => $sala->id,
                'equipe_id' => $equipeId,
                'nome' => trim($validated['nome']),
                'codigo_acesso' => $participanteCodigo,
                'conectado' => true,
                'pontuacao_total' => 0,
            ]);
        }

        session(['participante_id_'.$sala->id => $participante->id]);

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
                'premio_primeiro_lugar' => $sala->premio_primeiro_lugar,
                'premio_segundo_lugar' => $sala->premio_segundo_lugar,
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
            $sala = Sala::where('codigo_acesso', strtoupper(trim($codigo)))->first();

            if (! $sala) {
                abort(404, 'Sala não encontrada. Verifique o código digitado.');
            }
        }

        return view('participante.entrar', compact('sala', 'codigo'));
    }

    public function mostrar(Request $request, Sala $sala)
    {
        $participantes = $sala->participantes()->conectados()->with('equipe')->orderByDesc('pontuacao_total')->get();
        $equipes = $sala->equipes()->get();

        $jogo = Jogo::where('sala_id', $sala->id)
            ->whereIn('estado', ['em_curso', 'pausado', 'aguardando'])
            ->latest('id')
            ->first()
            ?? Jogo::where('sala_id', $sala->id)->latest('id')->first();

        $estadoJogo = $jogo?->estado ?? ($sala->status === 'terminada' ? 'terminado' : 'aguardando');
        $perguntaPublica = null;
        $numeroPergunta = 1;
        $totalPerguntas = 24;
        $faseAtual = 1;
        $placarMilhao = ['errar' => 0, 'parar' => 0, 'acertar' => 10, 'valor_pergunta' => 10, 'fase' => 1];
        $ajudasAtivas = [
            'opcoes_eliminadas' => [],
            'dica_biblica' => null,
            'dica_convidado' => null,
            'gabarito_revelado' => false,
        ];
        $tempoRestante = 60;

        if ($jogo && $jogo->dados_motor) {
            $engine = GameEngine::desserializarSeguro($jogo->dados_motor);
            if ($engine instanceof GameEngine) {
                $st = $engine->getState();
                $estadoJogo = $st['estado'];
                $numeroPergunta = $st['numero_pergunta'] ?? 1;
                $totalPerguntas = $st['total_perguntas'] ?? 24;
                $faseAtual = $st['fase_atual'] ?? 1;
                $placarMilhao = $st['placar_milhao'] ?? $placarMilhao;
                $tempoRestante = $st['tempo_restante'] ?? 60;
                $ajudasMotor = $st['ajudas'] ?? [];

                $gabaritoRevelado = ! empty($ajudasMotor['gabarito_revelado']);
                $ajudasAtivas = [
                    'opcoes_eliminadas' => $ajudasMotor['opcoes_eliminadas'] ?? [],
                    'dica_biblica' => $ajudasMotor['dica_biblica'] ?? null,
                    'dica_convidado' => $ajudasMotor['dica_convidado'] ?? null,
                    'gabarito_revelado' => $gabaritoRevelado,
                ];

                if (! empty($st['pergunta_atual'])) {
                    $q = $st['pergunta_atual'];
                    $opcoes = $q['opcoes'] ?? [];
                    if (is_string($opcoes)) {
                        $opcoes = json_decode($opcoes, true) ?: [];
                    }

                    // Resolver ID real se desajustado por migração ou reseed
                    $qId = $q['id'] ?? null;
                    if ($qId && ! Questao::where('id', $qId)->exists()) {
                        $qReal = null;
                        if (! empty($q['codigo_classico'])) {
                            $qReal = Questao::where('codigo_classico', $q['codigo_classico'])->first();
                        }
                        if (! $qReal && ! empty($q['texto'])) {
                            $qReal = Questao::where('texto', $q['texto'])->first();
                        }
                        if ($qReal) {
                            $qId = $qReal->id;
                        }
                    }

                    $perguntaPublica = [
                        'id' => $qId,
                        'texto' => $q['texto'] ?? '',
                        'opcoes' => $opcoes,
                        'livro_nome' => $q['livro_nome'] ?? null,
                        'dificuldade_nome' => $q['dificuldade_nome'] ?? '1ª Fase',
                        'dificuldade_nivel' => $q['dificuldade_nivel'] ?? $faseAtual,
                        'categoria_nome' => $q['categoria_nome'] ?? null,
                        'resposta_correta' => $gabaritoRevelado ? ($q['resposta_correta'] ?? null) : null,
                        'referencia_biblica' => $gabaritoRevelado ? ($q['referencia_biblica'] ?? null) : null,
                    ];
                }
            }
        }

        $jaRespondeu = false;
        $respostasRodada = [];
        if ($jogo && $perguntaPublica && ! empty($perguntaPublica['id'])) {
            $todasRespostas = Resposta::where('jogo_id', $jogo->id)
                ->where(function ($query) use ($perguntaPublica) {
                    $query->where('questao_id', $perguntaPublica['id']);
                })
                ->get();

            $letras = ['A', 'B', 'C', 'D'];
            $opcoesQ = $perguntaPublica['opcoes'] ?? [];

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
                    'correta' => $gabaritoRevelado ? (bool) $r->correta : null,
                    'tempo' => $r->tempo_resposta,
                    'pontos' => $gabaritoRevelado ? $r->pontos_ganhos : 0,
                ];
            }

            if ($request->filled('participante_id')) {
                $partId = $request->integer('participante_id');
                $jaRespondeu = isset($respostasRodada[$partId]);
                $partModel = $participantes->firstWhere('id', $partId) ?? Participante::find($partId);
                if ($partModel) {
                    $partAjudas = $partModel->ajudas_usadas ?? [];
                    $ajudasAtivas['dicas_restantes'] = max(0, 3 - ($partAjudas['dicas_usadas'] ?? 0));
                    $ajudasAtivas['cartas_restantes'] = max(0, 1 - ($partAjudas['cartas_usadas'] ?? 0));
                    $ajudasAtivas['convidados_restantes'] = max(0, 3 - ($partAjudas['convidados_usados'] ?? 0));

                    $pIdKey = $perguntaPublica['id'] ?? $numeroPergunta;
                    $hist = $partAjudas['historico_por_pergunta'][$pIdKey] ?? [];
                    if (! empty($hist['dica_biblica'])) {
                        $ajudasAtivas['dica_biblica'] = $hist['dica_biblica'];
                    }
                    if (! empty($hist['dica_convidado'])) {
                        $ajudasAtivas['dica_convidado'] = $hist['dica_convidado'];
                    }
                    if (! empty($hist['opcoes_eliminadas'])) {
                        $ajudasAtivas['opcoes_eliminadas'] = array_values(array_unique(array_merge(
                            $ajudasAtivas['opcoes_eliminadas'] ?? [],
                            $hist['opcoes_eliminadas']
                        )));
                    }
                }
            }
        }

        if (! isset($ajudasAtivas['dicas_restantes'])) {
            $ajudasAtivas['dicas_restantes'] = 3;
            $ajudasAtivas['cartas_restantes'] = 1;
            $ajudasAtivas['convidados_restantes'] = 3;
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
                'premio_primeiro_lugar' => $sala->premio_primeiro_lugar,
                'premio_segundo_lugar' => $sala->premio_segundo_lugar,
            ],
            'estado' => $estadoJogo,
            'numero_pergunta' => $numeroPergunta,
            'total_perguntas' => $totalPerguntas,
            'fase_atual' => $faseAtual,
            'placar_milhao' => $placarMilhao,
            'ajudas' => $ajudasAtivas,
            'tempo_restante' => $tempoRestante,
            'pergunta_atual' => $perguntaPublica,
            'ja_respondeu' => $jaRespondeu,
            'respostas_rodada' => $respostasRodada,
            'participantes' => $participantes,
            'equipes' => $equipes,
            'ranking' => $ranking,
        ]);
    }

    public function responder(Request $request, Sala $sala)
    {
        $validator = Validator::make($request->all(), [
            'participante_id' => 'required|exists:participantes,id',
            'questao_id' => 'nullable',
            'resposta' => 'required|string',
            'tempo_resposta' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $participante = Participante::find($validated['participante_id']);
        if (! $participante || (int) $participante->sala_id !== (int) $sala->id) {
            return response()->json(['error' => 'Participante não encontrado nesta sala.'], 404);
        }

        $jogo = $sala->jogoAtivo()->first();
        if (! $jogo) {
            $jogo = Jogo::create([
                'sala_id' => $sala->id,
                'estado' => 'em_curso',
                'pontuacao_total' => 0,
            ]);
        }

        // Validação de estado e cronômetro da pergunta atual
        if ($jogo && $jogo->dados_motor) {
            $engine = GameEngine::desserializarSeguro($jogo->dados_motor);
            if ($engine instanceof GameEngine) {
                $estadoMotor = $engine->getState();
                if ($estadoMotor['estado'] !== 'em_curso') {
                    return response()->json(['error' => 'A rodada está pausada ou já foi encerrada.'], 403);
                }
                if (! empty($estadoMotor['tempo_esgotado'])) {
                    return response()->json(['error' => 'Tempo esgotado para esta pergunta! Resposta não computada.'], 408);
                }
            }
        }

        // Resolução resiliente da Questão para garantir integridade referencial com sqlite
        $questaoId = $validated['questao_id'] ?? null;
        $questao = null;
        if ($questaoId && $questaoId !== 'null' && is_numeric($questaoId) && (int) $questaoId > 0) {
            $questao = Questao::with('dificuldade')->find($questaoId);
        }

        if (! $questao && $jogo && $jogo->dados_motor) {
            $engine = GameEngine::desserializarSeguro($jogo->dados_motor);
            if ($engine instanceof GameEngine) {
                $curQ = $engine->getCurrentQuestion();
                if ($curQ) {
                    if (! empty($curQ['id'])) {
                        $questao = Questao::with('dificuldade')->find($curQ['id']);
                    }
                    if (! $questao && ! empty($curQ['codigo_classico'])) {
                        $questao = Questao::with('dificuldade')->where('codigo_classico', $curQ['codigo_classico'])->first();
                    }
                    if (! $questao && ! empty($curQ['texto'])) {
                        $questao = Questao::with('dificuldade')->where('texto', $curQ['texto'])->first();
                    }
                }
            }
        }
        if (! $questao && $questaoId && is_numeric($questaoId)) {
            $questao = Questao::with('dificuldade')->where('id', ((int) $questaoId) + 1000)->first();
        }
        if (! $questao) {
            return response()->json(['error' => 'Questão não encontrada no banco de dados.'], 404);
        }

        $questaoIdReal = $questao->id;

        $jaRespondeu = Resposta::where('jogo_id', $jogo->id)
            ->where(function ($query) use ($questaoIdReal, $questaoId) {
                $query->where('questao_id', $questaoIdReal);
                if ($questaoId && is_numeric($questaoId)) {
                    $query->orWhere('questao_id', (int) $questaoId);
                }
            })
            ->where('participante_id', $participante->id)
            ->exists();

        if ($jaRespondeu) {
            return response()->json(['error' => 'Você já respondeu esta pergunta!'], 409);
        }

        $opcoes = is_array($questao->opcoes) ? $questao->opcoes : (json_decode((string) $questao->opcoes, true) ?: []);

        $respostaTexto = trim($validated['resposta']);
        if (is_numeric($respostaTexto) && isset($opcoes[(int) $respostaTexto])) {
            $respostaTexto = (string) $opcoes[(int) $respostaTexto];
        }

        $acertou = mb_strtolower(trim($respostaTexto), 'UTF-8') === mb_strtolower(trim($questao->resposta_correta), 'UTF-8');
        $indiceCorreto = null;
        foreach ($opcoes as $idx => $opTexto) {
            if (mb_strtolower(trim((string) $opTexto), 'UTF-8') === mb_strtolower(trim((string) $questao->resposta_correta), 'UTF-8')) {
                $indiceCorreto = (int) $idx;
                break;
            }
        }
        $tempo = (int) ($validated['tempo_resposta'] ?? 10);

        // Pontuação clássica por fase (10, 20, 30 ou 50 pontos)
        $nivel = $questao->dificuldade?->nivel ?? 1;
        $pontosBaseFase = match ((int) $nivel) {
            1 => 10,
            2 => 20,
            3 => 30,
            default => 50,
        };
        $pontos = $acertou ? $pontosBaseFase : 0;

        DB::transaction(function () use (
            $jogo,
            $questaoIdReal,
            $participante,
            $respostaTexto,
            $acertou,
            $tempo,
            $pontos
        ) {
            Resposta::create([
                'jogo_id' => $jogo->id,
                'questao_id' => $questaoIdReal,
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
        });

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
            'sucesso' => true,
            'bloqueada' => true,
            'resposta_dada' => $respostaTexto,
            'mensagem' => 'Resposta bloqueada com sucesso! Aguarde a revelação do apresentador no telão.',
            'participante' => [
                'id' => $participante->id,
                'nome' => $participante->nome,
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

    public function usarAjuda(Request $request, Sala $sala)
    {
        try {
            $validator = Validator::make($request->all(), [
                'participante_id' => 'required|exists:participantes,id',
                'tipo' => 'required|in:dica,cartas,convidado',
                'indice_convidado' => 'nullable|integer|min:0|max:2',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 422);
            }

            $validated = $validator->validated();
            $participante = Participante::find($validated['participante_id']);

            if (! $participante || (int) $participante->sala_id !== (int) $sala->id) {
                return response()->json(['error' => 'Participante não pertence a esta sala.'], 404);
            }

            $jogo = $sala->jogoAtivo()->first();
            if (! $jogo || ! $jogo->dados_motor) {
                return response()->json(['error' => 'Partida não está em andamento.'], 400);
            }

            $engine = GameEngine::desserializarSeguro($jogo->dados_motor);
            if (! ($engine instanceof GameEngine)) {
                return response()->json(['error' => 'Sincronizando estado da partida. Tente em instantes.'], 409);
            }

            $estado = $engine->getState();
            if ($estado['estado'] !== 'em_curso') {
                return response()->json(['error' => 'A rodada está pausada ou não iniciada.'], 403);
            }

            if (! empty($estado['tempo_esgotado']) || ($estado['tempo_restante'] ?? 0) <= 0) {
                return response()->json(['error' => 'Tempo esgotado para esta pergunta.'], 408);
            }

            $curQ = $engine->getCurrentQuestion();
            if (! $curQ) {
                return response()->json(['error' => 'Nenhuma pergunta ativa no momento.'], 404);
            }

            $ajudasRaw = $participante->ajudas_usadas;
            $ajudasUsadas = is_array($ajudasRaw) ? $ajudasRaw : [];
            $ajudasUsadas['dicas_usadas'] = (int) ($ajudasUsadas['dicas_usadas'] ?? 0);
            $ajudasUsadas['cartas_usadas'] = (int) ($ajudasUsadas['cartas_usadas'] ?? 0);
            $ajudasUsadas['convidados_usados'] = (int) ($ajudasUsadas['convidados_usados'] ?? 0);
            if (! isset($ajudasUsadas['historico_por_pergunta']) || ! is_array($ajudasUsadas['historico_por_pergunta'])) {
                $ajudasUsadas['historico_por_pergunta'] = [];
            }

            $perguntaKey = (string) ($curQ['id'] ?? ($estado['numero_pergunta'] ?? 1));
            $ajudasDestaPergunta = $ajudasUsadas['historico_por_pergunta'][$perguntaKey] ?? [];
            $tipo = $validated['tipo'];

            if ($tipo === 'dica') {
                if ($ajudasUsadas['dicas_usadas'] >= 3) {
                    return response()->json(['error' => 'Você já utilizou todas as 3 Dicas Bíblicas permitidas.'], 422);
                }

                $ref = $curQ['referencia_biblica'] ?? null;
                if (! $ref) {
                    $ref = 'Referência Bíblica: Consulte o livro de '.($curQ['livro_nome'] ?? 'Escrituras Sagradas');
                }

                $ajudasUsadas['dicas_usadas']++;
                $ajudasDestaPergunta['dica_biblica'] = $ref;
                $ajudasUsadas['historico_por_pergunta'][$perguntaKey] = $ajudasDestaPergunta;
                $participante->update(['ajudas_usadas' => $ajudasUsadas]);

                return response()->json([
                    'sucesso' => true,
                    'tipo' => 'dica',
                    'dica_biblica' => $ref,
                    'dicas_restantes' => max(0, 3 - $ajudasUsadas['dicas_usadas']),
                    'mensagem' => 'Dica Bíblica revelada!',
                    'som_efeito' => 'dica',
                ]);
            }

            if ($tipo === 'cartas') {
                if ($ajudasUsadas['cartas_usadas'] >= 1) {
                    return response()->json(['error' => 'Você já utilizou as Cartas 50/50 nesta partida.'], 422);
                }

                $opcoes = $curQ['opcoes'] ?? [];
                if (is_string($opcoes)) {
                    $opcoes = json_decode($opcoes, true) ?: [];
                }

                $corretaRaw = trim((string) ($curQ['resposta_correta'] ?? ''));
                $corretaLower = mb_strtolower($corretaRaw, 'UTF-8');
                $corretaIdx = null;

                if (is_numeric($corretaRaw) && isset($opcoes[(int) $corretaRaw])) {
                    $corretaIdx = (int) $corretaRaw;
                } else {
                    foreach ($opcoes as $idx => $opTxt) {
                        if (mb_strtolower(trim((string) $opTxt), 'UTF-8') === $corretaLower) {
                            $corretaIdx = (int) $idx;
                            break;
                        }
                    }
                }

                $erradas = [];
                foreach ($opcoes as $idx => $opTxt) {
                    $i = (int) $idx;
                    if ($corretaIdx !== null && $i === $corretaIdx) {
                        continue;
                    }
                    $erradas[] = $i;
                }

                if ($corretaIdx === null && count($erradas) > 2) {
                    array_shift($erradas);
                }

                shuffle($erradas);
                $eliminadas = array_values(array_slice($erradas, 0, min(2, count($erradas))));
                sort($eliminadas);

                $ajudasUsadas['cartas_usadas']++;
                $ajudasDestaPergunta['opcoes_eliminadas'] = $eliminadas;
                $ajudasUsadas['historico_por_pergunta'][$perguntaKey] = $ajudasDestaPergunta;
                $participante->update(['ajudas_usadas' => $ajudasUsadas]);

                return response()->json([
                    'sucesso' => true,
                    'tipo' => 'cartas',
                    'opcoes_eliminadas' => $eliminadas,
                    'cartas_restantes' => 0,
                    'mensagem' => 'Cartas (50/50) eliminaram 2 alternativas incorretas!',
                    'som_efeito' => 'ajuda',
                ]);
            }

            if ($tipo === 'convidado') {
                if ($ajudasUsadas['convidados_usados'] >= 3) {
                    return response()->json(['error' => 'Você já consultou os Convidados Bíblicos 3 vezes.'], 422);
                }

                $idxConvidado = (int) ($validated['indice_convidado'] ?? 0);
                $ajudasQ = $curQ['ajudas'] ?? [];
                if (is_string($ajudasQ)) {
                    $ajudasQ = json_decode($ajudasQ, true) ?: [];
                }
                $convidados = $ajudasQ['convidados'] ?? ['Paulo', 'Pedro', 'Moisés'];
                $nomeConvidado = $convidados[$idxConvidado] ?? $convidados[0] ?? 'Personagem Bíblico';

                $msgConvidado = $idxConvidado < 2
                    ? 'Pelo meu conhecimento das Escrituras, creio firmemente na alternativa correta!'
                    : 'Este acontecimento ocorreu fora da minha época bíblica, por isso não posso opinar com certeza.';

                $dicaConvidado = [
                    'convidado' => $nomeConvidado,
                    'mensagem' => $msgConvidado,
                ];

                $ajudasUsadas['convidados_usados']++;
                $ajudasDestaPergunta['dica_convidado'] = $dicaConvidado;
                $ajudasUsadas['historico_por_pergunta'][$perguntaKey] = $ajudasDestaPergunta;
                $participante->update(['ajudas_usadas' => $ajudasUsadas]);

                return response()->json([
                    'sucesso' => true,
                    'tipo' => 'convidado',
                    'dica_convidado' => $dicaConvidado,
                    'convidados_restantes' => max(0, 3 - $ajudasUsadas['convidados_usados']),
                    'mensagem' => "{$nomeConvidado} foi consultado!",
                    'som_efeito' => 'sabe1',
                ]);
            }

            return response()->json(['error' => 'Tipo de ajuda inválido.'], 400);
        } catch (\Throwable $e) {
            Log::error('Erro ao processar ajuda multijogador: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Não foi possível utilizar este recurso agora. Tente novamente.',
            ], 400);
        }
    }

    public function juntarEquipe(Request $request, Sala $sala, Participante $participante)
    {
        if ((int) $participante->sala_id !== (int) $sala->id) {
            return response()->json(['error' => 'Participante não pertence a esta sala.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'equipe_id' => 'nullable',
            'equipe_nome' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $equipeId = $request->input('equipe_id');
        $equipeNome = trim((string) $request->input('equipe_nome'));

        if (! empty($equipeNome)) {
            $equipe = Equipe::firstOrCreate(
                [
                    'sala_id' => $sala->id,
                    'nome' => $equipeNome,
                ],
                [
                    'cor' => '#'.str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
                ]
            );
            $equipeId = $equipe->id;
        } elseif ($equipeId && is_numeric($equipeId) && (int) $equipeId > 0) {
            $equipe = Equipe::where('sala_id', $sala->id)->where('id', (int) $equipeId)->first();
            if (! $equipe) {
                return response()->json(['error' => 'Equipe não encontrada nesta sala.'], 404);
            }
            $equipeId = $equipe->id;
        } else {
            $equipeId = null;
        }

        $participante->update(['equipe_id' => $equipeId]);
        $participante->load('equipe');

        $this->tentarBroadcast(new RankingAtualizado($sala->id, []));

        return response()->json([
            'sucesso' => true,
            'mensagem' => $participante->equipe ? "Você agora faz parte da equipe «{$participante->equipe->nome}»!" : 'Você agora joga individualmente na sala.',
            'equipe_id' => $participante->equipe_id,
            'equipe_nome' => $participante->equipe?->nome,
            'participante' => $participante,
        ]);
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
