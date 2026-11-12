<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f59e0b">
    <title>{{ config('app.name', 'Bíblia Show') }} — O Jogo Bíblico Estilo Show do Milhão</title>
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

                    <!-- Ações Principais (Ultra-otimizadas para Mobile) -->
                    <div class="space-y-3 pt-1 max-w-md mx-auto lg:mx-0">
                        <!-- Botão Gigante Ouro: Jogar Modo Individual -->
                        <a href="{{ route('game.play', ['novo' => 1]) }}"
                           class="sm-cta !text-sm sm:!text-base cursor-pointer shadow-2xl py-3.5 sm:py-4">
                            <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24">
                                <path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                            </svg>
                            <span>Jogar</span>
                        </a>

                        <!-- Caixa Rápida: Entrar em Sala por Código -->
                        <form action="{{ route('entrar.view') }}" method="GET"
                              onsubmit="event.preventDefault(); const c = this.codigo.value.trim().toUpperCase(); window.location.href = c ? '/entrar/' + c : '/entrar';"
                              class="bg-blue-950/90 border-2 border-blue-400/50 rounded-2xl p-2 flex items-center gap-2 shadow-lg">
                            <div class="pl-3 flex items-center gap-1.5 text-amber-300 font-bold text-xs uppercase">
                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                                <span class="hidden sm:inline">Sala:</span>
                            </div>
                            <input type="text" name="codigo" maxlength="6" placeholder="CÓDIGO (EX.: AB12CD)"
                                   class="flex-1 bg-transparent px-2 py-1.5 text-amber-300 font-mono font-black uppercase tracking-widest text-xs sm:text-sm outline-none placeholder:text-blue-300/40">
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-black text-xs uppercase tracking-wider transition shadow shrink-0">
                                Entrar →
                            </button>
                        </form>
                    </div>

                    <!-- Links Rápidos de Estúdio e Regras -->
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2.5 pt-2 text-xs font-bold text-blue-200">
                        <a href="{{ route('apresentador.painel') }}" class="px-3.5 py-1.5 rounded-full bg-blue-950/80 border border-blue-400/40 hover:text-white hover:border-amber-400 transition inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"/></svg>
                            <span>Painel do Apresentador</span>
                        </a>
                        <a href="{{ route('curiosidades') }}" class="px-3.5 py-1.5 rounded-full bg-blue-950/80 border border-blue-400/40 hover:text-white hover:border-amber-400 transition inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                            <span>Regras Oficiais & 4 Fases</span>
                        </a>
                        <button type="button" onclick="abrirModalJogarAgora()" class="px-3.5 py-1.5 rounded-full bg-blue-950/80 border border-blue-400/40 text-amber-300 hover:text-amber-200 hover:border-amber-300 transition inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span>Escolher Modo</span>
                        </button>
                    </div>
                </div>

                <!-- Coluna Direita: Preview da Arena (Exibido no Desktop; Ocultado em Telas Pequenas para não poluir o celular!) -->
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

                        <!-- Pergunta Exemplo Real do Clássico -->
                        <div class="sm-question pt-4 pb-3.5 px-4 mb-3 shadow-lg">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">Gênesis 6:14 • Pergunta #001</p>
                            <p class="sm-question-text text-base sm:text-lg leading-snug">
                                De qual madeira Deus ordenou que Noé construísse a arca?
                            </p>
                        </div>

                        <!-- Balão do Convidado Bíblico -->
                        <div class="flex items-center gap-2 mb-3 px-1">
                            <div class="sm-avatar !w-8 !h-8 text-xs">J</div>
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
                                <div class="sm-ingot-value">0 pts</div>
                                <div class="sm-ingot-label">Errar</div>
                            </div>
                            <div class="sm-ingot">
                                <div class="sm-ingot-value">0 pts</div>
                                <div class="sm-ingot-label">Parar</div>
                            </div>
                            <div class="sm-ingot is-acertar">
                                <div class="sm-ingot-value">10 pts</div>
                                <div class="sm-ingot-label">Acertar</div>
                            </div>
                        </div>

                        <div class="sm-banner-red py-1.5 px-3 rounded-xl text-[10px] uppercase tracking-wider text-center mt-3">
                            Não sabe a resposta? Use ajuda!
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faixa Rápida de Estatísticas em Pílulas (Concisa em Mobile e Desktop) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-10">
                <div class="sm-panel p-3.5 text-center">
                    <p class="text-2xl font-black text-amber-300">1.000+</p>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-200 uppercase tracking-wider mt-0.5">Questões Oficiais</p>
                </div>
                <div class="sm-panel p-3.5 text-center">
                    <p class="text-2xl font-black text-emerald-400">66 Livros</p>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-200 uppercase tracking-wider mt-0.5">Velho & Novo Testamento</p>
                </div>
                <div class="sm-panel p-3.5 text-center">
                    <p class="text-2xl font-black text-blue-300">25 Sons</p>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-200 uppercase tracking-wider mt-0.5">Efeitos & Trilhas</p>
                </div>
                <div class="sm-panel p-3.5 text-center">
                    <p class="text-2xl font-black text-purple-300">4 Fases</p>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-200 uppercase tracking-wider mt-0.5">Escada do Milhão</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SEÇÃO COMPACTA: AS 4 FASES CLÁSSICAS -->
    <section class="py-10 px-4 sm:px-6 lg:px-8 border-t border-blue-400/20 bg-blue-950/40">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-300">Estrutura Oficial</span>
                    <h2 class="text-xl sm:text-2xl font-black text-white mt-0.5">Mecânica das 4 Fases</h2>
                </div>
                <a href="{{ route('curiosidades') }}" class="text-xs font-bold text-amber-300 hover:underline">
                    Ver Regras →
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="sm-panel p-4 text-center">
                    <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-200 font-black text-[10px] uppercase">1ª Fase</span>
                    <h3 class="text-sm font-black text-white mt-1.5">Fácil</h3>
                    <p class="text-xs text-emerald-300 font-bold mt-1">+10 pts / acerto</p>
                </div>
                <div class="sm-panel p-4 text-center">
                    <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-200 font-black text-[10px] uppercase">2ª Fase</span>
                    <h3 class="text-sm font-black text-white mt-1.5">Média</h3>
                    <p class="text-xs text-emerald-300 font-bold mt-1">+20 pts / acerto</p>
                </div>
                <div class="sm-panel p-4 text-center">
                    <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-200 font-black text-[10px] uppercase">3ª Fase</span>
                    <h3 class="text-sm font-black text-white mt-1.5">Difícil</h3>
                    <p class="text-xs text-emerald-300 font-bold mt-1">+30 pts / acerto</p>
                </div>
                <div class="sm-panel p-4 text-center border-2 !border-amber-400/60">
                    <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] uppercase">4ª Fase</span>
                    <h3 class="text-sm font-black text-amber-300 mt-1.5">Desafio</h3>
                    <p class="text-xs text-amber-300 font-bold mt-1">+50 pts / acerto</p>
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
                    <h2 class="text-xl sm:text-2xl font-black text-white mt-0.5">Enciclopédia Ilustrada</h2>
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
                    <a href="{{ route('curiosidades') }}" class="sm-panel overflow-hidden group block transition hover:scale-[1.02]">
                        <div class="h-24 sm:h-32 overflow-hidden relative bg-blue-950">
                            <img src="{{ $card['img'] }}" alt="{{ $card['titulo'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 opacity-85">
                        </div>
                        <div class="p-3">
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
    <footer class="border-t border-blue-400/20 bg-[#060e33] py-6 px-4 sm:px-6 lg:px-8 text-xs text-blue-200">
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
        // Toca automaticamente a trilha clássica ao entrar na página inicial
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
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>

</html>