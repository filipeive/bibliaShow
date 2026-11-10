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
    public function jogar(Request $request)
    {
        $novaPartida = $request->boolean('novo');
        if ($novaPartida) {
            $request->session()->forget(['game_engine', 'resultados']);
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

            $engine->start($questoes);
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
            'numero_pergunta' => $estado['numero_pergunta'] ?? 1,
            'total_perguntas' => $estado['total_perguntas'] ?? 24,
            'fase_atual' => $estado['fase_atual'] ?? 1,
            'placar_milhao' => $estado['placar_milhao'] ?? ['errar' => 0, 'parar' => 0, 'acertar' => 10, 'valor_pergunta' => 10, 'fase' => 1],
            'sequencia_acertos' => $estado['sequencia_acertos'] ?? 0,
            'pontuacao' => $estado['pontuacao'],
            'tempo_restante' => $estado['tempo_restante'],
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
        ]);

        $engine = $this->carregarEngineDaSessao($request);

        if (!$engine) {
            return redirect()->route('game.play', ['novo' => 1])
                ->with('error', 'Sessão de jogo expirada. Uma nova partida foi iniciada.');
        }

        $pergunta = $engine->getState()['pergunta_atual'];
        if (!$pergunta) {
            return redirect()->route('game.play', ['novo' => 1]);
        }

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

        $this->salvarEngineNaSessao($request, $engine);

        if ($engine->getState()['estado'] === 'terminado') {
            $request->session()->put('resultados', $engine->getResults());
            return redirect()->route('game.result')->with('ultimo_resultado', $resultado);
        }

        return redirect()->route('game.play')->with('resultado', $resultado);
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
            $engine->finishGame();
            $request->session()->put('resultados', $engine->getResults());
            $request->session()->forget('game_engine');
            return redirect()->route('game.result');
        }

        return redirect()->route('game.play', ['novo' => 1]);
    }

    public function resultado(Request $request)
    {
        $resultados = $request->session()->get('resultados');

        if (!$resultados) {
            $engine = $this->carregarEngineDaSessao($request);
            if ($engine) {
                $resultados = $engine->getResults();
            }
        }

        if (!$resultados) {
            return redirect()->route('game.play', ['novo' => 1]);
        }

        return view('game.result', [
            'resultados' => $resultados,
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

        // Seleção progressiva clássica das 4 Fases (6 de cada nível 1..4)
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

        if (!$dados) {
            return null;
        }

        $engine = @unserialize($dados);

        return $engine instanceof GameEngine ? $engine : null;
    }

    private function salvarEngineNaSessao(Request $request, GameEngine $engine): void
    {
        $request->session()->put('game_engine', serialize($engine));
    }
}
