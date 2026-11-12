<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f59e0b">
    <title>Bíblia Show — Palco do Jogo</title>
    <link rel="icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sm-box-subtle {
            background: rgba(11, 26, 77, 0.7);
            border: 1.5px solid rgba(147, 197, 253, 0.35);
            backdrop-filter: blur(8px);
        }
        .opcao-bloqueada {
            background: linear-gradient(180deg, #fbbf24 0%, #d97706 60%, #92400e 100%) !important;
            border-color: #fff3c4 !important;
            color: #1c0f00 !important;
            box-shadow: 0 0 24px rgba(251, 191, 36, .7) !important;
            animation: sm-blink 0.7s ease-in-out infinite alternate;
        }
        .opcao-correta-reveal {
            background: linear-gradient(180deg, #34d399 0%, #059669 60%, #065f46 100%) !important;
            border-color: #a7f3d0 !important;
            box-shadow: 0 0 28px rgba(16, 185, 129, .75) !important;
            animation: none;
        }
        .opcao-errada-reveal {
            background: linear-gradient(180deg, #fb7185 0%, #e11d48 60%, #881337 100%) !important;
            border-color: #fecdd3 !important;
            box-shadow: 0 0 28px rgba(244, 63, 94, .75) !important;
            animation: none;
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
            background: rgba(225, 29, 72, 0.25) !important;
            box-shadow: 0 0 24px rgba(244, 63, 94, 0.5) !important;
        }
    </style>
</head>
<body class="sm-stage min-h-screen text-slate-100 flex flex-col justify-between">

    @include('partials.navbar', ['dark' => true, 'hideJogarAgora' => true, 'hideOnMobile' => false])

    <div class="max-w-7xl mx-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 flex-1">

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
            <div class="mb-4 rounded-2xl p-4 bg-amber-500/20 border-2 border-amber-400 text-amber-200 text-sm flex items-center gap-3 shadow-xl backdrop-blur-md">
                <svg class="w-5 h-5 text-amber-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <span class="font-bold">{{ $ajudaFeedback['mensagem'] }}</span>
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

            <!-- ===================== HUD MOBILE (Show do Milhão Bíblico) ===================== -->
            <div class="lg:hidden flex items-center justify-between gap-2 mb-3 sm-panel p-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border-2 border-amber-200 text-slate-950 font-black text-base shrink-0 shadow-md">
                        {{ mb_strtoupper(mb_substr($jogador_nome ?? 'P', 0, 1)) }}
                    </div>
                    <div class="min-w-0 leading-tight">
                        <p class="text-xs font-black text-white truncate max-w-[120px]">{{ $jogador_nome ?? 'Participante' }}</p>
                        <div class="flex items-center gap-1.5 mt-1" title="Erros cometidos">
                            <span class="text-[9px] uppercase font-bold text-blue-200">Erros:</span>
                            @for($i = 1; $i <= ($max_erros ?? 3); $i++)
                                <span class="w-2.5 h-2.5 rounded-full {{ $i <= ($erros_count ?? 0) ? 'bg-rose-500 shadow-sm shadow-rose-500/80 border border-rose-300' : 'bg-blue-950 border border-blue-400/30' }}"></span>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" onclick="abrirEscadaPremios()" class="sm-lifeline !w-9 !h-9 flex items-center justify-center" title="Escada de Prêmios">
                        <svg class="w-4 h-4 text-amber-300 fill-current" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1c1.94-.37 3.5-1.99 3.61-3.96C19.08 11.63 21 9.55 21 7V5c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>
                    </button>
                    <button type="button" onclick="typeof abrirModalDefinicoes === 'function' && abrirModalDefinicoes()" class="sm-lifeline !w-9 !h-9 flex items-center justify-center" title="Definições e Som">
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </button>
                    <button type="button" onclick="abrirModalConfirmarParar()" class="sm-lifeline !w-9 !h-9 flex items-center justify-center !border-rose-400" title="Parar e ver resultado">
                        <svg class="w-4 h-4 text-rose-300" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h12v12H6z"/></svg>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

                <!-- ======================================================== -->
                <!-- SIDEBAR ESQUERDA: PARTICIPANTE, VIDAS, PLACAR E JORNADA  -->
                <!-- ======================================================== -->
                <aside class="hidden lg:block lg:col-span-4 xl:col-span-4 space-y-4">
                    <!-- 1. Card do Participante no Palco -->
                    <div class="sm-panel p-5 shadow-2xl">
                        <div class="flex items-center gap-3.5 mb-4">
                            <div id="sidebarJogadorAvatar" class="w-14 h-14 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border-2 border-amber-200 text-slate-950 font-black text-2xl shrink-0 shadow-xl shadow-amber-500/30">
                                {{ mb_strtoupper(mb_substr($jogador_nome ?? 'P', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black uppercase tracking-widest text-blue-200">PARTICIPANTE NO PALCO</p>
                                <h3 id="sidebarJogadorNome" class="font-black text-white text-xl truncate leading-tight mt-0.5 sm-logo-title">{{ $jogador_nome ?? 'Participante' }}</h3>
                                <button type="button" onclick="abrirModalNomeJogador()" class="text-xs font-bold text-amber-300 hover:text-amber-200 hover:underline inline-flex items-center gap-1.5 mt-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                    <span>(Trocar nome)</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5 pt-3 border-t border-blue-400/30">
                            <a href="{{ route('game.play', ['novo' => 1]) }}"
                               class="inline-flex items-center justify-center gap-2 text-xs bg-blue-900/80 hover:bg-blue-800 text-blue-100 py-2.5 px-3 rounded-xl font-bold border border-blue-400/40 transition shadow">
                                <svg class="w-3.5 h-3.5 text-amber-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                </svg>
                                <span>Reiniciar</span>
                            </a>

                            <button type="button"
                                    onclick="abrirModalConfirmarParar()"
                                    class="w-full inline-flex items-center justify-center gap-2 text-xs bg-rose-600/30 hover:bg-rose-600/40 text-rose-200 border border-rose-400/50 py-2.5 px-3 rounded-xl font-bold transition shadow">
                                <svg class="w-3.5 h-3.5 text-rose-300 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <rect x="6" y="6" width="12" height="12" rx="2"/>
                                </svg>
                                <span>Parar</span>
                            </button>
                        </div>
                    </div>

                    <!-- 2. Card de Sobrevivência: Vidas & Tentativas de Tempo -->
                    <div class="sm-panel p-5 shadow-2xl space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-xl sm-box-subtle">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-rose-500/20 border border-rose-400/40 flex items-center justify-center text-rose-300 shrink-0 shadow-sm">
                                    <svg class="w-4 h-4 text-rose-400 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-200">VIDAS (MÁX. 3 ERROS)</p>
                                    <p class="text-xs font-bold text-white">Erros cometidos: <strong id="errosContador" class="text-rose-300 font-black">{{ $erros_count ?? 0 }}</strong> / {{ $max_erros ?? 3 }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5" id="coracoesVidas">
                                @for($i = 1; $i <= ($max_erros ?? 3); $i++)
                                    <span class="w-3.5 h-3.5 rounded-full {{ $i <= ($erros_count ?? 0) ? 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.85)] border border-rose-200' : 'bg-blue-950 border border-blue-400/40' }}"></span>
                                @endfor
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl sm-box-subtle">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-400/40 flex items-center justify-center text-amber-300 shrink-0 shadow-sm">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-200">TENTATIVAS DE TEMPO (MÁX. 3)</p>
                                    <p class="text-xs font-bold text-white">Tempos esgotados: <strong id="temposContador" class="text-amber-300 font-black">{{ $tempos_esgotados_count ?? 0 }}</strong> / {{ $max_tempos_esgotados ?? 3 }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                @for($i = 1; $i <= ($max_tempos_esgotados ?? 3); $i++)
                                    <span class="w-3.5 h-3.5 rounded-full {{ $i <= ($tempos_esgotados_count ?? 0) ? 'bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.85)] border border-amber-100' : 'bg-blue-950 border border-blue-400/40' }}"></span>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- 3. Lingotes de Premiação: ERRAR | PARAR | ACERTAR -->
                    <div class="sm-panel p-5 shadow-2xl space-y-2.5">
                        <div class="text-center mb-1">
                            <span class="sm-pill !text-[11px] !py-0.5 !px-3 font-black">PREMIAÇÃO DA RODADA</span>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-2">
                            <div class="sm-ingot is-errar !p-2.5">
                                <div class="sm-ingot-label">ERRAR</div>
                                <div class="sm-ingot-value !text-lg mt-0.5">
                                    {{ $placar_milhao['errar'] ?? 0 }} <span class="text-[10px]">pts</span>
                                </div>
                            </div>

                            <div class="sm-ingot !p-2.5">
                                <div class="sm-ingot-label">PARAR</div>
                                <div class="sm-ingot-value !text-lg mt-0.5">
                                    {{ $pontuacao }} <span class="text-[10px]">pts</span>
                                </div>
                            </div>

                            <div class="sm-ingot is-acertar !p-2.5">
                                <div class="sm-ingot-label">ACERTAR</div>
                                <div class="sm-ingot-value !text-lg mt-0.5">
                                    {{ $placar_milhao['acertar'] ?? ($pontuacao + 10) }} <span class="text-[10px]">pts</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Card de Fases e Progresso do Bíblia Show (1ª a 4ª Fase) -->
                    <div class="sm-panel p-5 shadow-2xl">
                        <div class="flex items-center justify-between mb-3.5 pb-2 border-b border-blue-400/20">
                            <span class="text-[10px] font-black uppercase tracking-widest text-blue-200">JORNADA DAS 4 FASES</span>
                            <span class="text-xs font-black text-amber-300">Pergunta {{ $numero_pergunta }} de {{ $total_perguntas }}</span>
                        </div>

                        <div class="space-y-2">
                            @php
                                $fasesInfo = [
                                    1 => ['nome' => '1ª Fase • Fácil', 'perguntas' => '1 a 6'],
                                    2 => ['nome' => '2ª Fase • Médio', 'perguntas' => '7 a 12'],
                                    3 => ['nome' => '3ª Fase • Difícil', 'perguntas' => '13 a 18'],
                                    4 => ['nome' => '4ª Fase • Pergunta do Milhão', 'perguntas' => '19 a 24'],
                                ];
                            @endphp
                            @foreach($fasesInfo as $fNum => $fInfo)
                                @php
                                    $isFaseAtual = ($fase_atual ?? 1) == $fNum;
                                    $isPassou = ($fase_atual ?? 1) > $fNum;
                                @endphp
                                <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition {{ $isFaseAtual ? 'bg-amber-400/25 border-2 border-amber-300 text-amber-200 font-black shadow-lg shadow-amber-500/20' : ($isPassou ? 'bg-blue-950/60 text-slate-300 font-semibold border border-blue-400/20' : 'bg-blue-950/30 text-blue-300/50 border border-transparent') }}">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black {{ $isFaseAtual ? 'bg-amber-400 text-slate-950 shadow' : ($isPassou ? 'bg-emerald-400 text-slate-950' : 'bg-blue-900 text-blue-300') }}">
                                            @if($isPassou)
                                                ✓
                                            @else
                                                {{ $fNum }}
                                            @endif
                                        </span>
                                        <span>{{ $fInfo['nome'] }}</span>
                                    </div>
                                    <span class="text-[11px] font-bold opacity-90">{{ $fInfo['perguntas'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <!-- ======================================================== -->
                <!-- ARENA CENTRAL DO JOGO: TEMPORIZADOR, PERGUNTA E AJUDAS   -->
                <!-- ======================================================== -->
                <main class="lg:col-span-8 xl:col-span-8 space-y-4 sm:space-y-5">
                    <!-- Barra de Destaque Superior: Pergunta & Cronômetro -->
                    <div class="sm-panel p-4 sm:p-5 shadow-2xl flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border-2 border-amber-200 text-slate-950 font-black text-xl shrink-0 shadow-lg shadow-amber-500/30">
                                {{ $numero_pergunta }}
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-black uppercase tracking-widest text-amber-300">PERGUNTA {{ $numero_pergunta }} DE {{ $total_perguntas }}</span>
                                    <span class="sm-pill !px-2.5 !py-0.5 !text-[10px]">FASE {{ $fase_atual ?? 1 }}</span>
                                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full bg-blue-900/80 text-blue-200 border border-blue-400/40">NV. {{ $pergunta['dificuldade_nivel'] ?? 1 }}</span>
                                    @if($sequencia_acertos > 0)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black px-2.5 py-0.5 rounded-full bg-emerald-500/25 text-emerald-300 border border-emerald-400/40 shadow-sm">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            {{ $sequencia_acertos }} seguidos
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs sm:text-sm font-bold text-white mt-1">
                                    {{ $pergunta['livro_nome'] ?? 'Escrituras Sagradas' }}
                                    @if(!empty($pergunta['categoria_nome']))
                                        <span class="text-blue-200 font-normal">• {{ $pergunta['categoria_nome'] }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Temporizador Destacado (Estilo Show do Milhão Oficial) -->
                        <div id="timerContainerBox" class="sm-box-subtle px-4 py-2.5 rounded-2xl shadow-inner flex items-center gap-3 transition-all">
                            <div class="text-right">
                                <p class="text-[9px] font-black uppercase tracking-widest text-blue-200">TEMPO RESTANTE</p>
                                <p class="text-2xl sm:text-3xl font-black font-mono text-white leading-none tracking-tight">
                                    <span id="timer" class="js-timer">{{ $tempo_restante }}</span><span class="text-sm font-bold text-amber-400 ml-0.5">s</span>
                                </p>
                            </div>
                            <div id="timerIconBox" class="w-10 h-10 rounded-xl bg-amber-500/20 border-2 border-amber-400/60 text-amber-300 flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Card Principal da Pergunta (sm-panel com sm-question azul vibrante) -->
                    <div class="sm-panel p-5 sm:p-7 shadow-2xl relative">
                        <!-- Badges Bíblicos (Livro, Categoria, #Código) -->
                        <div class="flex flex-wrap items-center justify-start gap-2 mb-4">
                            @if(!empty($pergunta['livro_nome']))
                                <span class="inline-flex items-center gap-1.5 text-xs font-black px-3.5 py-1 rounded-full bg-amber-400/20 text-amber-300 border border-amber-300/40 shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                    <span>{{ $pergunta['livro_nome'] }}</span>
                                </span>
                            @endif
                            @if(!empty($pergunta['categoria_nome']))
                                <span class="text-xs font-bold px-3.5 py-1 rounded-full bg-blue-900/80 text-blue-200 border border-blue-400/40">
                                    {{ $pergunta['categoria_nome'] }}
                                </span>
                            @endif
                            @if(!empty($pergunta['codigo_classico']) || !empty($pergunta['id']))
                                <span class="text-xs font-mono font-bold px-3 py-1 rounded-full sm-box-subtle text-amber-300/90">
                                    #{{ $pergunta['codigo_classico'] ?? $pergunta['id'] }}
                                </span>
                            @endif
                        </div>

                        <!-- Card de Pergunta Oficial Show do Milhão (.sm-question) -->
                        <div class="sm-question p-6 sm:p-8 mb-5 sm:mb-6 min-h-[130px] sm:min-h-[150px] flex items-center justify-center text-center">
                            <p class="sm-question-text text-lg sm:text-4xl font-black text-white leading-relaxed tracking-tight select-none">
                                {{ $pergunta['texto'] ?? $pergunta->texto }}
                            </p>
                        </div>

                        <!-- Dica Bíblica (Referência) quando acionada -->
                        @if($dicaBiblica)
                            <div class="mb-4 rounded-2xl p-4 bg-blue-950/80 border-2 border-blue-400 text-blue-100 flex items-center gap-3.5 shadow-lg">
                                <div class="w-10 h-10 rounded-xl bg-blue-500 text-slate-950 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-black uppercase tracking-wider text-amber-300">Dica de Referência Bíblica</p>
                                    <p class="text-sm sm:text-base font-bold text-white">{{ $dicaBiblica }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Resposta do Convidado Bíblico quando acionada -->
                        @if($dicaConvidado)
                            <div class="mb-4 rounded-2xl p-4 border-2 {{ !empty($dicaConvidado['sabe_resposta']) ? 'bg-amber-500/20 border-amber-300 text-amber-100' : 'bg-blue-950/80 border-blue-400/50 text-blue-200' }} shadow-lg">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 text-slate-950 font-black flex items-center justify-center shrink-0 shadow-md">
                                        <svg class="w-5 h-5 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wider font-black text-amber-300">
                                            Personagem Bíblico Consultado: {{ $dicaConvidado['convidado'] }}
                                        </p>
                                        <p class="text-sm sm:text-base mt-1 font-bold">
                                            {{ $dicaConvidado['mensagem'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- MOBILE: Ajudas circulares compactas -->
                        <div class="lg:hidden flex items-center justify-center gap-3 mb-4">
                            <button type="button" onclick="abrirCartasMobile()" id="btnLifelineCartasMobile" class="sm-lifeline" title="Cartas: eliminar 2 alternativas" @if($cartasUsada) disabled @endif>
                                −2
                                <span class="sm-badge" id="badgeCartasCountMobile">{{ $cartasUsada ? 0 : 1 }}</span>
                            </button>
                            <button type="button" onclick="abrirConvidadosMobile()" class="sm-lifeline flex items-center justify-center" title="Convidados bíblicos" @if($convidadosUsada || $convidadosRestantes <= 0) disabled @endif>
                                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                <span class="sm-badge">{{ $convidadosRestantes }}</span>
                            </button>
                            <form action="{{ route('game.lifeline') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="tipo" value="dica">
                                <button type="submit" class="sm-lifeline flex items-center justify-center" title="Dica bíblica" @if($dicasRestantes <= 0 || $dicaBiblica) disabled @endif>
                                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
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

                        <!-- Alternativas A, B, C, D (Pílulas Azuis Safira do Show do Milhão) -->
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4" id="gridAlternativas">
                                @foreach($opcoesArray as $indice => $opcao)
                                    @php
                                        $eliminada = in_array((int) $indice, $opcoesEliminadas, true);
                                        $letra = $letras[$indice] ?? ($indice + 1);
                                    @endphp
                                    @if($eliminada)
                                        <div class="sm-option is-eliminada select-none">
                                            <span class="flex items-center gap-2.5 min-w-0">
                                                <span class="sm-letter">{{ $letra }}:</span>
                                                <span class="text-blue-200/50 font-bold text-sm lg:text-base">{{ $opcao }}</span>
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
                        <div id="painelResultadoInstantaneo" class="hidden mt-6 rounded-2xl p-4 sm:p-5 border-2 transition-all"></div>
                    </div>

                    <!-- MOBILE: Lingotes ERRAR | PARAR | ACERTAR -->
                    <div class="lg:hidden grid grid-cols-3 gap-2 pt-1">
                        <div class="sm-ingot is-errar">
                            <div class="sm-ingot-label">Errar</div>
                            <div class="sm-ingot-value">{{ $placar_milhao['errar'] ?? 0 }}</div>
                        </div>
                        <div class="sm-ingot">
                            <div class="sm-ingot-label">Parar</div>
                            <div class="sm-ingot-value">{{ $pontuacao }}</div>
                        </div>
                        <div class="sm-ingot is-acertar">
                            <div class="sm-ingot-label">Acertar</div>
                            <div class="sm-ingot-value">{{ $placar_milhao['acertar'] ?? ($pontuacao + 10) }}</div>
                        </div>
                    </div>

                    <!-- Faixa Vermelha Show do Milhão no mobile -->
                    <div class="lg:hidden mt-2 sm-banner-red py-2 px-3 text-center rounded-xl text-xs uppercase tracking-wider shadow-md">
                        Não sabe a resposta? Use ajuda!
                    </div>

                    <!-- Área Destacada de Dicas & Ajudas do Clássico Bíblia Show -->
                    <div class="hidden lg:block sm-panel p-5 sm:p-6 shadow-2xl">
                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-blue-400/20">
                            <h2 class="text-xs sm:text-sm font-black uppercase tracking-widest text-amber-300 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-400 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <span>AJUDAS E DICAS DO CLÁSSICO BÍBLIA SHOW</span>
                            </h2>
                            <span class="sm-pill !text-[10px] !py-0.5 !px-3 font-bold">3 Dicas • 3 Convidados • Cartas • 3 Pulos</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <!-- 1. Dica Bíblica -->
                            <div class="sm-box-subtle rounded-2xl p-4 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-black text-blue-200">
                                            <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            Dica Bíblica
                                        </span>
                                        <span class="text-[11px] font-black px-2 py-0.5 rounded-full {{ $dicasRestantes <= 0 ? 'bg-blue-950 text-blue-400/50' : 'bg-blue-400/20 text-blue-200 border border-blue-400/30' }}">
                                            {{ $dicasRestantes }}/3
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-blue-200/80 mb-3">Mostra o livro e capítulo bíblico onde está a resposta.</p>
                                </div>
                                <form action="{{ route('game.lifeline') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tipo" value="dica">
                                    <button type="submit"
                                            onmouseenter="tocarHover()"
                                            @if($dicasRestantes <= 0 || $dicaBiblica) disabled @endif
                                            class="w-full py-2.5 rounded-xl text-xs font-black transition {{ ($dicasRestantes <= 0 || $dicaBiblica) ? 'bg-blue-950/60 text-blue-400/40 cursor-not-allowed border border-blue-400/20' : 'bg-blue-600/40 hover:bg-blue-600 text-white border-2 border-blue-300 shadow-lg' }}">
                                        {{ $dicaBiblica ? 'Dica Ativa' : ($dicasRestantes <= 0 ? 'Esgotadas' : 'Ler Dica Bíblica') }}
                                    </button>
                                </form>
                            </div>

                            <!-- 2. Personagens Bíblicos -->
                            <div class="sm-box-subtle rounded-2xl p-4 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-black text-amber-300">
                                            <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Convidados
                                        </span>
                                        <span class="text-[11px] font-black px-2 py-0.5 rounded-full {{ $convidadosRestantes <= 0 ? 'bg-blue-950 text-blue-400/50' : 'bg-amber-400/20 text-amber-300 border border-amber-400/30' }}">
                                            {{ $convidadosRestantes }}/3
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-blue-200/80 mb-2">Consulte personagens do relato bíblico:</p>
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
                                                        class="w-full px-1.5 py-2 rounded-xl bg-amber-500/25 hover:bg-amber-400 text-amber-200 hover:text-slate-950 border border-amber-300/40 text-[11px] font-black truncate transition shadow"
                                                        title="Consultar {{ $nomeConvidado }}">
                                                    {{ $nomeConvidado }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="py-2 rounded-xl bg-blue-950/60 text-blue-300/60 text-center text-xs font-bold border border-blue-400/20">
                                        {{ $dicaConvidado ? 'Consultado nesta pergunta' : 'Ajudas esgotadas' }}
                                    </div>
                                @endif
                            </div>

                            <!-- 3. Eliminar 2 Alternativas (Cartas) -->
                            <div class="sm-box-subtle rounded-2xl p-4 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-black text-emerald-300">
                                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <rect x="4" y="4" width="16" height="16" rx="2"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h6v6H9z"/>
                                            </svg>
                                            Cartas (50/50)
                                        </span>
                                        <span class="text-[11px] font-black px-2 py-0.5 rounded-full {{ $cartasUsada ? 'bg-blue-950 text-blue-400/50' : 'bg-emerald-400/20 text-emerald-300 border border-emerald-400/30' }}">
                                            {{ $cartasUsada ? '0/1' : '1/1' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-blue-200/80 mb-3">Remove duas alternativas incorretas da tela.</p>
                                </div>
                                <form action="{{ route('game.lifeline') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tipo" value="cartas">
                                    <button type="submit"
                                            onmouseenter="tocarHover()"
                                            @if($cartasUsada) disabled @endif
                                            class="w-full py-2.5 rounded-xl text-xs font-black transition {{ $cartasUsada ? 'bg-blue-950/60 text-blue-400/40 cursor-not-allowed border border-blue-400/20' : 'bg-emerald-600/40 hover:bg-emerald-500 text-white border-2 border-emerald-300 shadow-lg' }}">
                                        {{ $cartasUsada ? 'Já Utilizada' : 'Eliminar 2 Opções' }}
                                    </button>
                                </form>
                            </div>

                            <!-- 4. Pular Pergunta -->
                            <div class="sm-box-subtle rounded-2xl p-4 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-black text-purple-300">
                                            <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                            </svg>
                                            Pular Pergunta
                                        </span>
                                        <span class="text-[11px] font-black px-2 py-0.5 rounded-full {{ $pulosRestantes <= 0 ? 'bg-blue-950 text-blue-400/50' : 'bg-purple-400/20 text-purple-300 border border-purple-400/30' }}">
                                            {{ $pulosRestantes }}/3
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-blue-200/80 mb-3">Sorteia outra pergunta sem perder pontuação.</p>
                                </div>
                                <form action="{{ route('game.lifeline') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tipo" value="pulo">
                                    <button type="submit"
                                            onmouseenter="tocarHover()"
                                            @if($pulosRestantes <= 0) disabled @endif
                                            class="w-full py-2.5 rounded-xl text-xs font-black transition {{ $pulosRestantes <= 0 ? 'bg-blue-950/60 text-blue-400/40 cursor-not-allowed border border-blue-400/20' : 'bg-purple-600/40 hover:bg-purple-500 text-white border-2 border-purple-300 shadow-lg' }}">
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
                    <div class="sm-panel-title px-4 py-3 text-center flex flex-col items-center justify-center">
                        <svg class="w-7 h-7 text-amber-300 fill-current mb-1" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1c1.94-.37 3.5-1.99 3.61-3.96C19.08 11.63 21 9.55 21 7V5c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>
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

            <!-- ===== MODAL: Cartas (eliminar 2 alternativas) ===== -->
            <div id="modalCartasMobile" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md items-center justify-center p-4" onclick="if(event.target===this) fecharCartasMobile()">
                <div class="sm-panel w-full max-w-sm text-center relative overflow-hidden border-2 border-amber-300 shadow-2xl">
                    <div class="sm-panel-title px-4 py-3 text-center flex items-center justify-between">
                        <span class="text-xs uppercase tracking-widest font-black text-amber-300 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                            AJUDA DAS CARTAS
                        </span>
                        <button type="button" onclick="fecharCartasMobile()" class="text-slate-300 hover:text-white text-base">✕</button>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-black text-blue-100 mb-4">Escolha uma das 4 cartas para virar e eliminar 2 opções incorretas:</p>
                        <form action="{{ route('game.lifeline') }}" method="POST" id="formCartasMobile">
                            @csrf
                            <input type="hidden" name="tipo" value="cartas">
                            <div class="grid grid-cols-2 gap-3.5 max-w-[260px] mx-auto">
                                @for($c = 0; $c < 4; $c++)
                                    <button type="button" onclick="virarCartaEscolhida(this)" class="sm-card-back group relative overflow-hidden focus:outline-none cursor-pointer" title="Virar Carta {{ $c + 1 }}">
                                        <div class="card-inner w-full h-full flex flex-col items-center justify-center pointer-events-none">
                                            <span class="text-2xl font-black text-amber-300 drop-shadow-md">✝</span>
                                            <span class="text-[9px] font-black uppercase tracking-wider text-amber-200 mt-1">BÍBLIA</span>
                                        </div>
                                    </button>
                                @endfor
                            </div>
                        </form>
                        <button type="button" onclick="fecharCartasMobile()" class="sm-cta mt-5 !py-2.5 text-xs font-black">Voltar ao Jogo</button>
                    </div>
                </div>
            </div>

            <!-- ===== MOBILE: Convidados Bíblicos ===== -->
            <div id="modalConvidadosMobile" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm items-center justify-center p-4" onclick="if(event.target===this) fecharConvidadosMobile()">
                <div class="sm-panel w-full max-w-xs">
                    <div class="sm-panel-title px-4 py-2.5 text-center font-black text-white tracking-wider">CONVIDADOS BÍBLICOS</div>
                    <div class="p-4 space-y-2.5">
                        <p class="text-center text-xs font-black text-blue-100">Quem você quer consultar?</p>
                        @foreach($convidadosBiblicos as $idx => $nomeConvidado)
                            <form action="{{ route('game.lifeline') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tipo" value="convidado">
                                <input type="hidden" name="convidado_indice" value="{{ $idx }}">
                                <button type="submit" class="sm-option" onclick="vibrar(30)">
                                    <span class="flex items-center gap-2.5">
                                        <span class="w-8 h-8 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border border-amber-200 text-slate-950 font-black text-xs">{{ mb_strtoupper(mb_substr($nomeConvidado, 0, 1)) }}</span>
                                        <span>{{ $nomeConvidado }}</span>
                                    </span>
                                    <span class="text-amber-300 text-xs font-black">›</span>
                                </button>
                            </form>
                        @endforeach
                        <button type="button" onclick="fecharConvidadosMobile()" class="sm-cta !py-2.5 text-sm">Voltar</button>
                    </div>
                </div>
            </div>

            <!-- Modal 0: Continuar Como [Nome] ou Trocar de Participante -->
            <div id="modalContinuarComo" class="fixed inset-0 z-50 {{ !empty($perguntar_continuar) ? 'flex' : 'hidden' }} items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
                <div class="sm-panel max-w-md w-full p-6 sm:p-8 text-center relative text-white shadow-2xl">
                    <div class="inline-flex items-center justify-center mb-3">
                        <span class="sm-pill text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-950 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <span>Arena Bíblia Show</span>
                        </span>
                    </div>
                    <div class="w-16 h-16 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border-2 border-amber-200 shadow-xl shadow-amber-500/30 text-amber-950 mx-auto mb-3">
                        <svg class="w-8 h-8 text-amber-950" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black text-white sm-logo-title mb-2">Continuar Partida?</h3>
                    <p class="text-xs sm:text-sm text-blue-200 mb-6 leading-relaxed">
                        Você deseja continuar jogando como <strong class="text-amber-300 text-base font-black">{{ $jogador_nome }}</strong> ou deseja entrar com outro nome?
                    </p>
                    <div class="space-y-3">
                        <button type="button" id="btnContinuarComoAtual" onclick="confirmarContinuarComo()"
                                class="sm-cta !py-3.5 !text-sm cursor-pointer">
                            Continuar como {{ $jogador_nome }} →
                        </button>
                        <button type="button" onclick="trocarParaNovoParticipante()"
                                class="w-full py-3 px-5 rounded-full bg-blue-900/60 hover:bg-blue-800 text-blue-200 font-bold text-xs uppercase tracking-wider border-2 border-blue-400/40 transition">
                            Entrar com Outro Nome / Participante
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal 1: Identificação Clássica do Jogador ao Iniciar -->
            <div id="modalNomeJogador" class="fixed inset-0 z-50 {{ !empty($precisa_identificacao) ? 'flex' : 'hidden' }} items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
                <div class="sm-panel max-w-md w-full p-6 sm:p-8 text-center relative text-white shadow-2xl">
                    <div class="inline-flex items-center justify-center mb-3">
                        <span class="sm-pill text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-950 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <span>Bíblia Show Clássico</span>
                        </span>
                    </div>
                    <div class="w-16 h-16 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border-2 border-amber-200 shadow-xl shadow-amber-500/30 text-amber-950 mx-auto mb-3">
                        <svg class="w-8 h-8 text-amber-950" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black text-white sm-logo-title mb-2">Qual é o seu nome?</h3>
                    <p class="text-xs sm:text-sm text-blue-200 mb-5 leading-relaxed">
                        Seu nome aparecerá no palco e será registrado no <strong class="text-amber-300 font-black">Ranking Geral do Bíblia Show</strong> ao final da partida!
                    </p>
                    <form id="formDefinirJogador" action="{{ route('game.player') }}" method="POST" onsubmit="submeterNomeJogador(event)" class="space-y-4">
                        @csrf
                        <input type="text" name="jogador_nome" id="inputNomeJogadorModal" required maxlength="60"
                               value="{{ ($jogador_nome ?? '') !== 'Participante' ? ($jogador_nome ?? '') : '' }}"
                               placeholder="Digite seu nome ou apelido..."
                               class="w-full px-4 py-3.5 rounded-xl bg-blue-950/90 border-2 border-blue-400/60 text-white font-bold text-center text-base focus:border-amber-400 focus:ring-2 focus:ring-amber-400 outline-none placeholder-blue-300/40 shadow-inner">
                        <button type="submit" id="btnIniciarPartidaModal"
                                class="sm-cta !py-3.5 !text-sm cursor-pointer">
                            Começar Partida! →
                        </button>
                    </form>
                </div>
            </div>

            <!-- Modal 2: "Você tem certeza?" (Estilo Show do Milhão) -->
            <div id="modalCerteza" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
                <div class="sm-panel max-w-md w-full p-6 sm:p-8 text-center relative text-white shadow-2xl">
                    <div class="inline-flex items-center justify-center mb-2">
                        <span class="sm-pill text-xs uppercase tracking-wider">RESPOSTA FINAL</span>
                    </div>
                    <div class="w-16 h-16 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border-2 border-amber-200 shadow-xl shadow-amber-500/30 text-amber-950 mx-auto mb-3">
                        <svg class="w-8 h-8 text-amber-950" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black text-white sm-logo-title mb-2">Você tem certeza?</h3>
                    <p class="text-xs sm:text-sm text-blue-200 mb-5">
                        Alternativa escolhida:
                        <span id="modalAlternativaTexto" class="block mt-2 p-3 rounded-2xl bg-blue-950/90 border-2 border-amber-300 text-amber-300 font-black text-base shadow-inner"></span>
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="fecharConfirmacao()"
                                class="py-3 px-4 rounded-full bg-blue-900/70 hover:bg-blue-800 text-blue-200 font-extrabold text-xs uppercase tracking-wider border-2 border-blue-400/40 transition">
                            NÃO, VOLTAR
                        </button>
                        <button type="button" onclick="confirmarRespostaFinal()"
                                class="sm-cta !py-3 !text-xs cursor-pointer">
                            SIM, CONFIRMAR!
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal 3: Mensagem Amigável de Fim de Jogo -->
            <div id="modalFimAmigavel" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
                <div class="sm-panel max-w-lg w-full p-6 sm:p-8 text-center relative text-white shadow-2xl">
                    <div class="inline-flex items-center justify-center mb-2">
                        <span class="sm-pill text-xs uppercase tracking-wider">FIM DA JORNADA</span>
                    </div>
                    <div class="w-16 h-16 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border-2 border-amber-200 shadow-xl shadow-amber-500/30 text-amber-950 mx-auto mb-3">
                        <svg class="w-8 h-8 text-amber-950" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                        </svg>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black text-white sm-logo-title mb-2">Valeu pela participação, {{ $jogador_nome ?? 'Participante' }}!</h3>
                    <p id="textoMensagemAmigavel" class="text-xs sm:text-sm text-blue-200 leading-relaxed mb-6"></p>
                    <div class="sm-ingot max-w-xs mx-auto p-3 mb-6">
                        <div class="sm-ingot-label">Sua Pontuação no Ranking</div>
                        <div id="pontosModalFim" class="sm-ingot-value !text-2xl mt-0.5">{{ $pontuacao }} pts</div>
                    </div>
                    <a id="btnIrParaResultado" href="{{ route('game.result') }}"
                       class="sm-cta !py-3.5 !text-sm cursor-pointer">
                        Ver Meu Resultado e Ranking Geral →
                    </a>
                </div>
            </div>

            <!-- Modal 4: Confirmação de Parar a Partida -->
            <div id="modalConfirmarParar" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
                <div class="sm-panel max-w-md w-full p-6 sm:p-8 text-center relative text-white shadow-2xl">
                    <div class="inline-flex items-center justify-center mb-2">
                        <span class="sm-pill text-xs uppercase tracking-wider">PARAR A PARTIDA</span>
                    </div>
                    <div class="w-16 h-16 rounded-full bg-gradient-to-b from-rose-400 via-rose-600 to-rose-800 flex items-center justify-center border-2 border-rose-300 shadow-xl shadow-rose-500/30 text-white mx-auto mb-3">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <rect x="6" y="6" width="12" height="12" rx="2"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black text-white sm-logo-title mb-2">Deseja Parar Agora?</h3>
                    <p class="text-xs sm:text-sm text-blue-200 mb-3 leading-relaxed">
                        Você já acumulou <strong class="text-amber-300 font-black text-base">{{ $pontuacao }} pontos</strong> nesta jornada!
                    </p>
                    <p class="text-xs text-blue-300/80 mb-6">
                        Se parar agora, seus <strong>{{ $pontuacao }} pontos</strong> ficarão garantidos no Ranking Geral do Bíblia Show.
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="fecharModalConfirmarParar()"
                                class="py-3 px-4 rounded-full bg-blue-900/70 hover:bg-blue-800 text-blue-200 font-extrabold text-xs uppercase tracking-wider border-2 border-blue-400/40 transition">
                            Continuar Jogando
                        </button>
                        <form action="{{ route('game.finish') }}" method="POST" class="inline" onsubmit="desarmarBeforeUnload()">
                            @csrf
                            <button type="submit" class="sm-cta !py-3 !text-xs cursor-pointer w-full">
                                Sim, Parar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal 5: Confirmação de Saída / Pausa do Jogo -->
            <div id="modalConfirmarSaida" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
                <div class="sm-panel max-w-md w-full p-6 sm:p-8 text-center relative text-white shadow-2xl">
                    <div class="inline-flex items-center justify-center mb-2">
                        <span class="sm-pill text-xs uppercase tracking-wider">PAUSAR PARTIDA</span>
                    </div>
                    <div class="w-16 h-16 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border-2 border-amber-200 shadow-xl shadow-amber-500/30 text-slate-950 mx-auto mb-3">
                        <svg class="w-8 h-8 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black text-white sm-logo-title mb-2">Voltar ao Início?</h3>
                    <p class="text-xs sm:text-sm text-blue-200 mb-4 leading-relaxed">
                        Você está na <strong class="text-amber-300 font-black">Pergunta {{ $numero_pergunta }} de {{ $total_perguntas }}</strong>. Seu progresso continuará salvo para você continuar quando quiser!
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="fecharModalConfirmarSaida()"
                                class="sm-cta !py-3 !text-xs cursor-pointer">
                            Continuar no Palco
                        </button>
                        <a id="linkDestinoSaida" href="/" onclick="desarmarBeforeUnload()"
                           class="py-3 px-4 rounded-full bg-blue-900/70 hover:bg-blue-800 text-blue-200 font-extrabold text-xs uppercase tracking-wider border-2 border-blue-400/40 transition flex items-center justify-center">
                            Ir para Início
                        </a>
                    </div>
                </div>
            </div>

            <script>
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const BASE_AUDIO_URL = "{{ asset('audio/classico') }}";
                let precisaIdentificar = {{ !empty($precisa_identificacao) ? 'true' : 'false' }};
                let perguntarContinuar = {{ !empty($perguntar_continuar) ? 'true' : 'false' }};
                let jogoIniciado = !precisaIdentificar && !perguntarContinuar;

                function abrirModalConfirmarParar() {
                    vibrar(30);
                    const m = document.getElementById('modalConfirmarParar');
                    if (m) {
                        m.classList.remove('hidden');
                        m.classList.add('flex');
                    }
                }

                function fecharModalConfirmarParar() {
                    const m = document.getElementById('modalConfirmarParar');
                    if (m) {
                        m.classList.add('hidden');
                        m.classList.remove('flex');
                    }
                }

                function tentarSairJogoAoVivo(e, destinoUrl = '/') {
                    if (!jogoIniciado || enviado) return true;
                    if (e) e.preventDefault();
                    const modal = document.getElementById('modalConfirmarSaida');
                    const link = document.getElementById('linkDestinoSaida');
                    if (link) link.href = destinoUrl;
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    }
                    return false;
                }

                function fecharModalConfirmarSaida() {
                    const modal = document.getElementById('modalConfirmarSaida');
                    if (modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                }

                let beforeUnloadArmado = true;
                function desarmarBeforeUnload() {
                    beforeUnloadArmado = false;
                }

                window.addEventListener('beforeunload', (e) => {
                    if (beforeUnloadArmado && jogoIniciado && !enviado) {
                        e.preventDefault();
                        e.returnValue = '';
                    }
                });

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
                audioLocucao.loop = false;

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

                audioLocucao.addEventListener('ended', () => {
                    tocarFase1ESuspense();
                });

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
                                painel.className = 'mt-6 rounded-2xl p-4 sm:p-5 border-2 flex flex-wrap items-center justify-between gap-4 ' +
                                    (isCerta
                                        ? 'bg-emerald-950/90 border-emerald-400 text-emerald-100 shadow-xl'
                                        : 'bg-rose-950/90 border-rose-400 text-rose-100 shadow-xl');

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
                                       class="px-5 py-2.5 rounded-xl bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 border-2 border-amber-200 hover:brightness-110 text-slate-950 font-black text-xs sm:text-sm shadow-xl transition shrink-0">
                                        ${data.fim_de_jogo ? 'Ver Resultado Final →' : 'Próxima Pergunta →'}
                                    </a>
                                `;
                                painel.classList.remove('hidden');
                            }

                            await tocarEfeitoComPromise(data.som_efeito);

                            if (data.perdeu_partida) {
                                desarmarBeforeUnload();
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

                            setTimeout(() => {
                                desarmarBeforeUnload();
                                window.location.href = data.redirect_url || "{{ route('game.play') }}";
                            }, 1800);
                        }, 1300);
                    } catch (e) {
                        desarmarBeforeUnload();
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

                async function virarCartaEscolhida(btn) {
                    if (btn.classList.contains('is-flipped')) return;
                    btn.classList.add('is-flipped', 'pointer-events-none');
                    vibrar(50);

                    const inner = btn.querySelector('.card-inner');
                    if (inner) {
                        inner.innerHTML = '<span class="text-2xl font-black text-amber-300">−2</span><span class="text-[8px] font-black uppercase text-amber-200 mt-0.5">ELIMINOU 2</span>';
                    }

                    try {
                        const resp = await fetch("{{ route('game.lifeline') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ tipo: 'cartas' }),
                        });

                        const data = await resp.json();
                        if (data.sucesso) {
                            const eliminadas = data.opcoes_eliminadas || [];
                            setTimeout(() => {
                                fecharCartasMobile();
                                aplicarEliminadasNaTela(eliminadas);
                                document.querySelectorAll('#btnLifelineCartasMobile').forEach(b => {
                                    b.disabled = true;
                                    b.classList.add('opacity-40', 'cursor-not-allowed');
                                });
                                document.querySelectorAll('#badgeCartasCountMobile').forEach(b => b.textContent = '0');
                            }, 500);
                            return;
                        }
                    } catch (e) {
                        console.warn('Fallback submit form cartas:', e);
                    }

                    document.getElementById('formCartasMobile').submit();
                }

                function aplicarEliminadasNaTela(indices) {
                    if (!Array.isArray(indices)) return;
                    indices.forEach(idx => {
                        const btn = document.getElementById(`btnOpcao_${idx}`);
                        if (btn) {
                            btn.disabled = true;
                            btn.classList.remove('group');
                            btn.classList.add('is-eliminada', 'opacity-30', 'line-through', 'cursor-not-allowed', 'pointer-events-none');
                            const statusTag = document.getElementById(`statusTag_${idx}`);
                            if (statusTag) {
                                statusTag.textContent = 'ELIMINADA';
                                statusTag.classList.remove('hidden');
                                statusTag.classList.add('bg-rose-950/80', 'text-rose-300', 'border', 'border-rose-500/40');
                            }
                        }
                    });
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
                    const box = document.getElementById('timerContainerBox');
                    if (tempo <= 10) {
                        if (timerEl) timerEl.classList.add('timer-warning');
                        if (box) box.classList.add('timer-box-warning');
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
                            fx.volume = typeof obterVolumeGlobal === 'function' ? Math.min(1, obterVolumeGlobal() * 1.1) : 0.85;
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
