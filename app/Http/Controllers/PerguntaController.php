<?php

namespace App\Http\Controllers;

use App\Models\Capitulo;
use App\Models\Categoria;
use App\Models\LivroBiblico;
use App\Models\NivelDificuldade;
use App\Models\Questao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PerguntaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Questao::with(['livro', 'capitulo', 'dificuldade', 'categoria']);

        // Se for admin de igreja ou editor, visualiza o acervo global/clássico mais o específico da sua igreja
        if ($user && $user->papel !== 'admin_geral' && $user->igreja_id) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('igreja_id')
                    ->orWhere('igreja_id', $user->igreja_id)
                    ->orWhereNotNull('codigo_classico');
            });
        }

        if ($request->filled('aprovada')) {
            $query->where('aprovada', filter_var($request->aprovada, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('livro_id')) {
            $query->where('livro_id', $request->livro_id);
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('dificuldade_id')) {
            $query->where('dificuldade_id', $request->dificuldade_id);
        }

        if ($request->filled('busca')) {
            $busca = trim((string) $request->busca);
            $query->where(function ($q) use ($busca) {
                $q->where('texto', 'like', "%{$busca}%")
                    ->orWhere('referencia_biblica', 'like', "%{$busca}%")
                    ->orWhere('codigo_classico', 'like', "%{$busca}%");
            });
        }

        $questoes = $query->orderBy('id')->paginate(20)->withQueryString();

        $livros = LivroBiblico::orderBy('id')->get();
        $categorias = Categoria::orderBy('nome')->get();
        $dificuldades = NivelDificuldade::orderBy('nivel')->get();

        return view('perguntas.index', compact('questoes', 'livros', 'categorias', 'dificuldades'));
    }

    public function create()
    {
        $livros = LivroBiblico::orderBy('id')->get();
        $categorias = Categoria::orderBy('nome')->get();
        $dificuldades = NivelDificuldade::orderBy('nivel')->get();

        return view('perguntas.create', compact('livros', 'categorias', 'dificuldades'));
    }

    public function store(Request $request)
    {
        $opcoesInput = $request->input('opcoes', []);
        if (is_string($opcoesInput)) {
            $decoded = json_decode($opcoesInput, true);
            $opcoesInput = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode("\n", $opcoesInput)));
        }
        if (is_array($opcoesInput)) {
            $opcoesInput = array_values(array_filter(array_map(fn ($v) => trim((string) $v), $opcoesInput), fn ($v) => $v !== ''));
        }

        $request->merge(['opcoes' => $opcoesInput]);

        $validator = Validator::make($request->all(), [
            'igreja_id' => 'required|exists:igrejas,id',
            'livro_id' => 'required|exists:livros_biblicos,id',
            'capitulo_id' => 'nullable|exists:capitulos,id',
            'versiculo_de' => 'nullable|integer|min:1',
            'versiculo_ate' => 'nullable|integer|min:1|gte:versiculo_de',
            'referencia_biblica' => 'nullable|string|max:120',
            'texto' => 'required|string|min:5',
            'opcoes' => 'required|array|min:2',
            'resposta_correta' => 'required|string|min:1',
            'explicacao' => 'nullable|string',
            'contexto' => 'nullable|string',
            'dificuldade_id' => 'nullable|exists:niveis_dificuldade,id',
            'categoria_id' => 'nullable|exists:categorias,id',
            'referencia_verificada' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $user = $request->user();
        if ($user && $user->papel !== 'admin_geral' && $user->igreja_id) {
            $validated['igreja_id'] = $user->igreja_id;
        }

        $validated['aprovada'] = true;
        $validated['submetida_por'] = auth()->id() ?? 1;
        $validated['opcoes'] = $opcoesInput;

        Questao::create($validated);

        return redirect()->route('perguntas.index')
            ->with('success', 'Pergunta criada com sucesso e adicionada ao banco do Bíblia Show.');
    }

    private function verificarAcessoQuestao(Request $request, Questao $questao, bool $escrita = false): void
    {
        $user = $request->user();
        if (! $user || $user->papel === 'admin_geral') {
            return;
        }

        if ($escrita && ! $questao->igreja_id) {
            abort(403, 'Acesso negado: apenas o administrador geral pode modificar questões globais.');
        }

        if ($questao->igreja_id && (int) $user->igreja_id !== (int) $questao->igreja_id) {
            abort(403, 'Acesso negado: questão pertence a outra igreja.');
        }
    }

    public function show(Request $request, Questao $questao)
    {
        $this->verificarAcessoQuestao($request, $questao);
        $questao->load(['livro', 'capitulo', 'dificuldade', 'categoria', 'submetidaPor', 'revisadaPor']);

        return view('perguntas.show', compact('questao'));
    }

    public function edit(Request $request, Questao $questao)
    {
        $this->verificarAcessoQuestao($request, $questao, true);
        $livros = LivroBiblico::orderBy('id')->get();
        $categorias = Categoria::orderBy('nome')->get();
        $dificuldades = NivelDificuldade::orderBy('nivel')->get();
        $capitulos = Capitulo::where('livro_id', $questao->livro_id)->orderBy('numero')->get();

        return view('perguntas.edit', compact('questao', 'livros', 'capitulos', 'categorias', 'dificuldades'));
    }

    public function update(Request $request, Questao $questao)
    {
        $this->verificarAcessoQuestao($request, $questao, true);
        $opcoesInput = $request->input('opcoes', []);
        if (is_string($opcoesInput)) {
            $decoded = json_decode($opcoesInput, true);
            $opcoesInput = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode("\n", $opcoesInput)));
        }
        if (is_array($opcoesInput)) {
            $opcoesInput = array_values(array_filter(array_map(fn ($v) => trim((string) $v), $opcoesInput), fn ($v) => $v !== ''));
        }

        $request->merge(['opcoes' => $opcoesInput]);

        $validator = Validator::make($request->all(), [
            'igreja_id' => 'required|exists:igrejas,id',
            'livro_id' => 'required|exists:livros_biblicos,id',
            'capitulo_id' => 'nullable|exists:capitulos,id',
            'versiculo_de' => 'nullable|integer|min:1',
            'versiculo_ate' => 'nullable|integer|min:1|gte:versiculo_de',
            'referencia_biblica' => 'nullable|string|max:120',
            'texto' => 'required|string|min:5',
            'opcoes' => 'required|array|min:2',
            'resposta_correta' => 'required|string|min:1',
            'explicacao' => 'nullable|string',
            'contexto' => 'nullable|string',
            'dificuldade_id' => 'nullable|exists:niveis_dificuldade,id',
            'categoria_id' => 'nullable|exists:categorias,id',
            'referencia_verificada' => 'boolean',
            'aprovada' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $user = $request->user();
        if ($user && $user->papel !== 'admin_geral' && $user->igreja_id) {
            $validated['igreja_id'] = $user->igreja_id;
        }
        $validated['opcoes'] = $opcoesInput;
        $validated['referencia_verificada'] = $request->boolean('referencia_verificada');
        $validated['aprovada'] = $request->boolean('aprovada');

        $questao->update($validated);

        return redirect()->route('perguntas.show', $questao->id)
            ->with('success', 'Pergunta atualizada com sucesso.');
    }

    public function destroy(Request $request, Questao $questao)
    {
        $this->verificarAcessoQuestao($request, $questao, true);
        $questao->delete();

        return redirect()->route('perguntas.index')
            ->with('success', 'Pergunta removida com sucesso.');
    }

    public function aprovar(Request $request, Questao $questao)
    {
        $this->verificarAcessoQuestao($request, $questao);
        $questao->update([
            'aprovada' => true,
            'referencia_verificada' => true,
            'revisada_por' => auth()->id() ?? 1,
            'revisada_em' => now(),
        ]);

        return back()->with('success', 'Pergunta aprovada e disponível para uso no jogo.');
    }

    public function rejeitar(Request $request, Questao $questao)
    {
        $this->verificarAcessoQuestao($request, $questao);
        $request->validate([
            'notas' => 'nullable|string',
        ]);

        $questao->update([
            'aprovada' => false,
            'revisada_por' => auth()->id() ?? 1,
            'revisada_em' => now(),
            'notas' => $request->notas,
        ]);

        return back()->with('success', 'Pergunta rejeitada.');
    }
}
