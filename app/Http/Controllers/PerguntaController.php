<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use App\Models\LivroBiblico;
use App\Models\Capitulo;
use App\Models\Categoria;
use App\Models\NivelDificuldade;
use App\Models\Igreja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PerguntaController extends Controller
{
    public function index(Request $request)
    {
        $query = Questao::with(['livro', 'capitulo', 'dificuldade', 'categoria']);

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

        $questoes = $query->latest()->paginate(15);

        $livros = LivroBiblico::orderBy('nome')->get();
        $categorias = Categoria::orderBy('nome')->get();
        $dificuldades = NivelDificuldade::orderBy('nivel')->get();

        return view('perguntas.index', compact('questoes', 'livros', 'categorias', 'dificuldades'));
    }

    public function create()
    {
        $livros = LivroBiblico::orderBy('nome')->get();
        $categorias = Categoria::orderBy('nome')->get();
        $dificuldades = NivelDificuldade::orderBy('nivel')->get();

        return view('perguntas.create', compact('livros', 'categorias', 'dificuldades'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'igreja_id' => 'required|exists:igrejas,id',
            'livro_id' => 'required|exists:livros_biblicos,id',
            'capitulo_id' => 'nullable|exists:capitulos,id',
            'versiculo_de' => 'nullable|integer|min:1',
            'versiculo_ate' => 'nullable|integer|min:1|gte:versiculo_de',
            'texto' => 'required|string|min:5',
            'opcoes' => 'nullable|array',
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

        $validated['aprovada'] = false;
        $validated['submetida_por'] = auth()->id() ?? 1;
        $validated['opcoes'] = $request->input('opcoes', []);

        Questao::create($validated);

        return redirect()->route('perguntas.index')
            ->with('success', 'Pergunta submetida com sucesso e aguarda revisão editorial.');
    }

    public function show(Questao $questao)
    {
        $questao->load(['livro', 'capitulo', 'dificuldade', 'categoria', 'submetidaPor', 'revisadaPor']);
        return view('perguntas.show', compact('questao'));
    }

    public function edit(Questao $questao)
    {
        $livros = LivroBiblico::orderBy('nome')->get();
        $categorias = Categoria::orderBy('nome')->get();
        $dificuldades = NivelDificuldade::orderBy('nivel')->get();
        $capitulos = Capitulo::where('livro_id', $questao->livro_id)->orderBy('numero')->get();

        return view('perguntas.edit', compact('questao', 'livros', 'capitulos', 'categorias', 'dificuldades'));
    }

    public function update(Request $request, Questao $questao)
    {
        $validator = Validator::make($request->all(), [
            'igreja_id' => 'required|exists:igrejas,id',
            'livro_id' => 'required|exists:livros_biblicos,id',
            'capitulo_id' => 'nullable|exists:capitulos,id',
            'versiculo_de' => 'nullable|integer|min:1',
            'versiculo_ate' => 'nullable|integer|min:1|gte:versiculo_de',
            'texto' => 'required|string|min:5',
            'opcoes' => 'nullable|array',
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
        $validated['opcoes'] = $request->input('opcoes', []);

        $questao->update($validated);

        return redirect()->route('perguntas.index')
            ->with('success', 'Pergunta atualizada com sucesso.');
    }

    public function destroy(Questao $questao)
    {
        $questao->delete();
        return redirect()->route('perguntas.index')
            ->with('success', 'Pergunta removida com sucesso.');
    }

    public function aprovar(Request $request, Questao $questao)
    {
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
