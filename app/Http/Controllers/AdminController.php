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
        $questoes = Questao::with(['livro', 'dificuldade', 'categoria'])->orderBy('id')->get();

        if ($formato === 'csv') {
            $linhas = ["ID;Codigo_Classico;Fase;Livro;Referencia;Pergunta;Opcao_A;Opcao_B;Opcao_C;Opcao_D;Resposta_Correta"];
            foreach ($questoes as $q) {
                $opcoes = is_array($q->opcoes) ? $q->opcoes : (json_decode((string) $q->opcoes, true) ?: []);
                $campos = [
                    $q->id,
                    $q->codigo_classico ?? '',
                    $q->dificuldade?->nivel ?? 1,
                    $q->livro?->nome ?? '',
                    $q->referencia_biblica ?? '',
                    str_replace([';', "\r", "\n"], [',', ' ', ' '], $q->texto),
                    str_replace(';', ',', $opcoes[0] ?? ''),
                    str_replace(';', ',', $opcoes[1] ?? ''),
                    str_replace(';', ',', $opcoes[2] ?? ''),
                    str_replace(';', ',', $opcoes[3] ?? ''),
                    str_replace(';', ',', $q->resposta_correta),
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
}
