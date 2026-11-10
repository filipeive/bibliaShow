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
use App\Models\NivelDificuldade;
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
            ->limit(15)
            ->get();

        return view('apresentador.painel', compact('salas'));
    }

    public function criarSala(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'quantidade_perguntas' => 'nullable|integer|min:4|max:50',
            'dificuldade_id' => 'nullable|integer',
            'premio_primeiro_lugar' => 'nullable|string|max:255',
            'premio_segundo_lugar' => 'nullable|string|max:255',
        ]);

        $igrejaId = $request->user()?->igreja_id ?? Igreja::value('id') ?? 1;
        $apresentadorId = $request->user()?->id ?? Usuario::value('id') ?? 1;

        $sala = Sala::create([
            'igreja_id' => $igrejaId,
            'codigo_acesso' => Str::upper(Str::random(6)),
            'nome' => $request->nome,
            'apresentador_id' => $apresentadorId,
            'status' => 'em_curso',
            'premio_primeiro_lugar' => $request->input('premio_primeiro_lugar'),
            'premio_segundo_lugar' => $request->input('premio_segundo_lugar'),
        ]);

        $qtd = (int) ($request->input('quantidade_perguntas') ?: 24);
        $questoes = $this->selecionarQuestoesSala($qtd, $request->input('dificuldade_id'));

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
            $questoes = $this->selecionarQuestoesSala(24);

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
                60
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
            'total_perguntas' => $estadoMotor['total_perguntas'] ?? 24,
            'fase_atual' => $estadoMotor['fase_atual'] ?? 1,
            'placar_milhao' => $estadoMotor['placar_milhao'] ?? ['errar' => 0, 'parar' => 0, 'acertar' => 10, 'valor_pergunta' => 10, 'fase' => 1],
            'ajudas_motor' => $estadoMotor['ajudas'] ?? [],
            'pontuacao' => $pontuacaoTotalSala,
            'tempo_restante' => $estadoMotor['tempo_restante'],
            'tempo_esgotado' => $estadoMotor['tempo_esgotado'],
            'participantes' => $participantes,
            'equipes' => $equipes,
            'ranking' => $ranking,
        ]);
    }

    public function usarAjuda(Request $request, $salaId)
    {
        $request->validate([
            'tipo' => 'required|in:dica,convidado,cartas,pulo,revelar',
            'indice_convidado' => 'nullable|integer|min:0|max:2',
        ]);

        $engine = $this->carregarEngineDaSala($salaId);
        if (!$engine) {
            return back()->with('error', 'Sessão de jogo não encontrada.');
        }

        $tipo = $request->input('tipo');
        $resultado = ['sucesso' => false, 'mensagem' => 'Ação inválida.'];

        if ($tipo === 'dica') {
            $resultado = $engine->usarDica();
        } elseif ($tipo === 'convidado') {
            $resultado = $engine->consultarConvidado((int) $request->input('indice_convidado', 0));
        } elseif ($tipo === 'cartas') {
            $resultado = $engine->usarCartas();
        } elseif ($tipo === 'revelar') {
            $resultado = $engine->revelarGabarito();
        } elseif ($tipo === 'pulo') {
            $estadoAtual = $engine->getState();
            $idsUsados = collect($engine->getResults()['historico_perguntas'] ?? [])->pluck('id')->filter()->all();
            $faseAtual = $estadoAtual['fase_atual'] ?? 1;
            $nivelModel = NivelDificuldade::where('nivel', $faseAtual)->first();

            $query = Questao::aprovadas()
                ->with(['livro', 'dificuldade', 'categoria'])
                ->whereNotIn('id', $idsUsados);

            if ($nivelModel) {
                $query->where('dificuldade_id', $nivelModel->id);
            }

            $novaQuestao = $query->inRandomOrder()->first();
            $reserva = $novaQuestao ? $this->formatarQuestao($novaQuestao) : null;
            $resultado = $engine->usarPulo($reserva);
        }

        $this->salvarEngineNaSala($salaId, $engine);

        if (!$resultado['sucesso']) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => $resultado['mensagem'] ?? 'Não foi possível usar este recurso.',
                ], 422);
            }
            return back()->with('error', $resultado['mensagem'] ?? 'Não foi possível usar este recurso.');
        }

        if (!empty($resultado['som_efeito'])) {
            session()->flash('som_efeito', $resultado['som_efeito']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            $estadoAtualizado = $engine->getState();
            return response()->json([
                'sucesso' => true,
                'mensagem' => $resultado['mensagem'] ?? 'Recurso ativado com sucesso!',
                'som_efeito' => $resultado['som_efeito'] ?? null,
                'estado' => $estadoAtualizado['estado'] ?? 'em_curso',
                'ajudas' => $estadoAtualizado['ajudas'] ?? [],
                'pergunta' => $estadoAtualizado['pergunta_atual'] ?? null,
            ]);
        }

        return back()->with('success', $resultado['mensagem'] ?? 'Recurso ativado com sucesso!');
    }

    public function atualizarPremios(Request $request, $salaId)
    {
        $request->validate([
            'premio_primeiro_lugar' => 'nullable|string|max:255',
            'premio_segundo_lugar' => 'nullable|string|max:255',
        ]);

        $sala = Sala::findOrFail($salaId);
        $sala->update([
            'premio_primeiro_lugar' => $request->input('premio_primeiro_lugar'),
            'premio_segundo_lugar' => $request->input('premio_segundo_lugar'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Prêmios de 1º e 2º lugar atualizados com sucesso!',
                'premio_primeiro_lugar' => $sala->premio_primeiro_lugar,
                'premio_segundo_lugar' => $sala->premio_segundo_lugar,
            ]);
        }

        return back()->with('success', 'Prêmios de 1º e 2º lugar atualizados para a sala!');
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

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'equipe' => $equipe]);
        }

        return back()->with('success', "Equipe «{$equipe->nome}» criada!");
    }

    public function proximaPergunta(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSala($salaId);
        if (!$engine) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['sucesso' => false, 'mensagem' => 'Sessão de jogo não encontrada.'], 404);
            }
            return back()->with('error', 'Sessão de jogo não encontrada.');
        }

        $faseAntes = $engine->getState()['fase_atual'] ?? 1;
        $engine->startNextQuestion();
        $this->salvarEngineNaSala($salaId, $engine);

        $estado = $engine->getState();

        if ($estado['estado'] === 'terminado') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'sucesso' => true,
                    'estado' => 'terminado',
                    'redirect' => route('apresentador.resultado', $salaId),
                ]);
            }
            return redirect()->route('apresentador.resultado', $salaId);
        }

        $faseDepois = $estado['fase_atual'] ?? 1;
        $som = $faseDepois > $faseAntes ? ('fase' . min(4, $faseDepois)) : 'inicio';
        session()->flash('som_efeito', $som);

        $this->tentarBroadcast(new PerguntaIniciada(
            (int) $salaId,
            $estado['pergunta_atual'] ?? [],
            60
        ));
        $this->tentarBroadcast(new JogoEstadoAlterado(
            (int) $salaId,
            $estado['estado'],
            $estado['pergunta_atual'] ?? null,
            $estado['tempo_restante']
        ));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Próxima pergunta iniciada com sucesso!',
                'som_efeito' => $som,
                'estado' => $estado['estado'],
                'fase_atual' => $estado['fase_atual'] ?? 1,
                'numero_pergunta' => $estado['numero_pergunta'] ?? 1,
                'total_perguntas' => $estado['total_perguntas'] ?? 24,
                'placar_milhao' => $estado['placar_milhao'] ?? null,
                'pergunta' => $estado['pergunta_atual'] ?? null,
                'ajudas' => $estado['ajudas'] ?? [],
            ]);
        }

        return back()->with('success', 'Próxima pergunta iniciada no telão e smartphones.');
    }

    public function pausar(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSala($salaId);
        if (!$engine) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['sucesso' => false, 'mensagem' => 'Sessão de jogo não encontrada.'], 404);
            }
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Jogo pausado.',
                'estado' => 'pausado',
            ]);
        }

        return back()->with('success', 'Jogo pausado.');
    }

    public function retomar(Request $request, $salaId)
    {
        $engine = $this->carregarEngineDaSala($salaId);
        if (!$engine) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['sucesso' => false, 'mensagem' => 'Sessão de jogo não encontrada.'], 404);
            }
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Jogo retomado.',
                'estado' => 'em_curso',
            ]);
        }

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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Partida finalizada!',
                'redirect' => route('apresentador.resultado', $salaId),
            ]);
        }

        return redirect()->route('apresentador.resultado', $salaId);
    }

    public function eliminarSala(Request $request, $salaId)
    {
        $sala = Sala::findOrFail($salaId);
        $nome = $sala->nome;
        $codigo = $sala->codigo_acesso;

        session()->forget('game_engine_' . $salaId);

        try {
            foreach ($sala->jogos as $j) {
                $j->respostas()->delete();
                $j->delete();
            }
            $sala->participantes()->delete();
            $sala->equipes()->delete();
            $sala->delete();
        } catch (\Throwable $e) {
            $sala->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => "Sala «{$nome}» ({$codigo}) excluída com sucesso!",
                'sala_id' => (int) $salaId,
            ]);
        }

        return redirect()->route('apresentador.painel')->with('success', "Sala «{$nome}» ({$codigo}) excluída.");
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

    private function selecionarQuestoesSala(int $qtd = 24, $dificuldadeId = null): array
    {
        if ($dificuldadeId) {
            return Questao::aprovadas()
                ->with(['livro', 'dificuldade', 'categoria'])
                ->where('dificuldade_id', (int) $dificuldadeId)
                ->inRandomOrder()
                ->limit($qtd)
                ->get()
                ->map(fn (Questao $q) => $this->formatarQuestao($q))
                ->toArray();
        }

        $porFase = max(1, (int) ceil($qtd / 4));
        $selecionadas = [];

        for ($nivel = 1; $nivel <= 4; $nivel++) {
            $nivelModel = NivelDificuldade::where('nivel', $nivel)->first();
            if (!$nivelModel) {
                continue;
            }

            $lote = Questao::aprovadas()
                ->with(['livro', 'dificuldade', 'categoria'])
                ->where('dificuldade_id', $nivelModel->id)
                ->inRandomOrder()
                ->limit($porFase)
                ->get()
                ->map(fn (Questao $q) => $this->formatarQuestao($q))
                ->toArray();

            $selecionadas = array_merge($selecionadas, $lote);
        }

        if (count($selecionadas) < $qtd) {
            $idsExistentes = array_column($selecionadas, 'id');
            $extras = Questao::aprovadas()
                ->with(['livro', 'dificuldade', 'categoria'])
                ->whereNotIn('id', $idsExistentes)
                ->inRandomOrder()
                ->limit($qtd - count($selecionadas))
                ->get()
                ->map(fn (Questao $q) => $this->formatarQuestao($q))
                ->toArray();
            $selecionadas = array_merge($selecionadas, $extras);
        }

        return array_slice($selecionadas, 0, $qtd);
    }

    private function formatarQuestao(Questao $q): array
    {
        $arr = $q->toArray();
        $arr['livro_nome'] = $q->livro?->nome;
        $arr['dificuldade_nome'] = $q->dificuldade?->nome ?? '1ª Fase';
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
            // Ignora falha caso servidor Reverb não esteja ativo
        }
    }
}
