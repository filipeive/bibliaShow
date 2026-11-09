<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show - Nova Pergunta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-3xl mx-auto px-4 py-8">
        <header class="mb-8">
            <a href="{{ route('perguntas.index') }}" class="text-slate-500 hover:text-slate-700">← Voltar</a>
            <h1 class="text-2xl font-bold text-slate-900">Nova Pergunta</h1>
            <p class="text-slate-600">Crie uma questão bíblica para o banco de conteúdos</p>
        </header>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-6">
                <h3 class="text-sm font-semibold text-red-800 mb-2">Corrija os seguintes erros:</h3>
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('perguntas.store') }}" class="bg-white rounded-xl shadow-lg p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Igreja</label>
                    <select name="igreja_id" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        @php $igrejas = \App\Models\Igreja::orderBy('nome')->get(); @endphp
                        @foreach($igrejas as $igreja)
                            <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Livro Bíblico</label>
                    <select name="livro_id" required id="livro_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach($livros as $livro)
                            <option value="{{ $livro->id }}">{{ $livro->abreviacao }} - {{ $livro->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Capítulo</label>
                    <select name="capitulo_id" id="capitulo_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Selecione o capítulo</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Versículo de</label>
                        <input type="number" name="versiculo_de" min="1" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Versículo até</label>
                        <input type="number" name="versiculo_ate" min="1" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Texto da Pergunta</label>
                <textarea name="texto" rows="4" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Escreva a pergunta aqui..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Opções de Resposta (JSON)</label>
                <textarea name="opcoes" rows="3" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm" placeholder='Ex: ["Opção A", "Opção B", "Opção C"]'></textarea>
                <p class="text-xs text-slate-500 mt-1">Opções múltiplas em formato JSON (opcional).</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Resposta Correta</label>
                <input type="text" name="resposta_correta" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Digite a resposta correta">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Explicação Contextual</label>
                    <textarea name="explicacao" rows="3" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Breve explicação da resposta..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Contexto da Passagem</label>
                    <textarea name="contexto" rows="3" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Quem, onde, quando..."></textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Categoria</label>
                    <select name="categoria_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Sem categoria</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nível de Dificuldade</label>
                    <select name="dificuldade_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Sem dificuldade definida</option>
                        @foreach($dificuldades as $dificuldade)
                            <option value="{{ $dificuldade->id }}">Nível {{ $dificuldade->nivel }} - {{ $dificuldade->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="referencia_verificada" value="1" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-slate-700">Referência verificada</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('perguntas.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-6 py-3 rounded-lg font-medium">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md">Submeter Pergunta</button>
            </div>
        </form>

        <script>
            document.getElementById('livro_id').addEventListener('change', async function() {
                const livroId = this.value;
                const capituloSelect = document.getElementById('capitulo_id');
                
                if (!livroId) {
                    capituloSelect.innerHTML = '<option value="">Selecione o capítulo</option>';
                    return;
                }

                try {
                    const response = await fetch(`/api/livros/${livroId}/capitulos`);
                    const capitulos = await response.json();
                    
                    capituloSelect.innerHTML = '<option value="">Selecione o capítulo</option>';
                    capitulos.forEach(c => {
                        const option = document.createElement('option');
                        option.value = c.id;
                        option.textContent = 'Capítulo ' + c.numero;
                        capituloSelect.appendChild(option);
                    });
                } catch (e) {
                    console.error('Erro ao carregar capítulos:', e);
                }
            });
        </script>
    </div>

</body>
</html>