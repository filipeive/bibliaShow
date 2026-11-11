<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Banco de Perguntas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="sm-stage min-h-screen text-slate-100">

    @include('partials.navbar', ['dark' => true])

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <header class="flex flex-wrap justify-between items-center gap-4 mb-8">
            <div>
                <a href="/" class="inline-flex items-center gap-1.5 text-sm text-blue-300 hover:text-white font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Início
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-white sm-logo-title mt-1">Banco de Perguntas Bíblicas</h1>
                <p class="text-blue-200 text-sm">Acervo de {{ $questoes->total() }} questões com referências e dicas bíblicas</p>
            </div>
            <a href="{{ route('perguntas.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 px-5 py-2.5 rounded-xl font-extrabold text-sm shadow-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nova Pergunta
            </a>
        </header>

        @if(session('success'))
            <div class="bg-emerald-950/80 border border-emerald-500/50 p-4 rounded-xl mb-6">
                <p class="text-emerald-200 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Filtros -->
        <div class="sm-panel p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-200 mb-1.5">Pesquisar Texto / Referência</label>
                    <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Ex.: Moisés, Arca, Gênesis 1..."
                           class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-200 mb-1.5">Livro</label>
                    <select name="livro_id" class="w-full px-3 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                        <option value="">Todos os 66 livros</option>
                        @foreach($livros as $livro)
                            <option value="{{ $livro->id }}" {{ request('livro_id') == $livro->id ? 'selected' : '' }}>
                                {{ $livro->abreviacao }} - {{ $livro->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-200 mb-1.5">Fase / Dificuldade</label>
                    <select name="dificuldade_id" class="w-full px-3 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                        <option value="">Todas as Fases</option>
                        @foreach($dificuldades as $dificuldade)
                            <option value="{{ $dificuldade->id }}" {{ request('dificuldade_id') == $dificuldade->id ? 'selected' : '' }}>
                                {{ $dificuldade->nivel }}ª Fase - {{ $dificuldade->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 px-4 py-2.5 rounded-xl font-black text-sm shadow transition">
                        Filtrar
                    </button>
                    <a href="{{ route('perguntas.index') }}" class="bg-blue-950 hover:bg-blue-900 border border-blue-400/30 text-blue-200 px-3.5 py-2.5 rounded-xl font-bold text-sm transition">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Lista de perguntas -->
        <div class="sm-panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-blue-400/20 bg-blue-950/60">
                            <th class="px-5 py-3.5 text-xs font-black text-amber-300 uppercase">Cód.</th>
                            <th class="px-5 py-3.5 text-xs font-black text-amber-300 uppercase">Referência Bíblica</th>
                            <th class="px-5 py-3.5 text-xs font-black text-amber-300 uppercase">Pergunta</th>
                            <th class="px-5 py-3.5 text-xs font-black text-amber-300 uppercase">Fase</th>
                            <th class="px-5 py-3.5 text-xs font-black text-amber-300 uppercase text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-400/10">
                        @foreach($questoes as $questao)
                            <tr class="hover:bg-blue-900/30 transition">
                                <td class="px-5 py-3.5 font-mono text-xs text-blue-300">
                                    {{ $questao->codigo_classico ?? ('#' . $questao->id) }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-xs text-amber-300">
                                    {{ $questao->referencia_biblica ?: (($questao->livro->nome ?? '') . ' ' . ($questao->capitulo->numero ?? '') . ':' . ($questao->versiculo_de ?? '')) }}
                                </td>
                                <td class="px-5 py-3.5 text-sm text-white max-w-md truncate font-medium" title="{{ $questao->texto }}">
                                    {{ $questao->texto }}
                                </td>
                                <td class="px-5 py-3.5 text-xs font-bold text-blue-200">
                                    {{ $questao->dificuldade->nivel ?? 1 }}ª Fase ({{ $questao->dificuldade->pontos_base ?? 10 }} pts)
                                </td>
                                <td class="px-5 py-3.5 text-right space-x-2">
                                    <a href="{{ route('perguntas.show', $questao->id) }}" class="text-amber-300 hover:text-amber-200 text-xs font-bold">Ver</a>
                                    <a href="{{ route('perguntas.edit', $questao->id) }}" class="text-blue-300 hover:text-white text-xs font-bold">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($questoes->count() > 0)
                <div class="px-6 py-4 border-t border-blue-400/20 text-slate-100">
                    {{ $questoes->links() }}
                </div>
            @endif
        </div>
    </div>

</body>
</html>
