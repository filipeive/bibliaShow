<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Nova Pergunta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900">

    <div class="max-w-3xl mx-auto px-4 py-8">
        <header class="mb-8">
            <a href="{{ route('perguntas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Voltar ao Banco de Perguntas
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Nova Pergunta</h1>
            <p class="text-slate-600 text-sm">Cadastre uma questão bíblica com 4 alternativas e referência</p>
        </header>

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl mb-6">
                <h3 class="text-sm font-bold text-rose-800 mb-2">Corrija os seguintes campos:</h3>
                <ul class="list-disc list-inside text-sm text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('perguntas.store') }}" class="bg-white rounded-2xl shadow-md border border-slate-200 p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Igreja</label>
                    <select name="igreja_id" required class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                        @php $igrejas = \App\Models\Igreja::orderBy('nome')->get(); @endphp
                        @foreach($igrejas as $igreja)
                            <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Livro Bíblico</label>
                    <select name="livro_id" required id="livro_id" class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                        @foreach($livros as $livro)
                            <option value="{{ $livro->id }}">{{ $livro->abreviacao }} - {{ $livro->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Referência Bíblica (Dica)</label>
                    <input type="text" name="referencia_biblica" value="{{ old('referencia_biblica') }}" placeholder="Ex.: Gênesis 6:14"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Versículo de</label>
                        <input type="number" name="versiculo_de" min="1" value="{{ old('versiculo_de') }}" class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Versículo até</label>
                        <input type="number" name="versiculo_ate" min="1" value="{{ old('versiculo_ate') }}" class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Enunciado da Pergunta</label>
                <textarea name="texto" rows="3" required class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm" placeholder="Escreva a pergunta aqui...">{{ old('texto') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">4 Alternativas de Resposta</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach(['A', 'B', 'C', 'D'] as $i => $letra)
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-amber-500 text-slate-950 font-extrabold flex items-center justify-center text-sm shrink-0">{{ $letra }}</span>
                            <input type="text" name="opcoes[]" value="{{ old('opcoes.' . $i) }}" required placeholder="Alternativa {{ $letra }}"
                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Resposta Correta (deve coincidir com uma das alternativas)</label>
                <input type="text" name="resposta_correta" required value="{{ old('resposta_correta') }}" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm" placeholder="Digite exatamente o texto da alternativa correta">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Explicação Contextual</label>
                    <textarea name="explicacao" rows="2" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm" placeholder="Breve explicação da resposta...">{{ old('explicacao') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Fase / Nível de Dificuldade</label>
                    <select name="dificuldade_id" class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                        @foreach($dificuldades as $dificuldade)
                            <option value="{{ $dificuldade->id }}">{{ $dificuldade->nivel }}ª Fase - {{ $dificuldade->nome }} ({{ $dificuldade->pontos_base }} pts)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('perguntas.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-6 py-2.5 rounded-xl font-semibold text-sm">Cancelar</a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-6 py-2.5 rounded-xl font-extrabold text-sm shadow">Salvar Pergunta</button>
            </div>
        </form>
    </div>

</body>
</html>
