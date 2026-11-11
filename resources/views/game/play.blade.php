<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f59e0b">
    <title>Bíblia Show — Jogar</title>
    <link rel="icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg {
            background: radial-gradient(circle at top, #1e293b 0%, #090d16 75%);
        }
        .milhao-card {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.98) 100%);
        }
        @media (min-width: 1024px) {
            .milhao-card-lg {
                background: linear-gradient(180deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.98) 100%);
            }
        }
        .milhao-option {
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 100%);
        }
        .milhao-option:hover:not(:disabled) {
            background: linear-gradient(90deg, rgba(180, 83, 9, 0.25) 0%, rgba(30, 41, 59, 0.95) 100%);
        }
        .opcao-bloqueada {
            background: linear-gradient(90deg, rgba(217, 119, 6, 0.4) 0%, rgba(120, 53, 15, 0.55) 100%) !important;
            border-color: #fbbf24 !important;
            box-shadow: 0 0 25px rgba(251, 191, 36, 0.3);
        }
        .opcao-correta-reveal {
            background: linear-gradient(90deg, rgba(5, 150, 105, 0.95) 0%, rgba(6, 95, 70, 0.98) 100%) !important;
            border-color: #34d399 !important;
            box-shadow: 0 0 32px rgba(16, 185, 129, 0.5);
        }
        .opcao-errada-reveal {
            background: linear-gradient(90deg, rgba(225, 29, 72, 0.95) 0%, rgba(136, 19, 55, 0.98) 100%) !important;
            border-color: #fb7185 !important;
            box-shadow: 0 0 32px rgba(244, 63, 94, 0.5);
        }
        @keyframes timerPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.06); }
        }
        .timer-warning {
            color: #fb7185 !important;
            animation: timerPulse 0.9s infinite ease-in-out;
        }
        .timer-box-warning {
            border-color: #f43f5e !important;
            background: rgba(225, 29, 72, 0.22) !important;
            box-shadow: 0 0 24px rgba(244, 63, 94, 0.45) !important;
        }
    </style>
</head>
<body class="milhao-bg sm-stage-mobile text-slate-100 min-h-screen">

    @include('partials.navbar', ['dark' => true, 'hideJogarAgora' => true, 'hideOnMobile' => true])

    <div class="max-w-7xl mx-auto px-2.5 sm:px-6 lg:px-8 py-2.5 sm:py-7">

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

            $somAutomatico = null;
            if (!empty($ajudaFeedback['som_efeito'])) {
                $somAutomatico = $ajudaFeedback['som_efeito'];
            }
            $novaFaseSom = $resultado['nova_fase'] ?? null;
        @endphp

        <!-- Feedback de Recurso de Ajuda -->
        @if($ajudaFeedback && !empty($ajudaFeedback['mensagem']))
            <div class="mb-5 rounded-2xl p-4 bg-amber-500/15 border border-amber-500/40 text-amber-200 text-sm flex items-center gap-3 shadow-lg">
                <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <span>{{ $ajudaFeedback['mensagem'] }}</span>
            </div>
        @endif

        @if($estado === 'em_curso' && $pergunta)
            @php
                $valoresFase = [1 => 10, 2 => 20, 3 => 30, 4 => 50];
                $escadaPremios = [];
                $acumulado = 0;
                for ($n = 1; $n <= ($total_perguntas ?? 24); $n++) {
                    $faseN = min(4, (int) ceil($n / 6));
                    $acumulado += $valoresFase[$faseN];
                    $escadaPremios[$n] = ['pts' => $acumulado, 'fase' => $faseN, 'checkpoint' => $n % 6 === 0];
                }
                $valorPerguntaAtual = $placar_milhao['valor_pergunta'] ?? 10;
            @endphp

            <!-- ===================== HUD MOBILE (estilo Show do Milhão) ===================== -->
            <div class="lg:hidden flex items-center justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="sm-avatar !w-9 !h-9 text-sm">{{ mb_strtoupper(mb_substr($jogador_nome ?? 'P', 0, 1)) }}</div>
                    <div class="min-w-0 leading-tight">
                        <p class="text-[11px] font-black text-white truncate max-w-[110px]">{{ $jogador_nome ?? 'Participante' }}</p>
                        <div class="flex items-center gap-1 mt-0.5" title="Vidas">
                            @for($i = 1; $i <= ($max_erros ?? 3); $i++)
                                <span class="text-[10px] {{ $i <= ($erros_restantes ?? 3) ? 'text-rose-400' : 'text-slate-600' }}">♥</span>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" onclick="abrirEscadaPremios()" class="sm-lifeline !w-10 !h-9 !text-sm" title="Escada de Prêmios">🏆</button>
                    <button type="button" onclick="typeof abrirModalDefinicoes === 'function' && abrirModalDefinicoes()" class="sm-lifeline !w-10 !h-9 !text-sm" title="Som e Definições">⚙️</button>
                    <form action="{{ route('game.finish') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="sm-lifeline !w-10 !h-9 !text-sm" title="Parar e ver resultado">⏹</button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-6 items-start">

                <!-- ======================================================== -->
                <!-- SIDEBAR ESQUERDA: PARTICIPANTE, VIDAS, PLACAR E JORNADA  -->
                <!-- ======================================================== -->
                <aside class="hidden lg:block lg:col-span-4 xl:col-span-4 space-y-4">
                    <!-- 1. Card do Participante no Palco -->
                    <div class="bg-slate-900/95 border border-slate-800 rounded-3xl p-5 shadow-xl">
                        <div class="flex items-center gap-3.5 mb-4">
                            <div id="sidebarJogadorAvatar" class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-xl flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0">
                                {{ mb_strtoupper(mb_substr($jogador_nome ?? 'P', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Participante no Palco</p>
                                <h3 id="sidebarJogadorNome" class="font-black text-white text-base sm:text-lg truncate">{{ $jogador_nome ?? 'Participante' }}</h3>
                                <button type="button" onclick="abrirModalNomeJogador()" class="text-xs font-bold text-amber-400 hover:text-amber-300 hover:underline inline-flex items-center gap-1 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                    <span>(Trocar nome)</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-800/80">
                            <a href="{{ route('game.play', ['novo' => 1]) }}"
                               class="inline-flex items-center justify-center gap-1.5 text-xs bg-slate-800 hover:bg-slate-700 text-slate-200 py-2.5 px-3 rounded-xl font-bold border border-slate-700 transition">
                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Reiniciar</span>
                            </a>

                            <form action="{{ route('game.finish') }}" method="POST" class="inline w-full">
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 text-xs bg-rose-500/15 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 py-2.5 px-3 rounded-xl font-bold transition">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="6" y="6" width="12" height="12" rx="2"/>
                                    </svg>
                                    <span>Parar</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- 2. Card de Sobrevivência: Vidas & Tentativas de Tempo -->
                    <div class="bg-slate-900/95 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-400 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Vidas (Máx. 3 Erros)</p>
                                    <p class="text-xs font-bold text-slate-200">Erros cometidos: <strong id="errosContador" class="text-rose-400">{{ $erros_count ?? 0 }}</strong> / {{ $max_erros ?? 3 }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5" id="coracoesVidas">
                                @for($i = 1; $i <= ($max_erros ?? 3); $i++)
                                    <span class="w-3.5 h-3.5 rounded-full {{ $i <= ($erros_restantes ?? 3) ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-slate-800 border border-slate-700' }}"></span>
                                @endfor
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Tentativas de Tempo (Máx. 3)</p>
                                    <p class="text-xs font-bold text-slate-200">Tempos esgotados: <strong id="temposContador" class="text-amber-400">{{ $tempos_esgotados_count ?? 0 }}</strong> / {{ $max_tempos_esgotados ?? 3 }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                @for($i = 1; $i <= ($max_tempos_esgotados ?? 3); $i++)
                                    <span class="w-3.5 h-3.5 rounded-full {{ $i <= ($tempos_esgotados_restantes ?? 3) ? 'bg-amber-400 shadow-sm shadow-amber-400/50' : 'bg-slate-800 border border-slate-700' }}"></span>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- 3. Placar Estilo Show do Milhão: ERRAR | PARAR | ACERTAR -->
                    <div class="bg-slate-900/95 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-2.5">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 text-center mb-1">Premiação da Rodada</p>
                        
                        <div class="grid grid-cols-3 gap-2">
                            <div class="bg-rose-950/40 border border-rose-500/30 rounded-2xl p-2.5 sm:p-3 text-center">
                                <p class="text-[9px] font-extrabold uppercase tracking-wider text-rose-400">Errar</p>
                                <p class="text-base sm:text-lg font-black text-rose-200 mt-0.5">
                                    {{ $placar_milhao['errar'] ?? 0 }} <span class="text-[10px] font-normal text-rose-400">pts</span>
                                </p>
                            </div>

                            <div class="bg-amber-500/15 border border-amber-500/40 rounded-2xl p-2.5 sm:p-3 text-center">
                                <p class="text-[9px] font-extrabold uppercase tracking-wider text-amber-300">Parar</p>
                                <p class="text-base sm:text-lg font-black text-amber-400 mt-0.5">
                                    {{ $pontuacao }} <span class="text-[10px] font-normal text-amber-300/70">pts</span>
                                </p>
                            </div>

                            <div class="bg-emerald-950/40 border border-emerald-500/40 rounded-2xl p-2.5 sm:p-3 text-center">
                                <p class="text-[9px] font-extrabold uppercase tracking-wider text-emerald-400">Acertar</p>
                                <p class="text-base sm:text-lg font-black text-emerald-300 mt-0.5">
                                    {{ $placar_milhao['acertar'] ?? ($pontuacao + 10) }} <span class="text-[10px] font-normal text-emerald-400/80">pts</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Card de Fases e Progresso do Bíblia Show (1ª a 4ª Fase) -->
                    <div class="bg-slate-900/95 border border-slate-800 rounded-3xl p-5 shadow-xl">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Jornada das 4 Fases</span>
                            <span class="text-xs font-black text-amber-400">Pergunta {{ $numero_pergunta }} de {{ $total_perguntas }}</span>
                        </div>

                        <div class="space-y-2">
                            @php
                                $fasesInfo = [
                                    1 => ['nome' => '1ª Fase • Fácil', 'perguntas' => '1 a 6', 'cor' => 'emerald'],
                                    2 => ['nome' => '2ª Fase • Médio', 'perguntas' => '7 a 12', 'cor' => 'blue'],
                                    3 => ['nome' => '3ª Fase • Difícil', 'perguntas' => '13 a 18', 'cor' => 'amber'],
                                    4 => ['nome' => '4ª Fase • Pergunta do Milhão', 'perguntas' => '19 a 24', 'cor' => 'purple'],
                                ];
                            @endphp
                            @foreach($fasesInfo as $fNum => $fInfo)
                                @php
                                    $isFaseAtual = ($fase_atual ?? 1) == $fNum;
                                    $isPassou = ($fase_atual ?? 1) > $fNum;
                                @endphp
                                <div class="flex items-center justify-between px-3 py-2 rounded-xl text-xs transition {{ $isFaseAtual ? 'bg-amber-500/20 border border-amber-500/40 text-amber-300 font-extrabold' : ($isPassou ? 'bg-slate-950/60 text-slate-400 font-semibold' : 'bg-slate-950/30 text-slate-600') }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black {{ $isFaseAtual ? 'bg-amber-400 text-slate-950' : ($isPassou ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-500') }}">
                                            @if($isPassou)
                                                ✓
                                            @else
                                                {{ $fNum }}
                                            @endif
                                        </span>
                                        <span>{{ $fInfo['nome'] }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold opacity-80">{{ $fInfo['perguntas'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <!-- ======================================================== -->
                <!-- ARENA CENTRAL DO JOGO: TEMPORIZADOR, PERGUNTA E AJUDAS   -->
                <!-- ======================================================== -->
                <main class="lg:col-span-8 xl:col-span-8 space-y-3 lg:space-y-5">
                    <!-- Barra de Destaque Superior: Temporizador & Contexto da Pergunta -->
                    <div class="milhao-card border border-amber-500/40 rounded-3xl p-4 sm:p-5 shadow-2xl hidden lg:flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center font-black text-lg shadow-inner shrink-0">
                                {{ $numero_pergunta }}
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-black uppercase tracking-widest text-amber-400">Pergunta {{ $numero_pergunta }} de {{ $total_perguntas }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700">Fase {{ $fase_atual ?? 1 }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">Nv. {{ $pergunta['dificuldade_nivel'] ?? 1 }}</span>
                                    @if($sequencia_acertos > 0)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            {{ $sequencia_acertos }} seguidos
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs sm:text-sm font-bold text-slate-200 mt-1">
                                    {{ $pergunta['livro_nome'] ?? 'Escrituras Sagradas' }}
                                    @if(!empty($pergunta['categoria_nome']))
                                        <span class="text-slate-400 font-normal">• {{ $pergunta['categoria_nome'] }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Temporizador Destacado e Chamativo -->
                        <div id="timerContainerBox" class="flex items-center gap-3 bg-slate-950/90 border border-amber-500/40 px-4 py-2.5 rounded-2xl shadow-inner transition-all">
                            <div class="text-right">
                                <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Tempo Restante</p>
                                <p class="text-2xl sm:text-3xl font-black font-mono text-white leading-none tracking-tight">
                                    <span id="timer" class="js-timer">{{ $tempo_restante }}</span><span class="text-sm font-bold text-amber-400 ml-0.5">s</span>
                                </p>
                            </div>
                            <div id="timerIconBox" class="w-11 h-11 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="13" r="8"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4l2.5 2.5M9 2h6"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Card Principal da Pergunta -->
                    <div class="milhao-card-lg lg:border-2 lg:border-amber-500/30 rounded-3xl lg:shadow-2xl p-0 lg:p-8">
                        <!-- MOBILE: Pílula dourada + cronômetro circular -->
                        <div class="lg:hidden flex items-center justify-between gap-2 mb-[-14px] px-1 relative z-10">
                            <div class="sm-timer js-timer-box" id="timerMobileBox"><span class="js-timer">{{ $tempo_restante }}</span></div>
                            <div class="sm-pill text-sm">
                                <span>
                                    ◆ {{ $valorPerguntaAtual }} PTS ◆
                                    <small>{{ $numero_pergunta }} / {{ $total_perguntas }} • {{ $fase_atual ?? 1 }}ª FASE</small>
                                </span>
                            </div>
                            <div class="sm-timer !border-blue-300 !text-[10px] !leading-none text-center flex-col" title="Pontos atuais">
                                <span class="text-amber-300 text-sm">{{ $pontuacao }}</span><span class="text-[8px] opacity-80">PTS</span>
                            </div>
                        </div>
                        <div class="hidden lg:flex flex-wrap items-center justify-between gap-2 mb-4">
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
                        </div>

                        <!-- Caixa de Enunciado Estilo Show do Milhão -->
                        <div class="sm-question lg:hidden pt-7 pb-4 px-4 mb-3">
                            <p class="sm-question-text text-[17px] sm:text-xl">
                                {{ $pergunta['texto'] ?? $pergunta->texto }}
                            </p>
                            @if(!empty($pergunta['livro_nome']))
                                <p class="text-[10px] font-bold uppercase tracking-wider text-blue-200/80 mt-2">📖 {{ $pergunta['livro_nome'] }}</p>
                            @endif
                        </div>
                        <div class="hidden lg:block bg-gradient-to-r from-red-950/80 via-slate-900 to-red-950/80 border-2 border-amber-400/50 rounded-2xl p-5 sm:p-7 mb-6 shadow-inner">
                            <p class="text-lg sm:text-2xl font-extrabold text-white leading-relaxed tracking-tight">
                                {{ $pergunta['texto'] ?? $pergunta->texto }}
                            </p>
                        </div>

                        <!-- Dica Bíblica (Referência) quando acionada -->
                        @if($dicaBiblica)
                            <div class="mb-3 lg:mb-5 rounded-2xl p-2.5 lg:p-4 bg-blue-950/60 border border-blue-400/40 text-blue-100 flex items-center gap-3">
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
                            <!-- MOBILE: Balão de fala do convidado -->
                            <div class="lg:hidden flex items-center gap-2.5 mb-3 px-1">
                                <div class="sm-avatar">{{ mb_strtoupper(mb_substr($dicaConvidado['convidado'] ?? '?', 0, 1)) }}</div>
                                <div class="sm-speech flex-1">
                                    <span class="block text-[9px] uppercase tracking-wider text-blue-700 font-black">{{ $dicaConvidado['convidado'] }} diz:</span>
                                    {{ $dicaConvidado['mensagem'] }}
                                </div>
                            </div>
                            <div class="hidden lg:block mb-6 rounded-2xl p-4 border {{ !empty($dicaConvidado['sabe_resposta']) ? 'bg-amber-500/15 border-amber-400/50 text-amber-100' : 'bg-slate-800/90 border-slate-600 text-slate-200' }}">
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

                        <!-- MOBILE: Ajudas circulares (estilo Show do Milhão) -->
                        <div class="lg:hidden flex items-center justify-center gap-3 mb-3">
                            <button type="button" onclick="abrirCartasMobile()" class="sm-lifeline" title="Cartas: eliminar 2 alternativas" @if($cartasUsada) disabled @endif>
                                −2
                                <span class="sm-badge">{{ $cartasUsada ? 0 : 1 }}</span>
                            </button>
                            <button type="button" onclick="abrirConvidadosMobile()" class="sm-lifeline" title="Convidados bíblicos" @if($convidadosUsada || $convidadosRestantes <= 0) disabled @endif>
                                👥
                                <span class="sm-badge">{{ $convidadosRestantes }}</span>
                            </button>
                            <form action="{{ route('game.lifeline') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="tipo" value="dica">
                                <button type="submit" class="sm-lifeline" title="Dica bíblica" @if($dicasRestantes <= 0 || $dicaBiblica) disabled @endif>
                                    📖
                                    <span class="sm-badge">{{ $dicasRestantes }}</span>
                                </button>
                            </form>
                            <form action="{{ route('game.lifeline') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="tipo" value="pulo">
                                <button type="submit" class="sm-lifeline" title="Pular pergunta" @if($pulosRestantes <= 0) disabled @endif>
                                    ⇄
                                    <span class="sm-badge">{{ $pulosRestantes }}</span>
                                </button>
                            </form>
                        </div>

                        <!-- Alternativas A, B, C, D -->
                        <form action="{{ route('game.answer') }}" method="POST" id="respostaForm">
                            @csrf
                            <input type="hidden" name="tempo_resposta" id="tempo_resposta" value="0">
                            <input type="hidden" name="resposta" id="respostaSelecionada" value="">
                            <input type="hidden" name="tempo_esgotado" id="tempoEsgotadoInput" value="0">

                            @php
                                $opcoes = $pergunta['opcoes'] ?? [];
                                $opcoesArray = is_string($opcoes) ? json_decode($opcoes, true) : $opcoes;
                                $letras = ['A', 'B', 'C', 'D'];
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 lg:gap-3.5" id="gridAlternativas">
                                @foreach($opcoesArray as $indice => $opcao)
                                    @php
                                        $eliminada = in_array((int) $indice, $opcoesEliminadas, true);
                                        $letra = $letras[$indice] ?? ($indice + 1);
                                    @endphp
                                    @if($eliminada)
                                        <div class="sm-option is-eliminada select-none">
                                            <span class="flex items-center gap-2 min-w-0">
                                                <span class="sm-letter">{{ $letra }}:</span>
                                                <span class="text-slate-400 font-medium text-sm lg:text-base">{{ $opcao }}</span>
                                            </span>
                                        </div>
                                    @else
                                        <button type="button"
                                                id="btnOpcao_{{ $indice }}"
                                                data-indice="{{ $indice }}"
                                                onmouseenter="tocarHover()"
                                                onclick="pedirConfirmacao('{{ $indice }}', '{{ $letra }}', @js($opcao))"
                                                class="btn-alternativa sm-option group">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span id="badgeLetra_{{ $indice }}" class="sm-letter text-base lg:text-lg">
                                                    {{ $letra }}:
                                                </span>
                                                <span class="text-white font-extrabold text-sm sm:text-base lg:text-lg leading-snug">
                                                    {{ $opcao }}
                                                </span>
                                            </div>
                                            <span id="statusTag_{{ $indice }}" class="hidden shrink-0 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full"></span>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </form>

                        <!-- Barra de Resultado Instantâneo após Bloqueio -->
                        <div id="painelResultadoInstantaneo" class="hidden mt-6 rounded-2xl p-4 sm:p-5 border transition-all"></div>
                    </div>

                    <!-- MOBILE: Lingotes ERRAR | PARAR | ACERTAR -->
                    <div class="lg:hidden grid grid-cols-3 gap-2 pt-1">
                        <div class="sm-ingot is-errar">
                            <div class="sm-ingot-value">{{ $placar_milhao['errar'] ?? 0 }}</div>
                            <div class="sm-ingot-label">Errar</div>
                        </div>
                        <div class="sm-ingot">
                            <div class="sm-ingot-value">{{ $pontuacao }}</div>
                            <div class="sm-ingot-label">Parar</div>
                        </div>
                        <div class="sm-ingot is-acertar">
                            <div class="sm-ingot-value">{{ $placar_milhao['acertar'] ?? ($pontuacao + 10) }}</div>
                            <div class="sm-ingot-label">Acertar</div>
                        </div>
                    </div>

                    <!-- Área Destacada de Dicas & Ajudas do Clássico Bíblia Show -->
                    <div class="hidden lg:block milhao-card border-2 border-amber-500/30 rounded-3xl p-5 sm:p-6 shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xs sm:text-sm font-extrabold uppercase tracking-widest text-amber-400 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>Ajudas e Dicas do Clássico Bíblia Show</span>
                            </h2>
                            <span class="text-xs text-slate-400 font-semibold">3 Dicas • 3 Convidados • Cartas • 3 Pulos</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <!-- 1. Dica Bíblica -->
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

                            <!-- 2. Personagens Bíblicos -->
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
                </main>
            </div>

            <!-- ===== MOBILE: Escada de Prêmios ===== -->
            <div id="modalEscadaPremios" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm items-center justify-center p-3" onclick="if(event.target===this) fecharEscadaPremios()">
                <div class="sm-panel w-full max-w-sm max-h-[90vh] flex flex-col">
                    <div class="sm-panel-title px-4 py-3 text-center">
                        <div class="text-2xl">🏆</div>
                        <div class="sm-pill text-xs mt-1">24 ◆ {{ end($escadaPremios)['pts'] ?? 660 }} PTS</div>
                    </div>
                    <div class="flex-1 overflow-y-auto p-3 space-y-1.5">
                        @foreach(array_reverse($escadaPremios, true) as $n => $deg)
                            @php
                                $classe = $n < $numero_pergunta ? 'is-done' : ($n == $numero_pergunta ? 'is-current' : '');
                                if ($deg['checkpoint']) $classe .= ' is-checkpoint';
                            @endphp
                            <div class="sm-ladder-row {{ $classe }}">
                                <span>{{ $n }} <span class="text-[9px] opacity-70 ml-1">{{ $deg['fase'] }}ª fase</span></span>
                                <span class="sm-ladder-pts">{{ $deg['pts'] }} pts</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-3">
                        <button type="button" onclick="fecharEscadaPremios()" class="sm-cta">Voltar ao Jogo</button>
                    </div>
                </div>
            </div>

            <!-- ===== MOBILE: Cartas (eliminar 2) ===== -->
            <div id="modalCartasMobile" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm items-center justify-center p-4" onclick="if(event.target===this) fecharCartasMobile()">
                <div class="sm-panel w-full max-w-xs">
                    <div class="sm-panel-title px-4 py-2.5 text-center font-black text-white tracking-wider">CARTAS</div>
                    <div class="p-4">
                        <p class="text-center text-xs font-bold text-blue-100 mb-3">Vire uma carta para eliminar 2 alternativas erradas</p>
                        <form action="{{ route('game.lifeline') }}" method="POST" id="formCartasMobile">
                            @csrf
                            <input type="hidden" name="tipo" value="cartas">
                            <div class="grid grid-cols-2 gap-3">
                                @for($c = 0; $c < 4; $c++)
                                    <button type="submit" class="sm-card-back" onclick="vibrar(40)">✝</button>
                                @endfor
                            </div>
                        </form>
                        <button type="button" onclick="fecharCartasMobile()" class="sm-cta mt-4 !py-2.5 text-sm">Voltar</button>
                    </div>
                </div>
            </div>

            <!-- ===== MOBILE: Convidados Bíblicos ===== -->
            <div id="modalConvidadosMobile" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm items-center justify-center p-4" onclick="if(event.target===this) fecharConvidadosMobile()">
                <div class="sm-panel w-full max-w-xs">
                    <div class="sm-panel-title px-4 py-2.5 text-center font-black text-white tracking-wider">CONVIDADOS BÍBLICOS</div>
                    <div class="p-4 space-y-2.5">
                        <p class="text-center text-xs font-bold text-blue-100">Quem você quer consultar?</p>
                        @foreach($convidadosBiblicos as $idx => $nomeConvidado)
                            <form action="{{ route('game.lifeline') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tipo" value="convidado">
                                <input type="hidden" name="convidado_indice" value="{{ $idx }}">
                                <button type="submit" class="sm-option" onclick="vibrar(30)">
                                    <span class="flex items-center gap-2.5">
                                        <span class="sm-avatar !w-8 !h-8 text-xs">{{ mb_strtoupper(mb_substr($nomeConvidado, 0, 1)) }}</span>
                                        <span>{{ $nomeConvidado }}</span>
                                    </span>
                                    <span class="text-amber-300 text-xs">›</span>
                                </button>
                            </form>
                        @endforeach
                        <button type="button" onclick="fecharConvidadosMobile()" class="sm-cta !py-2.5 text-sm">Voltar</button>
                    </div>
                </div>
            </div>

            <!-- Modal 0: Continuar Como [Nome] ou Trocar de Participante -->
            <div id="modalContinuarComo" class="fixed inset-0 z-50 {{ !empty($perguntar_continuar) ? 'flex' : 'hidden' }} items-center justify-center bg-slate-950/90 backdrop-blur-md p-4">
                <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl">
                    <div class="w-16 h-16 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-amber-400 mb-1">Arena Bíblia Show</p>
                    <h3 class="text-2xl font-black text-white mb-2">Continuar Partida?</h3>
                    <p class="text-sm text-slate-300 mb-6">
                        Você deseja continuar jogando como <strong class="text-amber-400 text-base font-black">{{ $jogador_nome }}</strong> ou deseja entrar com outro nome?
                    </p>
                    <div class="space-y-3">
                        <button type="button" id="btnContinuarComoAtual" onclick="confirmarContinuarComo()"
                                class="w-full py-4 px-6 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-sm uppercase tracking-wider shadow-lg transition transform hover:-translate-y-0.5">
                            Continuar como {{ $jogador_nome }} →
                        </button>
                        <button type="button" onclick="trocarParaNovoParticipante()"
                                class="w-full py-3 px-6 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs uppercase tracking-wider border border-slate-700 transition">
                            Entrar com Outro Nome / Participante
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal 1: Identificação Clássica do Jogador ao Iniciar -->
            <div id="modalNomeJogador" class="fixed inset-0 z-50 {{ !empty($precisa_identificacao) ? 'flex' : 'hidden' }} items-center justify-center bg-slate-950/90 backdrop-blur-md p-4">
                <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-amber-400 mb-1">Bíblia Show Clássico</p>
                    <h3 class="text-2xl font-black text-white mb-2">Qual é o seu nome?</h3>
                    <p class="text-xs sm:text-sm text-slate-300 mb-5">
                        Seu nome aparecerá no palco e será registrado no <strong>Ranking Geral do Bíblia Show</strong> ao final da partida!
                    </p>
                    <form id="formDefinirJogador" action="{{ route('game.player') }}" method="POST" onsubmit="submeterNomeJogador(event)" class="space-y-4">
                        @csrf
                        <input type="text" name="jogador_nome" id="inputNomeJogadorModal" required maxlength="60"
                               value="{{ ($jogador_nome ?? '') !== 'Participante' ? ($jogador_nome ?? '') : '' }}"
                               placeholder="Digite seu nome ou apelido..."
                               class="w-full px-4 py-3.5 rounded-xl bg-slate-950 border border-amber-500/40 text-white font-bold text-center text-base focus:ring-2 focus:ring-amber-400 outline-none">
                        <button type="submit" id="btnIniciarPartidaModal"
                                class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-sm uppercase tracking-wider shadow-lg transition">
                            Começar Partida! →
                        </button>
                    </form>
                </div>
            </div>

            <!-- Modal 2: "Você tem certeza?" (Estilo Show do Milhão) -->
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

            <!-- Modal 3: Mensagem Amigável de Fim de Jogo (antes de ir para /jogar/resultado) -->
            <div id="modalFimAmigavel" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/90 backdrop-blur-md p-4">
                <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-lg w-full p-6 sm:p-8 text-center shadow-2xl">
                    <div class="w-16 h-16 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                        </svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-amber-400 mb-1">Fim da Jornada nesta Partida</p>
                    <h3 class="text-2xl font-black text-white mb-3">Valeu pela participação, {{ $jogador_nome ?? 'Participante' }}!</h3>
                    <p id="textoMensagemAmigavel" class="text-sm sm:text-base text-slate-300 leading-relaxed mb-6"></p>
                    <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 mb-6">
                        <p class="text-xs uppercase font-bold text-slate-400">Sua Pontuação Registrada no Ranking</p>
                        <p id="pontosModalFim" class="text-3xl font-black text-amber-400 mt-1">{{ $pontuacao }} pts</p>
                    </div>
                    <a id="btnIrParaResultado" href="{{ route('game.result') }}"
                       class="block w-full py-4 px-6 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-sm uppercase tracking-wider shadow-xl transition">
                        Ver Meu Resultado e Ranking Geral →
                    </a>
                </div>
            </div>

            <script>
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const BASE_AUDIO_URL = "{{ asset('audio/classico') }}";
                let precisaIdentificar = {{ !empty($precisa_identificacao) ? 'true' : 'false' }};
                let perguntarContinuar = {{ !empty($perguntar_continuar) ? 'true' : 'false' }};
                let jogoIniciado = !precisaIdentificar && !perguntarContinuar;

                function vibrar(padrao = 30) {
                    const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
                    if (!ativo) return;
                    try {
                        if (window.AndroidNative) {
                            if (Array.isArray(padrao)) {
                                window.AndroidNative.vibratePattern(padrao);
                            } else {
                                window.AndroidNative.vibrate(Number(padrao) || 30);
                            }
                            return;
                        }
                        if (navigator.vibrate) {
                            navigator.vibrate(padrao);
                        }
                    } catch (e) {}
                }

                // Desbloqueio de áudio no mobile
                function destravarAudioMobile() {
                    try {
                        const dummy = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRhAgAAAAEA');
                        dummy.volume = 0.01;
                        dummy.play().catch(() => {});
                    } catch (e) {}
                }
                ['touchstart', 'pointerdown', 'click'].forEach(evt => {
                    window.addEventListener(evt, destravarAudioMobile, { once: true, passive: true });
                });

                const audioLocucao = new Audio(`${BASE_AUDIO_URL}/locucao.wav`);
                audioLocucao.loop = false; // "so fala uma vez"

                const audioSuspense = new Audio(`${BASE_AUDIO_URL}/suspense.wav`);
                audioSuspense.loop = true;

                const audioHover = new Audio(`${BASE_AUDIO_URL}/hover.wav`);
                const audioTecla = new Audio(`${BASE_AUDIO_URL}/tecla.wav`);

                function aplicarVolume(audioObj, fator = 1) {
                    const base = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85;
                    audioObj.volume = Math.max(0, Math.min(1, base * fator));
                }

                window.addEventListener('biblia-som-alterado', (ev) => {
                    if (!ev.detail.ativo) {
                        audioSuspense.pause();
                        audioLocucao.pause();
                    } else if (!enviado && jogoIniciado) {
                        aplicarVolume(audioSuspense, 0.28);
                        audioSuspense.play().catch(() => {});
                    }
                });

                let locucaoJaTocou = false;
                let locucaoInterrompida = false;

                function tocarLocucao() {
                    const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
                    if (!ativo || locucaoJaTocou) return;
                    locucaoJaTocou = true;

                    aplicarVolume(audioLocucao, 0.95);
                    audioLocucao.currentTime = 0;

                    const prom = audioLocucao.play();
                    if (prom && typeof prom.catch === 'function') {
                        prom.catch(() => {
                            // Desbloqueia na primeira interação caso o navegador barre autoplay sem clique
                            const desbloquear = () => {
                                if (!locucaoInterrompida && !jogoIniciado) {
                                    audioLocucao.play().catch(() => {});
                                }
                                document.removeEventListener('click', desbloquear);
                                document.removeEventListener('touchstart', desbloquear);
                                document.removeEventListener('keydown', desbloquear);
                            };
                            document.addEventListener('click', desbloquear, { passive: true });
                            document.addEventListener('touchstart', desbloquear, { passive: true });
                            document.addEventListener('keydown', desbloquear, { passive: true });
                        });
                    }
                }

                const audioFase1 = new Audio(`${BASE_AUDIO_URL}/fase1.wav`);
                audioFase1.loop = false;

                let fase1JaTocou = false;

                function tocarFase1ESuspense() {
                    if (fase1JaTocou) {
                        iniciarSomSuspense();
                        return;
                    }
                    fase1JaTocou = true;

                    const somLigado = typeof somEstaAtivo === 'function' ? somEstaAtivo() : true;
                    if (!somLigado) {
                        iniciarSomSuspense();
                        return;
                    }

                    aplicarVolume(audioFase1, 0.95);
                    audioFase1.currentTime = 0;

                    let finalizou = false;
                    const concluirFase1 = () => {
                        if (!finalizou) {
                            finalizou = true;
                            iniciarSomSuspense();
                        }
                    };

                    audioFase1.addEventListener('ended', concluirFase1, { once: true });
                    audioFase1.addEventListener('error', concluirFase1, { once: true });
                    setTimeout(concluirFase1, 4000);

                    const prom = audioFase1.play();
                    if (prom && typeof prom.catch === 'function') {
                        prom.catch(concluirFase1);
                    }
                }

                // "O audio fase1.wav deve vir logo depois que a locucao acabar"
                audioLocucao.addEventListener('ended', () => {
                    tocarFase1ESuspense();
                });

                // "ou se for interrompido com inicio do jogo"
                function pararLocucaoETocarFase1() {
                    locucaoInterrompida = true;
                    try {
                        audioLocucao.pause();
                        audioLocucao.currentTime = 0;
                    } catch (e) {}
                    tocarFase1ESuspense();
                }

                function iniciarSomSuspense() {
                    const somLigado = typeof somEstaAtivo === 'function' ? somEstaAtivo() : true;
                    if (somLigado && !enviado && audioSuspense.paused) {
                        aplicarVolume(audioSuspense, 0.28);
                        audioSuspense.play().catch(() => {});
                    }
                }

                function tocarEfeitoComPromise(nome) {
                    return new Promise((resolve) => {
                        const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
                        if (!ativo || !nome) {
                            setTimeout(resolve, 1600);
                            return;
                        }
                        const fx = new Audio(`${BASE_AUDIO_URL}/${nome}.wav`);
                        aplicarVolume(fx, 1.0);
                        let finalizado = false;
                        const concluir = () => {
                            if (!finalizado) {
                                finalizado = true;
                                resolve();
                            }
                        };
                        fx.addEventListener('ended', concluir);
                        fx.addEventListener('error', concluir);
                        setTimeout(concluir, 4500);
                        fx.play().catch(concluir);
                    });
                }

                function tocarHover() {
                    const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : true;
                    if (!ativo || !jogoIniciado || enviado) return;
                    aplicarVolume(audioHover, 0.45);
                    audioHover.currentTime = 0;
                    audioHover.play().catch(() => {});
                }

                function abrirModalNomeJogador() {
                    const mCont = document.getElementById('modalContinuarComo');
                    if (mCont) {
                        mCont.classList.add('hidden');
                        mCont.classList.remove('flex');
                    }
                    const m = document.getElementById('modalNomeJogador');
                    if (m) {
                        m.classList.remove('hidden');
                        m.classList.add('flex');
                        const inputNome = document.getElementById('inputNomeJogadorModal');
                        if (inputNome) {
                            inputNome.value = '';
                            setTimeout(() => inputNome.focus(), 250);
                        }
                    }
                    if (!locucaoJaTocou) {
                        tocarLocucao();
                    }
                }

                function trocarParaNovoParticipante() {
                    perguntarContinuar = false;
                    abrirModalNomeJogador();
                }

                async function confirmarContinuarComo() {
                    pararLocucaoETocarFase1();

                    const m = document.getElementById('modalContinuarComo');
                    if (m) {
                        m.classList.add('hidden');
                        m.classList.remove('flex');
                    }

                    precisaIdentificar = false;
                    perguntarContinuar = false;
                    jogoIniciado = true;

                    try {
                        await fetch("{{ route('game.confirmar-jogador') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            }
                        });
                    } catch (e) {}
                }

                async function submeterNomeJogador(e) {
                    e.preventDefault();
                    const input = document.getElementById('inputNomeJogadorModal');
                    const nome = input ? input.value.trim() : '';
                    if (!nome) return;

                    pararLocucaoETocarFase1();

                    const btn = document.getElementById('btnIniciarPartidaModal');
                    if (btn) {
                        btn.disabled = true;
                        btn.textContent = 'Iniciando Partida...';
                    }

                    try {
                        const resp = await fetch("{{ route('game.player') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ jogador_nome: nome })
                        });

                        if (resp.ok) {
                            const data = await resp.json().catch(() => ({}));
                            const nomeFinal = data.jogador_nome || nome;

                            const elNome = document.getElementById('sidebarJogadorNome');
                            if (elNome) elNome.textContent = nomeFinal;
                            const elAvatar = document.getElementById('sidebarJogadorAvatar');
                            if (elAvatar) elAvatar.textContent = nomeFinal.charAt(0).toUpperCase();

                            const m = document.getElementById('modalNomeJogador');
                            if (m) {
                                m.classList.add('hidden');
                                m.classList.remove('flex');
                            }

                            precisaIdentificar = false;
                            perguntarContinuar = false;
                            jogoIniciado = true;
                            return;
                        }
                    } catch (err) {}

                    e.target.submit();
                }

                window.addEventListener('DOMContentLoaded', () => {
                    const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : true;
                    @if($somAutomatico)
                        if (ativo) tocarEfeitoComPromise('{{ $somAutomatico }}');
                    @endif
                    @if($novaFaseSom)
                        if (ativo) setTimeout(() => tocarEfeitoComPromise('fase{{ $novaFaseSom }}'), 1500);
                    @endif

                    if (!jogoIniciado) {
                        // O jogo NÃO inicia sem antes colocar nome ou confirmar continuar
                        setTimeout(() => {
                            tocarLocucao();
                            if (precisaIdentificar) {
                                const inputNome = document.getElementById('inputNomeJogadorModal');
                                if (inputNome) inputNome.focus();
                            }
                        }, 400);
                    } else if (ativo) {
                        @if(($numero_pergunta ?? 1) == 1)
                            setTimeout(() => {
                                tocarFase1ESuspense();
                            }, 400);
                        @else
                            setTimeout(() => {
                                iniciarSomSuspense();
                            }, 900);
                        @endif
                    }

                    // Garante que o suspense seja tocado no primeiro toque caso bloqueado pelo navegador
                    ['click', 'touchstart', 'keydown'].forEach(evt => {
                        document.addEventListener(evt, () => {
                            const somLigado = typeof somEstaAtivo === 'function' ? somEstaAtivo() : true;
                            if (somLigado && !enviado && jogoIniciado && audioSuspense.paused) {
                                iniciarSomSuspense();
                            }
                        }, { once: true, passive: true });
                    });
                });

                let indicePendente = null;
                let enviado = false;

                function pedirConfirmacao(indice, letra, texto) {
                    if (!jogoIniciado || enviado) return;
                    vibrar(35);
                    const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : true;
                    if (ativo) {
                        aplicarVolume(audioTecla, 0.7);
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
                    if (enviado) return;
                    const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : true;
                    if (ativo) {
                        aplicarVolume(audioTecla, 0.7);
                        audioTecla.currentTime = 0;
                        audioTecla.play().catch(() => {});
                    }
                    indicePendente = null;
                    const modal = document.getElementById('modalCerteza');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                function bloquearOpcoes(indiceEscolhido) {
                    document.querySelectorAll('.btn-alternativa').forEach(btn => {
                        btn.disabled = true;
                        btn.classList.add('cursor-not-allowed');
                        if (String(btn.dataset.indice) === String(indiceEscolhido)) {
                            btn.classList.add('opcao-bloqueada');
                            const tag = document.getElementById(`statusTag_${indiceEscolhido}`);
                            if (tag) {
                                tag.textContent = 'OPÇÃO BLOQUEADA...';
                                tag.className = 'shrink-0 text-[11px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg bg-amber-400 text-slate-950 animate-pulse';
                            }
                        } else {
                            btn.classList.add('opacity-50');
                        }
                    });
                }

                function revelarCoresOpcoes(indiceEscolhido, indiceCorreto, acertou) {
                    if (acertou) {
                        vibrar([60, 60, 120]);
                    } else {
                        vibrar([150, 80, 150]);
                    }
                    document.querySelectorAll('.btn-alternativa').forEach(btn => {
                        const idx = String(btn.dataset.indice);
                        btn.classList.remove('opcao-bloqueada', 'opacity-50');

                        const tag = document.getElementById(`statusTag_${idx}`);
                        const badge = document.getElementById(`badgeLetra_${idx}`);

                        if (idx === String(indiceCorreto)) {
                            btn.classList.add('opcao-correta-reveal');
                            if (badge) {
                                badge.className = 'sm-letter text-base lg:text-lg';
                            }
                            if (tag) {
                                tag.textContent = acertou ? 'CERTA RESPOSTA!' : 'CORRETA';
                                tag.className = 'shrink-0 text-[11px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg bg-white text-emerald-800 shadow';
                            }
                        } else if (indiceEscolhido !== null && idx === String(indiceEscolhido) && !acertou) {
                            btn.classList.add('opcao-errada-reveal');
                            if (badge) {
                                badge.className = 'sm-letter text-base lg:text-lg';
                            }
                            if (tag) {
                                tag.textContent = 'VOCÊ ERROU';
                                tag.className = 'shrink-0 text-[11px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg bg-white text-rose-800 shadow';
                            }
                        } else {
                            btn.classList.add('opacity-35');
                        }
                    });
                }

                async function confirmarRespostaFinal() {
                    if (enviado || indicePendente === null) return;
                    enviado = true;
                    clearInterval(timerInterval);
                    audioSuspense.pause();

                    const indiceEscolhido = indicePendente;
                    const modal = document.getElementById('modalCerteza');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');

                    // 1. Bloqueia a opção escolhida
                    bloquearOpcoes(indiceEscolhido);
                    const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : true;
                    if (ativo) {
                        aplicarVolume(audioTecla, 0.7);
                        audioTecla.currentTime = 0;
                        audioTecla.play().catch(() => {});
                    }

                    const tempoGasto = Math.max(0, tempoInicial - tempo);
                    await enviarRespostaAjax({
                        resposta: String(indiceEscolhido),
                        tempo_resposta: tempoGasto,
                        tempo_esgotado: 0
                    }, indiceEscolhido);
                }

                async function acionarTempoEsgotado() {
                    if (enviado) return;
                    enviado = true;
                    clearInterval(timerInterval);
                    audioSuspense.pause();
                    fecharConfirmacao();

                    bloquearOpcoes(null);

                    await enviarRespostaAjax({
                        resposta: '',
                        tempo_resposta: 60,
                        tempo_esgotado: 1
                    }, null);
                }

                async function enviarRespostaAjax(payload, indiceEscolhido) {
                    try {
                        const resp = await fetch("{{ route('game.answer') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await resp.json();

                        // Pausa dramática (1.3s) com a opção bloqueada antes de vir o resultado
                        setTimeout(async () => {
                            revelarCoresOpcoes(indiceEscolhido, data.indice_correto, !!data.acertou);

                            if (typeof data.erros_count !== 'undefined') {
                                const errEl = document.getElementById('errosContador');
                                if (errEl) errEl.textContent = data.erros_count;
                            }
                            if (typeof data.tempos_esgotados_count !== 'undefined') {
                                const tmpEl = document.getElementById('temposContador');
                                if (tmpEl) tmpEl.textContent = data.tempos_esgotados_count;
                            }

                            const painel = document.getElementById('painelResultadoInstantaneo');
                            if (painel) {
                                const isCerta = !!data.acertou;
                                painel.className = 'mt-6 rounded-2xl p-4 sm:p-5 border flex flex-wrap items-center justify-between gap-4 ' +
                                    (isCerta
                                        ? 'bg-emerald-950/85 border-emerald-400/60 text-emerald-100'
                                        : 'bg-rose-950/85 border-rose-400/60 text-rose-100');

                                let tituloMsg = isCerta
                                    ? `Certa Resposta! (+${data.pontos || 0} pontos)`
                                    : (data.tempo_esgotado
                                        ? `Tempo Esgotado! (${data.tempos_esgotados_count || 1}/3 tentativas de tempo)`
                                        : `Resposta Incorreta! (${data.erros_count || 1}/3 erros permitidos)`);

                                painel.innerHTML = `
                                    <div>
                                        <p class="font-black text-base sm:text-lg text-white">${tituloMsg}</p>
                                        ${!isCerta && data.resposta_correta ? `<p class="text-xs sm:text-sm mt-0.5">Alternativa correta: <strong class="text-emerald-300 underline">${data.resposta_correta}</strong></p>` : ''}
                                        ${data.referencia_biblica ? `<p class="text-xs text-amber-300 font-bold mt-1">Referência: ${data.referencia_biblica}</p>` : ''}
                                    </div>
                                    <a href="${data.redirect_url || '{{ route('game.play') }}'}"
                                       class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs sm:text-sm shadow-lg transition shrink-0">
                                        ${data.fim_de_jogo ? 'Ver Resultado Final →' : 'Próxima Pergunta →'}
                                    </a>
                                `;
                                painel.classList.remove('hidden');
                            }

                            // Aguarda o áudio de acerto/erro tocar completamente antes de avançar!
                            await tocarEfeitoComPromise(data.som_efeito);

                            // Se o usuário perdeu (3 erros ou 3 tempos esgotados), exibe a mensagem amigável e leva para /jogar/resultado
                            if (data.perdeu_partida) {
                                const modalFim = document.getElementById('modalFimAmigavel');
                                const txtMsg = document.getElementById('textoMensagemAmigavel');
                                const ptsFim = document.getElementById('pontosModalFim');
                                const btnRes = document.getElementById('btnIrParaResultado');

                                if (txtMsg && data.mensagem_amigavel) txtMsg.textContent = data.mensagem_amigavel;
                                if (ptsFim && typeof data.pontuacao_final !== 'undefined') ptsFim.textContent = `${data.pontuacao_final} pts`;
                                if (btnRes && data.redirect_url) btnRes.href = data.redirect_url;

                                if (modalFim) {
                                    modalFim.classList.remove('hidden');
                                    modalFim.classList.add('flex');
                                }
                                return;
                            }

                            // Pausa extra de leitura (1.8s após o fim do áudio) antes de ir para a próxima pergunta
                            setTimeout(() => {
                                window.location.href = data.redirect_url || "{{ route('game.play') }}";
                            }, 1800);
                        }, 1300);
                    } catch (e) {
                        document.getElementById('respostaSelecionada').value = indiceEscolhido ?? '';
                        document.getElementById('tempoEsgotadoInput').value = indiceEscolhido === null ? '1' : '0';
                        document.getElementById('respostaForm').submit();
                    }
                }

                function abrirEscadaPremios() {
                    const m = document.getElementById('modalEscadaPremios');
                    if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
                    vibrar(25);
                }
                function fecharEscadaPremios() {
                    const m = document.getElementById('modalEscadaPremios');
                    if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
                }
                function abrirCartasMobile() {
                    const m = document.getElementById('modalCartasMobile');
                    if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
                    vibrar(30);
                }
                function fecharCartasMobile() {
                    const m = document.getElementById('modalCartasMobile');
                    if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
                }
                function abrirConvidadosMobile() {
                    const m = document.getElementById('modalConvidadosMobile');
                    if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
                    vibrar(30);
                }
                function fecharConvidadosMobile() {
                    const m = document.getElementById('modalConvidadosMobile');
                    if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
                }

                // Temporizador de 60 segundos (aguarda caso o modal de nome esteja aberto)
                let tempo = {{ max(1, (int) $tempo_restante) }};
                const tempoInicial = tempo;
                const timerEl = document.getElementById('timer');
                const hiddenTempo = document.getElementById('tempo_resposta');

                let timerInterval = setInterval(() => {
                    if (!jogoIniciado) return;
                    tempo--;
                    document.querySelectorAll('.js-timer').forEach(el => {
                        el.textContent = Math.max(0, tempo);
                    });
                    const timerMob = document.getElementById('timerMobileBox');
                    const box = document.getElementById('timerContainerBox');
                    if (tempo <= 10) {
                        if (timerEl) timerEl.classList.add('timer-warning');
                        if (box) box.classList.add('timer-box-warning');
                        if (timerMob) timerMob.classList.add('is-critical');
                        tocarSomContagemRegressiva(tempo);
                    }
                    if (hiddenTempo) hiddenTempo.value = Math.max(0, tempoInicial - tempo);

                    if (tempo <= 0 && !enviado) {
                        acionarTempoEsgotado();
                    }
                }, 1000);

                function tocarSomContagemRegressiva(segundos) {
                    if (segundos > 10 || segundos < 0) return;
                    const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
                    if (!ativo) return;

                    try {
                        if (segundos > 0) {
                            vibrar(25);
                            const audioNome = segundos <= 3 ? 'tempo_alerta.wav' : 'tempo_tick.wav';
                            const fx = new Audio(`${BASE_AUDIO_URL}/${audioNome}`);
                            fx.volume = typeof obterVolumeGlobal === 'function' ? Math.min(1, obterVolumeGlobal() * 1.1) : 0.85;
                            fx.play().catch(() => {});
                        } else if (segundos === 0) {
                            vibrar([50, 50, 100]);
                            const fx = new Audio(`${BASE_AUDIO_URL}/ks.wav`);
                            fx.volume = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85;
                            fx.play().catch(() => {});
                        }
                    } catch (e) {}
                }
            </script>
        @endif

    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
