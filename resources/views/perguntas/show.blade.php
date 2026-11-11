<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Pergunta {{ $questao->codigo_classico ? ('#' . $questao->codigo_classico) : ('#' . $questao->id) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="sm-stage min-h-screen text-slate-100 flex flex-col justify-between">

    @include('partials.navbar', ['dark' => true])

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full relative z-10">
        <header class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('perguntas.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 hover:text-amber-200 transition mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Voltar ao Banco de Perguntas
                </a>
                <h1 class="text-2xl sm:text-4xl font-black text-white sm-logo-title">
                    Pergunta {{ $questao->codigo_classico ? ('#' . $questao->codigo_classico) : ('#' . $questao->id) }}
                </h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('perguntas.edit', $questao->id) }}" class="sm-arcade-btn is-gold text-xs py-2 px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                    </svg>
                    <span>Editar Questão</span>
                </a>
            </div>
        </header>

        @if(session('success'))
            <div class="bg-emerald-950/80 border border-emerald-500/50 p-4 rounded-2xl mb-6 text-emerald-200 text-sm font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="sm-panel p-6 sm:p-8 space-y-6">
            <!-- Metadados da Pergunta -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-blue-950/70 border border-blue-400/30">
                    <p class="text-[10px] font-black text-amber-300 uppercase tracking-wider">Livro Bíblico</p>
                    <p class="text-base font-extrabold text-white mt-1">{{ $questao->livro->abreviacao ?? '' }} - {{ $questao->livro->nome ?? '-' }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-blue-950/70 border border-blue-400/30">
                    <p class="text-[10px] font-black text-amber-300 uppercase tracking-wider">Dica / Referência Bíblica</p>
                    <p class="text-base font-extrabold text-amber-300 mt-1">
                        {{ $questao->referencia_biblica ?: (($questao->livro->abreviacao ?? '') . ' ' . ($questao->capitulo->numero ?? '?') . ':' . ($questao->versiculo_de ?? '-')) }}
                    </p>
                </div>
                <div class="p-4 rounded-2xl bg-blue-950/70 border border-blue-400/30">
                    <p class="text-[10px] font-black text-amber-300 uppercase tracking-wider">Fase & Pontuação</p>
                    <div class="mt-1">
                        <span class="sm-badge-stage stage-{{ $questao->dificuldade->nivel ?? 1 }}">
                            {{ $questao->dificuldade->nivel ?? 1 }}ª FASE • {{ $questao->dificuldade->pontos_base ?? 10 }} PTS
                        </span>
                    </div>
                </div>
            </div>

            <!-- Caixa de Pergunta do Show -->
            <div>
                <p class="text-[11px] font-black uppercase tracking-wider text-amber-300 mb-2">Enunciado no Telão</p>
                <div class="p-5 sm:p-7 rounded-2xl sm-question-box">
                    <p class="text-lg sm:text-2xl font-black text-white text-center leading-relaxed">
                        {{ $questao->texto }}
                    </p>
                </div>
            </div>

            <!-- Alternativas A, B, C, D -->
            @php
                $opcoes = is_string($questao->opcoes) ? json_decode($questao->opcoes, true) : ($questao->opcoes ?? []);
                $letras = ['A', 'B', 'C', 'D'];
            @endphp

            @if(is_array($opcoes) && count($opcoes) > 0)
                <div>
                    <p class="text-[11px] font-black uppercase tracking-wider text-amber-300 mb-3">Alternativas do Show</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($opcoes as $idx => $opcao)
                            @php
                                $ehCorreta = mb_strtolower(trim((string) $opcao), 'UTF-8') === mb_strtolower(trim((string) $questao->resposta_correta), 'UTF-8');
                            @endphp
                            <div class="p-4 rounded-2xl border-2 flex items-center gap-3 transition {{ $ehCorreta ? 'bg-emerald-950/90 border-emerald-400 text-white shadow-lg shadow-emerald-500/20' : 'bg-blue-950/70 border-blue-400/30 text-blue-100' }}">
                                <span class="w-8 h-8 rounded-xl font-black flex items-center justify-center text-sm shrink-0 {{ $ehCorreta ? 'bg-emerald-500 text-slate-950' : 'bg-amber-500/20 text-amber-300 border border-amber-400/40' }}">
                                    {{ $letras[$idx] ?? ($idx + 1) }}
                                </span>
                                <span class="font-bold text-sm flex-1">{{ $opcao }}</span>
                                @if($ehCorreta)
                                    <span class="text-[10px] font-black bg-emerald-500 text-slate-950 px-2.5 py-0.5 rounded-full uppercase tracking-wider inline-flex items-center gap-1">
                                        <svg class="w-3 h-3 stroke-current" fill="none" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        <span>Correta</span>
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Convidados Bíblicos / Dicas Especiais -->
            @php
                $ajudas = is_string($questao->ajudas) ? json_decode($questao->ajudas, true) : ($questao->ajudas ?? []);
            @endphp
            @if(!empty($ajudas['convidados']))
                <div class="p-4 rounded-2xl bg-blue-950/50 border border-blue-400/20">
                    <p class="text-[10px] font-black uppercase tracking-wider text-amber-300 mb-2">Convidados Bíblicos (Ajuda Clássica)</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ajudas['convidados'] as $i => $conv)
                            <span class="px-3 py-1 rounded-xl bg-blue-900/60 border border-blue-400/30 text-blue-200 text-xs font-semibold inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                <span>{{ $conv['nome'] ?? 'Convidado ' . ($i+1) }}: <strong class="text-amber-300">{{ $conv['palpite'] ?? '-' }}</strong></span>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!empty($questao->explicacao))
                <div class="p-4 rounded-2xl bg-blue-950/50 border border-blue-400/20">
                    <p class="text-[10px] font-black uppercase tracking-wider text-amber-300 mb-1">Explicação Contextual</p>
                    <p class="text-xs sm:text-sm text-blue-200 leading-relaxed">{{ $questao->explicacao }}</p>
                </div>
            @endif
        </div>
    </main>

    <div class="sm-crowd w-full mt-auto"></div>

</body>
</html>