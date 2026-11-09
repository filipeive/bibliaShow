<?php

namespace App\Http\Controllers;

use App\Game\Engine\GameEngine;
use App\Game\Engine\Selectors\RandomSelector;
use App\Game\Engine\Scoring\StandardScoring;
use App\Game\Engine\Timers\CountdownTimer;
use App\Game\Engine\State\GameState;
use App\Models\Questao;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApresentadorController extends Controller
{
    public function painel()
    {
        $salas = \App\Models\Sala::where('status', '!=', 'terminada')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        return view('apresentador.painel', ['salas' => $salas]);
    }

    public function criarSala(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $sala = \App\Models\Sala::create([
            'igreja_id' => $request->user()?->igreja_id ?? 1,
            'codigo_acesso' => Str::upper(Str::random(6)),
            'nome' => $request->nome,
            'apresentador_id' => $request->user()?->id ?? 1,
            'status' => 'aguardando',
        ]);

        return redirect()->route('apresentador.control', $sala->id);
    }

    public function control($salaId)
    {
        $sala = \App\Models\Sala::findOrFail($salaId);

        $engine = $this->carregarEngineDaSessao($salaId);

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
            $this->salvarEngineNaSessao($salaId, $engine);
        }

        $estado = $engine->getState();

        return view('apresentador.control', [
            'sala' => $sala,
            'estado' => $estado['estado'],
            'pergunta' => $estado['pergunta_atual'],
            'pontuacao' => $estado['pontuacao'],
            'tempo_restante' => $estado['tempo_restante'],
            'tempo_esgotado' => $estado['tempo_esgotado'],
        ]);
    }

    public function proximaPergunta(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSessao($salaId);
        if (!$engine) {
            return back()->with('error', 'Sessão de jogo não encontrada.');
        }

        $engine->startNextQuestion();
        $this->salvarEngineNaSessao($salaId, $engine);

        return back()->with('success', 'Próxima pergunta iniciada.');
    }

    public function pausar(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSessao($salaId);
        if (!$engine) {
            return back()->with('error', 'Sessão de jogo não encontrada.');
        }

        $engine->pause();
        $this->salvarEngineNaSessao($salaId, $engine);

        return back()->with('success', 'Jogo pausado.');
    }

    public function retomar(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSessao($salaId);
        if (!$engine) {
            return back()->with('error', 'Sessão de jogo não encontrada.');
        }

        $engine->resume();
        $this->salvarEngineNaSessao($salaId, $engine);

        return back()->with('success', 'Jogo retomado.');
    }

    public function finalizar(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSessao($salaId);
        if (!$engine) {
            return back()->with('error', 'Sessão de jogo não encontrada.');
        }

        $engine->finishGame();
        $this->salvarEngineNaSessao($salaId, $engine);

        return redirect()->route('apresentador.resultado', $salaId);
    }

    public function resultado($salaId)
    {
        $engine = $this->carregarEngineDaSessao($salaId);
        if (!$engine) {
            return redirect()->route('apresentador.control', $salaId);
        }

        $resultados = $engine->getResults();

        return view('apresentador.resultado', [
            'salaId' => $salaId,
            'resultados' => $resultados,
        ]);
    }

    private function carregarEngineDaSessao($salaId): ?GameEngine
    {
        $dados = session('game_engine_' . $salaId);
        if (!$dados) {
            return null;
        }
        $engine = unserialize($dados);
        return $engine instanceof GameEngine ? $engine : null;
    }

    private function salvarEngineNaSessao($salaId, GameEngine $engine): void
    {
        session(['game_engine_' . $salaId => serialize($engine)]);
    }
}
