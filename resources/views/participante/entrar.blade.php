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
<body class="milhao-bg text-slate-100 min-h-screen flex flex-col justify-between">

    @include('partials.navbar', ['dark' => true, 'hideOnMobile' => true])

    <main class="w-full max-w-xl mx-auto px-3 sm:px-4 py-2 sm:py-5 flex-1 flex flex-col justify-between">

        <!-- ============================================================== -->
        <!-- ETAPA 1: Formulário de Entrada na Sala (Antes do Jogo)        -->
        <!-- ============================================================== -->
        <div id="etapaEntrada" class="my-auto bg-slate-900/95 border border-slate-700/80 rounded-3xl shadow-2xl p-5 sm:p-7">
            <div class="text-center mb-5">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center mx-auto mb-3 text-amber-400 shadow-lg shadow-amber-500/10">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-white">Entrar na Arena Ao Vivo</h1>
                <p class="text-slate-400 text-xs sm:text-sm mt-1">
                    {{ $sala ? $sala->nome : 'Digite o código da sala exibido no projetor' }}
                </p>
            </div>

            @if($sala && ($sala->premio_primeiro_lugar || $sala->premio_segundo_lugar))
                <div class="mb-4 p-3 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-xs space-y-1">
                    <p class="text-[10px] font-black uppercase tracking-wider text-amber-400">Prêmios em Disputa:</p>
                    @if($sala->premio_primeiro_lugar)
                        <p class="text-white font-bold"><strong class="text-amber-300">1º Lugar:</strong> {{ $sala->premio_primeiro_lugar }}</p>
                    @endif
                    @if($sala->premio_segundo_lugar)
                        <p class="text-slate-300 font-semibold"><strong class="text-slate-200">2º Lugar:</strong> {{ $sala->premio_segundo_lugar }}</p>
                    @endif
                </div>
            @endif

            <form id="entrarForm" class="space-y-3.5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Código da Sala (PIN)</label>
                    <input type="text" id="inputCodigo" name="codigo_acesso" required maxlength="6"
                           value="{{ $sala?->codigo_acesso ?? $codigo ?? '' }}"
                           placeholder="Ex.: AB12CD"
                           class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-amber-400 font-mono font-black uppercase text-xl tracking-widest text-center focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Seu Nome / Apelido</label>
                    <input type="text" id="inputNome" name="nome" required
                           placeholder="Como quer ser chamado no telão?"
                           class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-white font-bold text-base focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Equipe / Célula (Opcional)</label>
                    <div id="containerSelecaoEquipe" class="space-y-2">
                        <select id="selectEquipe" name="equipe_id"
                                onchange="aoMudarSelectEquipe(this)"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white text-xs font-semibold focus:ring-2 focus:ring-amber-500 outline-none">
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
                                   class="w-full px-4 py-2 rounded-xl border border-amber-500/50 bg-slate-950 text-amber-300 text-xs font-bold focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                    </div>
                </div>

                <button type="submit" id="btnEntrar"
                        class="w-full bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black py-3.5 rounded-xl transition shadow-lg shadow-amber-500/20 text-base active:scale-95">
                    Entrar na Sala →
                </button>
            </form>

            <div id="errorEntrada" class="hidden bg-rose-950/80 border border-rose-500/40 p-3 rounded-xl text-rose-200 text-xs mt-3 text-center"></div>
        </div>

        <!-- ============================================================== -->
        <!-- ETAPA 2: TELA MOBILE-FIRST DE JOGO AO VIVO                     -->
        <!-- ============================================================== -->
        <div id="etapaJogo" class="hidden flex-1 flex flex-col justify-between gap-2.5 w-full">

            <!-- 1. HEADER DO APP: Identidade, Pontos e Controles Rápidos -->
            <header class="bg-slate-900/90 backdrop-blur-md border border-slate-800 rounded-2xl p-2.5 sm:p-3 shadow-lg flex items-center justify-between gap-2">
                <!-- Esquerda: Avatar + Nome + Sala PIN -->
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-lg flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0" id="jogadorAvatarDisplay">
                        P
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h3 class="font-black text-white text-sm sm:text-base truncate leading-none" id="jogadorNomeDisplay">-</h3>
                            <span class="text-[9px] font-mono font-black px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 shrink-0" id="codigoSalaBadge">-</span>
                        </div>
                        <p class="text-[10px] text-amber-400 font-bold truncate mt-0.5" id="jogadorEquipeDisplay">Individual</p>
                    </div>
                </div>

                <!-- Direita: Pontos + Botões Rápidos (Ranking, Som, Sair) -->
                <div class="flex items-center gap-2 shrink-0">
                    <div class="text-right px-2 py-1 rounded-xl bg-slate-950/80 border border-slate-800">
                        <p class="text-[8px] uppercase tracking-wider text-slate-400 font-bold leading-none">Pontos</p>
                        <p class="text-base sm:text-lg font-black text-amber-400 font-mono leading-none mt-0.5">
                            <span id="jogadorPontosDisplay">0</span>
                        </p>
                    </div>

                    <!-- Botão Toggle Som/Vibração -->
                    <button type="button" onclick="alternarSomMobile()" id="btnToggleSomMobile" title="Ativar/Desativar Sons e Vibração"
                            class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 flex items-center justify-center border border-slate-700 active:scale-95 transition">
                        <svg id="iconeSomLigado" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.414 0-.75-.336-.75-.75V8.25c0-.414.336-.75.75-.75h2.24z" />
                        </svg>
                        <svg id="iconeSomMudo" class="w-4 h-4 hidden text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-3.75l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.414 0-.75-.336-.75-.75V8.25c0-.414.336-.75.75-.75h2.24z" />
                        </svg>
                    </button>

                    <!-- Botão Abrir Gaveta do Ranking / Equipes -->
                    <button type="button" onclick="abrirDrawerRanking()" title="Ver Ranking da Sala"
                            class="w-9 h-9 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 flex items-center justify-center active:scale-95 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004-6.75A4.5 4.5 0 1012 3a4.5 4.5 0 005.004 5.625z" />
                        </svg>
                    </button>
                </div>
            </header>

            <!-- 2. PLACAR COMPACTO SHOW DO MILHÃO (ERRAR | PARAR | ACERTAR) -->
            <div class="grid grid-cols-3 gap-1.5 px-1">
                <div class="py-1.5 px-2 rounded-xl bg-rose-950/40 border border-rose-500/30 text-center">
                    <p class="text-[8px] font-black uppercase text-rose-300">Se Errar</p>
                    <p class="text-xs sm:text-sm font-black text-rose-400 font-mono" id="placarErrarDisplay">0 <span class="text-[8px]">pts</span></p>
                </div>
                <div class="py-1.5 px-2 rounded-xl bg-slate-950/90 border border-slate-700 text-center">
                    <p class="text-[8px] font-black uppercase text-amber-300">Parar (Atual)</p>
                    <p class="text-xs sm:text-sm font-black text-amber-400 font-mono" id="placarPararDisplay">0 <span class="text-[8px]">pts</span></p>
                </div>
                <div class="py-1.5 px-2 rounded-xl bg-emerald-950/50 border border-emerald-500/40 text-center">
                    <p class="text-[8px] font-black uppercase text-emerald-300">Se Acertar</p>
                    <p class="text-xs sm:text-sm font-black text-emerald-400 font-mono" id="placarAcertarDisplay">10 <span class="text-[8px]">pts</span></p>
                </div>
            </div>

            <!-- 3. FEEDBACK DA RESPOSTA (Aparece ao bloquear ou pós-revelação) -->
            <div id="feedbackResposta" class="hidden rounded-2xl p-3 border text-xs text-center transition-all"></div>

            <!-- 4. BANNER DE DICAS OU CONVIDADO ATIVOS -->
            <div id="dicaBiblicaViva" class="hidden p-2.5 rounded-xl bg-amber-500/20 border border-amber-400/40 text-xs font-bold text-amber-200 flex items-center gap-2">
                <span class="text-base shrink-0">📖</span>
                <div class="min-w-0">
                    <span class="text-[9px] uppercase font-black tracking-wider text-amber-400 block">Dica Bíblica:</span>
                    <span id="conteudoDicaBiblicaTexto" class="truncate block">-</span>
                </div>
            </div>

            <div id="dicaConvidadoViva" class="hidden p-2.5 rounded-xl bg-blue-950/90 border border-blue-400/40 text-xs font-bold text-blue-100 flex items-center gap-2">
                <span class="text-base shrink-0">👥</span>
                <div class="min-w-0">
                    <span class="text-[9px] uppercase font-black tracking-wider text-blue-300 block" id="nomeConvidadoViva">Convidado Bíblico:</span>
                    <span id="conteudoConvidadoTexto" class="truncate block">-</span>
                </div>
            </div>

            <!-- 5. CRONÔMETRO COMPACTO COM BARRA REGRESSIVA -->
            <div id="containerSuperCronometro" class="p-2 sm:p-2.5 rounded-2xl bg-slate-950/90 border-2 border-amber-500/40 shadow-inner flex flex-col gap-1.5 transition-all">
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-1.5">
                        <span id="faseBadge" class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-500 text-slate-950">
                            1ª Fase
                        </span>
                        <span id="perguntaContadorBadge" class="text-[10px] text-amber-300 font-extrabold">
                            Pergunta 1/24
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase" id="labelStatusCronometro">Tempo</span>
                        <span id="tempoRestanteDisplay" class="text-xl sm:text-2xl font-black font-mono text-white tracking-tight leading-none">60</span><span class="text-[10px] font-bold text-amber-400">s</span>
                    </div>
                </div>
                <!-- Barra de progresso do tempo -->
                <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                    <div id="barraProgressoTempo" class="bg-gradient-to-r from-amber-500 to-amber-300 h-1.5 rounded-full transition-all duration-300" style="width: 100%;"></div>
                </div>
            </div>

            <!-- AVISO DE TEMPO ESGOTADO SILENCIOSO (Sem alert) -->
            <div id="avisoTempoEsgotado" class="hidden p-2 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-200 text-xs font-bold text-center">
                ⏱️ Tempo esgotado para esta pergunta! Aguarde o apresentador.
            </div>

            <!-- 6. CARD PRINCIPAL DA PERGUNTA BÍBLICA -->
            <div class="question-card rounded-2xl p-3.5 sm:p-5 shadow-xl flex flex-col justify-center min-h-[90px] sm:min-h-[110px]">
                <div class="flex items-center justify-between gap-1 mb-1.5 text-[10px] font-black uppercase tracking-wider text-amber-400" id="badgeBiblicoCard">
                    <span id="textoBiblicoCard">Sagradas Escrituras</span>
                    <span id="valorPontosBadge" class="text-amber-300 font-extrabold">Vale 10 pts</span>
                </div>
                <p id="textoPerguntaViva" class="text-sm sm:text-base md:text-lg font-black text-white leading-snug">
                    Aguardando início da pergunta...
                </p>
            </div>

            <!-- 7. BOTÕES DAS 4 ALTERNATIVAS (ESTILO MOBILE GAME TÁTIL KAHOOT/HQ TRIVIA) -->
            <div id="opcoesBotoes" class="grid grid-cols-1 gap-2 sm:gap-2.5">
                <!-- Inserido dinamicamente via JS -->
            </div>

            <!-- 8. DOCK DE AJUDAS BÍBLICAS NO RODAPÉ -->
            <div id="barraAjudasJogador" class="bg-slate-900/90 border border-slate-800 rounded-2xl p-2 shadow-lg">
                <div class="grid grid-cols-3 gap-2">
                    <!-- Ajuda: Dica -->
                    <button type="button" id="btnAjudaMobileDica" onclick="usarAjudaMobile('dica')"
                            class="p-2 rounded-xl border border-blue-500/30 bg-blue-950/60 hover:bg-blue-900 active:scale-95 transition text-center disabled:opacity-40 disabled:cursor-not-allowed">
                        <span class="text-xs font-black text-white block">📖 Dica</span>
                        <span id="badgeDicasRestantesMobile" class="text-[9px] text-amber-300 font-bold block">3 restantes</span>
                    </button>

                    <!-- Ajuda: Cartas 50/50 -->
                    <button type="button" id="btnAjudaMobileCartas" onclick="usarAjudaMobile('cartas')"
                            class="p-2 rounded-xl border border-blue-500/30 bg-blue-950/60 hover:bg-blue-900 active:scale-95 transition text-center disabled:opacity-40 disabled:cursor-not-allowed">
                        <span class="text-xs font-black text-white block">🃏 50/50</span>
                        <span id="badgeCartasRestantesMobile" class="text-[9px] text-amber-300 font-bold block">Eliminar 2</span>
                    </button>

                    <!-- Ajuda: Convidados -->
                    <button type="button" id="btnAjudaMobileConvidado" onclick="abrirModalConvidadoMobile()"
                            class="p-2 rounded-xl border border-blue-500/30 bg-blue-950/60 hover:bg-blue-900 active:scale-95 transition text-center disabled:opacity-40 disabled:cursor-not-allowed">
                        <span class="text-xs font-black text-white block">👥 Convidado</span>
                        <span id="badgeConvidadosRestantesMobile" class="text-[9px] text-amber-300 font-bold block">3 restantes</span>
                    </button>
                </div>
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
                    <span class="text-xl">🏆</span>
                    <h3 class="text-base sm:text-lg font-black text-white">Placar da Sala Ao Vivo</h3>
                </div>
                <button type="button" onclick="fecharDrawerRanking()" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center font-black">
                    ✕
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

    <!-- Modal Escolha de Convidado Bíblico -->
    <div id="modalConvidadoMobile" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-sm w-full p-5 text-slate-100 shadow-2xl relative">
            <button type="button" onclick="fecharModalConvidadoMobile()" class="absolute top-4 right-4 text-slate-400 hover:text-white font-black text-lg">✕</button>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xl">👥</span>
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
            const barraEl = document.getElementById('barraProgressoTempo');
            const boxEl = document.getElementById('containerSuperCronometro');
            const labelEl = document.getElementById('labelStatusCronometro');

            if (timerEl) timerEl.innerText = Math.max(0, segundos);

            const pct = Math.max(0, Math.min(100, (segundos / 60) * 100));
            if (barraEl) {
                barraEl.style.width = `${pct}%`;
            }

            if (segundos <= 0) {
                if (boxEl) {
                    boxEl.className = 'p-2 sm:p-2.5 rounded-2xl bg-slate-950/90 border-2 border-slate-700 shadow-inner flex flex-col gap-1.5 transition-all';
                }
                if (labelEl) {
                    labelEl.textContent = 'Encerrado';
                    labelEl.className = 'text-[10px] font-bold text-rose-400 uppercase';
                }
                if (barraEl) {
                    barraEl.className = 'bg-rose-500 h-1.5 rounded-full transition-all duration-300';
                }
            } else if (segundos <= 10) {
                if (boxEl) {
                    boxEl.className = 'p-2 sm:p-2.5 rounded-2xl border-2 timer-critico shadow-inner flex flex-col gap-1.5 transition-all';
                }
                if (labelEl) {
                    labelEl.textContent = 'Atenção!';
                    labelEl.className = 'text-[10px] font-black text-rose-300 uppercase animate-pulse';
                }
                if (barraEl) {
                    barraEl.className = 'bg-rose-500 h-1.5 rounded-full transition-all duration-300';
                }
            } else {
                if (boxEl) {
                    boxEl.className = 'p-2 sm:p-2.5 rounded-2xl bg-slate-950/90 border-2 border-amber-500/40 shadow-inner flex flex-col gap-1.5 transition-all';
                }
                if (labelEl) {
                    labelEl.textContent = 'Tempo';
                    labelEl.className = 'text-[10px] font-bold text-slate-400 uppercase';
                }
                if (barraEl) {
                    barraEl.className = 'bg-gradient-to-r from-amber-500 to-amber-300 h-1.5 rounded-full transition-all duration-300';
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

        function abrirModalConvidadoMobile() {
            if (estadoSessao.convidadosRestantes <= 0 || estadoSessao.tempoEsgotado || estadoSessao.tempoRestante <= 0) return;
            vibrar(30);
            const m = document.getElementById('modalConvidadoMobile');
            if (m) m.classList.remove('hidden');
        }

        function fecharModalConvidadoMobile() {
            const m = document.getElementById('modalConvidadoMobile');
            if (m) m.classList.add('hidden');
        }

        // Renderização Mobile-First das Alternativas Grandes e Táteis
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
                const estiloKahoot = coresKahoot[idx % 4];

                let classesBtn = 'btn-opcao-mobile opcao-btn w-full text-left p-3.5 sm:p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3 shadow-md ';
                let tagHtml = '';

                if (isEliminada) {
                    classesBtn += 'opacity-25 border-slate-800 bg-slate-950 line-through cursor-not-allowed';
                } else if (isEscolhida) {
                    classesBtn += 'opcao-bloqueada cursor-not-allowed';
                    tagHtml = '<span class="shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-amber-400 text-slate-950">BLOQUEADA 🔒</span>';
                } else if (estaBloqueada) {
                    classesBtn += 'opacity-40 border-slate-800 bg-slate-950 cursor-not-allowed';
                } else {
                    classesBtn += `${estiloKahoot.border} ${estiloKahoot.bg} hover:border-amber-400 active:scale-[0.98] cursor-pointer`;
                }

                return `
                    <button type="button"
                            id="btnOpcaoMobile_${idx}"
                            data-indice="${idx}"
                            ${isEliminada || estaBloqueada ? 'disabled' : ''}
                            onclick="confirmarOpcao(${idx}, '${String(opcao).replace(/'/g, "\\'")}')"
                            class="${classesBtn}">
                        <div class="flex items-center gap-3 min-w-0">
                            <span id="badgeMobile_${idx}" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl ${isEscolhida ? 'bg-amber-400 text-slate-950' : estiloKahoot.letraBg} font-black flex items-center justify-center shrink-0 text-sm shadow">
                                ${letras[idx] || (idx + 1)}
                            </span>
                            <span class="font-bold text-white text-xs sm:text-sm leading-snug break-words">${opcao}</span>
                        </div>
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
                    btn.className = 'btn-opcao-mobile opcao-btn w-full text-left p-3.5 sm:p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3 opcao-bloqueada cursor-not-allowed';
                    if (tag) {
                        tag.innerHTML = '<span class="shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-amber-400 text-slate-950">BLOQUEADA 🔒</span>';
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
                const badge = document.getElementById(`badgeMobile_${idx}`);
                const textoBtn = String(btn.querySelector('.font-bold')?.textContent || '').trim().toLowerCase();

                const ehCorreta = corretaTexto && textoBtn === corretaTexto;
                const ehEscolhida = indiceEscolhido !== null && idx === String(indiceEscolhido);
                const acertou = acertouParam !== null ? acertouParam : (ehEscolhida && ehCorreta);

                if (ehCorreta) {
                    btn.className = 'btn-opcao-mobile opcao-btn w-full text-left p-3.5 sm:p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3 opcao-correta-reveal';
                    if (badge) badge.className = 'w-8 h-8 rounded-xl bg-white text-emerald-800 font-black flex items-center justify-center shrink-0 text-sm';
                    if (tag) {
                        tag.innerHTML = '<span class="shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-white text-emerald-800">' + (acertou ? 'CERTA!' : 'CORRETA') + '</span>';
                    }
                } else if (ehEscolhida && !acertou) {
                    btn.className = 'btn-opcao-mobile opcao-btn w-full text-left p-3.5 sm:p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3 opcao-errada-reveal';
                    if (badge) badge.className = 'w-8 h-8 rounded-xl bg-white text-rose-800 font-black flex items-center justify-center shrink-0 text-sm';
                    if (tag) {
                        tag.innerHTML = '<span class="shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-white text-rose-800">ERROU</span>';
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
                            <span>🔒 Resposta Bloqueada!</span>
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