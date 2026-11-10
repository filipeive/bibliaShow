<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Banco de Perguntas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900">

    @include('partials.navbar', ['dark' => true])

    <div class="max-w-6xl mx-auto px-4 py-8">
        <header class="flex flex-wrap justify-between items-center gap-4 mb-8">
            <div>
                <a href="/" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Início
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Banco de Perguntas Bíblicas</h1>
                <p class="text-slate-600 text-sm">Acervo de {{ $questoes->total() }} questões com referências e dicas bíblicas</p>
            </div>
            <a href="{{ route('perguntas.create') }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-slate-950 px-5 py-2.5 rounded-xl font-extrabold text-sm shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nova Pergunta
            </a>
        </header>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl mb-6">
                <p class="text-emerald-800 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Pesquisar Texto / Referência</label>
                    <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Ex.: Moisés, Arca, Gênesis 1..."
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Livro</label>
                    <select name="livro_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                        <option value="">Todos os 66 livros</option>
                        @foreach($livros as $livro)
                            <option value="{{ $livro->id }}" {{ request('livro_id') == $livro->id ? 'selected' : '' }}>
                                {{ $livro->abreviacao }} - {{ $livro->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Fase / Dificuldade</label>
                    <select name="dificuldade_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                        <option value="">Todas as Fases</option>
                        @foreach($dificuldades as $dificuldade)
                            <option value="{{ $dificuldade->id }}" {{ request('dificuldade_id') == $dificuldade->id ? 'selected' : '' }}>
                                {{ $dificuldade->nivel }}ª Fase - {{ $dificuldade->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold text-sm">
                        Filtrar
                    </button>
                    <a href="{{ route('perguntas.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-3.5 py-2 rounded-xl font-semibold text-sm">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Lista de perguntas -->
        <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase">Cód.</th>
                            <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase">Referência Bíblica</th>
                            <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase">Pergunta</th>
                            <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase">Fase</th>
                            <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($questoes as $questao)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-500">
                                    {{ $questao->codigo_classico ?? ('#' . $questao->id) }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-xs text-amber-800">
                                    {{ $questao->referencia_biblica ?: (($questao->livro->nome ?? '') . ' ' . ($questao->capitulo->numero ?? '') . ':' . ($questao->versiculo_de ?? '')) }}
                                </td>
                                <td class="px-5 py-3.5 text-sm text-slate-800 max-w-md truncate font-medium" title="{{ $questao->texto }}">
                                    {{ $questao->texto }}
                                </td>
                                <td class="px-5 py-3.5 text-xs font-bold text-slate-600">
                                    {{ $questao->dificuldade->nivel ?? 1 }}ª Fase ({{ $questao->dificuldade->pontos_base ?? 10 }} pts)
                                </td>
                                <td class="px-5 py-3.5 text-right space-x-2">
                                    <a href="{{ route('perguntas.show', $questao->id) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold">Ver</a>
                                    <a href="{{ route('perguntas.edit', $questao->id) }}" class="text-slate-600 hover:text-slate-900 text-xs font-bold">Editar</a>
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
