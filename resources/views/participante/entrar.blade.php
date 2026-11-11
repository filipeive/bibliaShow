    <!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Participar ao Vivo — Bíblia Show</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
        .question-banner { background: linear-gradient(180deg, #991b1b 0%, #7f1d1d 100%); border: 2px solid #fbbf24; }
        .opcao-bloqueada {
            background: linear-gradient(90deg, rgba(217, 119, 6, 0.45) 0%, rgba(120, 53, 15, 0.6) 100%) !important;
            border-color: #fbbf24 !important;
        }
        .opcao-correta-reveal {
            background: linear-gradient(90deg, rgba(5, 150, 105, 0.92) 0%, rgba(6, 95, 70, 0.95) 100%) !important;
            border-color: #34d399 !important;
        }
        .opcao-errada-reveal {
            background: linear-gradient(90deg, rgba(225, 29, 72, 0.92) 0%, rgba(136, 19, 55, 0.95) 100%) !important;
            border-color: #fb7185 !important;
        }
        .timer-critico {
            animation: pulse-critico 1s infinite;
        }
        @keyframes pulse-critico {
            0%, 100% { border-color: #ef4444; background-color: rgba(136, 19, 55, 0.7); }
            50% { border-color: #fca5a5; background-color: rgba(159, 18, 57, 0.9); }
        }
    </style>
</head>
<body class="milhao-bg text-slate-100 min-h-screen">

    @include('partials.navbar', ['dark' => true])

    <div class="max-w-2xl sm:max-w-3xl lg:max-w-4xl mx-auto px-4 py-5 sm:py-7">

        <!-- Badge da Sala Ativa -->
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-black uppercase tracking-wider text-amber-400 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                Arena Multiplayer
            </span>
            <span id="badgeSala" class="text-xs font-mono font-black px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 {{ $sala ? '' : 'hidden' }}">
                SALA: <span id="codigoSalaTexto">{{ $sala?->codigo_acesso }}</span>
            </span>
        </div>

        <!-- ETAPA 1: Formulário de Entrada na Sala -->
        <div id="etapaEntrada" class="bg-slate-900/95 border border-slate-700/80 rounded-3xl shadow-2xl p-6 sm:p-8">
            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center mx-auto mb-3 text-amber-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                </div>
                <h1 class="text-2xl font-black text-white">Entrar na Sala Ao Vivo</h1>
                <p class="text-slate-400 text-sm mt-1">
                    {{ $sala ? $sala->nome : 'Digite o código exibido no projetor da igreja para jogar' }}
                </p>
            </div>

            @if($sala && ($sala->premio_primeiro_lugar || $sala->premio_segundo_lugar))
                <div class="mb-5 p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 space-y-1">
                    <p class="text-[11px] font-black uppercase tracking-wider text-amber-400">Prêmios desta Sala:</p>
                    @if($sala->premio_primeiro_lugar)
                        <p class="text-xs text-white font-bold"><strong class="text-amber-300">1º Lugar:</strong> {{ $sala->premio_primeiro_lugar }}</p>
                    @endif
                    @if($sala->premio_segundo_lugar)
                        <p class="text-xs text-slate-300 font-semibold"><strong class="text-slate-200">2º Lugar:</strong> {{ $sala->premio_segundo_lugar }}</p>
                    @endif
                </div>
            @endif

            <form id="entrarForm" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Código da Sala (6 caracteres)</label>
                    <input type="text" id="inputCodigo" name="codigo_acesso" required maxlength="6"
                           value="{{ $sala?->codigo_acesso ?? $codigo ?? '' }}"
                           placeholder="Ex.: AB12CD"
                           class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-amber-400 font-mono font-black uppercase text-lg tracking-widest focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Seu Nome</label>
                    <input type="text" id="inputNome" name="nome" required
                           placeholder="Digite seu nome"
                           class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Equipe / Célula (Opcional)</label>
                    <div id="containerSelecaoEquipe" class="space-y-2 mb-2">
                        <select id="selectEquipe" name="equipe_id"
                                onchange="aoMudarSelectEquipe(this)"
                                class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-white text-sm font-semibold focus:ring-2 focus:ring-amber-500 outline-none">
                            <option value="">Nenhuma equipe (Jogar individualmente na sala)</option>
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
                                   placeholder="Digite o nome da nova equipe ou célula"
                                   class="w-full px-4 py-2.5 rounded-xl border border-amber-500/50 bg-slate-950 text-amber-300 text-sm font-bold focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                    </div>
                </div>

                <button type="submit" id="btnEntrar"
                        class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-black py-4 rounded-xl transition shadow-lg text-base">
                    Entrar e Jogar →
                </button>
            </form>

            <div id="errorEntrada" class="hidden bg-rose-950/80 border border-rose-500/40 p-3.5 rounded-xl text-rose-200 text-sm mt-4"></div>
        </div>

        <!-- ETAPA 2: Tela de Jogo ao Vivo no Smartphone (Organizada no estilo Modo Individual) -->
        <div id="etapaJogo" class="hidden space-y-4">

            <!-- 1. Card do Participante no Palco + Placar Show do Milhão -->
            <div class="bg-slate-900/95 border border-slate-800 rounded-3xl p-4 sm:p-5 shadow-xl space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-xl flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0" id="jogadorAvatarDisplay">
                            P
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Participante no Palco</p>
                            <h3 class="font-black text-white text-base sm:text-lg truncate" id="jogadorNomeDisplay">-</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-amber-400 font-bold truncate" id="jogadorEquipeDisplay">Individual</span>
                                <button type="button" onclick="abrirModalTrocarEquipe()" class="text-[10px] font-black uppercase px-2 py-0.5 rounded-lg bg-blue-600/30 hover:bg-blue-600 text-blue-300 hover:text-white border border-blue-500/40 transition shrink-0">
                                    Equipes ▾
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[10px] text-slate-400 uppercase font-black">Seus Pontos</p>
                        <p class="text-2xl sm:text-3xl font-black text-amber-400 font-mono leading-none mt-1">
                            <span id="jogadorPontosDisplay">0</span> <span class="text-xs font-normal text-slate-400">pts</span>
                        </p>
                    </div>
                </div>

                <!-- Placar Estilo Show do Milhão: ERRAR | PARAR | ACERTAR -->
                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-800/80">
                    <div class="p-2.5 rounded-2xl bg-rose-950/40 border border-rose-500/30 text-center">
                        <p class="text-[9px] font-black uppercase tracking-wider text-rose-300">Se Errar</p>
                        <p class="text-sm sm:text-base font-black text-rose-400 font-mono" id="placarErrarDisplay">0 <span class="text-[9px]">pts</span></p>
                    </div>
                    <div class="p-2.5 rounded-2xl bg-slate-950 border border-slate-700 text-center">
                        <p class="text-[9px] font-black uppercase tracking-wider text-amber-300">Parar (Atual)</p>
                        <p class="text-sm sm:text-base font-black text-amber-400 font-mono" id="placarPararDisplay">0 <span class="text-[9px]">pts</span></p>
                    </div>
                    <div class="p-2.5 rounded-2xl bg-emerald-950/50 border border-emerald-500/40 text-center">
                        <p class="text-[9px] font-black uppercase tracking-wider text-emerald-300">Se Acertar</p>
                        <p class="text-sm sm:text-base font-black text-emerald-400 font-mono" id="placarAcertarDisplay">10 <span class="text-[9px]">pts</span></p>
                    </div>
                </div>
            </div>

            <!-- Prêmios da Sala em disputa -->
            <div id="boxPremiosSala" class="hidden bg-amber-500/10 border border-amber-500/30 rounded-2xl p-3.5 flex flex-wrap items-center justify-between gap-2 text-xs">
                <span class="font-black uppercase tracking-wider text-amber-400">Prêmios em Disputa:</span>
                <div class="flex flex-wrap gap-3">
                    <span id="premio1Display" class="font-bold text-white"></span>
                    <span id="premio2Display" class="font-semibold text-slate-300"></span>
                </div>
            </div>

            <!-- Feedback da Resposta na Rodada -->
            <div id="feedbackResposta" class="hidden rounded-2xl p-4 border"></div>

            <!-- 2. Card Principal da Pergunta Ao Vivo -->
            <div id="cardPerguntaViva" class="bg-slate-900/95 border-2 border-amber-500/40 rounded-3xl p-5 sm:p-6 shadow-2xl space-y-4">

                <!-- Topo Bíblico + Fase + Valor da Pergunta -->
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span id="progressoPergunta" class="text-xs font-black uppercase tracking-wider px-3 py-1 rounded-full bg-amber-500 text-slate-950">
                            1ª Fase • Pergunta 1/24
                        </span>
                        <span id="nivelPergunta" class="text-xs text-amber-300 font-extrabold px-2.5 py-0.5 rounded-full bg-amber-500/15 border border-amber-500/30">
                            Vale 10 pts
                        </span>
                    </div>
                    <div id="badgeBiblicoCard" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs font-black uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                        <span id="textoBiblicoCard">Sagradas Escrituras</span>
                    </div>
                </div>

                <!-- SUPER CRONÔMETRO DESTACADO COM BARRA REGRESSIVA -->
                <div id="containerSuperCronometro" class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/90 border-2 border-amber-500/40 shadow-inner transition-all flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div id="iconeCronometroBox" class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="13" r="8"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4l2.5 2.5M9 2h6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Tempo para Responder</p>
                                <p id="labelStatusCronometro" class="text-xs font-bold text-amber-300">Rodada em andamento</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span id="tempoRestanteDisplay" class="text-3xl sm:text-4xl font-black font-mono text-white tracking-tight">60</span><span class="text-sm font-bold text-amber-400 ml-1">s</span>
                        </div>
                    </div>
                    <!-- Barra visual decrescente -->
                    <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                        <div id="barraProgressoTempo" class="bg-gradient-to-r from-amber-500 to-amber-300 h-2 rounded-full transition-all duration-300" style="width: 100%;"></div>
                    </div>
                </div>

                <!-- AVISO SUAVE DE TEMPO ESGOTADO (Sem alerts invasivos) -->
                <div id="avisoTempoEsgotado" class="hidden p-3.5 rounded-2xl bg-rose-950/60 border border-rose-500/40 text-rose-200 text-xs font-bold text-center flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Tempo esgotado para esta pergunta! Respostas encerradas. Aguarde o apresentador.</span>
                </div>

                <!-- BARRA DE AJUDAS DO JOGADOR (Concedidas pelas regras do jogo) -->
                <div id="barraAjudasJogador" class="p-3 rounded-2xl bg-slate-950/90 border border-amber-500/30">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            Ajudas Bíblicas Disponíveis
                        </span>
                        <span class="text-[10px] text-slate-400">Regras do Jogo</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <!-- 1. Dica Bíblica -->
                        <button type="button" id="btnAjudaMobileDica" onclick="usarAjudaMobile('dica')"
                                class="p-2 sm:p-2.5 rounded-xl border border-blue-500/40 bg-blue-950/70 hover:bg-blue-900 active:scale-95 transition text-center disabled:opacity-40 disabled:cursor-not-allowed">
                            <span class="text-[11px] sm:text-xs font-black text-white block">📖 Dica</span>
                            <span id="badgeDicasRestantesMobile" class="text-[10px] text-amber-300 font-bold">3 restantes</span>
                        </button>

                        <!-- 2. Cartas 50/50 -->
                        <button type="button" id="btnAjudaMobileCartas" onclick="usarAjudaMobile('cartas')"
                                class="p-2 sm:p-2.5 rounded-xl border border-blue-500/40 bg-blue-950/70 hover:bg-blue-900 active:scale-95 transition text-center disabled:opacity-40 disabled:cursor-not-allowed">
                            <span class="text-[11px] sm:text-xs font-black text-white block">🃏 Cartas 50/50</span>
                            <span id="badgeCartasRestantesMobile" class="text-[10px] text-amber-300 font-bold">Eliminar 2</span>
                        </button>

                        <!-- 3. Convidados Bíblicos -->
                        <button type="button" id="btnAjudaMobileConvidado" onclick="abrirModalConvidadoMobile()"
                                class="p-2 sm:p-2.5 rounded-xl border border-blue-500/40 bg-blue-950/70 hover:bg-blue-900 active:scale-95 transition text-center disabled:opacity-40 disabled:cursor-not-allowed">
                            <span class="text-[11px] sm:text-xs font-black text-white block">👥 Convidados</span>
                            <span id="badgeConvidadosRestantesMobile" class="text-[10px] text-amber-300 font-bold">3 restantes</span>
                        </button>
                    </div>
                </div>

                <!-- Dica Bíblica / Convidado Bíblico revelados -->
                <div id="dicaBiblicaViva" class="hidden p-3.5 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-xs sm:text-sm font-bold text-amber-200 flex items-start gap-2.5">
                    <span class="text-base shrink-0">📖</span>
                    <div>
                        <p class="text-[10px] uppercase font-black tracking-wider text-amber-400 mb-0.5">Referência Bíblica:</p>
                        <p id="conteudoDicaBiblicaTexto">-</p>
                    </div>
                </div>

                <div id="dicaConvidadoViva" class="hidden p-3.5 rounded-2xl bg-blue-950/90 border border-blue-400/40 text-xs sm:text-sm font-bold text-blue-100 flex items-start gap-2.5">
                    <span class="text-base shrink-0">👥</span>
                    <div>
                        <p class="text-[10px] uppercase font-black tracking-wider text-blue-300 mb-0.5" id="nomeConvidadoViva">Convidado Bíblico:</p>
                        <p id="conteudoConvidadoTexto">-</p>
                    </div>
                </div>

                <!-- Banner Clássico Vermelho Escuro com Borda Dourada -->
                <div class="question-banner rounded-2xl p-4 sm:p-6 shadow-xl">
                    <p id="textoPerguntaViva" class="text-base sm:text-xl font-black text-white leading-relaxed">
                        Carregando pergunta da sala...
                    </p>
                </div>

                <!-- Alternativas A, B, C, D -->
                <div id="opcoesBotoes" class="space-y-2.5"></div>
            </div>

            <!-- Ranking da Sala com Alternância Jogadores e Equipes -->
            <div class="bg-slate-900/95 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3 border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <button type="button" id="tabJogadores" onclick="alternarTabRanking('jogadores')"
                                class="text-xs font-black px-3 py-1.5 rounded-xl bg-amber-500 text-slate-950 transition">
                            Jogadores
                        </button>
                        <button type="button" id="tabEquipes" onclick="alternarTabRanking('equipes')"
                                class="text-xs font-black px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition">
                            Equipes / Células
                        </button>
                    </div>
                    <button type="button" onclick="sairDaSala()" class="text-xs text-slate-400 hover:text-rose-400 font-bold transition">
                        Sair da Sala
                    </button>
                </div>
                <div id="miniRankingJogadores" class="space-y-2 text-sm"></div>
                <div id="miniRankingEquipes" class="hidden space-y-2 text-sm"></div>
            </div>
        </div>

    </div>

    <!-- Modal para Juntar-se ou Trocar de Equipe na Sala -->
    <div id="modalTrocarEquipe" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-md w-full p-6 text-slate-100 shadow-2xl relative">
            <button type="button" onclick="fecharModalTrocarEquipe()" class="absolute top-4 right-4 text-slate-400 hover:text-white font-black text-lg">✕</button>
            <div class="flex items-center gap-2.5 mb-2">
                <span class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </span>
                <h3 class="text-lg font-black text-white">Equipes / Células da Sala</h3>
            </div>
            <p class="text-xs text-slate-400 mb-4">Escolha uma equipe criada pelo apresentador para pontuar em conjunto:</p>

            <div id="listaEquipesModal" class="space-y-2 max-h-56 overflow-y-auto pr-1 mb-4">
                <p class="text-xs text-slate-500">Carregando equipes...</p>
            </div>

            <div class="border-t border-slate-800 pt-3 space-y-2">
                <p class="text-[11px] font-bold text-slate-400 uppercase">Ou crie uma nova equipe:</p>
                <div class="flex gap-2">
                    <input type="text" id="inputNovaEquipeModal" placeholder="Ex.: Tribo de Judá"
                           class="flex-1 px-3 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white outline-none focus:border-amber-400 font-bold">
                    <button type="button" onclick="criarEquipeModal()"
                            class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs uppercase shadow transition">
                        Criar & Entrar
                    </button>
                </div>
                <button type="button" onclick="sairEquipeModal()"
                        class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition">
                    Jogar Individualmente (Sem Equipe)
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Escolha de Convidado Bíblico -->
    <div id="modalConvidadoMobile" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-sm w-full p-6 text-slate-100 shadow-2xl relative">
            <button type="button" onclick="fecharModalConvidadoMobile()" class="absolute top-4 right-4 text-slate-400 hover:text-white font-black text-lg">✕</button>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xl">👥</span>
                <h3 class="text-lg font-black text-white">Consultar Convidado Bíblico</h3>
            </div>
            <p class="text-xs text-slate-400 mb-4">Escolha um dos personagens para obter ajuda na pergunta:</p>
            <div class="space-y-2.5">
                <button type="button" onclick="usarAjudaMobile('convidado', 0)" class="w-full p-3 rounded-xl bg-blue-950/80 border border-blue-500/40 hover:border-amber-400 text-left font-bold text-sm flex items-center justify-between text-white transition">
                    <div>
                        <p class="font-black text-white">1. Jacó</p>
                        <p class="text-[10px] text-slate-400 font-normal">Patriarca das 12 Tribos</p>
                    </div>
                    <span class="text-[10px] text-emerald-400 font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-950/60 border border-emerald-500/30">Sabe a certa</span>
                </button>
                <button type="button" onclick="usarAjudaMobile('convidado', 1)" class="w-full p-3 rounded-xl bg-blue-950/80 border border-blue-500/40 hover:border-amber-400 text-left font-bold text-sm flex items-center justify-between text-white transition">
                    <div>
                        <p class="font-black text-white">2. Paulo</p>
                        <p class="text-[10px] text-slate-400 font-normal">Apóstolo dos Gentios</p>
                    </div>
                    <span class="text-[10px] text-emerald-400 font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-950/60 border border-emerald-500/30">Sabe a certa</span>
                </button>
                <button type="button" onclick="usarAjudaMobile('convidado', 2)" class="w-full p-3 rounded-xl bg-blue-950/80 border border-blue-500/40 hover:border-amber-400 text-left font-bold text-sm flex items-center justify-between text-white transition">
                    <div>
                        <p class="font-black text-white">3. Jafé</p>
                        <p class="text-[10px] text-slate-400 font-normal">Filho de Noé</p>
                    </div>
                    <span class="text-[10px] text-amber-400 font-extrabold uppercase px-2 py-0.5 rounded bg-amber-950/60 border border-amber-500/30">Histórico</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal "Você tem certeza?" estilo Show do Milhão -->
    <div id="modalCerteza" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl">
            <p class="text-xs font-black uppercase tracking-widest text-amber-400 mb-1">Confirmação Bíblia Show</p>
            <h3 class="text-2xl font-black text-white mb-2">Você tem certeza?</h3>
            <p id="textoOpcaoConfirmar" class="text-sm font-bold text-amber-200 bg-slate-950 p-3 rounded-xl border border-slate-800 mb-5"></p>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="fecharModalCerteza()"
                        class="py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-sm">
                    Trocar
                </button>
                <button type="button" id="btnConfirmarFinal"
                        class="py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm shadow-lg">
                    Sim, Confirmar!
                </button>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const letras = ['A', 'B', 'C', 'D'];

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
        };

        let indicePendenteConfirmacao = null;
        let timerTickInterval = null;

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

        document.getElementById('entrarForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = document.getElementById('btnEntrar');
            const errBox = document.getElementById('errorEntrada');
            errBox.classList.add('hidden');
            btn.disabled = true;
            btn.innerText = 'Conectando...';

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
                iniciarModoJogo();
            } catch (err) {
                errBox.innerText = err.message;
                errBox.classList.remove('hidden');
                btn.disabled = false;
                btn.innerText = 'Entrar e Jogar →';
            }
        });

        function iniciarModoJogo() {
            document.getElementById('etapaEntrada').classList.add('hidden');
            document.getElementById('etapaJogo').classList.remove('hidden');
            document.getElementById('badgeSala').classList.remove('hidden');
            document.getElementById('codigoSalaTexto').innerText = estadoSessao.codigoSala;
            document.getElementById('jogadorNomeDisplay').innerText = estadoSessao.participanteNome;
            const avatarEl = document.getElementById('jogadorAvatarDisplay');
            if (avatarEl && estadoSessao.participanteNome) {
                avatarEl.innerText = estadoSessao.participanteNome.trim().charAt(0).toUpperCase();
            }
            document.getElementById('jogadorEquipeDisplay').innerText = estadoSessao.equipeNome ? 'Equipe: ' + estadoSessao.equipeNome : 'Individual';
            document.getElementById('jogadorPontosDisplay').innerText = estadoSessao.pontuacao;

            sincronizarEstadoSala();
            setInterval(sincronizarEstadoSala, 1200);

            // Timer local suave a cada 1s
            if (!timerTickInterval) {
                timerTickInterval = setInterval(tickCronometroLocal, 1000);
            }
        }

        function tickCronometroLocal() {
            if (estadoSessao.tempoRestante > 0 && !estadoSessao.jaRespondeuAtual) {
                estadoSessao.tempoRestante--;
                atualizarVisualCronometro(estadoSessao.tempoRestante);
                if (estadoSessao.tempoRestante <= 10) {
                    tocarSomContagemRegressiva(estadoSessao.tempoRestante);
                }
                if (estadoSessao.tempoRestante <= 0) {
                    desabilitarOpcoesPorTempoEsgotado();
                }
            }
        }

        function tocarSomContagemRegressiva(segundos) {
            if (segundos > 10 || segundos < 0) return;
            const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
            if (!ativo) return;

            try {
                if (segundos > 0) {
                    const audioSrc = segundos <= 3 ? '/audio/classico/tempo_alerta.wav' : '/audio/classico/tempo_tick.wav';
                    const fx = new Audio(audioSrc);
                    fx.volume = typeof obterVolumeGlobal === 'function' ? Math.min(1, obterVolumeGlobal() * 1.1) : 0.85;
                    fx.play().catch(() => {});
                } else if (segundos === 0) {
                    const fx = new Audio('/audio/classico/ks.wav');
                    fx.volume = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85;
                    fx.play().catch(() => {});
                }
            } catch (e) {}
        }

        function atualizarVisualCronometro(segundos) {
            const timerEl = document.getElementById('tempoRestanteDisplay');
            const barraEl = document.getElementById('barraProgressoTempo');
            const boxEl = document.getElementById('containerSuperCronometro');
            const labelEl = document.getElementById('labelStatusCronometro');
            const iconeBox = document.getElementById('iconeCronometroBox');

            if (timerEl) timerEl.innerText = Math.max(0, segundos);

            const pct = Math.max(0, Math.min(100, (segundos / 60) * 100));
            if (barraEl) {
                barraEl.style.width = `${pct}%`;
            }

            if (segundos <= 0) {
                if (boxEl) {
                    boxEl.className = 'p-3.5 sm:p-4 rounded-2xl bg-slate-950/90 border-2 border-slate-700 shadow-inner flex flex-col gap-2';
                }
                if (labelEl) {
                    labelEl.textContent = 'Tempo Encerrado';
                    labelEl.className = 'text-xs font-bold text-rose-400';
                }
                if (barraEl) {
                    barraEl.className = 'bg-rose-500 h-2 rounded-full transition-all duration-300';
                }
            } else if (segundos <= 10) {
                // Alerta vermelho piscante nos últimos 10s
                if (boxEl) {
                    boxEl.className = 'p-3.5 sm:p-4 rounded-2xl border-2 timer-critico shadow-inner flex flex-col gap-2';
                }
                if (labelEl) {
                    labelEl.textContent = 'Atenção! Últimos segundos!';
                    labelEl.className = 'text-xs font-bold text-rose-300 animate-pulse';
                }
                if (barraEl) {
                    barraEl.className = 'bg-rose-500 h-2 rounded-full transition-all duration-300';
                }
            } else {
                if (boxEl) {
                    boxEl.className = 'p-3.5 sm:p-4 rounded-2xl bg-slate-950/90 border-2 border-amber-500/40 shadow-inner transition-all flex flex-col gap-2';
                }
                if (labelEl) {
                    labelEl.textContent = 'Rodada em andamento';
                    labelEl.className = 'text-xs font-bold text-amber-300';
                }
                if (barraEl) {
                    barraEl.className = 'bg-gradient-to-r from-amber-500 to-amber-300 h-2 rounded-full transition-all duration-300';
                }
            }
        }

        function desabilitarOpcoesPorTempoEsgotado() {
            estadoSessao.tempoEsgotado = true;

            // Fechar modal de confirmação imediatamente se estiver aberto
            fecharModalCerteza();
            if (typeof Swal !== 'undefined' && Swal.isVisible()) {
                Swal.close();
            }

            // Desabilitar todas as opções de resposta
            document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-40', 'cursor-not-allowed');
            });

            // Exibir faixa amigável e silenciosa de tempo esgotado (sem alert)
            const aviso = document.getElementById('avisoTempoEsgotado');
            if (aviso && !estadoSessao.jaRespondeuAtual) {
                aviso.classList.remove('hidden');
            }

            // Desabilitar botões de ajudas
            atualizarBotoesAjudasMobile();
        }

        async function sincronizarEstadoSala() {
            if (!estadoSessao.salaId) return;

            try {
                const url = `/api/salas/${estadoSessao.salaId}?participante_id=${estadoSessao.participanteId || ''}`;
                const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!resp.ok) return;
                const data = await resp.json();

                if (data.sala && (data.sala.premio_primeiro_lugar || data.sala.premio_segundo_lugar)) {
                    document.getElementById('boxPremiosSala').classList.remove('hidden');
                    document.getElementById('premio1Display').innerHTML = data.sala.premio_primeiro_lugar ? `1º: <strong class="text-amber-300">${data.sala.premio_primeiro_lugar}</strong>` : '';
                    document.getElementById('premio2Display').innerHTML = data.sala.premio_segundo_lugar ? `2º: <strong>${data.sala.premio_segundo_lugar}</strong>` : '';
                }

                if (data.tempo_restante !== undefined) {
                    estadoSessao.tempoRestante = Number(data.tempo_restante);
                    atualizarVisualCronometro(estadoSessao.tempoRestante);
                }

                const placar = data.placar_milhao || {};
                const errarEl = document.getElementById('placarErrarDisplay');
                const pararEl = document.getElementById('placarPararDisplay');
                const acertarEl = document.getElementById('placarAcertarDisplay');
                if (errarEl && placar.errar !== undefined) errarEl.innerHTML = `${placar.errar} <span class="text-[9px]">pts</span>`;
                if (pararEl && placar.parar !== undefined) pararEl.innerHTML = `${placar.parar} <span class="text-[9px]">pts</span>`;
                if (acertarEl && placar.acertar !== undefined) acertarEl.innerHTML = `${placar.acertar} <span class="text-[9px]">pts</span>`;

                estadoSessao.equipes = data.equipes || [];
                atualizarRanking(data.ranking || [], data.equipes || []);

                if (data.estado === 'terminado') {
                    document.getElementById('textoPerguntaViva').innerText = 'Partida encerrada pelo apresentador! Confira sua colocação abaixo.';
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
                document.getElementById('progressoPergunta').innerText = `${fase}ª Fase • Pergunta ${data.numero_pergunta || 1}/${data.total_perguntas || 24}`;
                document.getElementById('nivelPergunta').innerText = `Vale ${placar.valor_pergunta || 10} pts`;

                const ajudas = data.ajudas || {};

                // Atualizar saldos de ajudas
                if (ajudas.dicas_restantes !== undefined) estadoSessao.dicasRestantes = ajudas.dicas_restantes;
                if (ajudas.cartas_restantes !== undefined) estadoSessao.cartasRestantes = ajudas.cartas_restantes;
                if (ajudas.convidados_restantes !== undefined) estadoSessao.convidadosRestantes = ajudas.convidados_restantes;
                atualizarBotoesAjudasMobile();

                // Dica bíblica ativa
                const dicaEl = document.getElementById('dicaBiblicaViva');
                const dicaTexto = document.getElementById('conteudoDicaBiblicaTexto');
                if (ajudas.dica_biblica) {
                    if (dicaTexto) dicaTexto.textContent = ajudas.dica_biblica;
                    dicaEl.classList.remove('hidden');
                } else {
                    dicaEl.classList.add('hidden');
                }

                // Convidado bíblico ativo
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

                // Atualiza se o servidor já registrou a resposta desta rodada
                if (data.respostas_rodada && estadoSessao.participanteId && data.respostas_rodada[estadoSessao.participanteId]) {
                    const minhaResp = data.respostas_rodada[estadoSessao.participanteId];
                    estadoSessao.jaRespondeuAtual = true;
                    if (estadoSessao.opcaoEscolhida === null && typeof minhaResp.indice !== 'undefined' && minhaResp.indice !== null) {
                        estadoSessao.opcaoEscolhida = minhaResp.indice;
                    }
                }

                if (estadoSessao.questaoAtualId !== q.id) {
                    estadoSessao.questaoAtualId = q.id;
                    estadoSessao.jaRespondeuAtual = !!data.ja_respondeu;
                    estadoSessao.opcaoEscolhida = null;
                    estadoSessao.reveladoNaPerguntaAtual = false;
                    estadoSessao.tempoEsgotado = false;
                    estadoSessao.inicioPerguntaTimestamp = Date.now();
                    document.getElementById('feedbackResposta').classList.add('hidden');
                    document.getElementById('avisoTempoEsgotado').classList.add('hidden');
                    fecharModalCerteza();
                    renderizarPergunta(q);
                } else {
                    const gabaritoRevelado = !!(data.ajudas && data.ajudas.gabarito_revelado);
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

                        // 1. Revelar visualmente verde/vermelho apenas agora que o apresentador autorizou
                        revelarCoresOpcaoMobile(estadoSessao.opcaoEscolhida, respostaCorreta, acertou, q);

                        // 2. Tocar som de acerto/erro sincronizado com o telão
                        const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
                        if (ativo && acertou !== null) {
                            const fx = new Audio(acertou ? '/audio/classico/certa1.wav' : '/audio/classico/errou1.wav');
                            fx.volume = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85;
                            fx.play().catch(() => {});
                        }

                        // 3. Exibir feedback oficial pós-revelação
                        const fb = document.getElementById('feedbackResposta');
                        if (fb && estadoSessao.jaRespondeuAtual) {
                            fb.className = 'rounded-2xl p-4 border ' + (acertou
                                ? 'bg-emerald-950/80 border-emerald-500/40 text-emerald-100'
                                : 'bg-rose-950/80 border-rose-500/40 text-rose-100');

                            fb.innerHTML = `
                                <div class="font-black text-base mb-1">
                                    ${acertou ? `✓ Certa Resposta! (+${pontosGanhos || placar.valor_pergunta || 10} pts)` : `✕ Que pena, você errou!`}
                                </div>
                                ${!acertou && respostaCorreta ? `<div class="text-xs text-rose-200">Gabarito oficial: <strong class="text-emerald-300 underline">${respostaCorreta}</strong></div>` : ''}
                                ${data.pergunta_atual?.referencia_biblica ? `<div class="text-xs mt-1.5 text-slate-300">[${data.pergunta_atual.referencia_biblica}]</div>` : ''}
                                <div class="text-[11px] mt-2 text-amber-300 font-semibold">Aguarde o apresentador chamar a próxima pergunta.</div>
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
            new Audio('/audio/classico/tecla.wav').play().catch(() => {});

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

                // Tocar efeito sonoro clássico
                if (data.som_efeito) {
                    new Audio(`/audio/classico/${data.som_efeito}.wav`).play().catch(() => {});
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
                            btn.className = 'btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 opacity-30 border-slate-800 bg-slate-950 line-through cursor-not-allowed';
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

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: data.mensagem,
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#0f172a',
                        color: '#f8fafc',
                        customClass: { popup: 'rounded-3xl border border-amber-400' }
                    });
                }
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
            const m = document.getElementById('modalConvidadoMobile');
            if (m) m.classList.remove('hidden');
        }

        function fecharModalConvidadoMobile() {
            const m = document.getElementById('modalConvidadoMobile');
            if (m) m.classList.add('hidden');
        }

        function renderizarPergunta(q) {
            document.getElementById('textoPerguntaViva').innerText = q.texto;

            const badgeBiblico = document.getElementById('badgeBiblicoCard');
            const textoBiblico = document.getElementById('textoBiblicoCard');
            if (badgeBiblico && textoBiblico) {
                if (q.livro_nome || q.categoria_nome) {
                    textoBiblico.textContent = (q.livro_nome ? `LIVRO: ${q.livro_nome}` : 'SAGRADAS ESCRITURAS') + (q.categoria_nome ? ` • ${q.categoria_nome}` : '');
                    badgeBiblico.classList.remove('hidden');
                } else {
                    badgeBiblico.classList.add('hidden');
                }
            }

            const container = document.getElementById('opcoesBotoes');
            const eliminadas = estadoSessao.opcoesEliminadas || [];
            const tempoZerado = estadoSessao.tempoRestante <= 0;

            container.innerHTML = (q.opcoes || []).map((opcao, idx) => {
                const isEliminada = eliminadas.includes(idx);
                const isEscolhida = estadoSessao.opcaoEscolhida !== null && Number(estadoSessao.opcaoEscolhida) === idx;
                const estaBloqueada = estadoSessao.jaRespondeuAtual || (estadoSessao.opcaoEscolhida !== null) || tempoZerado;

                let classesBtn = 'btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 ';
                let tagHtml = '';

                if (isEliminada) {
                    classesBtn += 'opacity-30 border-slate-800 bg-slate-950 line-through cursor-not-allowed';
                } else if (isEscolhida) {
                    classesBtn += 'opcao-bloqueada cursor-not-allowed';
                    tagHtml = '<span class="shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-amber-400 text-slate-950">BLOQUEADA</span>';
                } else if (estaBloqueada) {
                    classesBtn += 'opacity-40 border-slate-800 bg-slate-950 cursor-not-allowed';
                } else {
                    classesBtn += 'border-blue-500/40 bg-blue-950/70 hover:border-amber-400 active:scale-[0.99] cursor-pointer';
                }

                return `
                    <button type="button"
                            id="btnOpcaoMobile_${idx}"
                            data-indice="${idx}"
                            ${isEliminada || estaBloqueada ? 'disabled' : ''}
                            onclick="confirmarOpcao(${idx}, '${String(opcao).replace(/'/g, "\\'")}')"
                            class="${classesBtn}">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <span id="badgeMobile_${idx}" class="w-8 h-8 rounded-xl ${isEscolhida ? 'bg-amber-400 text-slate-950' : 'bg-amber-500 text-slate-950'} font-black flex items-center justify-center shrink-0 text-sm">
                                ${letras[idx] || (idx + 1)}
                            </span>
                            <span class="font-bold text-white text-sm sm:text-base">${opcao}</span>
                        </div>
                        <span id="tagMobile_${idx}" class="shrink-0">${tagHtml}</span>
                    </button>
                `;
            }).join('');

            atualizarBotoesAjudasMobile();
        }

        async function confirmarOpcao(idx, textoOpcao) {
            // Se já respondeu ou tempo esgotou, rejeita sem erro
            if (estadoSessao.jaRespondeuAtual || estadoSessao.tempoEsgotado || estadoSessao.tempoRestante <= 0) {
                return;
            }

            new Audio('/audio/classico/tecla.wav').play().catch(() => {});
            const letra = letras[idx] || (idx + 1);

            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: 'Você tem certeza?',
                    html: `
                        <p class="text-xs text-slate-300 uppercase tracking-wider font-bold mb-2">Alternativa Escolhida:</p>
                        <div class="p-3.5 rounded-2xl bg-slate-950 border-2 border-amber-400 text-amber-300 font-black text-base text-left flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-amber-400 text-slate-950 font-black flex items-center justify-center shrink-0">${letra}</span>
                            <span>${textoOpcao}</span>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'SIM, CONFIRMAR!',
                    cancelButtonText: 'Trocar Opção',
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#334155',
                    background: '#0f172a',
                    color: '#f8fafc',
                    customClass: {
                        popup: 'rounded-3xl border-2 border-amber-400/80 shadow-2xl',
                        confirmButton: 'font-black rounded-xl px-5 py-3 text-slate-950 text-sm shadow-lg',
                        cancelButton: 'font-bold rounded-xl px-4 py-3 text-sm'
                    }
                });

                if (result.isConfirmed) {
                    // Valida novamente tempo antes de enviar
                    if (estadoSessao.tempoRestante <= 0 || estadoSessao.tempoEsgotado) {
                        desabilitarOpcoesPorTempoEsgotado();
                        return;
                    }
                    enviarResposta(idx);
                }
            } else {
                indicePendenteConfirmacao = idx;
                document.getElementById('textoOpcaoConfirmar').innerText = `${letra}) ${textoOpcao}`;
                document.getElementById('modalCerteza').classList.remove('hidden');
                document.getElementById('btnConfirmarFinal').onclick = () => {
                    const escolhido = indicePendenteConfirmacao;
                    fecharModalCerteza();
                    if (estadoSessao.tempoRestante <= 0 || estadoSessao.tempoEsgotado) {
                        desabilitarOpcoesPorTempoEsgotado();
                        return;
                    }
                    enviarResposta(escolhido);
                };
            }
        }

        function fecharModalCerteza() {
            const m = document.getElementById('modalCerteza');
            if (m) m.classList.add('hidden');
            indicePendenteConfirmacao = null;
        }

        function bloquearOpcaoMobile(indiceEscolhido) {
            document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                btn.disabled = true;
                const idx = String(btn.dataset.indice);
                const tag = document.getElementById(`tagMobile_${idx}`);
                if (idx === String(indiceEscolhido)) {
                    btn.className = 'btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 opcao-bloqueada cursor-not-allowed';
                    if (tag) {
                        tag.innerHTML = '<span class="shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-amber-400 text-slate-950">BLOQUEADA</span>';
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
                    btn.className = 'btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 opcao-correta-reveal';
                    if (badge) badge.className = 'w-8 h-8 rounded-xl bg-white text-emerald-700 font-black flex items-center justify-center shrink-0 text-sm';
                    if (tag) {
                        tag.innerHTML = '<span class="shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-white text-emerald-800">' + (acertou ? 'CERTA!' : 'CORRETA') + '</span>';
                    }
                } else if (ehEscolhida && !acertou) {
                    btn.className = 'btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 opcao-errada-reveal';
                    if (badge) badge.className = 'w-8 h-8 rounded-xl bg-white text-rose-700 font-black flex items-center justify-center shrink-0 text-sm';
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

            // 1. Bloquear imediatamente a opção escolhida na tela
            bloquearOpcaoMobile(indiceOpcao);

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
                    // Se o erro for tempo esgotado (408), trata silenciosamente com a mensagem elegante
                    if (resp.status === 408) {
                        desabilitarOpcoesPorTempoEsgotado();
                        return;
                    }
                    const msgErro = data.error || (data.errors ? Object.values(data.errors).flat()[0] : null) || 'Não foi possível enviar a resposta.';
                    throw new Error(msgErro);
                }

                // Exibe feedback de Resposta Bloqueada (aguardando revelação oficial do apresentador no telão)
                const fb = document.getElementById('feedbackResposta');
                if (fb) {
                    fb.className = 'rounded-2xl p-4 border bg-amber-500/15 border-amber-500/40 text-amber-200';
                    fb.innerHTML = `
                        <div class="font-black text-base flex items-center gap-2">
                            <span>🔒</span>
                            <span>Alternativa Bloqueada com Sucesso!</span>
                        </div>
                        <p class="text-xs text-slate-300 mt-1">Sua resposta foi registrada no estúdio. Aguarde o apresentador comandar a revelação oficial no telão!</p>
                    `;
                    fb.classList.remove('hidden');
                }
                sessionStorage.setItem('biblia_show_participante', JSON.stringify(estadoSessao));
            } catch (err) {
                // Em caso de erro diferente de tempo esgotado, reabilita as opções e notifica via SweetAlert2
                estadoSessao.jaRespondeuAtual = false;
                estadoSessao.opcaoEscolhida = null;
                document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                    const idx = Number(btn.dataset.indice);
                    const isEliminada = (estadoSessao.opcoesEliminadas || []).includes(idx);
                    if (!isEliminada && estadoSessao.tempoRestante > 0) {
                        btn.disabled = false;
                        btn.className = 'btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 border-blue-500/40 bg-blue-950/70 hover:border-amber-400 active:scale-[0.99] cursor-pointer';
                        const tag = document.getElementById(`tagMobile_${idx}`);
                        if (tag) tag.innerHTML = '';
                    }
                });

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Falha no Envio',
                        text: err.message,
                        confirmButtonText: 'Tentar Novamente',
                        confirmButtonColor: '#f59e0b',
                        background: '#0f172a',
                        color: '#f8fafc',
                        customClass: {
                            popup: 'rounded-3xl border-2 border-rose-500 shadow-2xl',
                            confirmButton: 'font-black rounded-xl px-5 py-3 text-slate-950 text-sm'
                        }
                    });
                } else {
                    alert(err.message);
                }
            }
        }

        function alternarTabRanking(tab) {
            const tabJ = document.getElementById('tabJogadores');
            const tabE = document.getElementById('tabEquipes');
            const boxJ = document.getElementById('miniRankingJogadores');
            const boxE = document.getElementById('miniRankingEquipes');
            if (tab === 'jogadores') {
                if (tabJ) tabJ.className = 'text-xs font-black px-3 py-1.5 rounded-xl bg-amber-500 text-slate-950 transition';
                if (tabE) tabE.className = 'text-xs font-black px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition';
                if (boxJ) boxJ.classList.remove('hidden');
                if (boxE) boxE.classList.add('hidden');
            } else {
                if (tabE) tabE.className = 'text-xs font-black px-3 py-1.5 rounded-xl bg-amber-500 text-slate-950 transition';
                if (tabJ) tabJ.className = 'text-xs font-black px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition';
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
                    boxJ.innerHTML = '<p class="text-slate-500 text-xs">Aguardando participantes entrarem...</p>';
                } else {
                    boxJ.innerHTML = ranking.slice(0, 8).map((p, idx) => {
                        const souEu = Number(p.id) === Number(estadoSessao.participanteId);
                        return `
                            <div class="flex items-center justify-between p-2.5 rounded-xl ${souEu ? 'bg-amber-500/20 border border-amber-500/40 text-amber-200' : 'bg-slate-950/70 text-slate-200'}">
                                <span class="font-bold truncate">
                                    <strong class="text-amber-400 mr-1.5">${idx + 1}º</strong> ${p.nome} ${souEu ? '(Você)' : ''}
                                    ${p.equipe ? `<span class="text-[10px] text-slate-400 block">${p.equipe}</span>` : ''}
                                </span>
                                <span class="font-mono font-black text-amber-300 text-xs">${p.pontuacao} pts</span>
                            </div>
                        `;
                    }).join('');
                }
            }

            if (boxE) {
                if (!equipes || equipes.length === 0) {
                    boxE.innerHTML = '<p class="text-slate-500 text-xs">Nenhuma equipe pontuando nesta sala ainda.</p>';
                } else {
                    const equipesOrd = [...equipes].sort((a, b) => (b.pontuacao_total || 0) - (a.pontuacao_total || 0));
                    boxE.innerHTML = equipesOrd.map((eq, idx) => `
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/70 border border-slate-800">
                            <span class="font-bold text-white truncate">
                                <strong class="text-emerald-400 mr-1.5">${idx + 1}º</strong> ${eq.nome}
                            </span>
                            <span class="font-mono font-black text-emerald-300 text-xs">${eq.pontuacao_total || 0} pts</span>
                        </div>
                    `).join('');
                }
            }
        }

        function abrirModalTrocarEquipe() {
            const m = document.getElementById('modalTrocarEquipe');
            const list = document.getElementById('listaEquipesModal');
            if (!m || !list) return;

            const equipes = estadoSessao.equipes || [];
            if (equipes.length === 0) {
                list.innerHTML = '<p class="text-xs text-slate-400 p-3 bg-slate-950 rounded-xl">Nenhuma equipe criada na sala ainda. Digite um nome abaixo para fundar a primeira equipe!</p>';
            } else {
                list.innerHTML = equipes.map(eq => `
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-amber-400 transition">
                        <div>
                            <p class="font-black text-sm text-white">${eq.nome}</p>
                            <p class="text-[10px] text-amber-400 font-bold">${eq.pontuacao_total || 0} pts acumulados</p>
                        </div>
                        <button type="button" onclick="submeterTrocaEquipe(${eq.id}, null)"
                                class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-black text-xs transition">
                            Juntar-se →
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

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Equipe Atualizada!',
                        text: data.mensagem,
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#0f172a',
                        color: '#f8fafc',
                    });
                }
            } catch (e) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Erro', text: e.message, background: '#0f172a', color: '#f8fafc' });
                } else {
                    alert(e.message);
                }
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
