<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Editar Pergunta {{ $questao->codigo_classico ? ('#' . $questao->codigo_classico) : ('#' . $questao->id) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="sm-stage min-h-screen text-slate-100 flex flex-col justify-between">

    @include('partials.navbar', ['dark' => true])

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full relative z-10">
        <header class="mb-8">
            <a href="{{ route('perguntas.show', $questao->id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 hover:text-amber-200 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Voltar aos Detalhes da Pergunta
            </a>
            <h1 class="text-2xl sm:text-4xl font-black text-white sm-logo-title">
                Editar Pergunta {{ $questao->codigo_classico ? ('#' . $questao->codigo_classico) : ('#' . $questao->id) }}
            </h1>
            <p class="text-xs sm:text-sm text-blue-200 mt-1">Atualize alternativas, texto bíblico de apoio ou a fase desta rodada</p>
        </header>

        @if($errors->any())
            <div class="bg-rose-950/80 border border-rose-500/50 p-4 rounded-2xl mb-6">
                <h3 class="text-xs font-black uppercase tracking-wider text-rose-300 mb-2">Corrija os seguintes erros:</h3>
                <ul class="list-disc list-inside text-xs sm:text-sm text-rose-200 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $opcoesAtuais = is_string($questao->opcoes) ? (json_decode($questao->opcoes, true) ?: []) : ($questao->opcoes ?? []);
        @endphp

        <form method="POST" action="{{ route('perguntas.update', $questao->id) }}" class="sm-panel p-6 sm:p-8 space-y-6">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-2">Igreja / Congregação</label>
                    <select name="igreja_id" required class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                        @php $igrejas = \App\Models\Igreja::orderBy('nome')->get(); @endphp
                        @foreach($igrejas as $igreja)
                            <option value="{{ $igreja->id }}" {{ old('igreja_id', $questao->igreja_id) == $igreja->id ? 'selected' : '' }}>
                                {{ $igreja->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-2">Livro Bíblico</label>
                    <select name="livro_id" required id="livro_id" class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                        @foreach($livros as $livro)
                            <option value="{{ $livro->id }}" {{ old('livro_id', $questao->livro_id) == $livro->id ? 'selected' : '' }}>
                                {{ $livro->abreviacao }} - {{ $livro->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-2">Referência Bíblica (Dica)</label>
                    <input type="text" name="referencia_biblica" value="{{ old('referencia_biblica', $questao->referencia_biblica) }}"
                           class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-2">Versículo De</label>
                        <input type="number" name="versiculo_de" min="1" value="{{ old('versiculo_de', $questao->versiculo_de) }}"
                               class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-2">Versículo Até</label>
                        <input type="number" name="versiculo_ate" min="1" value="{{ old('versiculo_ate', $questao->versiculo_ate) }}"
                               class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-2">Enunciado da Pergunta</label>
                <textarea name="texto" rows="3" required
                          class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 outline-none text-sm transition font-medium">{{ old('texto', $questao->texto) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-3">4 Alternativas do Show</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach(['A', 'B', 'C', 'D'] as $i => $letra)
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-sm shrink-0 shadow">{{ $letra }}</span>
                            <input type="text" name="opcoes[]" value="{{ old('opcoes.' . $i, $opcoesAtuais[$i] ?? '') }}" required
                                   class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-2">Resposta Correta</label>
                <input type="text" name="resposta_correta" required value="{{ old('resposta_correta', $questao->resposta_correta) }}"
                       class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-emerald-400/60 rounded-xl text-emerald-200 placeholder-blue-300/40 focus:ring-2 focus:ring-emerald-400 outline-none text-sm font-bold transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-2">Explicação Contextual</label>
                    <textarea name="explicacao" rows="2"
                              class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">{{ old('explicacao', $questao->explicacao) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-2">Fase / Dificuldade</label>
                    <select name="dificuldade_id" class="w-full px-3.5 py-2.5 bg-blue-950/80 border border-blue-400/40 rounded-xl text-white focus:ring-2 focus:ring-amber-400 outline-none text-sm transition">
                        @foreach($dificuldades as $dificuldade)
                            <option value="{{ $dificuldade->id }}" {{ old('dificuldade_id', $questao->dificuldade_id) == $dificuldade->id ? 'selected' : '' }}>
                                {{ $dificuldade->nivel }}ª Fase — {{ $dificuldade->nome }} ({{ $dificuldade->pontos_base }} pts)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="referencia_verificada" value="1" {{ old('referencia_verificada', $questao->referencia_verificada) ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-amber-500 bg-blue-950 border-blue-400/40">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-200">Referência bíblica conferida</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="aprovada" value="1" {{ old('aprovada', $questao->aprovada) ? 'checked' : '' }} class="rounded text-emerald-500 focus:ring-emerald-500 bg-blue-950 border-blue-400/40">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-300">Aprovada para jogar em salas ao vivo</span>
                </label>
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t border-blue-400/20">
                <a href="{{ route('perguntas.show', $questao->id) }}" class="sm-arcade-btn text-xs py-2.5 px-5">
                    <span>Cancelar</span>
                </a>
                <button type="submit" class="sm-arcade-btn is-gold text-xs py-2.5 px-6 cursor-pointer">
                    <span>Salvar Alterações</span>
                </button>
            </div>
        </form>
    </main>

    <div class="sm-crowd w-full mt-auto"></div>

</body>
</html>