<?php

namespace App\Http\Controllers;

use App\Events\JogoEstadoAlterado;
use App\Events\PerguntaIniciada;
use App\Events\RankingAtualizado;
use App\Game\Engine\GameEngine;
use App\Game\Engine\Scoring\StandardScoring;
use App\Game\Engine\Selectors\RandomSelector;
use App\Game\Engine\State\GameState;
use App\Game\Engine\Timers\CountdownTimer;
use App\Models\Equipe;
use App\Models\Igreja;
use App\Models\Jogo;
use App\Models\Questao;
use App\Models\Sala;
use App\Models\Usuario;
use Illuminate\Http\Request;
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
            'quantidade_perguntas' => 'nullable|integer|min:5|max:50',
            'dificuldade_id' => 'nullable|integer',
        ]);

        $igrejaId = $request->user()?->igreja_id ?? Igreja::value('id') ?? 1;
        $apresentadorId = $request->user()?->id ?? Usuario::value('id') ?? 1;

        $sala = Sala::create([
            'igreja_id' => $igrejaId,
            'codigo_acesso' => Str::upper(Str::random(6)),
            'nome' => $request->nome,
            'apresentador_id' => $apresentadorId,
            'status' => 'em_curso',
        ]);

        $qtd = (int) ($request->input('quantidade_perguntas') ?: 12);

        $query = Questao::aprovadas()->with(['livro', 'dificuldade', 'categoria']);
        if ($request->filled('dificuldade_id')) {
            $query->where('dificuldade_id', $request->integer('dificuldade_id'));
        }

        $questoes = $query->inRandomOrder()
            ->limit($qtd)
            ->get()
            ->map(fn (Questao $q) => $this->formatarQuestao($q))
            ->toArray();

        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );
        $engine->start($questoes);

        $this->salvarEngineNaSala($sala->id, $engine);

        return redirect()->route('apresentador.control', $sala->id)
            ->with('success', "Sala «{$sala->nome}» criada! Código de entrada: {$sala->codigo_acesso}");
    }

    public function control(Request $request, $salaId)
    {
        $sala = Sala::with(['participantes.equipe', 'equipes'])->findOrFail($salaId);

        if ($request->boolean('nova_partida')) {
            session()->forget('game_engine_' . $salaId);
            Jogo::where('sala_id', $salaId)->whereIn('estado', ['aguardando', 'em_curso', 'pausado'])->update(['estado' => 'terminado']);
            $sala->update(['status' => 'em_curso']);
        }

        $engine = $this->carregarEngineDaSala($salaId);

        if (!$engine) {
            $questoes = Questao::aprovadas()
                ->with(['livro', 'dificuldade', 'categoria'])
                ->inRandomOrder()
                ->limit(12)
                ->get()
                ->map(fn (Questao $q) => $this->formatarQuestao($q))
                ->toArray();

            $engine = new GameEngine(
                new RandomSelector(),
                new StandardScoring(),
                new CountdownTimer(),
                new GameState()
            );

            $engine->start($questoes);
            $this->salvarEngineNaSala($salaId, $engine);

            $estadoInicial = $engine->getState();
            $this->tentarBroadcast(new PerguntaIniciada(
                (int) $salaId,
                $estadoInicial['pergunta_atual'] ?? [],
                30
            ));
        }

        $estadoMotor = $engine->getState();
        $participantes = $sala->participantes()->conectados()->with('equipe')->orderByDesc('pontuacao_total')->get();
        $equipes = $sala->equipes;

        $ranking = $participantes->map(function ($p) {
            return [
                'id' => $p->id,
                'nome' => $p->nome,
                'equipe' => $p->equipe?->nome,
                'pontuacao' => $p->pontuacao_total,
            ];
        })->values()->all();

        $pontuacaoTotalSala = max((int) $estadoMotor['pontuacao'], (int) $participantes->sum('pontuacao_total'));

        $this->tentarBroadcast(new RankingAtualizado((int) $salaId, $ranking));
        $this->tentarBroadcast(new JogoEstadoAlterado(
            (int) $salaId,
            $estadoMotor['estado'],
            $estadoMotor['pergunta_atual'] ?? null,
            $estadoMotor['tempo_restante']
        ));

        return view('apresentador.control', [
            'sala' => $sala,
            'estado' => $estadoMotor['estado'],
            'pergunta' => $estadoMotor['pergunta_atual'],
            'numero_pergunta' => $estadoMotor['numero_pergunta'] ?? 1,
            'total_perguntas' => $estadoMotor['total_perguntas'] ?? 12,
            'pontuacao' => $pontuacaoTotalSala,
            'tempo_restante' => $estadoMotor['tempo_restante'],
            'tempo_esgotado' => $estadoMotor['tempo_esgotado'],
            'participantes' => $participantes,
            'equipes' => $equipes,
            'ranking' => $ranking,
        ]);
    }

    public function criarEquipe(Request $request, $salaId)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'cor' => 'nullable|string|size:7',
        ]);

        Sala::findOrFail($salaId);

        $equipe = Equipe::create([
            'sala_id' => $salaId,
            'nome' => $request->nome,
            'cor' => $request->cor ?? '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
        ]);

        $this->tentarBroadcast(new RankingAtualizado((int) $salaId, []));

        return response()->json(['success' => true, 'equipe' => $equipe]);
    }

    public function proximaPergunta(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSala($salaId);
        if (!$engine) {
            return back()->with('error', 'Sessão de jogo não encontrada.');
        }

        $engine->startNextQuestion();
        $this->salvarEngineNaSala($salaId, $engine);

        $estado = $engine->getState();

        if ($estado['estado'] === 'terminado') {
            return redirect()->route('apresentador.resultado', $salaId);
        }

        $this->tentarBroadcast(new PerguntaIniciada(
            (int) $salaId,
            $estado['pergunta_atual'] ?? [],
            30
        ));
        $this->tentarBroadcast(new JogoEstadoAlterado(
            (int) $salaId,
            $estado['estado'],
            $estado['pergunta_atual'] ?? null,
            $estado['tempo_restante']
        ));

        return back()->with('success', 'Próxima pergunta iniciada.');
    }

    public function pausar(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSala($salaId);
        if (!$engine) {
            return back()->with('error', 'Sessão de jogo não encontrada.');
        }

        $engine->pause();
        $this->salvarEngineNaSala($salaId, $engine);

        $estado = $engine->getState();
        $this->tentarBroadcast(new JogoEstadoAlterado(
            (int) $salaId,
            $estado['estado'],
            $estado['pergunta_atual'] ?? null,
            $estado['tempo_restante']
        ));

        return back()->with('success', 'Jogo pausado.');
    }

    public function retomar(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSala($salaId);
        if (!$engine) {
            return back()->with('error', 'Sessão de jogo não encontrada.');
        }

        $engine->resume();
        $this->salvarEngineNaSala($salaId, $engine);

        $estado = $engine->getState();
        $this->tentarBroadcast(new JogoEstadoAlterado(
            (int) $salaId,
            $estado['estado'],
            $estado['pergunta_atual'] ?? null,
            $estado['tempo_restante']
        ));

        return back()->with('success', 'Jogo retomado.');
    }

    public function finalizar(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSala($salaId);
        if ($engine) {
            $engine->finishGame();
            $this->salvarEngineNaSala($salaId, $engine);
        }

        $sala = Sala::find($salaId);
        if ($sala) {
            $sala->update(['status' => 'terminada']);
        }

        $this->tentarBroadcast(new JogoEstadoAlterado((int) $salaId, 'terminado'));

        return redirect()->route('apresentador.resultado', $salaId);
    }

    public function resultado($salaId)
    {
        $engine = $this->carregarEngineDaSala($salaId);
        if (!$engine) {
            return redirect()->route('apresentador.control', $salaId);
        }

        $resultados = $engine->getResults();

        $sala = Sala::find($salaId);
        $participantes = $sala?->participantes()->with('equipe')->orderByDesc('pontuacao_total')->get() ?? collect();

        return view('apresentador.resultado', compact(
            'salaId',
            'resultados',
            'sala',
            'participantes'
        ));
    }

    private function formatarQuestao(Questao $q): array
    {
        $arr = $q->toArray();
        $arr['livro_nome'] = $q->livro?->nome;
        $arr['dificuldade_nome'] = $q->dificuldade?->nome ?? 'Iniciante';
        $arr['dificuldade_nivel'] = $q->dificuldade?->nivel ?? 1;
        $arr['categoria_nome'] = $q->categoria?->nome;

        return $arr;
    }

    private function carregarEngineDaSala($salaId): ?GameEngine
    {
        $jogo = Jogo::where('sala_id', $salaId)->latest()->first();
        if ($jogo && $jogo->dados_motor) {
            $engine = @unserialize($jogo->dados_motor);
            if ($engine instanceof GameEngine) {
                return $engine;
            }
        }

        $dados = session('game_engine_' . $salaId);
        if (!$dados) {
            return null;
        }
        $engine = @unserialize($dados);

        return $engine instanceof GameEngine ? $engine : null;
    }

    private function salvarEngineNaSala($salaId, GameEngine $engine): void
    {
        $serializado = serialize($engine);
        session(['game_engine_' . $salaId => $serializado]);

        $estado = $engine->getState();
        $perguntaId = $estado['pergunta_atual']['id'] ?? null;

        $jogo = Jogo::where('sala_id', $salaId)
            ->whereIn('estado', ['aguardando', 'em_curso', 'pausado'])
            ->latest()
            ->first();

        if (!$jogo) {
            Jogo::create([
                'sala_id' => $salaId,
                'estado' => $estado['estado'],
                'pergunta_atual_id' => $perguntaId,
                'pontuacao_total' => $estado['pontuacao'] ?? 0,
                'dados_motor' => $serializado,
            ]);
        } else {
            $jogo->update([
                'estado' => $estado['estado'],
                'pergunta_atual_id' => $perguntaId,
                'dados_motor' => $serializado,
            ]);
        }
    }

    private function tentarBroadcast(object $evento): void
    {
        try {
            broadcast($evento);
        } catch (\Throwable $e) {
            // Ignora falha caso servidor Reverb não esteja ativo (fallback via polling continua ativo)
        }
    }
}
