<?php

namespace App\Http\Controllers;

use App\Game\Engine\GameEngine;
use App\Game\Engine\Scoring\StandardScoring;
use App\Game\Engine\Selectors\RandomSelector;
use App\Game\Engine\State\GameState;
use App\Game\Engine\Timers\CountdownTimer;
use App\Models\Categoria;
use App\Models\NivelDificuldade;
use App\Models\Questao;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function definirJogador(Request $request)
    {
        $request->validate([
            'jogador_nome' => 'required|string|max:60',
            'novo' => 'nullable|boolean',
        ]);

        $nome = trim($request->input('jogador_nome'));
        $request->session()->put('jogador_nome', $nome);
        $request->session()->put('jogador_confirmado', true);

        if ($request->boolean('novo')) {
            $request->session()->forget(['game_engine', 'resultados']);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => true,
                'jogador_nome' => $nome,
            ]);
        }

        return redirect()->route('game.play');
    }

    public function confirmarJogador(Request $request)
    {
        $request->session()->put('jogador_confirmado', true);
        return response()->json([
            'sucesso' => true,
            'jogador_nome' => (string) $request->session()->get('jogador_nome', 'Participante'),
        ]);
    }

    public function jogar(Request $request)
    {
        if ($request->filled('jogador_nome')) {
            $request->session()->put('jogador_nome', trim((string) $request->input('jogador_nome')));
            $request->session()->put('jogador_confirmado', true);
        }

        $novaPartida = $request->boolean('novo');
        if ($novaPartida) {
            $request->session()->forget(['game_engine', 'resultados', 'jogador_confirmado']);
        }

        $jogadorNome = (string) $request->session()->get('jogador_nome', $request->user()?->nome ?? '');
        $precisaIdentificacao = ($jogadorNome === '' || $jogadorNome === 'Participante');
        $jogadorConfirmado = (bool) $request->session()->get('jogador_confirmado', false);
        $perguntarContinuarComo = false;

        if ($request->boolean('trocar_nome')) {
            $precisaIdentificacao = true;
            $request->session()->forget('jogador_confirmado');
        } elseif (!$jogadorConfirmado) {
            if (!$precisaIdentificacao) {
                $perguntarContinuarComo = true;
            } else {
                $precisaIdentificacao = true;
            }
        } elseif ($request->boolean('confirmar_nome') && !$precisaIdentificacao) {
            $perguntarContinuarComo = true;
        }

        $engine = $this->carregarEngineDaSessao($request);
        $iniciouAgora = false;

        if (!$engine || $engine->getState()['estado'] === 'terminado') {
            $questoes = $this->selecionarQuestoesPartida($request);

            $engine = new GameEngine(
                new RandomSelector(),
                new StandardScoring(),
                new CountdownTimer(),
                new GameState()
            );

            $engine->start($questoes, [['id' => 1, 'nome' => $jogadorNome ?: 'Participante']]);
            $this->salvarEngineNaSessao($request, $engine);
            $iniciouAgora = true;
        }

        $estado = $engine->getState();
        $pergunta = $estado['pergunta_atual'];

        $categorias = Categoria::orderBy('nome')->get();
        $niveis = NivelDificuldade::orderBy('nivel')->get();

        return view('game.play', [
            'estado' => $estado['estado'],
            'pergunta' => $pergunta,
            'jogador_nome' => $jogadorNome ?: 'Participante',
            'precisa_identificacao' => $precisaIdentificacao,
            'perguntar_continuar' => $perguntarContinuarComo,
            'ranking_geral' => $this->obterRankingIndividual(),
            'numero_pergunta' => $estado['numero_pergunta'] ?? 1,
            'total_perguntas' => $estado['total_perguntas'] ?? 24,
            'fase_atual' => $estado['fase_atual'] ?? 1,
            'placar_milhao' => $estado['placar_milhao'] ?? ['errar' => 0, 'parar' => 0, 'acertar' => 10, 'valor_pergunta' => 10, 'fase' => 1],
            'sequencia_acertos' => $estado['sequencia_acertos'] ?? 0,
            'pontuacao' => $estado['pontuacao'],
            'tempo_restante' => $estado['tempo_restante'],
            'erros_count' => $estado['erros_count'] ?? 0,
            'max_erros' => $estado['max_erros'] ?? 3,
            'erros_restantes' => $estado['erros_restantes'] ?? 3,
            'tempos_esgotados_count' => $estado['tempos_esgotados_count'] ?? 0,
            'max_tempos_esgotados' => $estado['max_tempos_esgotados'] ?? 3,
            'tempos_esgotados_restantes' => $estado['tempos_esgotados_restantes'] ?? 3,
            'ajudas' => $estado['ajudas'] ?? [],
            'categorias' => $categorias,
            'niveis' => $niveis,
            'iniciou_agora' => $iniciouAgora,
        ]);
    }

    public function responder(Request $request)
    {
        $request->validate([
            'resposta' => 'nullable|string',
            'tempo_resposta' => 'required|integer|min:0',
            'tempo_esgotado' => 'nullable|boolean',
        ]);

        $engine = $this->carregarEngineDaSessao($request);

        if (!$engine) {
            if ($request->expectsJson()) {
                return response()->json([
                    'redirect_url' => route('game.play', ['novo' => 1]),
                    'error' => 'Sessão expirada.',
                ]);
            }
            return redirect()->route('game.play', ['novo' => 1])
                ->with('error', 'Sessão de jogo expirada. Uma nova partida foi iniciada.');
        }

        $pergunta = $engine->getState()['pergunta_atual'];
        if (!$pergunta) {
            if ($request->expectsJson()) {
                return response()->json(['redirect_url' => route('game.play', ['novo' => 1])]);
            }
            return redirect()->route('game.play', ['novo' => 1]);
        }

        $foiTempoEsgotado = $request->boolean('tempo_esgotado') || ($request->input('resposta') === null || $request->input('resposta') === '');

        if ($foiTempoEsgotado) {
            $resultado = $engine->registrarTempoEsgotado(1);
        } else {
            $resposta = (string) ($request->resposta ?? '');
            $opcoes = $pergunta['opcoes'] ?? [];
            $opcoesArray = is_string($opcoes) ? json_decode($opcoes, true) : $opcoes;
            if ($resposta !== '' && is_array($opcoesArray) && isset($opcoesArray[$resposta])) {
                $resposta = (string) $opcoesArray[$resposta];
            }

            $resultado = $engine->answerQuestion(
                1,
                $pergunta['id'] ?? 0,
                $resposta,
                (int) $request->tempo_resposta
            );
        }

        $nomeJogador = (string) $request->session()->get('jogador_nome', $request->user()?->nome ?? 'Participante');

        // Se atingiu 3 erros ou 3 tempos esgotados: encerra a partida com mensagem amigável e leva para /jogar/resultado
        if (!empty($resultado['reiniciar_por_erros']) || !empty($resultado['reiniciar_por_tempo'])) {
            $engine->finishGame();
            $resultadosPartida = $engine->getResults();

            $msgAmigavel = !empty($resultado['reiniciar_por_erros'])
                ? "Que jornada incrível, {$nomeJogador}! Você alcançou o limite de 3 respostas incorretas nesta partida. Não desanime: cada pergunta é uma oportunidade de aprender mais sobre as Escrituras. Veja abaixo seu resumo e escolha quando quiser jogar novamente!"
                : "Boa tentativa, {$nomeJogador}! O cronômetro esgotou em 3 perguntas nesta partida. Respire fundo, confira sua pontuação no Ranking abaixo e inicie uma nova tentativa quando estiver pronto!";

            $resultadosPartida['jogador_nome'] = $nomeJogador;
            $resultadosPartida['perdeu_partida'] = true;
            $resultadosPartida['motivo_fim'] = !empty($resultado['reiniciar_por_erros']) ? 'limite_erros' : 'tempo_esgotado';
            $resultadosPartida['mensagem_amigavel'] = $msgAmigavel;

            $this->registrarNoRankingIndividual($nomeJogador, $resultadosPartida);

            $request->session()->put('resultados', $resultadosPartida);
            $request->session()->forget(['game_engine', 'jogador_confirmado']);

            if ($request->expectsJson()) {
                return response()->json(array_merge($resultado, [
                    'fim_de_jogo' => true,
                    'perdeu_partida' => true,
                    'jogador_nome' => $nomeJogador,
                    'mensagem_amigavel' => $msgAmigavel,
                    'pontuacao_final' => $resultadosPartida['pontuacao_total'] ?? 0,
                    'redirect_url' => route('game.result'),
                ]));
            }

            return redirect()->route('game.result')->with('ultimo_resultado', $resultado);
        }

        $this->salvarEngineNaSessao($request, $engine);

        if ($engine->getState()['estado'] === 'terminado') {
            $resultadosPartida = $engine->getResults();
            $resultadosPartida['jogador_nome'] = $nomeJogador;
            $resultadosPartida['perdeu_partida'] = false;
            $resultadosPartida['mensagem_amigavel'] = "Parabéns, {$nomeJogador}! Você concluiu todas as etapas desta partida do Bíblia Show!";

            $this->registrarNoRankingIndividual($nomeJogador, $resultadosPartida);

            $request->session()->put('resultados', $resultadosPartida);
            $request->session()->forget(['game_engine', 'jogador_confirmado']);

            if ($request->expectsJson()) {
                return response()->json(array_merge($resultado, [
                    'fim_de_jogo' => true,
                    'perdeu_partida' => false,
                    'jogador_nome' => $nomeJogador,
                    'mensagem_amigavel' => $resultadosPartida['mensagem_amigavel'],
                    'pontuacao_final' => $resultadosPartida['pontuacao_total'] ?? 0,
                    'redirect_url' => route('game.result'),
                ]));
            }

            return redirect()->route('game.result');
        }

        $request->session()->flash('resultado', $resultado);

        if ($request->expectsJson()) {
            return response()->json(array_merge($resultado, [
                'fim_de_jogo' => false,
                'redirect_url' => route('game.play'),
            ]));
        }

        return redirect()->route('game.play');
    }

    public function usarAjuda(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:pulo,cartas,convidado,dica',
            'convidado_indice' => 'nullable|integer|min:0|max:2',
        ]);

        $engine = $this->carregarEngineDaSessao($request);
        if (!$engine) {
            return redirect()->route('game.play', ['novo' => 1]);
        }

        $tipo = $request->input('tipo');

        if ($tipo === 'pulo') {
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
        } elseif ($tipo === 'cartas') {
            $retorno = $engine->usarCartas();
        } elseif ($tipo === 'dica') {
            $retorno = $engine->usarDica();
        } else {
            $indice = (int) $request->input('convidado_indice', 0);
            $retorno = $engine->consultarConvidado($indice);
        }

        $this->salvarEngineNaSessao($request, $engine);

        if ($engine->getState()['estado'] === 'terminado') {
            $request->session()->put('resultados', $engine->getResults());
            return redirect()->route('game.result');
        }

        return redirect()->route('game.play')->with('ajuda_feedback', $retorno);
    }

    public function encerrar(Request $request)
    {
        $engine = $this->carregarEngineDaSessao($request);
        if ($engine) {
            $nomeJogador = (string) $request->session()->get('jogador_nome', $request->user()?->nome ?? 'Participante');
            $engine->finishGame();
            $resultados = $engine->getResults();
            $resultados['jogador_nome'] = $nomeJogador;
            $resultados['perdeu_partida'] = false;
            $resultados['mensagem_amigavel'] = "Partida encerrada, {$nomeJogador}! Você garantiu sua pontuação atual no Ranking do Bíblia Show.";

            $this->registrarNoRankingIndividual($nomeJogador, $resultados);

            $request->session()->put('resultados', $resultados);
            $request->session()->forget(['game_engine', 'jogador_confirmado']);
            return redirect()->route('game.result');
        }

        return redirect()->route('game.play', ['novo' => 1]);
    }

    public function resultado(Request $request)
    {
        $resultados = $request->session()->get('resultados');
        $nomeJogador = (string) $request->session()->get('jogador_nome', $request->user()?->nome ?? 'Participante');

        if (!$resultados) {
            $engine = $this->carregarEngineDaSessao($request);
            if ($engine) {
                $resultados = $engine->getResults();
                $resultados['jogador_nome'] = $nomeJogador;
            }
        }

        if (!$resultados) {
            return redirect()->route('game.play', ['novo' => 1]);
        }

        return view('game.result', [
            'resultados' => $resultados,
            'jogador_nome' => $resultados['jogador_nome'] ?? $nomeJogador,
            'ranking_geral' => $this->obterRankingIndividual(),
        ]);
    }

    public function curiosidades()
    {
        $jsonPath = public_path('data/enciclopedia.json');
        $artigos = file_exists($jsonPath)
            ? (json_decode(file_get_contents($jsonPath), true) ?: [])
            : [];

        return view('game.curiosidades', compact('artigos'));
    }

    private function obterArquivoRanking(): string
    {
        return storage_path('app/ranking_individual.json');
    }

    private function obterRankingIndividual(): array
    {
        $path = $this->obterArquivoRanking();
        if (!file_exists($path)) {
            $inicial = [
                ['nome' => 'Daniel', 'pontuacao' => 2400, 'acertos' => 24, 'data' => 'Clássico'],
                ['nome' => 'Débora', 'pontuacao' => 1950, 'acertos' => 20, 'data' => 'Clássico'],
                ['nome' => 'Salomão', 'pontuacao' => 1600, 'acertos' => 17, 'data' => 'Clássico'],
                ['nome' => 'Ester', 'pontuacao' => 1250, 'acertos' => 14, 'data' => 'Clássico'],
                ['nome' => 'Timóteo', 'pontuacao' => 900, 'acertos' => 10, 'data' => 'Clássico'],
            ];
            @file_put_contents($path, json_encode($inicial, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $inicial;
        }

        $dados = json_decode((string) file_get_contents($path), true);
        return is_array($dados) ? $dados : [];
    }

    private function registrarNoRankingIndividual(string $nome, array $resultados): void
    {
        $nomeLimpo = trim($nome) ?: 'Participante';
        $pontos = (int) ($resultados['pontuacao_total'] ?? 0);
        $acertos = (int) ($resultados['total_acertos'] ?? 0);

        $ranking = $this->obterRankingIndividual();
        $ranking[] = [
            'nome' => $nomeLimpo,
            'pontuacao' => $pontos,
            'acertos' => $acertos,
            'data' => now()->format('d/m/Y H:i'),
        ];

        usort($ranking, function ($a, $b) {
            if (($b['pontuacao'] ?? 0) !== ($a['pontuacao'] ?? 0)) {
                return ($b['pontuacao'] ?? 0) <=> ($a['pontuacao'] ?? 0);
            }
            return ($b['acertos'] ?? 0) <=> ($a['acertos'] ?? 0);
        });

        $ranking = array_slice($ranking, 0, 20);
        @file_put_contents($this->obterArquivoRanking(), json_encode($ranking, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function selecionarQuestoesPartida(Request $request): array
    {
        if ($request->filled('categoria_id') || $request->filled('dificuldade_id')) {
            $query = Questao::aprovadas()->with(['livro', 'dificuldade', 'categoria']);
            if ($request->filled('categoria_id')) {
                $query->where('categoria_id', $request->integer('categoria_id'));
            }
            if ($request->filled('dificuldade_id')) {
                $query->where('dificuldade_id', $request->integer('dificuldade_id'));
            }

            $filtradas = $query->inRandomOrder()
                ->limit(24)
                ->get()
                ->map(fn (Questao $q) => $this->formatarQuestaoParaMotor($q))
                ->toArray();

            if (!empty($filtradas)) {
                return $filtradas;
            }
        }

        $colecao = collect();
        foreach ([1, 2, 3, 4] as $nivel) {
            $porNivel = Questao::aprovadas()
                ->with(['livro', 'dificuldade', 'categoria'])
                ->whereHas('dificuldade', fn ($q) => $q->where('nivel', $nivel))
                ->inRandomOrder()
                ->limit(6)
                ->get();
            $colecao = $colecao->concat($porNivel);
        }

        if ($colecao->isEmpty()) {
            $colecao = Questao::aprovadas()
                ->with(['livro', 'dificuldade', 'categoria'])
                ->inRandomOrder()
                ->limit(24)
                ->get();
        }

        return $colecao->map(fn (Questao $q) => $this->formatarQuestaoParaMotor($q))->values()->all();
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

    private function carregarEngineDaSessao(Request $request): ?GameEngine
    {
        $dados = $request->session()->get('game_engine');

        return GameEngine::desserializarSeguro($dados);
    }

    private function salvarEngineNaSessao(Request $request, GameEngine $engine): void
    {
        $request->session()->put('game_engine', $engine->toJsonState());
    }
}
