<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#f59e0b">
    <title>Bíblia Show — O Jogo Bíblico Estilo Show do Milhão</title>
    <meta property="og:site_name" content="Bíblia Show 2.0">
    <meta property="og:title" content="Bíblia Show — O Jogo Bíblico Estilo Show do Milhão">
    <meta property="og:description" content="Participe no Bíblia Show 2.0: modo individual offline/online e arena ao vivo para igrejas e grupos!">
    <meta name="description"
        content="Bíblia Show 2.0 — O clássico jogo de perguntas e respostas bíblicas recriado com 1.000 questões originais, locuções clássicas, 4 fases, salas multiplayer por QR Code e projeção para igrejas.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="sm-stage text-slate-100 min-h-screen flex flex-col selection:bg-amber-500 selection:text-slate-950">

    @php
        $jogoAtivoInfo = null;
        try {
            $dadosEngine = session('game_engine');
            if (!empty($dadosEngine)) {
                $engine = \App\Game\Engine\GameEngine::desserializarSeguro($dadosEngine);
                if ($engine) {
                    $st = $engine->getState();
                    if (($st['estado'] ?? '') === 'em_curso' && !empty($st['pergunta_atual'])) {
                        $jogoAtivoInfo = [
                            'numero' => $st['numero_pergunta'] ?? 1,
                            'total' => $st['total_perguntas'] ?? 24,
                            'fase' => $st['fase_atual'] ?? 1,
                            'pontos' => $st['placar_individual'][1] ?? 0,
                            'jogador' => session('jogador_nome', 'Participante'),
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {}
    @endphp

    <!-- Navbar Estilizada -->
    @include('partials.navbar', ['dark' => true])

    <!-- HERO SECTION (Mobile-First Show do Milhão) -->
    <section class="relative pt-6 sm:pt-10 pb-12 sm:pb-16 px-4 sm:px-6 lg:px-8 flex-1">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-center">
                
                <!-- Coluna Principal (Texto e Ações Rápidas) -->
                <div class="lg:col-span-7 space-y-4 sm:space-y-6 text-center lg:text-left">
                    
                    <!-- Pílula Ouro Topo -->
                    <div class="inline-flex items-center justify-center">
                        <span class="sm-pill !px-3.5 !py-1 !text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                            <svg class="w-3.5 h-3.5 text-amber-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <span>Acervo Oficial • 1.000 Perguntas & Sons</span>
                        </span>
                    </div>

                    <!-- Título de Palco Auditório -->
                    <h1 class="text-3xl sm:text-5xl xl:text-6xl font-black leading-tight tracking-tight text-white sm-logo-title">
                        Bíblia Show
                    </h1>

                    <p class="text-sm sm:text-base text-blue-200 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        A recriação fiel do clássico dos anos 2000 no formato <strong>Show do Milhão</strong>: 4 fases (10, 20, 30 e 50 pontos), Cartas 50/50, Personagens Bíblicos e salas ao vivo para cultos e jovens!
                    </p>

                    <!-- Ações Principais (Sem Redundância) -->
                    <div class="space-y-3.5 pt-1 max-w-lg mx-auto lg:mx-0">
                        @if($jogoAtivoInfo)
                            <!-- Card Único e Elegante: Partida em Andamento -->
                            <div class="sm-panel p-4 text-left shadow-2xl relative">
                                <div class="flex items-center gap-3.5 mb-3">
                                    <div class="sm-avatar !w-12 !h-12 !text-lg !font-black shrink-0">
                                        {{ mb_strtoupper(mb_substr($jogoAtivoInfo['jogador'] ?? 'P', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="sm-pill !px-2.5 !py-0.5 !text-[10px] uppercase font-black">Em Curso</span>
                                            <span class="text-xs font-black text-amber-300">{{ $jogoAtivoInfo['fase'] }}ª Fase</span>
                                        </div>
                                        <p class="font-black text-white text-sm sm:text-base truncate mt-0.5">
                                            {{ $jogoAtivoInfo['jogador'] }} • Pergunta {{ $jogoAtivoInfo['numero'] }} de {{ $jogoAtivoInfo['total'] }}
                                        </p>
                                        <p class="text-xs text-blue-200 font-mono font-bold">{{ $jogoAtivoInfo['pontos'] }} pontos acumulados</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <a href="{{ route('game.play') }}"
                                       class="sm-cta !text-sm sm:!text-base cursor-pointer shadow-xl !py-3">
                                        Continuar Partida (Pergunta {{ $jogoAtivoInfo['numero'] }}) →
                                    </a>
                                    <div class="text-center pt-1">
                                        <a href="{{ route('game.play', ['novo' => 1]) }}"
                                           onclick="return confirm('Deseja iniciar uma nova partida? O progresso da partida atual será reiniciado.');"
                                           class="text-xs font-bold text-blue-300 hover:text-amber-300 hover:underline transition">
                                            ⟳ Ou iniciar uma nova partida do zero
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Botão Ouro Clássico: Iniciar Jogo -->
                            <button type="button" onclick="abrirModalJogarAgora()"
                                    class="sm-cta !text-sm sm:!text-base cursor-pointer shadow-2xl !py-4">
                                <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24">
                                    <path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                </svg>
                                <span>Começar a Jogar Agora! →</span>
                            </button>
                        @endif

                        <!-- Entrada Rápida em Sala por Código PIN -->
                        <form action="{{ route('entrar.view') }}" method="GET"
                              onsubmit="event.preventDefault(); const c = this.codigo.value.trim().toUpperCase(); window.location.href = '{{ url('/entrar') }}' + (c ? '/' + c : '');"
                              class="sm-panel !p-2 flex items-center gap-2 shadow-xl">
                            <div class="pl-3 flex items-center gap-2 text-amber-300 font-black text-xs uppercase shrink-0">
                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                                <span class="hidden sm:inline">Entrar na Sala:</span>
                            </div>
                            <input type="text" name="codigo" maxlength="6" placeholder="DIGITE O PIN (EX: AB12CD)"
                                   class="flex-1 bg-transparent px-2.5 py-2 text-amber-300 font-mono font-black uppercase tracking-widest text-xs sm:text-sm outline-none placeholder:text-blue-300/40">
                            <button type="submit"
                                    class="sm-cta !w-auto !py-2 !px-4 !text-xs cursor-pointer shrink-0">
                                Entrar →
                            </button>
                        </form>

                        <!-- Atalhos Rápidos para Estúdio e Regras -->
                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2.5 pt-1 text-xs font-bold text-blue-200">
                            <a href="{{ route('apresentador.painel') }}" class="px-3.5 py-1.5 rounded-full bg-blue-950/80 border border-blue-400/40 hover:text-white hover:border-amber-400 transition inline-flex items-center gap-1.5 shadow">
                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z"/></svg>
                                <span>Painel do Apresentador</span>
                            </a>
                            <a href="{{ route('curiosidades') }}" class="px-3.5 py-1.5 rounded-full bg-blue-950/80 border border-blue-400/40 hover:text-white hover:border-amber-400 transition inline-flex items-center gap-1.5 shadow">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                <span>Regras Oficiais & 4 Fases</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Coluna Direita: Preview da Arena (Show do Milhão Autêntico) -->
                <div class="hidden lg:block lg:col-span-5">
                    <div class="sm-panel p-6 shadow-2xl relative">
                        <!-- Topo do Card Preview: Pílula Dourada e Cronômetro -->
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="sm-lifeline !w-9 !h-9 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                            </span>
                            <div class="sm-pill text-xs">
                                <span>
                                    10 PTS • <small>1 / 24 • 1ª FASE</small>
                                </span>
                            </div>
                            <span class="sm-timer !w-9 !h-9 !text-xs !border-amber-400">60s</span>
                        </div>

                        <!-- Pergunta Exemplo Real do Clássico (.sm-question azul vibrante) -->
                        <div class="sm-question pt-4 pb-3.5 px-4 mb-3 shadow-lg">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">Gênesis 6:14 • Pergunta #001</p>
                            <p class="sm-question-text text-base sm:text-lg leading-snug">
                                De qual madeira Deus ordenou que Noé construísse a arca?
                            </p>
                        </div>

                        <!-- Balão do Convidado Bíblico -->
                        <div class="flex items-center gap-2 mb-3 px-1">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border border-amber-200 text-slate-950 font-black text-xs">J</div>
                            <div class="sm-speech flex-1 text-xs">
                                <span class="block text-[9px] uppercase tracking-wider text-blue-700 font-black">Convidado Jacó:</span>
                                Parece que a resposta é <strong class="text-amber-600 font-black">B</strong>
                            </div>
                        </div>

                        <!-- Botões de Ajudas Redondos -->
                        <div class="flex items-center justify-center gap-2.5 mb-3">
                            <span class="sm-lifeline !w-10 !h-8 !text-xs font-black">−2<span class="sm-badge">1</span></span>
                            <span class="sm-lifeline !w-10 !h-8 !text-xs flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                <span class="sm-badge">3</span>
                            </span>
                            <span class="sm-lifeline !w-10 !h-8 !text-xs flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                                <span class="sm-badge">3</span>
                            </span>
                            <span class="sm-lifeline !w-10 !h-8 !text-xs font-black">⇄<span class="sm-badge">3</span></span>
                        </div>

                        <!-- 4 Alternativas em Cápsula -->
                        <div class="space-y-2 mb-4">
                            @foreach(['A' => 'Madeira de Cedro', 'B' => 'Madeira de Gofer', 'C' => 'Madeira de Acácia', 'D' => 'Madeira de Oliveira'] as $letra => $textoOp)
                                <div class="sm-option {{ $letra === 'B' ? 'opcao-correta-reveal' : '' }}">
                                    <span class="flex items-center gap-2">
                                        <span class="sm-letter text-sm">{{ $letra }}:</span>
                                        <span class="text-xs sm:text-sm font-bold">{{ $textoOp }}</span>
                                    </span>
                                    @if($letra === 'B')
                                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-700 text-white">Certa!</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Placar 3 Lingotes (ERRAR | PARAR | ACERTAR) -->
                        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-blue-400/20">
                            <div class="sm-ingot is-errar">
                                <div class="sm-ingot-label">Errar</div>
                                <div class="sm-ingot-value">0 pts</div>
                            </div>
                            <div class="sm-ingot">
                                <div class="sm-ingot-label">Parar</div>
                                <div class="sm-ingot-value">0 pts</div>
                            </div>
                            <div class="sm-ingot is-acertar">
                                <div class="sm-ingot-label">Acertar</div>
                                <div class="sm-ingot-value">10 pts</div>
                            </div>
                        </div>

                        <div class="sm-banner-red py-1.5 px-3 rounded-xl text-[10px] uppercase tracking-wider text-center mt-3">
                            Não sabe a resposta? Use ajuda!
                        </div>
                    </div>
                </div>
            </div>

    <!-- SEÇÃO ORGANIZADA: RECURSOS & MODOS DA ARENA (SEM REDUNDÂNCIAS) -->
    <section class="py-12 px-4 sm:px-6 lg:px-8 border-t border-blue-400/20 bg-blue-950/40">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="sm-pill !px-3 !py-0.5 !text-[11px] uppercase tracking-widest mb-2">Recursos & Modos de Jogo</span>
                <h2 class="text-2xl sm:text-4xl font-black text-white sm-logo-title mt-1">Como Funciona a Arena</h2>
                <p class="text-xs sm:text-sm text-blue-200 mt-2">Partidas individuais desafiadoras para o ranking ou dinamismo interativo para eventos e cultos.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1: Modo Solo & Escada de Prêmios -->
                <div class="sm-panel p-6 flex flex-col justify-between shadow-2xl relative">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="sm-pill !px-3 !py-0.5 !text-[10px] uppercase font-black">SOLO • RANKING</span>
                            <span class="text-xs font-mono font-bold text-amber-300">4 Fases • 24 Q</span>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-300 flex items-center justify-center mb-4 shadow">
                            <svg class="w-7 h-7 text-amber-300 fill-current" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1c1.94-.37 3.5-1.99 3.61-3.96C19.08 11.63 21 9.55 21 7V5c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-white mb-2">Escada de Prêmios & Ranking</h3>
                        <p class="text-xs sm:text-sm text-blue-200 leading-relaxed mb-4">
                            Suba a escada de 4 fases com valores crescentes (+10, +20, +30 e +50 pts por acerto). Use Cartas 50/50, Convidados e Pulos para alcançar a pontuação máxima no Ranking Geral.
                        </p>
                    </div>
                    <button type="button" onclick="abrirModalJogarAgora()" class="sm-cta !py-2.5 !text-xs cursor-pointer">
                        Jogar Modo Solo →
                    </button>
                </div>

                <!-- Card 2: Arena Ao Vivo para Igrejas -->
                <div class="sm-panel p-6 flex flex-col justify-between shadow-2xl relative border-2 !border-amber-400/80">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/40 text-[10px] font-black uppercase tracking-wider">AO VIVO • MULTIJOGADOR</span>
                            <span class="text-xs font-mono font-bold text-emerald-300">Projeção + Celular</span>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 flex items-center justify-center mb-4 shadow">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-white mb-2">Arena Ao Vivo com Telão</h3>
                        <p class="text-xs sm:text-sm text-blue-200 leading-relaxed mb-4">
                            O apresentador projeta as perguntas com QR Code automático no telão da igreja. A congregação responde pelos smartphones, com disputa de equipes, corrida ao vivo e trilhas clássicas.
                        </p>
                    </div>
                    <a href="{{ route('apresentador.painel') }}" class="block text-center w-full py-2.5 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 hover:from-emerald-300 hover:to-emerald-500 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg transition">
                        Abrir Painel do Apresentador →
                    </a>
                </div>

                <!-- Card 3: Acervo & Enciclopédia -->
                <div class="sm-panel p-6 flex flex-col justify-between shadow-2xl relative">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="sm-pill !px-3 !py-0.5 !text-[10px] uppercase font-black">FONTES & HISTÓRIA</span>
                            <span class="text-xs font-mono font-bold text-amber-300">66 Livros • 1.000 Q</span>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-blue-500/20 border border-blue-400/40 text-blue-300 flex items-center justify-center mb-4 shadow">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-white mb-2">Acervo Bíblico Verificado</h3>
                        <p class="text-xs sm:text-sm text-blue-200 leading-relaxed mb-4">
                            Todas as perguntas possuem referência bíblica de conferência direta nas escrituras. Acompanha enciclopédia histórica sobre os manuscritos do Mar Morto, Gutenberg e a Bíblia em Português.
                        </p>
                    </div>
                    <a href="{{ route('curiosidades') }}" class="block text-center w-full py-2.5 rounded-full bg-blue-900/60 hover:bg-blue-800 text-blue-200 border border-blue-400/40 font-bold text-xs transition">
                        Ver Regras & Enciclopédia →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SEÇÃO COMPACTA: ENCICLOPÉDIA BÍBLICA CLÁSSICA -->
    <section class="py-10 px-4 sm:px-6 lg:px-8 border-t border-blue-400/20">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-300">Estudos Históricos</span>
                    <h2 class="text-xl sm:text-2xl font-black text-white mt-0.5 sm-logo-title">Enciclopédia Ilustrada</h2>
                </div>
                <a href="{{ route('curiosidades') }}" class="text-xs font-bold text-amber-300 hover:underline">
                    Ver os 10 artigos →
                </a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach([
                        ['img' => '/images/classico/Sabia.jpg', 'titulo' => 'Você Sabia?', 'desc' => 'Gutenberg e fatos bíblicos.'],
                        ['img' => '/images/classico/Morto.jpg', 'titulo' => 'Mar Morto', 'desc' => 'Cavernas de Qumran.'],
                        ['img' => '/images/classico/Septuaginta.jpg', 'titulo' => 'A Septuaginta', 'desc' => 'Tradução grega no Egito.'],
                        ['img' => '/images/classico/Portugues.jpg', 'titulo' => 'Em Português', 'desc' => 'João Ferreira de Almeida.'],
                    ] as $card)
                    <a href="{{ route('curiosidades') }}" class="sm-panel overflow-hidden group block transition hover:scale-[1.02] shadow-xl">
                        <div class="h-24 sm:h-32 overflow-hidden relative bg-blue-950">
                            <img src="{{ $card['img'] }}" alt="{{ $card['titulo'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 opacity-90">
                        </div>
                        <div class="p-3.5">
                            <h3 class="font-black text-white text-xs sm:text-sm truncate">{{ $card['titulo'] }}</h3>
                            <p class="text-[10px] text-blue-200 mt-0.5 truncate">{{ $card['desc'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Silhueta da Plateia no Rodapé -->
    <div class="sm-crowd w-full mt-auto"></div>

    <!-- Footer Clássico -->
    <footer class="border-t-2 border-blue-400/20 bg-[#060e33] py-6 px-4 sm:px-6 lg:px-8 text-xs text-blue-200">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="sm-pill !px-2 !py-0.5 !text-[9px]">2.0</span>
                <span class="font-bold text-white">Bíblia Show — Edição Clássica</span>
            </div>
            <div class="flex flex-wrap justify-center gap-4 font-bold text-[11px]">
                <a href="{{ route('game.play', ['novo' => 1]) }}" class="hover:text-amber-300">Jogar Solo</a>
                <a href="{{ route('entrar.view') }}" class="hover:text-amber-300">Entrar em Sala</a>
                <a href="{{ route('apresentador.painel') }}" class="hover:text-amber-300">Apresentador</a>
                <a href="{{ route('curiosidades') }}" class="hover:text-amber-300">Enciclopédia</a>
                <a href="{{ route('sobre') }}" class="hover:text-amber-300 text-amber-300">Sobre</a>
            </div>
        </div>
    </footer>

    <script>
        let musicaHome = null;

        function iniciarAudioHome() {
            if (localStorage.getItem('biblia_show_som') === 'off') return;

            if (!musicaHome) {
                musicaHome = new Audio('/audio/classico/abertura.wav');
                const vol = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85;
                musicaHome.volume = vol * 0.70;
            }

            musicaHome.play().catch(() => {
                const tocarNoPrimeiroGesto = () => {
                    if (localStorage.getItem('biblia_show_som') !== 'off' && musicaHome && musicaHome.paused) {
                        musicaHome.play().catch(() => {});
                    }
                };
                ['click', 'touchstart', 'keydown'].forEach(evt => {
                    document.addEventListener(evt, tocarNoPrimeiroGesto, { once: true, passive: true });
                });
            });
        }

        window.addEventListener('biblia-som-alterado', (ev) => {
            if (musicaHome) {
                if (!ev.detail.ativo) {
                    musicaHome.pause();
                } else {
                    musicaHome.volume = ev.detail.volume * 0.70;
                    if (musicaHome.paused) {
                        musicaHome.play().catch(() => {});
                    }
                }
            }
        });

        document.addEventListener('DOMContentLoaded', iniciarAudioHome);

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}").catch(() => {});
            });
        }
    </script>
</body>

</html>
