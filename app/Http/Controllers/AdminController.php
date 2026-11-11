<?php

namespace App\Http\Controllers;

use App\Models\Capitulo;
use App\Models\Categoria;
use App\Models\Igreja;
use App\Models\Jogo;
use App\Models\LivroBiblico;
use App\Models\NivelDificuldade;
use App\Models\Participante;
use App\Models\Questao;
use App\Models\Resposta;
use App\Models\Sala;
use App\Models\Usuario;
use Database\Seeders\QuestoesClassicoSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_questoes' => Questao::count(),
            'questoes_aprovadas' => Questao::where('aprovada', true)->count(),
            'questoes_pendentes' => Questao::where('aprovada', false)->count(),
            'questoes_classicas' => Questao::whereNotNull('codigo_classico')->count(),
            'total_livros' => LivroBiblico::count(),
            'total_capitulos' => Capitulo::count(),
            'salas_ativas' => Sala::where('status', '!=', 'terminada')->count(),
            'total_salas' => Sala::count(),
            'total_jogos' => Jogo::count(),
            'total_participantes' => Participante::count(),
            'total_respostas' => Resposta::count(),
            'total_igrejas' => Igreja::count(),
            'total_usuarios' => Usuario::count(),
        ];

        $distribuicaoFases = NivelDificuldade::orderBy('nivel')
            ->get()
            ->map(function ($d) {
                return [
                    'nivel' => $d->nivel,
                    'nome' => $d->nome,
                    'pontos' => $d->pontos_base,
                    'total' => Questao::where('dificuldade_id', $d->id)->count(),
                ];
            });

        $questoesPendentes = Questao::with(['livro', 'dificuldade'])
            ->where('aprovada', false)
            ->latest()
            ->limit(10)
            ->get();

        $salasRecentes = Sala::withCount('participantes')
            ->with('igreja')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        $usuarios = Usuario::with('igreja')->orderBy('id')->get();
        $igrejas = Igreja::withCount(['usuarios', 'salas', 'questoes'])->orderBy('nome')->get();
        $categorias = Categoria::orderBy('nome')->get();

        return view('admin.dashboard', compact(
            'stats',
            'distribuicaoFases',
            'questoesPendentes',
            'salasRecentes',
            'usuarios',
            'igrejas',
            'categorias'
        ));
    }

    public function criarIgreja(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:150',
            'plano' => 'nullable|in:gratuito,comunidade,premium',
        ]);

        $slugBase = Str::slug($validated['nome']);
        $slug = $slugBase ?: 'igreja-' . Str::lower(Str::random(5));
        if (Igreja::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        Igreja::create([
            'nome' => trim($validated['nome']),
            'slug' => $slug,
            'plano' => $validated['plano'] ?? 'gratuito',
        ]);

        return back()->with('success', 'Congregação / Igreja cadastrada com sucesso.');
    }

    public function salvarUsuario(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:120',
            'email' => 'required|email|max:150|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'igreja_id' => 'required|exists:igrejas,id',
            'papel' => 'required|in:admin_geral,admin_igreja,apresentador,editor,participante',
        ]);

        Usuario::create([
            'igreja_id' => $validated['igreja_id'],
            'nome' => trim($validated['nome']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'papel' => $validated['papel'],
        ]);

        return back()->with('success', 'Usuário adicionado com sucesso.');
    }

    public function reimportarClassico()
    {
        $seeder = new QuestoesClassicoSeeder();
        $seeder->run();

        return back()->with('success', 'Acervo clássico Bíblia Show (1.000 perguntas, 66 livros e ajudas) sincronizado com sucesso!');
    }

    public function exportarPerguntas(Request $request)
    {
        $formato = $request->query('formato', 'json');
        $user = $request->user();
        $query = Questao::with(['livro', 'dificuldade', 'categoria']);

        // Se for admin de igreja, exporta acervo da sua igreja + perguntas clássicas
        if ($user && $user->papel !== 'admin_geral' && $user->igreja_id) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('igreja_id')
                    ->orWhere('igreja_id', $user->igreja_id)
                    ->orWhereNotNull('codigo_classico');
            });
        }

        $questoes = $query->orderBy('id')->get();

        if ($formato === 'csv') {
            $sanitizarCsv = function ($val): string {
                $v = trim((string) $val);
                // Previne Formula/CSV Injection se a célula começar com =, +, -, @ ou tab
                if ($v !== '' && in_array($v[0], ['=', '+', '-', '@', "\t", "\r"])) {
                    $v = "'" . $v;
                }
                return str_replace([';', "\r", "\n"], [',', ' ', ' '], $v);
            };

            $linhas = ["ID;Codigo_Classico;Fase;Livro;Referencia;Pergunta;Opcao_A;Opcao_B;Opcao_C;Opcao_D;Resposta_Correta"];
            foreach ($questoes as $q) {
                $opcoes = is_array($q->opcoes) ? $q->opcoes : (json_decode((string) $q->opcoes, true) ?: []);
                $campos = [
                    $q->id,
                    $q->codigo_classico ?? '',
                    $q->dificuldade?->nivel ?? 1,
                    $sanitizarCsv($q->livro?->nome ?? ''),
                    $sanitizarCsv($q->referencia_biblica ?? ''),
                    $sanitizarCsv($q->texto),
                    $sanitizarCsv($opcoes[0] ?? ''),
                    $sanitizarCsv($opcoes[1] ?? ''),
                    $sanitizarCsv($opcoes[2] ?? ''),
                    $sanitizarCsv($opcoes[3] ?? ''),
                    $sanitizarCsv($q->resposta_correta),
                ];
                $linhas[] = implode(';', $campos);
            }

            return response(implode("\n", $linhas), 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="biblia-show-questoes.csv"',
            ]);
        }

        return response()->json([
            'sucesso' => true,
            'total' => $questoes->count(),
            'questoes' => $questoes,
        ], 200, [
            'Content-Disposition' => 'attachment; filename="biblia-show-questoes.json"',
        ]);
    }

    public function eliminarSala(Request $request, Sala $sala)
    {
        $user = $request->user();
        if ($user && $user->papel !== 'admin_geral' && (int) $user->igreja_id !== (int) $sala->igreja_id) {
            abort(403, 'Acesso negado: você só pode excluir salas da sua própria igreja.');
        }

        $nome = $sala->nome;
        $codigo = $sala->codigo_acesso;

        // Limpar sessões do motor em cache/sessão
        session()->forget('game_engine_' . $sala->id);

        // Deletar respostas, participantes e jogos vinculados
        try {
            foreach ($sala->jogos as $j) {
                $j->respostas()->delete();
                $j->delete();
            }
            $sala->participantes()->delete();
            $sala->equipes()->delete();
            $sala->delete();
        } catch (\Throwable $e) {
            // Em caso de chave estrangeira com soft deletes
            $sala->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => "Sala «{$nome}» ({$codigo}) foi eliminada com sucesso!",
                'sala_id' => $sala->id,
            ]);
        }

        return back()->with('success', "Sala «{$nome}» ({$codigo}) eliminada com sucesso.");
    }
}

