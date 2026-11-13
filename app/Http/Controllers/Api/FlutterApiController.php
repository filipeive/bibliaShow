<?php

namespace App\Http\Controllers\Api;

use App\Game\Engine\GameEngine;
use App\Game\Engine\Scoring\StandardScoring;
use App\Game\Engine\Selectors\RandomSelector;
use App\Game\Engine\State\GameState;
use App\Game\Engine\Timers\CountdownTimer;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\LivroBiblico;
use App\Models\NivelDificuldade;
use App\Models\Questao;
use App\Models\Sala;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FlutterApiController extends Controller
{
    /**
     * Retorna os metadados do jogo para inicialização do app Flutter.
     * GET /api/v1/meta
     */
    public function meta(): JsonResponse
    {
        $dados = Cache::remember('api_v1_meta', 1800, function () {
            $categorias = Categoria::select('id', 'nome', 'descricao')->orderBy('nome')->get();
            $niveis = NivelDificuldade::select('id', 'nivel', 'nome')->orderBy('nivel')->get();
            $livros = LivroBiblico::select('id', 'nome', 'testamento', 'abreviacao')->orderBy('id')->get();

            return [
                'sucesso' => true,
                'app_nome' => 'Bíblia Show 2.0',
                'versao_api' => '1.0.0',
                'estrutura_jogo' => [
                    'total_fases' => 4,
                    'perguntas_por_fase' => 6,
                    'total_perguntas' => 24,
                    'pontuacao_maxima' => 660,
                    'max_erros' => 3,
                    'max_tempos_esgotados' => 3,
                    'tempo_padrao_segundos' => 60,
                    'ajudas' => [
                        'cartas' => ['max' => 1, 'descricao' => 'Elimina 2 alternativas incorretas'],
                        'convidados' => ['max' => 3, 'descricao' => 'Opinião de até 3 personagens bíblicos'],
                        'dica' => ['max' => 3, 'descricao' => 'Livro e capítulo onde está a resposta'],
                        'pulo' => ['max' => 3, 'descricao' => 'Sorteia nova pergunta da mesma fase sem perder pontos'],
                    ],
                ],
                'categorias' => $categorias,
                'niveis' => $niveis,
                'livros' => $livros,
            ];
        });

        return response()->json($dados);
    }

    /**
     * Inicia uma nova partida individual para o app Flutter.
     * POST /api/v1/game/start
     */
    public function startGame(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'jogador_nome' => 'nullable|string|max:60',
            'categoria_id' => 'nullable|exists:categorias,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['sucesso' => false, 'erro' => $validator->errors()->first()], 422);
        }

        $jogadorNome = trim((string) $request->input('jogador_nome', 'Participante'));
        if ($jogadorNome === '') {
            $jogadorNome = 'Participante';
        }

        // Seleciona 24 questões aprovadas (6 por cada nível)
        $questoesFormatadas = $this->sortearQuestoesJogo($request->input('categoria_id'));
        if (count($questoesFormatadas) < 1) {
            return response()->json(['sucesso' => false, 'erro' => 'Não há questões suficientes cadastradas.'], 500);
        }

        $engine = new GameEngine(
            new RandomSelector,
            new StandardScoring,
            new CountdownTimer,
            new GameState
        );

        $engine->start($questoesFormatadas, [['id' => 1, 'nome' => $jogadorNome]]);

        // Gera token único e salva o estado do motor no Cache (1 dia de expiração)
        $token = 'bs_'.Str::random(40);
        Cache::put("flutter_session_{$token}", [
            'jogador_nome' => $jogadorNome,
            'engine_state' => $engine->toJsonState(),
            'criado_em' => now()->toIso8601String(),
        ], 86400);

        return response()->json([
            'sucesso' => true,
            'session_token' => $token,
            'jogador_nome' => $jogadorNome,
            'estado' => $this->formatarRespostaEstado($engine, $token),
        ]);
    }

    /**
     * Envia uma resposta na partida individual.
     * POST /api/v1/game/answer
     */
    public function answer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_token' => 'required|string',
            'resposta' => 'nullable|string',
            'tempo_resposta' => 'required|integer|min:0',
            'tempo_esgotado' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['sucesso' => false, 'erro' => $validator->errors()->first()], 422);
        }

        $token = $request->input('session_token');
        $sessao = Cache::get("flutter_session_{$token}");
        if (! $sessao || empty($sessao['engine_state'])) {
            return response()->json(['sucesso' => false, 'sessao_expirada' => true, 'erro' => 'Sessão expirada. Inicie uma nova partida.'], 404);
        }

        $engine = GameEngine::desserializarSeguro($sessao['engine_state']);
        if (! ($engine instanceof GameEngine)) {
            return response()->json(['sucesso' => false, 'erro' => 'Erro ao carregar o estado da partida.'], 500);
        }

        $pergunta = $engine->getCurrentQuestion();
        if (! $pergunta) {
            return response()->json(['sucesso' => false, 'erro' => 'Nenhuma pergunta ativa no momento.'], 404);
        }

        $tempoResposta = (int) $request->input('tempo_resposta', 0);
        $tempoEsgotado = $request->boolean('tempo_esgotado') || ($request->input('resposta') === null || $request->input('resposta') === '');

        if ($tempoEsgotado) {
            $resultado = $engine->registrarTempoEsgotado(1);
        } else {
            $resposta = (string) $request->input('resposta', '');
            $opcoes = $pergunta['opcoes'] ?? [];
            $opcoesArray = is_string($opcoes) ? json_decode($opcoes, true) : $opcoes;
            if ($resposta !== '' && is_array($opcoesArray) && isset($opcoesArray[$resposta])) {
                $resposta = (string) $opcoesArray[$resposta];
            }

            $resultado = $engine->answerQuestion(
                1,
                $pergunta['id'] ?? 0,
                $resposta,
                $tempoResposta
            );
        }

        if (! empty($resultado['reiniciar_por_erros']) || ! empty($resultado['reiniciar_por_tempo'])) {
            $engine->finishGame();
        }

        // Salva estado atualizado
        $sessao['engine_state'] = $engine->toJsonState();
        Cache::put("flutter_session_{$token}", $sessao, 86400);

        $estadoMotor = $engine->getState();
        $isTerminado = $estadoMotor['estado'] === 'terminado';

        if ($isTerminado) {
            $this->gravarRankingFinal($sessao['jogador_nome'], $engine->getResults());
        }

        return response()->json([
            'sucesso' => true,
            'acertou' => (bool) ($resultado['acertou'] ?? false),
            'tempo_esgotado' => $tempoEsgotado,
            'resposta_correta' => $resultado['resposta_correta'] ?? '',
            'explicacao' => $resultado['explicacao'] ?? null,
            'referencia_biblica' => $resultado['referencia_biblica'] ?? null,
            'pontuacao' => $engine->getState()['pontuacao'],
            'placar' => $resultado['placar'] ?? null,
            'som_efeito' => $resultado['som_efeito'] ?? ($resultado['acertou'] ? 'certa' : 'errou'),
            'terminado' => $isTerminado,
            'motivo_termino' => $resultado['motivo_termino'] ?? null,
            'proxima_pergunta' => $isTerminado ? null : $this->formatarPerguntaFront($engine->getCurrentQuestion(), $engine),
            'estado' => $this->formatarRespostaEstado($engine, $token),
        ]);
    }

    /**
     * Utiliza uma ajuda clássica (cartas, dica, convidado, pulo).
     * POST /api/v1/game/lifeline
     */
    public function lifeline(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_token' => 'required|string',
            'tipo' => 'required|in:pulo,cartas,convidado,dica',
            'convidado_indice' => 'nullable|integer|min:0|max:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['sucesso' => false, 'erro' => $validator->errors()->first()], 422);
        }

        $token = $request->input('session_token');
        $sessao = Cache::get("flutter_session_{$token}");
        if (! $sessao || empty($sessao['engine_state'])) {
            return response()->json(['sucesso' => false, 'sessao_expirada' => true, 'erro' => 'Sessão expirada.'], 404);
        }

        $engine = GameEngine::desserializarSeguro($sessao['engine_state']);
        if (! ($engine instanceof GameEngine)) {
            return response()->json(['sucesso' => false, 'erro' => 'Estado da partida indisponível.'], 500);
        }

        $tipo = $request->input('tipo');
        $retorno = ['sucesso' => false, 'mensagem' => 'Ação não processada.'];

        if ($tipo === 'cartas') {
            $retorno = $engine->usarCartas();
        } elseif ($tipo === 'dica') {
            $retorno = $engine->usarDica();
        } elseif ($tipo === 'convidado') {
            $indice = (int) $request->input('convidado_indice', 0);
            $retorno = $engine->consultarConvidado($indice);
        } elseif ($tipo === 'pulo') {
            $idsJaUsados = collect($engine->getResults()['historico_perguntas'] ?? [])
                ->pluck('id')
                ->filter()
                ->all();

            $reservaModel = Questao::aprovadas()
                ->with(['livro', 'dificuldade', 'categoria'])
                ->whereNotIn('id', $idsJaUsados)
                ->inRandomOrder()
                ->first();

            $reserva = $reservaModel ? $this->formatarQuestaoParaMotor($reservaModel) : null;
            $retorno = $engine->usarPulo($reserva);
        }

        $sessao['engine_state'] = $engine->toJsonState();
        Cache::put("flutter_session_{$token}", $sessao, 86400);

        return response()->json([
            'sucesso' => $retorno['sucesso'] ?? false,
            'tipo' => $tipo,
            'dados' => $retorno,
            'pergunta_atual' => $this->formatarPerguntaFront($engine->getCurrentQuestion(), $engine),
            'ajudas' => $engine->getState()['ajudas'] ?? [],
            'mensagem' => $retorno['mensagem'] ?? 'Ajuda aplicada com sucesso!',
        ]);
    }

    /**
     * O jogador decide parar voluntariamente e levar o valor acumulado.
     * POST /api/v1/game/stop
     */
    public function stopGame(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['sucesso' => false, 'erro' => $validator->errors()->first()], 422);
        }

        $token = $request->input('session_token');
        $sessao = Cache::get("flutter_session_{$token}");
        if (! $sessao || empty($sessao['engine_state'])) {
            return response()->json(['sucesso' => false, 'sessao_expirada' => true, 'erro' => 'Sessão expirada.'], 404);
        }

        $engine = GameEngine::desserializarSeguro($sessao['engine_state']);
        if (! ($engine instanceof GameEngine)) {
            return response()->json(['sucesso' => false, 'erro' => 'Motor indisponível.'], 500);
        }

        $engine->stopGame();
        $resultados = $engine->getResults();

        $this->gravarRankingFinal($sessao['jogador_nome'], $resultados);

        $sessao['engine_state'] = $engine->toJsonState();
        Cache::put("flutter_session_{$token}", $sessao, 86400);

        return response()->json([
            'sucesso' => true,
            'parou' => true,
            'mensagem' => 'Você decidiu parar e garantiu seu prêmio!',
            'pontuacao_final' => $resultados['pontuacao_final'] ?? 0,
            'perguntas_acertadas' => $resultados['acertos'] ?? 0,
            'total_perguntas' => $resultados['total_perguntas'] ?? 24,
            'resultados' => $resultados,
        ]);
    }

    /**
     * Consulta o status atual de uma partida individual por token.
     * GET /api/v1/game/status?session_token=...
     */
    public function status(Request $request): JsonResponse
    {
        $token = $request->query('session_token');
        if (! $token) {
            return response()->json(['sucesso' => false, 'erro' => 'session_token é obrigatório.'], 422);
        }

        $sessao = Cache::get("flutter_session_{$token}");
        if (! $sessao || empty($sessao['engine_state'])) {
            return response()->json(['sucesso' => false, 'sessao_expirada' => true, 'erro' => 'Partida não encontrada.'], 404);
        }

        $engine = GameEngine::desserializarSeguro($sessao['engine_state']);
        if (! ($engine instanceof GameEngine)) {
            return response()->json(['sucesso' => false, 'erro' => 'Erro ao carregar partida.'], 500);
        }

        return response()->json([
            'sucesso' => true,
            'jogador_nome' => $sessao['jogador_nome'],
            'estado' => $this->formatarRespostaEstado($engine, $token),
        ]);
    }

    /**
     * Retorna o Ranking dos melhores jogadores para o app Flutter.
     * GET /api/v1/ranking
     */
    public function ranking(): JsonResponse
    {
        $topFormatado = Cache::remember('api_v1_ranking', 60, function () {
            $path = storage_path('app/ranking_individual.json');
            $ranking = [];
            if (file_exists($path)) {
                $conteudo = file_get_contents($path);
                $ranking = json_decode($conteudo, true) ?: [];
            }

            if (empty($ranking)) {
                $ranking = [
                    ['nome' => 'Daniel', 'pontuacao' => 2400, 'acertos' => 24, 'data' => 'Clássico'],
                    ['nome' => 'Salomão', 'pontuacao' => 2100, 'acertos' => 22, 'data' => 'Clássico'],
                    ['nome' => 'Ester', 'pontuacao' => 1800, 'acertos' => 20, 'data' => 'Clássico'],
                ];
            }

            usort($ranking, function ($a, $b) {
                $ptsA = (int) ($a['pontuacao'] ?? 0);
                $ptsB = (int) ($b['pontuacao'] ?? 0);
                if ($ptsA === $ptsB) {
                    return ((int) ($b['acertos'] ?? 0)) <=> ((int) ($a['acertos'] ?? 0));
                }

                return $ptsB <=> $ptsA;
            });

            $top = array_slice($ranking, 0, 25);
            $resultado = [];
            foreach ($top as $idx => $r) {
                $resultado[] = [
                    'posicao' => $idx + 1,
                    'nome' => $r['nome'] ?? 'Participante',
                    'pontuacao' => (int) ($r['pontuacao'] ?? 0),
                    'acertos' => (int) ($r['acertos'] ?? 0),
                    'data' => $r['data'] ?? '',
                ];
            }

            return $resultado;
        });

        return response()->json([
            'sucesso' => true,
            'ranking' => $topFormatado,
        ]);
    }

    /**
     * Consulta os dados de uma sala de igreja ao vivo para multijogador no Flutter.
     * GET /api/v1/salas/{codigo}
     */
    public function consultarSalaAoVivo(string $codigo): JsonResponse
    {
        $codigoNorm = mb_strtoupper(trim($codigo));
        $payload = Cache::remember("api_v1_sala_{$codigoNorm}", 5, function () use ($codigoNorm) {
            $sala = Sala::where('codigo', $codigoNorm)->first();
            if (! $sala) {
                return null;
            }

            $jogo = $sala->jogoAtivo()->first();
            $partAtiva = ($jogo && $jogo->status === 'em_andamento');

            return [
                'id' => $sala->id,
                'codigo' => $sala->codigo,
                'nome' => $sala->nome,
                'status' => $sala->status,
                'partida_em_andamento' => $partAtiva,
                'total_participantes' => $sala->participantes()->count(),
            ];
        });

        if (! $payload) {
            return response()->json(['sucesso' => false, 'erro' => 'Sala não encontrada. Verifique o código.'], 404);
        }

        return response()->json([
            'sucesso' => true,
            'sala' => $payload,
        ]);
    }

    // --- Métodos Auxiliares de Formatação ---

    private function formatarRespostaEstado(GameEngine $engine, string $token): array
    {
        $estado = $engine->getState();
        $perguntaAtual = $this->formatarPerguntaFront($engine->getCurrentQuestion(), $engine);

        return [
            'session_token' => $token,
            'estado_jogo' => $estado['estado'],
            'numero_pergunta' => $estado['numero_pergunta'] ?? 1,
            'total_perguntas' => $estado['total_perguntas'] ?? 24,
            'fase_atual' => $estado['fase_atual'] ?? 1,
            'pontuacao_acumulada' => $estado['pontuacao'],
            'placar_milhao' => $estado['placar_milhao'] ?? null,
            'sequencia_acertos' => $estado['sequencia_acertos'] ?? 0,
            'erros_cometidos' => $estado['erros_count'] ?? 0,
            'max_erros' => $estado['max_erros'] ?? 3,
            'erros_restantes' => $estado['erros_restantes'] ?? 3,
            'tempo_restante' => $estado['tempo_restante'] ?? 60,
            'ajudas' => $estado['ajudas'] ?? [],
            'pergunta_atual' => $perguntaAtual,
        ];
    }

    private function formatarPerguntaFront(?array $q, GameEngine $engine): ?array
    {
        if (! $q) {
            return null;
        }

        $opcoes = $q['opcoes'] ?? [];
        if (is_string($opcoes)) {
            $opcoes = json_decode($opcoes, true) ?: [];
        }

        $ajudasPergunta = $q['ajudas'] ?? [];
        if (is_string($ajudasPergunta)) {
            $ajudasPergunta = json_decode($ajudasPergunta, true) ?: [];
        }

        $estado = $engine->getState();
        $opcoesEliminadas = $estado['ajudas']['opcoes_eliminadas'] ?? [];

        return [
            'id' => $q['id'] ?? null,
            'texto' => $q['texto'] ?? '',
            'opcoes' => array_values($opcoes),
            'opcoes_eliminadas' => array_values($opcoesEliminadas),
            'livro_nome' => $q['livro_nome'] ?? null,
            'dificuldade_nome' => $q['dificuldade_nome'] ?? 'Fácil',
            'dificuldade_nivel' => $q['dificuldade_nivel'] ?? 1,
            'categoria_nome' => $q['categoria_nome'] ?? 'Bíblia Geral',
            'convidados_disponiveis' => $ajudasPergunta['convidados'] ?? ['Paulo', 'Pedro', 'Moisés'],
        ];
    }

    private function sortearQuestoesJogo(?int $categoriaId = null): array
    {
        $questoesFormatadas = [];
        for ($fase = 1; $fase <= 4; $fase++) {
            $nivelModel = NivelDificuldade::where('nivel', $fase)->first();
            $query = Questao::aprovadas()
                ->with(['livro', 'dificuldade', 'categoria'])
                ->when($categoriaId, fn ($q) => $q->where('categoria_id', $categoriaId));

            if ($nivelModel) {
                $query->where('dificuldade_id', $nivelModel->id);
            }

            $perguntasNivel = $query->inRandomOrder()->limit(6)->get();

            if ($perguntasNivel->count() < 6) {
                $idsFaltantes = $perguntasNivel->pluck('id')->all();
                $completar = Questao::aprovadas()
                    ->with(['livro', 'dificuldade', 'categoria'])
                    ->whereNotIn('id', $idsFaltantes)
                    ->inRandomOrder()
                    ->limit(6 - $perguntasNivel->count())
                    ->get();
                $perguntasNivel = $perguntasNivel->merge($completar);
            }

            foreach ($perguntasNivel as $q) {
                $questoesFormatadas[] = $this->formatarQuestaoParaMotor($q);
            }
        }

        return $questoesFormatadas;
    }

    private function formatarQuestaoParaMotor(Questao $q): array
    {
        $arr = $q->toArray();
        $arr['livro_nome'] = $q->livro?->nome;
        $arr['dificuldade_nome'] = $q->dificuldade?->nome ?? 'Iniciante';
        $arr['dificuldade_nivel'] = $q->dificuldade?->nivel ?? 1;
        $arr['categoria_nome'] = $q->categoria?->nome;

        return $arr;
    }

    private function gravarRankingFinal(string $jogadorNome, array $resultados): void
    {
        try {
            $path = storage_path('app/ranking_individual.json');
            $ranking = [];
            if (file_exists($path)) {
                $ranking = json_decode(file_get_contents($path), true) ?: [];
            }

            $ranking[] = [
                'nome' => trim($jogadorNome) ?: 'Participante',
                'pontuacao' => (int) ($resultados['pontuacao_final'] ?? ($resultados['pontuacao_total'] ?? 0)),
                'acertos' => (int) ($resultados['acertos'] ?? ($resultados['total_acertos'] ?? 0)),
                'data' => now()->format('d/m/Y H:i'),
            ];

            usort($ranking, function ($a, $b) {
                return ((int) ($b['pontuacao'] ?? 0)) <=> ((int) ($a['pontuacao'] ?? 0));
            });

            $ranking = array_slice($ranking, 0, 50);
            file_put_contents($path, json_encode($ranking, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            Log::warning('Erro ao salvar ranking no Flutter API: '.$e->getMessage());
        }
    }
}
