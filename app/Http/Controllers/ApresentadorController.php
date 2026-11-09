<?php

namespace App\Http\Controllers;

use App\Events\JogoEstadoAlterado;
use App\Events\PerguntaIniciada;
use App\Events\RankingAtualizado;
use App\Game\Engine\GameEngine;
use App\Game\Engine\Selectors\RandomSelector;
use App\Game\Engine\Scoring\StandardScoring;
use App\Game\Engine\Timers\CountdownTimer;
use App\Game\Engine\State\GameState;
use App\Models\Equipe;
use App\Models\Jogo;
use App\Models\Participante;
use App\Models\Questao;
use App\Models\Sala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Str;

class ApresentadorController extends Controller
{
    public function painel()
    {
        $salas = Sala::where('status', '!=', 'terminada')
            ->withCount('participantes')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        return view('apresentador.painel', compact('salas'));
    }

    public function criarSala(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $sala = Sala::create([
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
        $sala = Sala::with(['participantes.equipe', 'equipes'])->findOrFail($salaId);

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

            // Broadcast primeira pergunta
            $estado = $engine->getState();
            broadcast(new PerguntaIniciada(
                $salaId,
                $estado['pergunta_atual'] ?? [],
                30
            ));
        }

        $estado = $engine->getState();
        $participantes = $sala->participantes()->conectados()->with('equipe')->get();
        $equipes = $sala->equipes;

        // Calcular ranking
        $ranking = $participantes->sortByDesc('pontuacao_total')->map(function ($p) {
            return [
                'id' => $p->id,
                'nome' => $p->nome,
                'equipe' => $p->equipe?->nome,
                'pontuacao' => $p->pontuacao_total,
            ];
        })->values()->all();

        broadcast(new RankingAtualizado($salaId, $ranking));

        broadcast(new JogoEstadoAlterado(
            $salaId,
            $estado['estado'],
            $estado['pergunta_atual'] ?? null,
            $estado['tempo_restante']
        ));

        return view('apresentador.control', compact(
            'sala',
            'estado',
            'participantes',
            'equipes',
            'ranking'
        ));
    }

    public function criarEquipe(Request $request, $salaId)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'cor' => 'nullable|string|size:7',
        ]);

        $sala = Sala::findOrFail($salaId);

        $equipe = Equipe::create([
            'sala_id' => $salaId,
            'nome' => $request->nome,
            'cor' => $request->cor ?? '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
        ]);

        broadcast(new RankingAtualizado($salaId, []));

        return response()->json(['success' => true, 'equipe' => $equipe]);
    }

    public function proximaPergunta(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSessao($salaId);
        if (!$engine) {
            return back()->with('error', 'Sessão de jogo não encontrada.');
        }

        $engine->startNextQuestion();
        $this->salvarEngineNaSessao($salaId, $engine);

        $estado = $engine->getState();
        broadcast(new PerguntaIniciada(
            $salaId,
            $estado['pergunta_atual'] ?? [],
            30
        ));
        broadcast(new JogoEstadoAlterado(
            $salaId,
            $estado['estado'],
            $estado['pergunta_atual'] ?? null,
            $estado['tempo_restante']
        ));

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

        $estado = $engine->getState();
        broadcast(new JogoEstadoAlterado(
            $salaId,
            $estado['estado'],
            null,
            $estado['tempo_restante']
        ));

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

        $estado = $engine->getState();
        broadcast(new JogoEstadoAlterado(
            $salaId,
            $estado['estado'],
            $estado['pergunta_atual'] ?? null,
            $estado['tempo_restante']
        ));

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

        $sala = Sala::find($salaId);
        if ($sala) {
            $sala->update(['status' => 'terminada']);
        }

        broadcast(new JogoEstadoAlterado($salaId, 'terminado'));

        return redirect()->route('apresentador.resultado', $salaId);
    }

    public function resultado($salaId)
    {
        $engine = $this->carregarEngineDaSessao($salaId);
        if (!$engine) {
            return redirect()->route('apresentador.control', $salaId);
        }

        $resultados = $engine->getResults();

        $sala = Sala::find($salaId);
        $participantes = $sala?->participantes()->with('equipe')->get() ?? collect();

        return view('apresentador.resultado', compact(
            'salaId',
            'resultados',
            'sala',
            'participantes'
        ));
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
