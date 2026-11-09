<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show - Gestão de Perguntas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-6xl mx-auto px-4 py-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <a href="/" class="text-slate-500 hover:text-slate-700">← Início</a>
                <h1 class="text-2xl font-bold text-slate-900">Gestão de Perguntas</h1>
                <p class="text-slate-600">Banco de questões bíblicas</p>
            </div>
            <a href="{{ route('perguntas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md">
                Nova Pergunta
            </a>
        </header>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-6">
                <p class="text-green-800 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Livro</label>
                    <select name="livro_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Todos</option>
                        @foreach($livros as $livro)
                            <option value="{{ $livro->id }}" {{ old('livro_id', request('livro_id')) == $livro->id ? 'selected' : '' }}>
                                {{ $livro->abreviacao }} - {{ $livro->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Categoria</label>
                    <select name="categoria_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Todas</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id', request('categoria_id')) == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Dificuldade</label>
                    <select name="dificuldade_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Todas</option>
                        @foreach($dificuldades as $dificuldade)
                            <option value="{{ $dificuldade->id }}" {{ old('dificuldade_id', request('dificuldade_id')) == $dificuldade->id ? 'selected' : '' }}>
                                Nível {{ $dificuldade->nivel }} - {{ $dificuldade->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                    <select name="aprovada" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Todos</option>
                        <option value="1" {{ old('aprovada', request('aprovada')) == '1' ? 'selected' : '' }}>Aprovadas</option>
                        <option value="0" {{ old('aprovada', request('aprovada')) == '0' ? 'selected' : '' }}>Pendentes</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg font-medium">
                        Filtrar
                    </button>
                    <a href="{{ route('perguntas.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg font-medium">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Lista de perguntas -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">{{ $questoes->total() }} Pergunta{{ $questoes->total() != 1 ? 's' : '' }}</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Ref.</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Pergunta</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Categoria</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Dificuldade</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($questoes as $questao)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-mono text-sm text-slate-700">
                                    {{ $questao->livro->abreviacao }} {{ $questao->capitulo->numero ?? '?' }}:{{ $questao->versiculo_de ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-800 max-w-xs truncate" title="{{ $questao->texto }}">
                                    {{ $questao->texto }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $questao->categoria->nome ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    Nível {{ $questao->dificuldade->nivel ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $questao->aprovada ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $questao->aprovada ? 'Aprovada' : 'Pendente' }}
                                    </span>
                                    @if(!$questao->referencia_verificada)
                                        <span class="ml-2 inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Ref. não verificada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('perguntas.show', $questao->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver</a>
                                    @if(!$questao->aprovada)
                                        <form action="{{ route('perguntas.aprovar', $questao->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium ml-2" onclick="return confirm('Aprovar esta pergunta?')">Aprovar</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('perguntas.edit', $questao->id) }}" class="text-slate-600 hover:text-slate-900 text-sm font-medium ml-2">Editar</a>
                                    <form action="{{ route('perguntas.destroy', $questao->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium ml-2" onclick="return confirm('Remover esta pergunta?')">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($questoes->count() > 0)
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $questoes->links() }}
                </div>
            @endif
        </div>
    </div>

</body>
</html>