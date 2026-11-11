<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#090d16">
    <title>Bíblia Show — Arena Ao Vivo</title>
    <link rel="icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
            touch-action: manipulation;
        }
        .milhao-bg {
            background: radial-gradient(circle at top, #172554 0%, #0f172a 45%, #020617 100%);
        }
        .question-card {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.99) 100%);
            border: 2px solid rgba(245, 158, 11, 0.5);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.8), 0 0 20px rgba(245, 158, 11, 0.15);
        }
        .opcao-btn {
            position: relative;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .opcao-btn:active:not(:disabled) {
            transform: scale(0.98);
        }
        .opcao-bloqueada {
            background: linear-gradient(90deg, rgba(217, 119, 6, 0.45) 0%, rgba(120, 53, 15, 0.6) 100%) !important;
            border-color: #fbbf24 !important;
            box-shadow: 0 0 25px rgba(251, 191, 36, 0.4) !important;
        }
        .opcao-correta-reveal {
            background: linear-gradient(90deg, rgba(5, 150, 105, 0.95) 0%, rgba(6, 95, 70, 0.98) 100%) !important;
            border-color: #34d399 !important;
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.5) !important;
        }
        .opcao-errada-reveal {
            background: linear-gradient(90deg, rgba(225, 29, 72, 0.95) 0%, rgba(136, 19, 55, 0.98) 100%) !important;
            border-color: #fb7185 !important;
            box-shadow: 0 0 30px rgba(244, 63, 94, 0.5) !important;
        }
        .timer-critico {
            animation: pulse-critico 0.85s infinite;
        }
        @keyframes pulse-critico {
            0%, 100% { border-color: #ef4444; background-color: rgba(136, 19, 55, 0.7); transform: scale(1); }
            50% { border-color: #fca5a5; background-color: rgba(159, 18, 57, 0.9); transform: scale(1.02); }
        }
    </style>
</head>
<body class="milhao-bg sm-stage-mobile text-slate-100 min-h-screen flex flex-col justify-between">

    @include('partials.navbar', ['dark' => true, 'hideOnMobile' => true])

    <main class="w-full max-w-xl mx-auto px-3 sm:px-4 py-2 sm:py-5 flex-1 flex flex-col justify-between">

        <!-- ============================================================== -->
        <!-- ETAPA 1: Formulário de Entrada na Sala (Antes do Jogo)        -->
        <!-- ============================================================== -->
        <div id="etapaEntrada" class="my-auto sm-panel p-5 sm:p-7 relative z-10">
            <div class="text-center mb-5">
                <div class="inline-flex items-center justify-center mb-2">
                    <span class="sm-pill text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <span>Arena Ao Vivo</span>
                    </span>
                </div>
                <h1 class="text-xl sm:text-3xl font-black text-white sm-logo-title">Entrar na Arena</h1>
                <p class="text-blue-200 text-xs sm:text-sm mt-1">
                    {{ $sala ? $sala->nome : 'Digite o código da sala exibido no telão' }}
                </p>
            </div>

            @if($sala && ($sala->premio_primeiro_lugar || $sala->premio_segundo_lugar))
                <div class="mb-4 p-3 rounded-2xl bg-amber-500/15 border border-amber-400/40 text-xs space-y-1">
                    <p class="text-[10px] font-black uppercase tracking-wider text-amber-300">Prêmios em Disputa:</p>
                    @if($sala->premio_primeiro_lugar)
                        <p class="text-white font-bold"><strong class="text-amber-300">1º Lugar:</strong> {{ $sala->premio_primeiro_lugar }}</p>
                    @endif
                    @if($sala->premio_segundo_lugar)
                        <p class="text-blue-200 font-semibold"><strong class="text-blue-100">2º Lugar:</strong> {{ $sala->premio_segundo_lugar }}</p>
                    @endif
                </div>
            @endif

            <form id="entrarForm" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-amber-300 mb-1">Código da Sala (PIN)</label>
                    <input type="text" id="inputCodigo" name="codigo_acesso" required maxlength="6"
                           value="{{ $sala?->codigo_acesso ?? $codigo ?? '' }}"
                           placeholder="Ex.: AB12CD"
                           class="w-full px-4 py-3 rounded-xl border border-amber-400/60 bg-blue-950/90 text-amber-300 font-mono font-black uppercase text-xl sm:text-2xl tracking-widest text-center focus:ring-2 focus:ring-amber-400 outline-none shadow-inner">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-amber-300 mb-1">Seu Nome / Apelido</label>
                    <input type="text" id="inputNome" name="nome" required
                           placeholder="Como quer ser chamado no telão?"
                           class="w-full px-4 py-3 rounded-xl border border-blue-400/40 bg-blue-950/80 text-white font-bold text-sm sm:text-base focus:ring-2 focus:ring-amber-400 outline-none transition">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-amber-300 mb-1">Equipe / Célula (Opcional)</label>
                    <div id="containerSelecaoEquipe" class="space-y-2">
                        <select id="selectEquipe" name="equipe_id"
                                onchange="aoMudarSelectEquipe(this)"
                                class="w-full px-4 py-2.5 rounded-xl border border-blue-400/40 bg-blue-950/80 text-white text-xs font-semibold focus:ring-2 focus:ring-amber-400 outline-none transition">
                            <option value="">Nenhuma equipe (Jogar individualmente)</option>
                            @if($sala && $sala->equipes->isNotEmpty())
                                <optgroup label="Equipes Criadas na Sala">
                                    @foreach($sala->equipes as $eq)
                                        <option value="{{ $eq->id }}">{{ $eq->nome }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            <option value="__nova__">+ Criar ou Digitar Outra Equipe...</option>
                        </select>
                        <div id="campoNovaEquipe" class="hidden">
                            <input type="text" id="inputEquipe" name="equipe_nome"
                                   placeholder="Nome da sua nova equipe"
                                   class="w-full px-4 py-2 rounded-xl border border-amber-400/60 bg-blue-950 text-amber-300 text-xs font-bold focus:ring-2 focus:ring-amber-400 outline-none transition">
                        </div>
                    </div>
                </div>

                <button type="submit" id="btnEntrar"
                        class="sm-arcade-btn is-gold w-full text-base py-3.5 mt-2 cursor-pointer">
                    <span>Entrar na Arena Ao Vivo →</span>
                </button>
            </form>

            <div id="errorEntrada" class="hidden bg-rose-950/90 border border-rose-500/50 p-3 rounded-xl text-rose-200 text-xs mt-3 text-center font-bold"></div>
        </div>

        <!-- ============================================================== -->
        <!-- ETAPA 2: TELA MOBILE-FIRST DE JOGO AO VIVO (SHOW DO MILHÃO)    -->
        <!-- ============================================================== -->
        <div id="etapaJogo" class="hidden flex-1 flex flex-col justify-between gap-2 w-full">

            <!-- 1. HEADER DO APP: Sair, Pílula Central Dourada, Pontos & Som -->
            <header class="flex items-center justify-between gap-2 px-1">
                <!-- Botão Sair / Portinha -->
                <button type="button" onclick="sairDaSala()" title="Sair da sala"
                        class="sm-lifeline !w-10 !h-10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                </button>

                <!-- Pílula Dourada Central do Show do Milhão -->
                <div class="sm-pill text-xs">
                    <span>
                        <span id="valorPontosBadge">10 pts</span> • 
                        <small id="perguntaContadorBadge">1 / 24 • 1ª FASE</small>
                    </span>
                </div>

                <!-- Lado Direito: Pontos + Ranking + Toggle Som -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" onclick="alternarSomMobile()" id="btnToggleSomMobile" title="Som e Vibração"
                            class="sm-lifeline !w-10 !h-10 flex items-center justify-center">
                        <span id="iconeSomLigado" class="flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/></svg>
                        </span>
                        <span id="iconeSomMudo" class="hidden items-center justify-center">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-7.5l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/></svg>
                        </span>
                    </button>
                    <button type="button" onclick="abrirDrawerRanking()" title="Ver Ranking da Sala"
                            class="sm-timer !w-10 !h-10 !text-[10px] !border-amber-400 leading-none flex flex-col items-center justify-center">
                        <span id="jogadorPontosDisplay" class="text-amber-300 font-black text-xs">0</span>
                        <span class="text-[7px] text-slate-400 font-bold">PTS</span>
                    </button>
                </div>
            </header>

            <!-- Participante + Equipe Subtítulo Discreto -->
            <div class="flex items-center justify-between px-2 text-[10px] text-slate-400">
                <span class="truncate max-w-[180px] inline-flex items-center gap-1">
                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    <strong class="text-white" id="jogadorNomeDisplay">-</strong> (<span id="codigoSalaBadge">-</span>)
                </span>
                <span class="text-amber-400 font-bold truncate" id="jogadorEquipeDisplay">Individual</span>
            </div>

            <!-- 2. CARD PRINCIPAL DA PERGUNTA BÍBLICA -->
            <div class="sm-question pt-4 pb-3.5 px-4 relative">
                <div class="text-[10px] font-black uppercase tracking-wider text-blue-200/80 mb-1" id="badgeBiblicoCard">
                    <span id="textoBiblicoCard">Sagradas Escrituras</span>
                </div>
                <p id="textoPerguntaViva" class="sm-question-text text-[16px] sm:text-lg leading-snug">
                    Aguardando o apresentador iniciar a pergunta...
                </p>
            </div>

            <!-- 3. BALÃO DO CONVIDADO / DICA (se ativos) -->
            <div id="dicaConvidadoViva" class="hidden flex items-center gap-2 px-1">
                <div class="sm-avatar !w-9 !h-9 text-xs flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                </div>
                <div class="sm-speech flex-1 text-xs">
                    <span class="block text-[9px] uppercase tracking-wider text-blue-700 font-black" id="nomeConvidadoViva">Convidado Bíblico:</span>
                    <span id="conteudoConvidadoTexto">-</span>
                </div>
            </div>

            <div id="dicaBiblicaViva" class="hidden flex items-center gap-2 px-1">
                <div class="sm-avatar !w-9 !h-9 text-xs flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                </div>
                <div class="sm-speech flex-1 text-xs bg-amber-50 text-amber-900 border border-amber-300">
                    <span class="block text-[9px] uppercase tracking-wider text-amber-700 font-black">Referência Bíblica:</span>
                    <span id="conteudoDicaBiblicaTexto">-</span>
                </div>
            </div>

            <!-- Feedback da Resposta Pós-Bloqueio -->
            <div id="feedbackResposta" class="hidden rounded-2xl p-2.5 border text-xs text-center transition-all"></div>

            <!-- 4. BOTÕES REDONDOS DE AJUDA & CRONÔMETRO -->
            <div id="barraAjudasJogador" class="flex items-center justify-center gap-3">
                <!-- Cartas 50/50 -->
                <button type="button" id="btnAjudaMobileCartas" onclick="abrirModalCartasParticipante()" class="sm-lifeline" title="Cartas (Eliminar 2)">
                    −2
                    <span id="badgeCartasRestantesMobile" class="sm-badge">1</span>
                </button>

                <!-- Convidados -->
                <button type="button" id="btnAjudaMobileConvidado" onclick="abrirModalConvidadoMobile()" class="sm-lifeline flex items-center justify-center" title="Convidados Bíblicos">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                    <span id="badgeConvidadosRestantesMobile" class="sm-badge">3</span>
                </button>

                <!-- Dica -->
                <button type="button" id="btnAjudaMobileDica" onclick="usarAjudaMobile('dica')" class="sm-lifeline flex items-center justify-center" title="Dica Bíblica">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    <span id="badgeDicasRestantesMobile" class="sm-badge">3</span>
                </button>

                <!-- Cronômetro Circular -->
                <div class="sm-timer" id="cronometroCircularBox" title="Tempo Restante">
                    <span id="tempoRestanteDisplay">60</span>
                </div>
            </div>

            <!-- AVISO SILENCIOSO DE TEMPO ESGOTADO -->
            <div id="avisoTempoEsgotado" class="hidden p-2 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-200 text-xs font-bold text-center">
                ⏱️ Tempo esgotado para esta pergunta! Aguarde o apresentador.
            </div>

            <!-- 5. AS 4 ALTERNATIVAS EM CÁPSULA (ESTILO SHOW DO MILHÃO MOBILE) -->
            <div id="opcoesBotoes" class="space-y-2">
                <!-- Inserido dinamicamente via JS -->
            </div>

            <!-- 6. OS 3 LINGOTES SHOW DO MILHÃO (ERRAR | PARAR | ACERTAR) -->
            <div class="grid grid-cols-3 gap-2 pt-1">
                <div class="sm-ingot is-errar">
                    <div class="sm-ingot-value" id="placarErrarDisplay">0 pts</div>
                    <div class="sm-ingot-label">Errar</div>
                </div>
                <div class="sm-ingot">
                    <div class="sm-ingot-value" id="placarPararDisplay">0 pts</div>
                    <div class="sm-ingot-label">Parar</div>
                </div>
                <div class="sm-ingot is-acertar">
                    <div class="sm-ingot-value" id="placarAcertarDisplay">10 pts</div>
                    <div class="sm-ingot-label">Acertar</div>
                </div>
            </div>

            <!-- FAIXA VERMELHA DE INCENTIVO -->
            <div class="sm-banner-red py-1.5 px-3 rounded-xl text-[11px] uppercase tracking-wider text-center">
                Não sabe a resposta? Use ajuda!
            </div>

        </div>

    </main>

    <!-- ============================================================== -->
    <!-- GAVETA / MODAL DESLIZANTE DE RANKING & EQUIPES DA SALA        -->
    <!-- ============================================================== -->
    <div id="drawerRanking" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 transition-all">
        <div class="bg-slate-900 border-t-2 sm:border-2 border-amber-400 rounded-t-3xl sm:rounded-3xl max-w-md w-full p-5 text-slate-100 shadow-2xl relative max-h-[85vh] flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1c1.94-.37 3.5-1.99 3.61-3.96C19.08 11.63 21 9.55 21 7V5c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>
                    <h3 class="text-base sm:text-lg font-black text-white">Placar da Sala Ao Vivo</h3>
                </div>
                <button type="button" onclick="fecharDrawerRanking()" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center font-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Abas Jogadores / Equipes -->
            <div class="flex items-center gap-2 my-3">
                <button type="button" id="tabJogadores" onclick="alternarTabRanking('jogadores')"
                        class="flex-1 text-xs font-black py-2 rounded-xl bg-amber-500 text-slate-950 transition">
                    Jogadores
                </button>
                <button type="button" id="tabEquipes" onclick="alternarTabRanking('equipes')"
                        class="flex-1 text-xs font-black py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition">
                    Equipes / Células
                </button>
            </div>

            <!-- Conteúdo do Ranking Rolável -->
            <div class="flex-1 overflow-y-auto space-y-2 pr-1" id="boxListaRanking">
                <div id="miniRankingJogadores" class="space-y-1.5 text-xs sm:text-sm"></div>
                <div id="miniRankingEquipes" class="hidden space-y-1.5 text-xs sm:text-sm"></div>
            </div>

            <!-- Rodapé da Gaveta: Gerenciar Equipe ou Sair -->
            <div class="border-t border-slate-800 pt-3 mt-3 flex items-center justify-between gap-2">
                <button type="button" onclick="abrirModalTrocarEquipe()" class="text-xs font-bold text-blue-400 hover:text-blue-300 underline">
                    Trocar / Criar Equipe
                </button>
                <button type="button" onclick="sairDaSala()" class="text-xs text-rose-400 hover:text-rose-300 font-bold">
                    Sair da Sala
                </button>
            </div>
        </div>
    </div>

    <!-- Modal das Cartas 50/50 (Estilo Show do Milhão) -->
    <div id="modalCartasMobile" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="sm-panel w-full max-w-xs text-center p-4">
            <div class="sm-panel-title py-2.5 px-3 text-white font-black text-sm tracking-wider uppercase mb-3 rounded-xl">
                CARTAS DO MILHÃO
            </div>
            <p class="text-xs text-blue-100 font-bold mb-3">Toque em uma carta para eliminar 2 alternativas incorretas:</p>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <button type="button" onclick="confirmarCartaEscolhida(0)" class="sm-card-back">✝</button>
                <button type="button" onclick="confirmarCartaEscolhida(1)" class="sm-card-back">✝</button>
                <button type="button" onclick="confirmarCartaEscolhida(2)" class="sm-card-back">✝</button>
                <button type="button" onclick="confirmarCartaEscolhida(3)" class="sm-card-back">✝</button>
            </div>
            <button type="button" onclick="fecharModalCartasParticipante()" class="sm-cta text-xs py-2">
                Voltar à Pergunta
            </button>
        </div>
    </div>

    <!-- Modal Escolha de Convidado Bíblico -->
    <div id="modalConvidadoMobile" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-sm w-full p-5 text-slate-100 shadow-2xl relative">
            <button type="button" onclick="fecharModalConvidadoMobile()" class="absolute top-4 right-4 text-slate-400 hover:text-white font-black text-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                <h3 class="text-base font-black text-white">Consultar Convidado Bíblico</h3>
            </div>
            <p class="text-xs text-slate-400 mb-3">Escolha um dos personagens para obter ajuda na pergunta:</p>
            <div class="space-y-2">
                <button type="button" onclick="usarAjudaMobile('convidado', 0)" class="w-full p-2.5 rounded-xl bg-blue-950/80 border border-blue-500/40 hover:border-amber-400 text-left font-bold text-xs flex items-center justify-between text-white transition active:scale-95">
                    <div>
                        <p class="font-black text-white">1. Jacó</p>
                        <p class="text-[10px] text-slate-400 font-normal">Patriarca de Israel</p>
                    </div>
                    <span class="text-[9px] text-emerald-400 font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-950/60 border border-emerald-500/30">Sabe a certa</span>
                </button>
                <button type="button" onclick="usarAjudaMobile('convidado', 1)" class="w-full p-2.5 rounded-xl bg-blue-950/80 border border-blue-500/40 hover:border-amber-400 text-left font-bold text-xs flex items-center justify-between text-white transition active:scale-95">
                    <div>
                        <p class="font-black text-white">2. Paulo</p>
                        <p class="text-[10px] text-slate-400 font-normal">Apóstolo dos Gentios</p>
                    </div>
                    <span class="text-[9px] text-emerald-400 font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-950/60 border border-emerald-500/30">Sabe a certa</span>
                </button>
                <button type="button" onclick="usarAjudaMobile('convidado', 2)" class="w-full p-2.5 rounded-xl bg-blue-950/80 border border-blue-500/40 hover:border-amber-400 text-left font-bold text-xs flex items-center justify-between text-white transition active:scale-95">
                    <div>
                        <p class="font-black text-white">3. Jafé</p>
                        <p class="text-[10px] text-slate-400 font-normal">Filho de Noé</p>
                    </div>
                    <span class="text-[9px] text-amber-400 font-extrabold uppercase px-2 py-0.5 rounded bg-amber-950/60 border border-amber-500/30">Histórico</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal para Juntar-se ou Trocar de Equipe na Sala -->
    <div id="modalTrocarEquipe" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-md w-full p-5 text-slate-100 shadow-2xl relative">
            <button type="button" onclick="fecharModalTrocarEquipe()" class="absolute top-4 right-4 text-slate-400 hover:text-white font-black text-lg">✕</button>
            <div class="flex items-center gap-2 mb-2">
                <span class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </span>
                <h3 class="text-base font-black text-white">Equipes / Células da Sala</h3>
            </div>
            <p class="text-xs text-slate-400 mb-3">Escolha uma equipe criada pelo apresentador para pontuar em conjunto:</p>

            <div id="listaEquipesModal" class="space-y-1.5 max-h-48 overflow-y-auto pr-1 mb-3">
                <p class="text-xs text-slate-500">Carregando equipes...</p>
            </div>

            <div class="border-t border-slate-800 pt-3 space-y-2">
                <p class="text-[10px] font-bold text-slate-400 uppercase">Ou crie uma nova equipe:</p>
                <div class="flex gap-2">
                    <input type="text" id="inputNovaEquipeModal" placeholder="Ex.: Tribo de Judá"
                           class="flex-1 px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white outline-none focus:border-amber-400 font-bold">
                    <button type="button" onclick="criarEquipeModal()"
                            class="px-3 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs uppercase shadow transition active:scale-95">
                        Criar
                    </button>
                </div>
                <button type="button" onclick="sairEquipeModal()"
                        class="w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition">
                    Jogar Individualmente (Sem Equipe)
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- SCRIPT DE SINCRONIZAÇÃO, ÁUDIO E HÁPTICOS (MOBILE-FIRST)        -->
    <!-- ============================================================== -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const BASE_AUDIO_URL = "{{ asset('audio/classico') }}";
        const letras = ['A', 'B', 'C', 'D'];

        // Cores vibrantes táteis Kahoot-Style para cada alternativa
        const coresKahoot = [
            { letraBg: 'bg-rose-500 text-white', border: 'border-rose-500/40', bg: 'bg-rose-950/50' },
            { letraBg: 'bg-blue-500 text-white', border: 'border-blue-500/40', bg: 'bg-blue-950/50' },
            { letraBg: 'bg-amber-400 text-slate-950', border: 'border-amber-500/40', bg: 'bg-amber-950/50' },
            { letraBg: 'bg-emerald-500 text-white', border: 'border-emerald-500/40', bg: 'bg-emerald-950/50' },
        ];

        let somAtivo = localStorage.getItem('biblia_show_som') !== 'off';

        // 1. Função unificada de vibração (Interface nativa Android + Web API)
        function vibrar(padrao = 30) {
            if (!somAtivo) return;
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

        // 2. Destravamento do Áudio no primeiro toque de tela (Android/iOS WebKit policy)
        function destravarAudioPrimeiroToque() {
            try {
                const dummy = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRhAgAAAAEA');
                dummy.volume = 0.01;
                dummy.play().catch(() => {});
            } catch (e) {}
        }
        ['touchstart', 'pointerdown', 'click'].forEach(evt => {
            window.addEventListener(evt, destravarAudioPrimeiroToque, { once: true, passive: true });
        });

        // 3. Tocador de Áudio Robusto
        function tocarAudio(nomeArquivo, volume = 0.85) {
            if (!somAtivo || !nomeArquivo) return null;
            try {
                const url = `${BASE_AUDIO_URL}/${nomeArquivo.replace(/^\/+/, '')}`;
                const audio = new Audio(url);
                audio.volume = Math.max(0, Math.min(1, volume));
                audio.play().catch(() => {});
                return audio;
            } catch (e) {
                return null;
            }
        }

        function alternarSomMobile() {
            somAtivo = !somAtivo;
            localStorage.setItem('biblia_show_som', somAtivo ? 'on' : 'off');
            atualizarIconeSomMobile();
            if (somAtivo) {
                vibrar(40);
                tocarAudio('tecla.wav', 0.8);
            }
        }

        function atualizarIconeSomMobile() {
            const lig = document.getElementById('iconeSomLigado');
            const mud = document.getElementById('iconeSomMudo');
            if (somAtivo) {
                lig?.classList.remove('hidden');
                mud?.classList.add('hidden');
            } else {
                lig?.classList.add('hidden');
                mud?.classList.remove('hidden');
            }
        }
        atualizarIconeSomMobile();

        // Estado da Sessão do Participante
        let estadoSessao = {
            salaId: {{ $sala?->id ?? 'null' }},
            codigoSala: '{{ $sala?->codigo_acesso ?? $codigo ?? '' }}',
            participanteId: null,
            participanteNome: '',
            equipeNome: '',
            pontuacao: 0,
            questaoAtualId: null,
            jaRespondeuAtual: false,
            opcaoEscolhida: null,
            reveladoNaPerguntaAtual: false,
            inicioPerguntaTimestamp: Date.now(),
            opcoesEliminadas: [],
            tempoRestante: 60,
            tempoEsgotado: false,
            dicasRestantes: 3,
            cartasRestantes: 1,
            convidadosRestantes: 3,
            equipes: [],
        };

        let timerTickInterval = null;

        // Recuperar sessão salva
        const salvo = sessionStorage.getItem('biblia_show_participante');
        if (salvo) {
            try {
                const parsed = JSON.parse(salvo);
                if (!estadoSessao.codigoSala || parsed.codigoSala === estadoSessao.codigoSala.toUpperCase()) {
                    estadoSessao = { ...estadoSessao, ...parsed };
                    iniciarModoJogo();
                }
            } catch (e) {}
        }

        function aoMudarSelectEquipe(sel) {
            const campo = document.getElementById('campoNovaEquipe');
            if (!campo) return;
            if (sel.value === '__nova__') {
                campo.classList.remove('hidden');
                document.getElementById('inputEquipe')?.focus();
            } else {
                campo.classList.add('hidden');
            }
        }

        // Submissão do Formulário de Entrada
        document.getElementById('entrarForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = document.getElementById('btnEntrar');
            const errBox = document.getElementById('errorEntrada');
            errBox.classList.add('hidden');
            btn.disabled = true;
            btn.innerText = 'Conectando à Arena...';

            const codigo = document.getElementById('inputCodigo').value.trim().toUpperCase();
            const nome = document.getElementById('inputNome').value.trim();
            const selEq = document.getElementById('selectEquipe');
            let equipeId = null;
            let equipeNome = null;

            if (selEq && selEq.value === '__nova__') {
                equipeNome = document.getElementById('inputEquipe')?.value.trim() || null;
            } else if (selEq && selEq.value) {
                equipeId = selEq.value;
            }

            try {
                const response = await fetch('/api/salas/entrar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        codigo_acesso: codigo,
                        nome: nome,
                        equipe_id: equipeId,
                        equipe_nome: equipeNome,
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    const msg = data.error || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Erro ao entrar na sala.');
                    throw new Error(msg);
                }

                estadoSessao.salaId = data.sala.id;
                estadoSessao.codigoSala = data.sala.codigo_acesso;
                estadoSessao.participanteId = data.participante.id;
                estadoSessao.participanteNome = data.participante.nome;
                estadoSessao.equipeNome = data.participante.equipe_nome || '';
                estadoSessao.pontuacao = data.participante.pontuacao_total || 0;

                sessionStorage.setItem('biblia_show_participante', JSON.stringify(estadoSessao));
                vibrar(50);
                tocarAudio('tecla.wav');
                iniciarModoJogo();
            } catch (err) {
                errBox.innerText = err.message;
                errBox.classList.remove('hidden');
                btn.disabled = false;
                btn.innerText = 'Entrar na Sala →';
                vibrar([100, 50, 100]);
            }
        });

        function iniciarModoJogo() {
            document.getElementById('etapaEntrada').classList.add('hidden');
            document.getElementById('etapaJogo').classList.remove('hidden');

            document.getElementById('codigoSalaBadge').innerText = estadoSessao.codigoSala;
            document.getElementById('jogadorNomeDisplay').innerText = estadoSessao.participanteNome;
            const avatarEl = document.getElementById('jogadorAvatarDisplay');
            if (avatarEl && estadoSessao.participanteNome) {
                avatarEl.innerText = estadoSessao.participanteNome.trim().charAt(0).toUpperCase();
            }
            document.getElementById('jogadorEquipeDisplay').innerText = estadoSessao.equipeNome ? 'Equipe: ' + estadoSessao.equipeNome : 'Individual';
            document.getElementById('jogadorPontosDisplay').innerText = estadoSessao.pontuacao;

            sincronizarEstadoSala();
            setInterval(sincronizarEstadoSala, 1200);

            if (!timerTickInterval) {
                timerTickInterval = setInterval(tickCronometroLocal, 1000);
            }
        }

        // Timer Local Suave com Som e Vibração nos 10s finais
        function tickCronometroLocal() {
            if (estadoSessao.tempoRestante > 0 && !estadoSessao.jaRespondeuAtual) {
                estadoSessao.tempoRestante--;
                atualizarVisualCronometro(estadoSessao.tempoRestante);

                if (estadoSessao.tempoRestante <= 10 && estadoSessao.tempoRestante > 0) {
                    vibrar(25);
                    const fxSrc = estadoSessao.tempoRestante <= 3 ? 'tempo_alerta.wav' : 'tempo_tick.wav';
                    tocarAudio(fxSrc, 0.9);
                } else if (estadoSessao.tempoRestante === 0) {
                    vibrar([50, 50, 100]);
                    tocarAudio('ks.wav', 0.9);
                    desabilitarOpcoesPorTempoEsgotado();
                }
            }
        }

        function atualizarVisualCronometro(segundos) {
            const timerEl = document.getElementById('tempoRestanteDisplay');
            const circEl = document.getElementById('cronometroCircularBox');

            if (timerEl) timerEl.innerText = Math.max(0, segundos);

            if (circEl) {
                if (segundos <= 10 && segundos > 0) {
                    circEl.classList.add('is-critical');
                } else {
                    circEl.classList.remove('is-critical');
                }
            }
        }

        function desabilitarOpcoesPorTempoEsgotado() {
            estadoSessao.tempoEsgotado = true;

            if (typeof Swal !== 'undefined' && Swal.isVisible()) {
                Swal.close();
            }

            document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-40', 'cursor-not-allowed');
            });

            const aviso = document.getElementById('avisoTempoEsgotado');
            if (aviso && !estadoSessao.jaRespondeuAtual) {
                aviso.classList.remove('hidden');
            }

            atualizarBotoesAjudasMobile();
        }

        // Sincronização Periódica com o Servidor
        async function sincronizarEstadoSala() {
            if (!estadoSessao.salaId) return;

            try {
                const url = `/api/salas/${estadoSessao.salaId}?participante_id=${estadoSessao.participanteId || ''}`;
                const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!resp.ok) return;
                const data = await resp.json();

                if (data.tempo_restante !== undefined) {
                    estadoSessao.tempoRestante = Number(data.tempo_restante);
                    atualizarVisualCronometro(estadoSessao.tempoRestante);
                }

                const placar = data.placar_milhao || {};
                const errarEl = document.getElementById('placarErrarDisplay');
                const pararEl = document.getElementById('placarPararDisplay');
                const acertarEl = document.getElementById('placarAcertarDisplay');
                if (errarEl && placar.errar !== undefined) errarEl.innerHTML = `${placar.errar} <span class="text-[8px]">pts</span>`;
                if (pararEl && placar.parar !== undefined) pararEl.innerHTML = `${placar.parar} <span class="text-[8px]">pts</span>`;
                if (acertarEl && placar.acertar !== undefined) acertarEl.innerHTML = `${placar.acertar} <span class="text-[8px]">pts</span>`;

                estadoSessao.equipes = data.equipes || [];
                atualizarRanking(data.ranking || [], data.equipes || []);

                if (data.estado === 'terminado') {
                    document.getElementById('textoPerguntaViva').innerText = 'Partida encerrada pelo apresentador! Veja sua colocação no ranking.';
                    document.getElementById('opcoesBotoes').innerHTML = '';
                    desabilitarOpcoesPorTempoEsgotado();
                    return;
                }

                const q = data.pergunta_atual;
                if (!q) {
                    document.getElementById('textoPerguntaViva').innerText = 'Aguardando o apresentador iniciar a próxima pergunta...';
                    document.getElementById('opcoesBotoes').innerHTML = '';
                    return;
                }

                const fase = data.fase_atual || 1;
                document.getElementById('faseBadge').innerText = `${fase}ª Fase`;
                document.getElementById('perguntaContadorBadge').innerText = `Pergunta ${data.numero_pergunta || 1}/${data.total_perguntas || 24}`;
                document.getElementById('valorPontosBadge').innerText = `Vale ${placar.valor_pergunta || 10} pts`;

                const ajudas = data.ajudas || {};

                if (ajudas.dicas_restantes !== undefined) estadoSessao.dicasRestantes = ajudas.dicas_restantes;
                if (ajudas.cartas_restantes !== undefined) estadoSessao.cartasRestantes = ajudas.cartas_restantes;
                if (ajudas.convidados_restantes !== undefined) estadoSessao.convidadosRestantes = ajudas.convidados_restantes;
                atualizarBotoesAjudasMobile();

                // Dica bíblica
                const dicaEl = document.getElementById('dicaBiblicaViva');
                const dicaTexto = document.getElementById('conteudoDicaBiblicaTexto');
                if (ajudas.dica_biblica) {
                    if (dicaTexto) dicaTexto.textContent = ajudas.dica_biblica;
                    dicaEl.classList.remove('hidden');
                } else {
                    dicaEl.classList.add('hidden');
                }

                // Convidado bíblico
                const convEl = document.getElementById('dicaConvidadoViva');
                const convNome = document.getElementById('nomeConvidadoViva');
                const convTexto = document.getElementById('conteudoConvidadoTexto');
                if (ajudas.dica_convidado) {
                    if (convNome) convNome.textContent = `Convidado: ${ajudas.dica_convidado.convidado}`;
                    if (convTexto) convTexto.textContent = ajudas.dica_convidado.mensagem;
                    convEl.classList.remove('hidden');
                } else {
                    convEl.classList.add('hidden');
                }

                const eliminadasStr = JSON.stringify(ajudas.opcoes_eliminadas || []);
                const mudouEliminadas = eliminadasStr !== JSON.stringify(estadoSessao.opcoesEliminadas);
                estadoSessao.opcoesEliminadas = ajudas.opcoes_eliminadas || [];

                // Resposta registrada no servidor
                if (data.respostas_rodada && estadoSessao.participanteId && data.respostas_rodada[estadoSessao.participanteId]) {
                    const minhaResp = data.respostas_rodada[estadoSessao.participanteId];
                    estadoSessao.jaRespondeuAtual = true;
                    if (estadoSessao.opcaoEscolhida === null && typeof minhaResp.indice !== 'undefined' && minhaResp.indice !== null) {
                        estadoSessao.opcaoEscolhida = minhaResp.indice;
                    }
                }

                // Nova Pergunta detectada
                if (estadoSessao.questaoAtualId !== q.id) {
                    estadoSessao.questaoAtualId = q.id;
                    estadoSessao.jaRespondeuAtual = !!data.ja_respondeu;
                    estadoSessao.opcaoEscolhida = null;
                    estadoSessao.reveladoNaPerguntaAtual = false;
                    estadoSessao.tempoEsgotado = false;
                    estadoSessao.inicioPerguntaTimestamp = Date.now();
                    document.getElementById('feedbackResposta').classList.add('hidden');
                    document.getElementById('avisoTempoEsgotado').classList.add('hidden');
                    renderizarPergunta(q);
                } else {
                    // Pergunta já em curso: checar se apresentador revelou gabarito oficial
                    const gabaritoRevelado = !!(ajudas && ajudas.gabarito_revelado);
                    if (gabaritoRevelado && !estadoSessao.reveladoNaPerguntaAtual) {
                        estadoSessao.reveladoNaPerguntaAtual = true;

                        const respostaCorreta = data.pergunta_atual?.resposta_correta;
                        let acertou = null;
                        let pontosGanhos = 0;

                        if (data.respostas_rodada && estadoSessao.participanteId && data.respostas_rodada[estadoSessao.participanteId]) {
                            const r = data.respostas_rodada[estadoSessao.participanteId];
                            acertou = r.correta;
                            pontosGanhos = r.pontos || 0;
                        } else if (estadoSessao.opcaoEscolhida !== null && respostaCorreta && q.opcoes) {
                            const textoEscolhido = q.opcoes[estadoSessao.opcaoEscolhida];
                            acertou = (textoEscolhido && String(textoEscolhido).trim().toLowerCase() === String(respostaCorreta).trim().toLowerCase());
                        }

                        revelarCoresOpcaoMobile(estadoSessao.opcaoEscolhida, respostaCorreta, acertou, q);

                        // Som e Vibração de Acerto / Erro
                        if (acertou !== null) {
                            if (acertou) {
                                vibrar([60, 60, 120]);
                                tocarAudio('certa1.wav', 0.95);
                            } else {
                                vibrar([150, 80, 150]);
                                tocarAudio('errou1.wav', 0.95);
                            }
                        }

                        // Feedback pós-revelação
                        const fb = document.getElementById('feedbackResposta');
                        if (fb && estadoSessao.jaRespondeuAtual) {
                            fb.className = 'rounded-2xl p-3 border ' + (acertou
                                ? 'bg-emerald-950/80 border-emerald-500/40 text-emerald-100'
                                : 'bg-rose-950/80 border-rose-500/40 text-rose-100');

                            fb.innerHTML = `
                                <div class="font-black text-sm mb-0.5">
                                    ${acertou ? `✓ Certa Resposta! (+${pontosGanhos || placar.valor_pergunta || 10} pts)` : `✕ Que pena, você errou!`}
                                </div>
                                ${!acertou && respostaCorreta ? `<div class="text-[11px] text-rose-200">Gabarito: <strong class="text-emerald-300 underline">${respostaCorreta}</strong></div>` : ''}
                                ${data.pergunta_atual?.referencia_biblica ? `<div class="text-[10px] mt-0.5 text-slate-300">[${data.pergunta_atual.referencia_biblica}]</div>` : ''}
                            `;
                            fb.classList.remove('hidden');
                        }
                    } else if (mudouEliminadas && !estadoSessao.jaRespondeuAtual) {
                        renderizarPergunta(q);
                    }

                    if (estadoSessao.tempoRestante <= 0 && !estadoSessao.jaRespondeuAtual) {
                        desabilitarOpcoesPorTempoEsgotado();
                    }
                }
            } catch (e) {}
        }

        function atualizarBotoesAjudasMobile() {
            const btnDica = document.getElementById('btnAjudaMobileDica');
            const btnCartas = document.getElementById('btnAjudaMobileCartas');
            const btnConv = document.getElementById('btnAjudaMobileConvidado');

            const txtDica = document.getElementById('badgeDicasRestantesMobile');
            const txtCartas = document.getElementById('badgeCartasRestantesMobile');
            const txtConv = document.getElementById('badgeConvidadosRestantesMobile');

            const bloqueadoGeral = estadoSessao.jaRespondeuAtual || estadoSessao.tempoEsgotado || (estadoSessao.tempoRestante <= 0);

            if (txtDica) txtDica.textContent = `${estadoSessao.dicasRestantes} restantes`;
            if (btnDica) btnDica.disabled = bloqueadoGeral || estadoSessao.dicasRestantes <= 0;

            if (txtCartas) txtCartas.textContent = estadoSessao.cartasRestantes > 0 ? 'Eliminar 2' : 'Usada';
            if (btnCartas) btnCartas.disabled = bloqueadoGeral || estadoSessao.cartasRestantes <= 0;

            if (txtConv) txtConv.textContent = `${estadoSessao.convidadosRestantes} restantes`;
            if (btnConv) btnConv.disabled = bloqueadoGeral || estadoSessao.convidadosRestantes <= 0;
        }

        async function usarAjudaMobile(tipo, indiceConvidado = null) {
            if (!estadoSessao.salaId || !estadoSessao.participanteId) return;
            if (estadoSessao.tempoEsgotado || estadoSessao.tempoRestante <= 0 || estadoSessao.jaRespondeuAtual) {
                return;
            }

            fecharModalConvidadoMobile();
            vibrar(40);
            tocarAudio('tecla.wav');

            try {
                const resp = await fetch(`/api/salas/${estadoSessao.salaId}/ajuda`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        participante_id: estadoSessao.participanteId,
                        tipo: tipo,
                        indice_convidado: indiceConvidado,
                    }),
                });

                const data = await resp.json();
                if (!resp.ok) {
                    throw new Error(data.error || 'Não foi possível utilizar esta ajuda.');
                }

                if (data.som_efeito) {
                    tocarAudio(`${data.som_efeito}.wav`, 0.95);
                }

                if (tipo === 'dica' && data.dica_biblica) {
                    estadoSessao.dicasRestantes = data.dicas_restantes ?? (estadoSessao.dicasRestantes - 1);
                    const dicaEl = document.getElementById('dicaBiblicaViva');
                    const dicaTexto = document.getElementById('conteudoDicaBiblicaTexto');
                    if (dicaTexto) dicaTexto.textContent = data.dica_biblica;
                    if (dicaEl) dicaEl.classList.remove('hidden');
                } else if (tipo === 'cartas' && Array.isArray(data.opcoes_eliminadas)) {
                    estadoSessao.cartasRestantes = 0;
                    estadoSessao.opcoesEliminadas = data.opcoes_eliminadas;
                    data.opcoes_eliminadas.forEach(idx => {
                        const btn = document.getElementById(`btnOpcaoMobile_${idx}`);
                        if (btn) {
                            btn.disabled = true;
                            btn.className = 'btn-opcao-mobile opcao-btn w-full text-left p-3.5 sm:p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3 opacity-25 border-slate-800 bg-slate-950 line-through cursor-not-allowed';
                        }
                    });
                } else if (tipo === 'convidado' && data.dica_convidado) {
                    estadoSessao.convidadosRestantes = data.convidados_restantes ?? (estadoSessao.convidadosRestantes - 1);
                    const convEl = document.getElementById('dicaConvidadoViva');
                    const convNome = document.getElementById('nomeConvidadoViva');
                    const convTexto = document.getElementById('conteudoConvidadoTexto');
                    if (convNome) convNome.textContent = `Convidado: ${data.dica_convidado.convidado}`;
                    if (convTexto) convTexto.textContent = data.dica_convidado.mensagem;
                    if (convEl) convEl.classList.remove('hidden');
                }

                atualizarBotoesAjudasMobile();
            } catch (err) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Ajuda Bíblica',
                        text: err.message,
                        background: '#0f172a',
                        color: '#f8fafc',
                    });
                } else {
                    alert(err.message);
                }
            }
        }

        function abrirModalCartasParticipante() {
            if (estadoSessao.cartasRestantes <= 0 || estadoSessao.tempoEsgotado || estadoSessao.tempoRestante <= 0 || estadoSessao.jaRespondeuAtual) return;
            vibrar(30);
            const m = document.getElementById('modalCartasMobile');
            if (m) m.classList.remove('hidden');
        }

        function fecharModalCartasParticipante() {
            const m = document.getElementById('modalCartasMobile');
            if (m) m.classList.add('hidden');
        }

        function confirmarCartaEscolhida(idx) {
            vibrar(40);
            fecharModalCartasParticipante();
            usarAjudaMobile('cartas');
        }

        function abrirModalConvidadoMobile() {
            if (estadoSessao.convidadosRestantes <= 0 || estadoSessao.tempoEsgotado || estadoSessao.tempoRestante <= 0 || estadoSessao.jaRespondeuAtual) return;
            vibrar(30);
            const m = document.getElementById('modalConvidadoMobile');
            if (m) m.classList.remove('hidden');
        }

        function fecharModalConvidadoMobile() {
            const m = document.getElementById('modalConvidadoMobile');
            if (m) m.classList.add('hidden');
        }

        // Renderização Mobile-First das Alternativas no Estilo Show do Milhão (Cápsulas)
        function renderizarPergunta(q) {
            document.getElementById('textoPerguntaViva').innerText = q.texto;

            const badgeBiblico = document.getElementById('badgeBiblicoCard');
            const textoBiblico = document.getElementById('textoBiblicoCard');
            if (badgeBiblico && textoBiblico) {
                if (q.livro_nome || q.categoria_nome) {
                    textoBiblico.textContent = (q.livro_nome ? `${q.livro_nome}` : 'SAGRADAS ESCRITURAS') + (q.categoria_nome ? ` • ${q.categoria_nome}` : '');
                } else {
                    textoBiblico.textContent = 'SAGRADAS ESCRITURAS';
                }
            }

            const container = document.getElementById('opcoesBotoes');
            const eliminadas = estadoSessao.opcoesEliminadas || [];
            const tempoZerado = estadoSessao.tempoRestante <= 0;

            container.innerHTML = (q.opcoes || []).map((opcao, idx) => {
                const isEliminada = eliminadas.includes(idx);
                const isEscolhida = estadoSessao.opcaoEscolhida !== null && Number(estadoSessao.opcaoEscolhida) === idx;
                const estaBloqueada = estadoSessao.jaRespondeuAtual || (estadoSessao.opcaoEscolhida !== null) || tempoZerado;

                let classesBtn = 'btn-opcao-mobile sm-option ';
                let tagHtml = '';

                if (isEliminada) {
                    classesBtn += 'is-eliminada';
                } else if (isEscolhida) {
                    classesBtn += 'opcao-bloqueada';
                    tagHtml = '<span class="text-[9px] font-black uppercase text-amber-950 px-2 py-0.5 rounded-full bg-amber-300">BLOQUEADA</span>';
                } else if (estaBloqueada) {
                    classesBtn += 'opacity-40 cursor-not-allowed';
                }

                return `
                    <button type="button"
                            id="btnOpcaoMobile_${idx}"
                            data-indice="${idx}"
                            ${isEliminada || estaBloqueada ? 'disabled' : ''}
                            onclick="confirmarOpcao(${idx}, '${String(opcao).replace(/'/g, "\\'")}')"
                            class="${classesBtn}">
                        <span class="flex items-center gap-2 min-w-0">
                            <span id="badgeMobile_${idx}" class="sm-letter text-base sm:text-lg">
                                ${letras[idx] || (idx + 1)}:
                            </span>
                            <span class="text-white font-extrabold text-sm sm:text-base leading-snug break-words">
                                ${opcao}
                            </span>
                        </span>
                        <span id="tagMobile_${idx}" class="shrink-0">${tagHtml}</span>
                    </button>
                `;
            }).join('');

            atualizarBotoesAjudasMobile();
        }

        async function confirmarOpcao(idx, textoOpcao) {
            if (estadoSessao.jaRespondeuAtual || estadoSessao.tempoEsgotado || estadoSessao.tempoRestante <= 0) {
                return;
            }

            vibrar(35);
            tocarAudio('tecla.wav');
            const letra = letras[idx] || (idx + 1);

            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: 'Confirmar Resposta?',
                    html: `
                        <div class="p-3 rounded-2xl bg-slate-950 border-2 border-amber-400 text-amber-300 font-black text-sm text-left flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-xl bg-amber-400 text-slate-950 font-black flex items-center justify-center shrink-0">${letra}</span>
                            <span class="break-words">${textoOpcao}</span>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'SIM, BLOQUEAR!',
                    cancelButtonText: 'Trocar',
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#334155',
                    background: '#0f172a',
                    color: '#f8fafc',
                    customClass: {
                        popup: 'rounded-3xl border-2 border-amber-400/80 shadow-2xl',
                        confirmButton: 'font-black rounded-xl px-4 py-2.5 text-slate-950 text-xs shadow-lg',
                        cancelButton: 'font-bold rounded-xl px-3 py-2.5 text-xs'
                    }
                });

                if (result.isConfirmed) {
                    if (estadoSessao.tempoRestante <= 0 || estadoSessao.tempoEsgotado) {
                        desabilitarOpcoesPorTempoEsgotado();
                        return;
                    }
                    enviarResposta(idx);
                }
            } else {
                if (confirm(`Confirmar alternativa ${letra}?`)) {
                    enviarResposta(idx);
                }
            }
        }

        function bloquearOpcaoMobile(indiceEscolhido) {
            document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                btn.disabled = true;
                const idx = String(btn.dataset.indice);
                const tag = document.getElementById(`tagMobile_${idx}`);
                if (idx === String(indiceEscolhido)) {
                    btn.className = 'btn-opcao-mobile sm-option opcao-bloqueada cursor-not-allowed';
                    if (tag) {
                        tag.innerHTML = '<span class="text-[9px] font-black uppercase text-amber-950 px-2 py-0.5 rounded-full bg-amber-300">BLOQUEADA</span>';
                    }
                } else {
                    btn.classList.add('opacity-40');
                }
            });
            atualizarBotoesAjudasMobile();
        }

        function revelarCoresOpcaoMobile(indiceEscolhido, respostaCorreta, acertouParam, questaoObj) {
            const corretaTexto = String(respostaCorreta || (questaoObj?.resposta_correta ?? '')).trim().toLowerCase();

            document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                const idx = String(btn.dataset.indice);
                btn.classList.remove('opcao-bloqueada', 'opacity-40', 'opacity-50');
                const tag = document.getElementById(`tagMobile_${idx}`);
                const textoBtn = String(btn.textContent || '').replace(/^[A-D]:\s*/, '').trim().toLowerCase();

                const ehCorreta = corretaTexto && textoBtn === corretaTexto;
                const ehEscolhida = indiceEscolhido !== null && idx === String(indiceEscolhido);
                const acertou = acertouParam !== null ? acertouParam : (ehEscolhida && ehCorreta);

                if (ehCorreta) {
                    btn.className = 'btn-opcao-mobile sm-option opcao-correta-reveal';
                    if (tag) {
                        tag.innerHTML = '<span class="text-[9px] font-black uppercase text-white px-2 py-0.5 rounded-full bg-emerald-700">' + (acertou ? 'CERTA!' : 'CORRETA') + '</span>';
                    }
                } else if (ehEscolhida && !acertou) {
                    btn.className = 'btn-opcao-mobile sm-option opcao-errada-reveal';
                    if (tag) {
                        tag.innerHTML = '<span class="text-[9px] font-black uppercase text-white px-2 py-0.5 rounded-full bg-rose-700">ERROU</span>';
                    }
                } else {
                    btn.classList.add('opacity-30');
                }
            });
        }

        async function enviarResposta(indiceOpcao) {
            if (estadoSessao.jaRespondeuAtual || estadoSessao.tempoRestante <= 0 || estadoSessao.tempoEsgotado) {
                desabilitarOpcoesPorTempoEsgotado();
                return;
            }

            estadoSessao.jaRespondeuAtual = true;
            estadoSessao.opcaoEscolhida = indiceOpcao;

            const tempoSegundos = Math.max(1, Math.min(60, Math.round((Date.now() - estadoSessao.inicioPerguntaTimestamp) / 1000)));

            bloquearOpcaoMobile(indiceOpcao);
            vibrar(40);

            try {
                const resp = await fetch(`/api/salas/${estadoSessao.salaId}/responder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        participante_id: estadoSessao.participanteId,
                        questao_id: estadoSessao.questaoAtualId || null,
                        resposta: String(indiceOpcao),
                        tempo_resposta: tempoSegundos,
                    }),
                });

                const data = await resp.json();
                if (!resp.ok) {
                    if (resp.status === 408) {
                        desabilitarOpcoesPorTempoEsgotado();
                        return;
                    }
                    const msgErro = data.error || (data.errors ? Object.values(data.errors).flat()[0] : null) || 'Não foi possível registrar a resposta.';
                    throw new Error(msgErro);
                }

                const fb = document.getElementById('feedbackResposta');
                if (fb) {
                    fb.className = 'rounded-2xl p-2.5 border bg-amber-500/15 border-amber-500/40 text-amber-200 text-xs text-center';
                    fb.innerHTML = `
                        <div class="font-black flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            <span>Resposta Bloqueada!</span>
                        </div>
                        <p class="text-[10px] text-slate-300 mt-0.5">Aguardando a revelação oficial do apresentador no telão...</p>
                    `;
                    fb.classList.remove('hidden');
                }
                sessionStorage.setItem('biblia_show_participante', JSON.stringify(estadoSessao));
            } catch (err) {
                estadoSessao.jaRespondeuAtual = false;
                estadoSessao.opcaoEscolhida = null;
                document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                    const idx = Number(btn.dataset.indice);
                    const isEliminada = (estadoSessao.opcoesEliminadas || []).includes(idx);
                    if (!isEliminada && estadoSessao.tempoRestante > 0) {
                        btn.disabled = false;
                        btn.classList.remove('opcao-bloqueada', 'opacity-40');
                        const tag = document.getElementById(`tagMobile_${idx}`);
                        if (tag) tag.innerHTML = '';
                    }
                });

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Falha no Envio',
                        text: err.message,
                        background: '#0f172a',
                        color: '#f8fafc',
                    });
                } else {
                    alert(err.message);
                }
            }
        }

        // ==============================================================
        // GAVETA DE RANKING & EQUIPES
        // ==============================================================
        function abrirDrawerRanking() {
            vibrar(25);
            const d = document.getElementById('drawerRanking');
            if (d) d.classList.remove('hidden');
        }

        function fecharDrawerRanking() {
            const d = document.getElementById('drawerRanking');
            if (d) d.classList.add('hidden');
        }

        function alternarTabRanking(tab) {
            const tabJ = document.getElementById('tabJogadores');
            const tabE = document.getElementById('tabEquipes');
            const boxJ = document.getElementById('miniRankingJogadores');
            const boxE = document.getElementById('miniRankingEquipes');
            if (tab === 'jogadores') {
                if (tabJ) tabJ.className = 'flex-1 text-xs font-black py-2 rounded-xl bg-amber-500 text-slate-950 transition';
                if (tabE) tabE.className = 'flex-1 text-xs font-black py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition';
                if (boxJ) boxJ.classList.remove('hidden');
                if (boxE) boxE.classList.add('hidden');
            } else {
                if (tabE) tabE.className = 'flex-1 text-xs font-black py-2 rounded-xl bg-amber-500 text-slate-950 transition';
                if (tabJ) tabJ.className = 'flex-1 text-xs font-black py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition';
                if (boxE) boxE.classList.remove('hidden');
                if (boxJ) boxJ.classList.add('hidden');
            }
        }

        function atualizarRanking(ranking, equipes = []) {
            const boxJ = document.getElementById('miniRankingJogadores');
            const boxE = document.getElementById('miniRankingEquipes');

            const meuItem = ranking.find(r => Number(r.id) === Number(estadoSessao.participanteId));
            if (meuItem) {
                estadoSessao.pontuacao = meuItem.pontuacao;
                document.getElementById('jogadorPontosDisplay').innerText = meuItem.pontuacao;
                if (meuItem.equipe) {
                    estadoSessao.equipeNome = meuItem.equipe;
                    document.getElementById('jogadorEquipeDisplay').textContent = meuItem.equipe;
                }
            }

            if (boxJ) {
                if (ranking.length === 0) {
                    boxJ.innerHTML = '<p class="text-slate-500 text-xs py-3 text-center">Nenhum participante conectado ainda.</p>';
                } else {
                    boxJ.innerHTML = ranking.map((p, idx) => {
                        const souEu = Number(p.id) === Number(estadoSessao.participanteId);
                        return `
                            <div class="flex items-center justify-between p-2 rounded-xl ${souEu ? 'bg-amber-500/20 border border-amber-500/40 text-amber-200' : 'bg-slate-950/70 text-slate-200'}">
                                <span class="font-bold truncate">
                                    <strong class="text-amber-400 mr-1.5">${idx + 1}º</strong> ${p.nome} ${souEu ? '(Você)' : ''}
                                    ${p.equipe ? `<span class="text-[9px] text-slate-400 block">${p.equipe}</span>` : ''}
                                </span>
                                <span class="font-mono font-black text-amber-300 text-xs">${p.pontuacao} pts</span>
                            </div>
                        `;
                    }).join('');
                }
            }

            if (boxE) {
                if (!equipes || equipes.length === 0) {
                    boxE.innerHTML = '<p class="text-slate-500 text-xs py-3 text-center">Nenhuma equipe pontuando nesta sala ainda.</p>';
                } else {
                    const equipesOrd = [...equipes].sort((a, b) => (b.pontuacao_total || 0) - (a.pontuacao_total || 0));
                    boxE.innerHTML = equipesOrd.map((eq, idx) => `
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-950/70 border border-slate-800">
                            <span class="font-bold text-white truncate">
                                <strong class="text-emerald-400 mr-1.5">${idx + 1}º</strong> ${eq.nome}
                            </span>
                            <span class="font-mono font-black text-emerald-300 text-xs">${eq.pontuacao_total || 0} pts</span>
                        </div>
                    `).join('');
                }
            }
        }

        // ==============================================================
        // MODAIS DE EQUIPES
        // ==============================================================
        function abrirModalTrocarEquipe() {
            fecharDrawerRanking();
            const m = document.getElementById('modalTrocarEquipe');
            const list = document.getElementById('listaEquipesModal');
            if (!m || !list) return;

            const equipes = estadoSessao.equipes || [];
            if (equipes.length === 0) {
                list.innerHTML = '<p class="text-xs text-slate-400 p-2.5 bg-slate-950 rounded-xl text-center">Nenhuma equipe criada na sala. Digite um nome abaixo para fundar!</p>';
            } else {
                list.innerHTML = equipes.map(eq => `
                    <div class="flex items-center justify-between p-2 rounded-xl bg-slate-950 border border-slate-800 hover:border-amber-400 transition">
                        <div>
                            <p class="font-black text-xs text-white">${eq.nome}</p>
                            <p class="text-[9px] text-amber-400 font-bold">${eq.pontuacao_total || 0} pts</p>
                        </div>
                        <button type="button" onclick="submeterTrocaEquipe(${eq.id}, null)"
                                class="px-2.5 py-1 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-black text-[10px] transition active:scale-95">
                            Entrar →
                        </button>
                    </div>
                `).join('');
            }

            m.classList.remove('hidden');
        }

        function fecharModalTrocarEquipe() {
            const m = document.getElementById('modalTrocarEquipe');
            if (m) m.classList.add('hidden');
        }

        async function submeterTrocaEquipe(equipeId, equipeNome) {
            if (!estadoSessao.salaId || !estadoSessao.participanteId) return;

            try {
                const resp = await fetch(`/api/salas/${estadoSessao.salaId}/participantes/${estadoSessao.participanteId}/equipe`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        equipe_id: equipeId,
                        equipe_nome: equipeNome,
                    }),
                });

                const data = await resp.json();
                if (!resp.ok) throw new Error(data.error || 'Erro ao alterar equipe.');

                estadoSessao.equipeNome = data.equipe_nome || '';
                document.getElementById('jogadorEquipeDisplay').textContent = data.equipe_nome ? data.equipe_nome : 'Individual';
                sessionStorage.setItem('biblia_show_participante', JSON.stringify(estadoSessao));
                fecharModalTrocarEquipe();
                vibrar(30);
            } catch (e) {
                alert(e.message);
            }
        }

        function criarEquipeModal() {
            const inp = document.getElementById('inputNovaEquipeModal');
            const nome = inp ? inp.value.trim() : '';
            if (!nome) return;
            submeterTrocaEquipe(null, nome);
            if (inp) inp.value = '';
        }

        function sairEquipeModal() {
            submeterTrocaEquipe(null, null);
        }

        function sairDaSala() {
            sessionStorage.removeItem('biblia_show_participante');
            location.reload();
        }
    </script>
</body>
</html>