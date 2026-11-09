<?php

namespace App\Http\Controllers;

use App\Game\Engine\GameEngine;
use App\Game\Engine\Selectors\RandomSelector;
use App\Game\Engine\Scoring\StandardScoring;
use App\Game\Engine\Timers\CountdownTimer;
use App\Game\Engine\State\GameState;
use App\Models\Questao;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function jogar(Request $request)
    {
        $engine = $this->carregarEngineDaSessao($request);

        if (!$engine) {
            $questoes = Questao::aprovadas()
                ->inRandomOrder()
                ->limit(10)
                ->get()
                ->map(fn ($q) => $q->toArray())
                ->toArray();

            $engine = new GameEngine(
                new RandomSelector(),
                new StandardScoring(),
                new CountdownTimer(),
                new GameState()
            );

            $engine->start($questoes);

            $this->salvarEngineNaSessao($request, $engine);
        }

        $estado = $engine->getState();
        $pergunta = $estado['pergunta_atual'];

        return view('game.play', [
            'estado' => $estado['estado'],
            'pergunta' => $pergunta,
            'pontuacao' => $estado['pontuacao'],
            'tempo_restante' => $estado['tempo_restante'],
        ]);
    }

    public function responder(Request $request)
    {
        $request->validate([
            'resposta' => 'required|string',
            'tempo_resposta' => 'required|integer|min:0',
        ]);

        $engine = $this->carregarEngineDaSessao($request);

        if (!$engine) {
            return redirect()->route('game.play')->with('error', 'Sessão de jogo expirada. Inicie uma nova partida.');
        }

        $pergunta = $engine->getState()['pergunta_atual'];
        $resultado = $engine->answerQuestion(
            1,
            $pergunta['id'] ?? 0,
            $request->resposta,
            $request->tempo_resposta
        );

        $this->salvarEngineNaSessao($request, $engine);

        if ($engine->getState()['estado'] === 'terminado') {
            return redirect()->route('game.result')->with('resultados', $engine->getResults());
        }

        return redirect()->route('game.play')->with('resultado', $resultado);
    }

    public function resultado(Request $request)
    {
        $resultados = $request->session()->get('resultados');

        if (!$resultados) {
            return redirect()->route('game.play');
        }

        $request->session()->forget('resultados');

        return view('game.result', [
            'resultados' => $resultados,
        ]);
    }

    private function carregarEngineDaSessao(Request $request): ?GameEngine
    {
        $dados = $request->session()->get('game_engine');

        if (!$dados) {
            return null;
        }

        $engine = unserialize($dados);

        return $engine instanceof GameEngine ? $engine : null;
    }

    private function salvarEngineNaSessao(Request $request, GameEngine $engine): void
    {
        $request->session()->put('game_engine', serialize($engine));
    }
}
