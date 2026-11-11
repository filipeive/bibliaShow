<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Acervo de Perguntas (Level Designer)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="sm-stage min-h-screen text-slate-100 flex flex-col justify-between">

    @include('partials.navbar', ['dark' => true])

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full relative z-10">
        <header class="flex flex-wrap justify-between items-center gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 mb-2">
                    <span class="sm-pill text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <span>Acervo Oficial de Fases</span>
                    </span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-white sm-logo-title">Banco de Perguntas Bíblicas</h1>
                <p class="text-xs sm:text-sm text-blue-200 mt-1">Acervo com {{ $questoes->total() }} questões clássicas com referências, dicas e 4 fases</p>
            </div>
            <a href="{{ route('perguntas.create') }}" class="sm-arcade-btn is-gold text-xs py-2.5 px-5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Nova Pergunta</span>
            </a>
        </header>

        @if(session('success'))
            <div class="bg-emerald-950/80 border border-emerald-500/50 p-4 rounded-2xl mb-6 text-emerald-200 text-sm font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filtros do Acervo -->
        <div class="sm-panel p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">Pesquisar Pergunta ou Referência</label>
                    <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Ex.: Moisés, Arca de Noé, Gênesis..."
                           class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">Livro Bíblico</label>
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
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">Fase / Nível</label>
                    <select name="dificuldade_id" class="w-full px-3 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                        <option value="">Todas as Fases</option>
                        @foreach($dificuldades as $dificuldade)
                            <option value="{{ $dificuldade->id }}" {{ request('dificuldade_id') == $dificuldade->id ? 'selected' : '' }}>
                                {{ $dificuldade->nivel }}ª Fase ({{ $dificuldade->pontos_base }} pts)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="sm-arcade-btn is-gold flex-1 text-xs py-2.5 cursor-pointer">
                        <span>Filtrar</span>
                    </button>
                    <a href="{{ route('perguntas.index') }}" class="sm-arcade-btn text-xs py-2.5 px-3">
                        <span>Limpar</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabela Arcade de Questões -->
        <div class="sm-panel overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-blue-400/20 bg-blue-950/80">
                            <th class="px-5 py-3.5 text-xs font-black text-amber-300 uppercase">Cód.</th>
                            <th class="px-5 py-3.5 text-xs font-black text-amber-300 uppercase">Referência</th>
                            <th class="px-5 py-3.5 text-xs font-black text-amber-300 uppercase">Enunciado da Pergunta</th>
                            <th class="px-5 py-3.5 text-xs font-black text-amber-300 uppercase">Fase</th>
                            <th class="px-5 py-3.5 text-xs font-black text-amber-300 uppercase text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-400/10">
                        @foreach($questoes as $questao)
                            <tr class="hover:bg-blue-900/30 transition">
                                <td class="px-5 py-3.5 font-mono text-xs text-amber-300/80 font-bold">
                                    {{ $questao->codigo_classico ?? ('#' . $questao->id) }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-xs text-amber-300 whitespace-nowrap">
                                    {{ $questao->referencia_biblica ?: (($questao->livro->nome ?? '') . ' ' . ($questao->capitulo->numero ?? '') . ':' . ($questao->versiculo_de ?? '')) }}
                                </td>
                                <td class="px-5 py-3.5 text-xs sm:text-sm text-white max-w-md truncate font-medium" title="{{ $questao->texto }}">
                                    {{ $questao->texto }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="sm-badge-stage stage-{{ $questao->dificuldade->nivel ?? 1 }}">
                                        {{ $questao->dificuldade->nivel ?? 1 }}ª FASE • {{ $questao->dificuldade->pontos_base ?? 10 }} PTS
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('perguntas.show', $questao->id) }}" class="sm-arcade-btn text-[11px] py-1 px-2.5 inline-flex">
                                        <span>Ver</span>
                                    </a>
                                    <a href="{{ route('perguntas.edit', $questao->id) }}" class="sm-arcade-btn is-gold text-[11px] py-1 px-2.5 inline-flex">
                                        <span>Editar</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($questoes->count() > 0)
                <div class="px-6 py-4 border-t border-blue-400/20 text-slate-100 bg-blue-950/40">
                    {{ $questoes->links() }}
                </div>
            @endif
        </div>
    </main>

    <div class="sm-crowd w-full mt-auto"></div>

</body>
</html>