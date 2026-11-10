<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Jogar</title>
    <link rel="icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg {
            background: radial-gradient(circle at top, #1e293b 0%, #090d16 75%);
        }
        .milhao-card {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.98) 100%);
        }
        .milhao-option {
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 100%);
        }
        .milhao-option:hover {
            background: linear-gradient(90deg, rgba(180, 83, 9, 0.25) 0%, rgba(30, 41, 59, 0.95) 100%);
        }
    </style>
</head>
<body class="milhao-bg text-slate-100 min-h-screen">

    <!-- Top Navigation Bar -->
    <nav class="border-b border-slate-800/80 bg-slate-950/80 backdrop-blur sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 h-16 flex justify-between items-center">
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/20 text-slate-950 font-black text-base">
                    B
                </div>
                <div>
                    <span class="font-extrabold text-lg text-white tracking-tight">Bíblia Show</span>
                    <span class="hidden sm:inline-flex items-center gap-1 ml-2 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30">
                         Fase {{ $fase_atual ?? 1 }} de 4
                    </span>
                </div>
            </a>

            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Controle de Áudio Original -->
                <button type="button" id="btnToggleSom" onclick="alternarSom()"
                        class="inline-flex items-center gap-2 text-xs sm:text-sm bg-slate-800/90 hover:bg-slate-700 text-amber-300 px-3.5 py-2 rounded-xl font-semibold border border-slate-700 transition"
                        title="Ativar/Desativar sons originais do Bíblia Show">
                    <svg id="iconSomOn" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5L6 9H2v6h4l5 4V5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.54 8.46a5 5 0 010 7.07M19.07 4.93a10 10 0 010 14.14"/>
                    </svg>
                    <svg id="iconSomOff" class="w-4 h-4 hidden text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5L6 9H2v6h4l5 4V5zM23 9l-6 6M17 9l6 6"/>
                    </svg>
                    <span id="labelSom" class="hidden sm:inline">Sons Ativos</span>
                </button>

                <a href="{{ route('curiosidades') }}"
                   class="hidden md:inline-flex items-center gap-1.5 text-xs sm:text-sm bg-slate-800/90 hover:bg-slate-700 text-slate-200 px-3.5 py-2 rounded-xl font-medium border border-slate-700 transition">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Enciclopédia & Regras</span>
                </a>

                <a href="{{ route('game.play', ['novo' => 1]) }}"
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm bg-slate-800/90 hover:bg-slate-700 text-slate-200 px-3.5 py-2 rounded-xl font-medium border border-slate-700 transition">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reiniciar</span>
                </a>

                <form action="{{ route('game.finish') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 text-xs sm:text-sm bg-rose-500/15 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 px-3.5 py-2 rounded-xl font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="6" y="6" width="12" height="12" rx="2"/>
                        </svg>
                        <span>Parar</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-6 sm:py-8">

        @php
            $resultado = session('resultado');
            $ajudaFeedback = session('ajuda_feedback');
            $opcoesEliminadas = $ajudas['opcoes_eliminadas'] ?? [];
            $dicaConvidado = $ajudas['dica_convidado'] ?? null;
            $dicaBiblica = $ajudas['dica_biblica'] ?? null;
            $pulosRestantes = $ajudas['pulos_restantes'] ?? 3;
            $dicasRestantes = $ajudas['dicas_restantes'] ?? 3;
            $convidadosRestantes = $ajudas['convidados_restantes'] ?? 3;
            $convidadosUsada = $ajudas['convidados_usada'] ?? false;
            $cartasUsada = $ajudas['cartas_usada'] ?? false;

            $ajudasPergunta = $pergunta['ajudas'] ?? [];
            if (is_string($ajudasPergunta)) {
                $ajudasPergunta = json_decode($ajudasPergunta, true) ?: [];
            }
            $convidadosBiblicos = $ajudasPergunta['convidados'] ?? ['Paulo', 'Pedro', 'Moisés'];

            // Determinar som automático de transição/feedback
            $somAutomatico = null;
            if (!empty($ajudaFeedback['som_efeito'])) {
                $somAutomatico = $ajudaFeedback['som_efeito'];
            } elseif (!empty($resultado['som_efeito'])) {
                $somAutomatico = $resultado['som_efeito'];
            } elseif (!empty($iniciou_agora)) {
                $somAutomatico = 'fase1';
            }
            $novaFaseSom = $resultado['nova_fase'] ?? null;
        @endphp

        <!-- Feedback da Pergunta Anterior -->
        @if($resultado)
            @php
                $acertou = $resultado['acertou'] ?? false;
                $pontosGanhos = $resultado['pontos'] ?? 0;
                $respostaCorreta = $resultado['resposta_correta'] ?? '';
            @endphp
            <div class="mb-6 rounded-2xl p-4 sm:p-5 border {{ $acertou ? 'bg-emerald-950/70 border-emerald-500/40 text-emerald-100' : 'bg-rose-950/70 border-rose-500/40 text-rose-100' }} shadow-xl">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $acertou ? 'bg-emerald-500 text-slate-950' : 'bg-rose-500 text-white' }}">
                        @if($acertou)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-extrabold text-base sm:text-lg">
                                {{ $acertou ? 'Certa Resposta!' : 'Resposta Incorreta!' }}
                            </p>
                            @if($acertou)
                                <span class="text-xs font-extrabold uppercase tracking-wider px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    +{{ $pontosGanhos }} pontos
                                </span>
                            @endif
                        </div>
                        @if(!$acertou)
                            <p class="text-sm mt-1 text-rose-200">
                                A alternativa correta era: <strong class="text-white underline">{{ $respostaCorreta }}</strong>
                            </p>
                        @endif
                        @if(!empty($resultado['explicacao']) || !empty($resultado['referencia_biblica']))
                            <div class="mt-2.5 pt-2.5 border-t border-white/10 text-xs sm:text-sm text-slate-200 flex flex-wrap gap-3 items-center">
                                @if(!empty($resultado['referencia_biblica']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-amber-500/20 text-amber-300 font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <span>{{ $resultado['referencia_biblica'] }}</span>
                                    </span>
                                @endif
                                @if(!empty($resultado['explicacao']))
                                    <span>{{ $resultado['explicacao'] }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Feedback de Recurso de Ajuda -->
        @if($ajudaFeedback && !empty($ajudaFeedback['mensagem']))
            <div class="mb-5 rounded-xl p-3.5 bg-amber-500/15 border border-amber-500/40 text-amber-200 text-sm flex items-center gap-2.5">
                <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <span>{{ $ajudaFeedback['mensagem'] }}</span>
            </div>
        @endif

        @if($estado === 'em_curso' && $pergunta)
            <!-- Painel Superior Estilo Show do Milhão: ERRAR | PARAR | ACERTAR + Temporizador -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
                <!-- Caixa 1: ERRAR -->
                <div class="bg-rose-950/40 border border-rose-500/30 rounded-2xl p-3.5 text-center">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-rose-400">Errar</p>
                    <p class="text-lg sm:text-xl font-black text-rose-200 mt-0.5">
                        {{ $placar_milhao['errar'] ?? 0 }} <span class="text-xs font-normal text-rose-400">pts</span>
                    </p>
                </div>

                <!-- Caixa 2: PARAR -->
                <div class="bg-amber-500/15 border border-amber-500/40 rounded-2xl p-3.5 text-center">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-amber-300">Parar (Atual)</p>
                    <p class="text-lg sm:text-xl font-black text-amber-400 mt-0.5">
                        {{ $pontuacao }} <span class="text-xs font-normal text-amber-300/70">pts</span>
                    </p>
                </div>

                <!-- Caixa 3: ACERTAR -->
                <div class="bg-emerald-950/40 border border-emerald-500/40 rounded-2xl p-3.5 text-center">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400">Acertar</p>
                    <p class="text-lg sm:text-xl font-black text-emerald-300 mt-0.5">
                        {{ ($pontuacao + 100) }} <span class="text-xs font-normal text-emerald-400/80">pts</span>
                    </p>
                </div>

                <!-- Caixa 4: FASE E PERGUNTA -->
                <div class="bg-slate-900/90 border border-slate-700/80 rounded-2xl p-3.5 flex flex-col justify-center">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Fase {{ $fase_atual ?? 1 }}</span>
                        <span class="text-xs font-bold text-amber-400">Nv. {{ $pergunta['dificuldade_nivel'] ?? 1 }}</span>
                    </div>
                    <p class="text-base sm:text-lg font-extrabold text-white mt-0.5">
                        Pergunta {{ $numero_pergunta }} <span class="text-slate-500 text-xs font-normal">/ {{ $total_perguntas }}</span>
                    </p>
                </div>

                <!-- Caixa 5: CRONÔMETRO 60s -->
                <div class="col-span-2 lg:col-span-1 bg-slate-900/90 border border-slate-700/80 rounded-2xl p-3.5 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Tempo</p>
                        <p class="text-xl font-black font-mono text-white mt-0.5"><span id="timer">{{ $tempo_restante }}</span>s</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="13" r="8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4l2.5 2.5M9 2h6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card Principal da Pergunta -->
            <div class="milhao-card border-2 border-amber-500/30 rounded-3xl shadow-2xl p-6 sm:p-8 mb-6">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                    <div class="flex flex-wrap items-center gap-2">
                        @if(!empty($pergunta['livro_nome']))
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                {{ $pergunta['livro_nome'] }}
                            </span>
                        @endif
                        @if(!empty($pergunta['categoria_nome']))
                            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-blue-500/15 text-blue-300 border border-blue-500/30">
                                {{ $pergunta['categoria_nome'] }}
                            </span>
                        @endif
                        @if(!empty($pergunta['codigo_classico']))
                            <span class="text-xs font-mono px-2.5 py-1 rounded-full bg-slate-800 text-slate-400 border border-slate-700">
                                #{{ $pergunta['codigo_classico'] }}
                            </span>
                        @endif
                    </div>

                    @if($sequencia_acertos > 0)
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            {{ $sequencia_acertos }} seguida(s)
                        </span>
                    @endif
                </div>

                <!-- Caixa de Enunciado Estilo Show do Milhão -->
                <div class="bg-gradient-to-r from-red-950/80 via-slate-900 to-red-950/80 border-2 border-amber-400/50 rounded-2xl p-5 sm:p-7 mb-6 shadow-inner">
                    <p class="text-lg sm:text-2xl font-extrabold text-white leading-relaxed tracking-tight">
                        {{ $pergunta['texto'] ?? $pergunta->texto }}
                    </p>
                </div>

                <!-- Dica Bíblica (Referência) quando acionada -->
                @if($dicaBiblica)
                    <div class="mb-5 rounded-2xl p-4 bg-blue-950/60 border border-blue-400/40 text-blue-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500 text-slate-950 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-wider text-blue-300">Dica de Referência Bíblica</p>
                            <p class="text-sm sm:text-base font-bold text-white">{{ $dicaBiblica }}</p>
                        </div>
                    </div>
                @endif

                <!-- Resposta do Convidado Bíblico quando acionada -->
                @if($dicaConvidado)
                    <div class="mb-6 rounded-2xl p-4 border {{ !empty($dicaConvidado['sabe_resposta']) ? 'bg-amber-500/15 border-amber-400/50 text-amber-100' : 'bg-slate-800/90 border-slate-600 text-slate-200' }}">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 font-black flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wider font-extrabold text-amber-400">
                                    Personagem Bíblico Consultado: {{ $dicaConvidado['convidado'] }}
                                </p>
                                <p class="text-sm sm:text-base mt-1 font-semibold">
                                    {{ $dicaConvidado['mensagem'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Alternativas A, B, C, D -->
                <form action="{{ route('game.answer') }}" method="POST" id="respostaForm">
                    @csrf
                    <input type="hidden" name="tempo_resposta" id="tempo_resposta" value="0">
                    <input type="hidden" name="resposta" id="respostaSelecionada" value="">

                    @php
                        $opcoes = $pergunta['opcoes'] ?? [];
                        $opcoesArray = is_string($opcoes) ? json_decode($opcoes, true) : $opcoes;
                        $letras = ['A', 'B', 'C', 'D'];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        @foreach($opcoesArray as $indice => $opcao)
                            @php
                                $eliminada = in_array((int) $indice, $opcoesEliminadas, true);
                                $letra = $letras[$indice] ?? ($indice + 1);
                            @endphp
                            @if($eliminada)
                                <div class="border-2 border-slate-800 bg-slate-950/50 rounded-2xl p-4 flex items-center gap-4 opacity-35 select-none cursor-not-allowed">
                                    <span class="w-10 h-10 rounded-xl bg-slate-800 text-slate-500 font-black flex items-center justify-center shrink-0">
                                        {{ $letra }}
                                    </span>
                                    <span class="text-slate-500 line-through font-medium">{{ $opcao }}</span>
                                </div>
                            @else
                                <button type="button"
                                        onmouseenter="tocarHover()"
                                        onclick="pedirConfirmacao('{{ $indice }}', '{{ $letra }}', @js($opcao))"
                                        class="milhao-option text-left w-full border-2 border-slate-700 hover:border-amber-400 rounded-2xl p-4 transition flex items-center gap-4 group shadow-md">
                                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-base flex items-center justify-center shrink-0 shadow group-hover:scale-105 transition">
                                        {{ $letra }}
                                    </span>
                                    <span class="text-slate-100 group-hover:text-white font-bold text-base sm:text-lg leading-snug">
                                        {{ $opcao }}
                                    </span>
                                </button>
                            @endif
                        @endforeach
                    </div>
                </form>
            </div>

            <!-- Painel de Recursos de Ajuda (Dicas, Convidados Bíblicos, Cartas, Pular) -->
            <div class="milhao-card border border-slate-700/90 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xs font-extrabold uppercase tracking-widest text-amber-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>Ajudas e Dicas do Clássico Bíblia Show</span>
                    </h2>
                    <span class="text-xs text-slate-400">3 Dicas • 3 Convidados • Cartas • 3 Pulos</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <!-- 1. Dica Bíblica (Referência) -->
                    <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-blue-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    Dica Bíblica
                                </span>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $dicasRestantes <= 0 ? 'bg-slate-800 text-slate-500' : 'bg-blue-500/20 text-blue-300' }}">
                                    {{ $dicasRestantes }}/3
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mb-3">Mostra o livro e capítulo bíblico onde está a resposta.</p>
                        </div>
                        <form action="{{ route('game.lifeline') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipo" value="dica">
                            <button type="submit"
                                    onmouseenter="tocarHover()"
                                    @if($dicasRestantes <= 0 || $dicaBiblica) disabled @endif
                                    class="w-full py-2.5 rounded-xl text-xs font-extrabold transition {{ ($dicasRestantes <= 0 || $dicaBiblica) ? 'bg-slate-800 text-slate-600 cursor-not-allowed' : 'bg-blue-500/20 hover:bg-blue-500 text-blue-300 hover:text-white border border-blue-500/30' }}">
                                {{ $dicaBiblica ? 'Dica Ativa' : ($dicasRestantes <= 0 ? 'Esgotadas' : 'Ler Dica Bíblica') }}
                            </button>
                        </form>
                    </div>

                    <!-- 2. Personagens Bíblicos (3 Convidados) -->
                    <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-amber-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Convidados
                                </span>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $convidadosRestantes <= 0 ? 'bg-slate-800 text-slate-500' : 'bg-amber-500/20 text-amber-300' }}">
                                    {{ $convidadosRestantes }}/3
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mb-2">Consulte quem viveu na época do relato:</p>
                        </div>
                        @if(!$convidadosUsada)
                            <div class="grid grid-cols-3 gap-1.5">
                                @foreach($convidadosBiblicos as $idx => $nomeConvidado)
                                    <form action="{{ route('game.lifeline') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tipo" value="convidado">
                                        <input type="hidden" name="convidado_indice" value="{{ $idx }}">
                                        <button type="submit"
                                                onmouseenter="tocarHover()"
                                                class="w-full px-1.5 py-2 rounded-xl bg-amber-500/15 hover:bg-amber-500 hover:text-slate-950 text-amber-300 border border-amber-500/30 text-[11px] font-extrabold truncate transition"
                                                title="Consultar {{ $nomeConvidado }}">
                                            {{ $nomeConvidado }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        @else
                            <div class="py-2 rounded-xl bg-slate-800 text-slate-500 text-center text-xs font-bold">
                                {{ $dicaConvidado ? 'Consultado nesta pergunta' : 'Ajudas esgotadas' }}
                            </div>
                        @endif
                    </div>

                    <!-- 3. Eliminar 2 Alternativas (Cartas) -->
                    <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-emerald-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="4" y="4" width="16" height="16" rx="2"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h6v6H9z"/>
                                    </svg>
                                    Cartas (50/50)
                                </span>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $cartasUsada ? 'bg-slate-800 text-slate-500' : 'bg-emerald-500/20 text-emerald-300' }}">
                                    {{ $cartasUsada ? '0/1' : '1/1' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mb-3">Remove duas alternativas incorretas da tela.</p>
                        </div>
                        <form action="{{ route('game.lifeline') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipo" value="cartas">
                            <button type="submit"
                                    onmouseenter="tocarHover()"
                                    @if($cartasUsada) disabled @endif
                                    class="w-full py-2.5 rounded-xl text-xs font-extrabold transition {{ $cartasUsada ? 'bg-slate-800 text-slate-600 cursor-not-allowed' : 'bg-emerald-500/20 hover:bg-emerald-500 text-emerald-300 hover:text-slate-950 border border-emerald-500/30' }}">
                                {{ $cartasUsada ? 'Já Utilizada' : 'Eliminar 2 Opções' }}
                            </button>
                        </form>
                    </div>

                    <!-- 4. Pular Pergunta -->
                    <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-purple-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                    </svg>
                                    Pular Pergunta
                                </span>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $pulosRestantes <= 0 ? 'bg-slate-800 text-slate-500' : 'bg-purple-500/20 text-purple-300' }}">
                                    {{ $pulosRestantes }}/3
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mb-3">Sorteia outra pergunta sem perder pontuação.</p>
                        </div>
                        <form action="{{ route('game.lifeline') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipo" value="pulo">
                            <button type="submit"
                                    onmouseenter="tocarHover()"
                                    @if($pulosRestantes <= 0) disabled @endif
                                    class="w-full py-2.5 rounded-xl text-xs font-extrabold transition {{ $pulosRestantes <= 0 ? 'bg-slate-800 text-slate-600 cursor-not-allowed' : 'bg-purple-500/20 hover:bg-purple-500 text-purple-300 hover:text-white border border-purple-500/30' }}">
                                {{ $pulosRestantes <= 0 ? 'Sem Pulos' : 'Pular Pergunta' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Clássico: "Você tem certeza?" (Estilo Show do Milhão) -->
            <div id="modalCerteza" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
                <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2">Você tem certeza?</h3>
                    <p class="text-sm text-slate-300 mb-6">
                        Alternativa escolhida:
                        <span id="modalAlternativaTexto" class="block mt-2 p-3 rounded-xl bg-slate-800 border border-amber-500/30 text-amber-300 font-bold text-base"></span>
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="fecharConfirmacao()"
                                class="py-3.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-extrabold text-sm border border-slate-700 transition">
                            NÃO, VOLTAR
                        </button>
                        <button type="button" onclick="confirmarRespostaFinal()"
                                class="py-3.5 px-4 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-slate-950 font-black text-sm shadow-lg transition">
                            SIM, CONFIRMAR!
                        </button>
                    </div>
                </div>
            </div>

            <script>
                // Gerenciador de Áudio Clássico Bíblia Show
                let somAtivo = localStorage.getItem('biblia_show_som') !== 'off';
                const audioSuspense = new Audio('/audio/classico/suspense.wav');
                audioSuspense.loop = true;
                audioSuspense.volume = 0.22;

                const audioHover = new Audio('/audio/classico/hover.wav');
                audioHover.volume = 0.4;

                const audioTecla = new Audio('/audio/classico/tecla.wav');
                audioTecla.volume = 0.6;

                function atualizarIconeSom() {
                    const onEl = document.getElementById('iconSomOn');
                    const offEl = document.getElementById('iconSomOff');
                    const lbl = document.getElementById('labelSom');
                    if (somAtivo) {
                        onEl?.classList.remove('hidden');
                        offEl?.classList.add('hidden');
                        if (lbl) lbl.textContent = 'Sons Ativos';
                    } else {
                        onEl?.classList.add('hidden');
                        offEl?.classList.remove('hidden');
                        if (lbl) lbl.textContent = 'Sem Som';
                        audioSuspense.pause();
                    }
                }

                function alternarSom() {
                    somAtivo = !somAtivo;
                    localStorage.setItem('biblia_show_som', somAtivo ? 'on' : 'off');
                    atualizarIconeSom();
                    if (somAtivo) {
                        audioSuspense.play().catch(() => {});
                    }
                }

                function tocarEfeito(nome) {
                    if (!somAtivo || !nome) return;
                    const fx = new Audio(`/audio/classico/${nome}.wav`);
                    fx.volume = 0.85;
                    fx.play().catch(() => {});
                }

                function tocarHover() {
                    if (!somAtivo) return;
                    audioHover.currentTime = 0;
                    audioHover.play().catch(() => {});
                }

                atualizarIconeSom();

                window.addEventListener('DOMContentLoaded', () => {
                    @if($somAutomatico)
                        tocarEfeito('{{ $somAutomatico }}');
                    @endif
                    @if($novaFaseSom)
                        setTimeout(() => tocarEfeito('fase{{ $novaFaseSom }}'), 1400);
                    @endif
                    if (somAtivo) {
                        setTimeout(() => {
                            audioSuspense.play().catch(() => {});
                        }, 900);
                    }
                });

                // Confirmação "Você tem certeza?"
                let indicePendente = null;
                let enviado = false;

                function pedirConfirmacao(indice, letra, texto) {
                    if (enviado) return;
                    if (somAtivo) {
                        audioTecla.currentTime = 0;
                        audioTecla.play().catch(() => {});
                    }
                    indicePendente = indice;
                    document.getElementById('modalAlternativaTexto').textContent = `${letra}) ${texto}`;
                    const modal = document.getElementById('modalCerteza');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function fecharConfirmacao() {
                    if (somAtivo) {
                        audioTecla.currentTime = 0;
                        audioTecla.play().catch(() => {});
                    }
                    indicePendente = null;
                    const modal = document.getElementById('modalCerteza');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                function confirmarRespostaFinal() {
                    if (enviado || indicePendente === null) return;
                    enviado = true;
                    audioSuspense.pause();
                    document.getElementById('respostaSelecionada').value = indicePendente;
                    document.getElementById('tempo_resposta').value = Math.max(0, tempoInicial - tempo);
                    document.getElementById('respostaForm').submit();
                }

                // Temporizador de 60 segundos
                let tempo = {{ max(1, (int) $tempo_restante) }};
                const tempoInicial = tempo;
                const timerEl = document.getElementById('timer');
                const form = document.getElementById('respostaForm');
                const hiddenTempo = document.getElementById('tempo_resposta');

                let timerInterval = setInterval(() => {
                    tempo--;
                    if (timerEl) timerEl.textContent = Math.max(0, tempo);
                    if (hiddenTempo) hiddenTempo.value = Math.max(0, tempoInicial - tempo);

                    if (tempo <= 0 && !enviado) {
                        enviado = true;
                        clearInterval(timerInterval);
                        audioSuspense.pause();
                        hiddenTempo.value = tempoInicial;
                        form.submit();
                    }
                }, 1000);
            </script>
        @endif

    </div>

</body>
</html>
