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
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .stitch-bg {
            background: radial-gradient(circle at 20% 0%, #1e3a8a 0%, #0f172a 45%, #020617 100%);
        }

        .stitch-card {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.92) 0%, rgba(2, 6, 23, 0.96) 100%);
        }

        .question-preview {
            background: linear-gradient(180deg, #04103aff 0%, #030b30ff 100%);
            border: 2px solid #fbbf24;
        }
    </style>
</head>

<body class="stitch-bg text-slate-100 min-h-screen selection:bg-amber-500 selection:text-slate-950">

    <!-- Navbar Sem Quebra de Linha -->
    @include('partials.navbar', ['dark' => true])

    <!-- HERO SECTION (Inspirado no Design Stitch com Dados Reais do Bíblia Show) -->
    <section class="relative pt-10 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <!-- Efeitos de Luz de Fundo -->
        <div
            class="pointer-events-none absolute -top-24 left-1/2 -translate-x-1/2 w-[680px] h-[340px] bg-amber-500/15 blur-[130px] rounded-full">
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <!-- Coluna Esquerda (7 cols): Headline, CTAs e Entrada Rápida por Código -->
                <div class="lg:col-span-7 space-y-6">
                    <div
                        class="inline-flex flex-wrap items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-amber-500/15 border border-amber-400/30 text-amber-300 text-xs font-black uppercase tracking-wider">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-400"></span>
                        </span>
                        <span>ACERVO CLÁSSICO OFICIAL • 1.000 PERGUNTAS & LOCUÇÕES ORIGINAIS</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl xl:text-6xl font-black leading-[1.08] tracking-tight text-white">
                        Desafie seu conhecimento na <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-amber-400 to-yellow-500">Arena
                            Bíblia Show</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed">
                        A recriação completa do clássico jogo bíblico estilo <strong>Show do Milhão</strong> com as 4
                        fases originais (10, 20, 30 e 50 pontos), dicas de referências bíblicas, consulta a personagens
                        bíblicos, enciclopédia ilustrada e salas ao vivo com QR Code para sua igreja.
                    </p>

                    <!-- Botões Principais Limpos e Diretos -->
                    <div class="flex flex-wrap items-center gap-3.5 pt-2">
                        <button type="button" onclick="abrirModalJogarAgora()"
                            class="inline-flex items-center gap-2.5 bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 px-7 py-4 rounded-2xl font-black text-base shadow-xl shadow-amber-500/25 ring-2 ring-amber-300/50 transition transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                            </svg>
                            Jogar Agora
                        </button>

                        <a href="{{ route('game.play', ['novo' => 1]) }}"
                            class="inline-flex items-center gap-2 bg-slate-900/90 hover:bg-slate-800 border-2 border-amber-500/50 text-amber-300 px-6 py-4 rounded-2xl font-extrabold text-base transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                            </svg>
                            Jogo Individual
                        </a>
                    </div>

                    <!-- Barra Rápida: Entrar numa Sala com Código -->
                    <div class="pt-2">
                        <form action="{{ route('entrar.view') }}" method="GET"
                            onsubmit="event.preventDefault(); const c = this.codigo.value.trim().toUpperCase(); window.location.href = c ? '/entrar/' + c : '/entrar';"
                            class="max-w-lg bg-slate-900/90 border border-slate-700/80 rounded-2xl p-2 flex items-center gap-2 shadow-lg">
                            <div class="pl-3 flex items-center gap-2 text-slate-400">
                                <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                </svg>
                                <span class="text-xs font-bold uppercase tracking-wider hidden sm:inline">Código da
                                    Sala:</span>
                            </div>
                            <input type="text" name="codigo" maxlength="6" placeholder="Ex.: AB12CD"
                                class="flex-1 bg-transparent px-2 py-2 text-amber-300 font-mono font-black uppercase tracking-widest text-sm outline-none placeholder:text-slate-600">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-500 text-white font-black text-xs px-4 py-2.5 rounded-xl transition shrink-0">
                                Entrar na Sala →
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Coluna Direita (5 cols): Preview Interativo da Arena Show do Milhão -->
                <div class="lg:col-span-5">
                    <div class="stitch-card border-2 border-amber-500/40 rounded-3xl p-5 sm:p-6 shadow-2xl relative">
                        <!-- Topo do Card Preview -->
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="px-3 py-1 rounded-full bg-amber-500 text-slate-950 font-black text-[11px] uppercase tracking-wider">
                                1ª Fase • Vale 10 Pontos
                            </span>
                            <span class="font-mono font-black text-amber-400 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                60s
                            </span>
                        </div>

                        <!-- Pergunta Exemplo Real do Clássico -->
                        <div class="question-preview rounded-2xl p-4 sm:p-5 mb-4 shadow-lg">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-amber-300 mb-1">Gênesis 6:14 •
                                Pergunta Clássica #001</p>
                            <p class="text-base sm:text-lg font-black text-white leading-snug">
                                De qual madeira Deus ordenou que Noé construísse a arca?
                            </p>
                        </div>

                        <!-- 4 Alternativas -->
                        <div class="space-y-2 mb-5">
                            @foreach(['A' => 'Madeira de Cedro', 'B' => 'Madeira de Gofer', 'C' => 'Madeira de Acácia', 'D' => 'Madeira de Oliveira'] as $letra => $textoOp)
                                <div
                                    class="p-3 rounded-xl border flex items-center gap-3 {{ $letra === 'B' ? 'bg-emerald-950/70 border-emerald-400 text-emerald-100' : 'bg-blue-950/60 border-blue-500/30 text-slate-200' }}">
                                    <span
                                        class="w-7 h-7 rounded-lg {{ $letra === 'B' ? 'bg-emerald-400 text-slate-950' : 'bg-amber-500 text-slate-950' }} font-black text-xs flex items-center justify-center shrink-0">
                                        {{ $letra }}
                                    </span>
                                    <span class="text-xs sm:text-sm font-bold flex-1">{{ $textoOp }}</span>
                                    @if($letra === 'B')
                                        <span
                                            class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300">Certa!</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Placar 3 Caixas (ERRAR | PARAR | ACERTAR) -->
                        <div class="grid grid-cols-3 gap-2.5 pt-3 border-t border-slate-800">
                            <div class="bg-rose-950/60 border border-rose-500/40 rounded-xl p-2.5 text-center">
                                <span class="text-[10px] font-black uppercase text-rose-300 block">Errar</span>
                                <span class="text-base font-black text-rose-400">25 pts</span>
                            </div>
                            <div class="bg-amber-500/20 border border-amber-400/50 rounded-xl p-2.5 text-center">
                                <span class="text-[10px] font-black uppercase text-amber-200 block">Parar</span>
                                <span class="text-base font-black text-amber-300">50 pts</span>
                            </div>
                            <div class="bg-emerald-950/60 border border-emerald-500/40 rounded-xl p-2.5 text-center">
                                <span class="text-[10px] font-black uppercase text-emerald-300 block">Acertar</span>
                                <span class="text-base font-black text-emerald-400">60 pts</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de Estatísticas Reais do Acervo -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-14">
                <div class="stitch-card border border-slate-800 rounded-2xl p-5 text-center">
                    <p class="text-3xl font-black text-amber-400">1.000+</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Questões Clássicas (DBF)
                    </p>
                </div>
                <div class="stitch-card border border-slate-800 rounded-2xl p-5 text-center">
                    <p class="text-3xl font-black text-emerald-400">66 Livros</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Antigo e Novo Testamento
                    </p>
                </div>
                <div class="stitch-card border border-slate-800 rounded-2xl p-5 text-center">
                    <p class="text-3xl font-black text-blue-400">25 Sons</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Locuções e Trilhas WAV</p>
                </div>
                <div class="stitch-card border border-slate-800 rounded-2xl p-5 text-center">
                    <p class="text-3xl font-black text-purple-400">4 Fases</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Com Dicas e Convidados</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SEÇÃO: AS 4 FASES E AJUDAS DO CLÁSSICO -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 border-t border-slate-800/80 bg-slate-950/60">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-10">
                <div>
                    <span class="text-xs font-black uppercase tracking-widest text-amber-400">Mecânica Oficial
                        Restaurada</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-white mt-1">Como funcionam as 4 Fases e Ajudas
                        Bíblicas</h2>
                </div>
                <a href="{{ route('curiosidades') }}" class="text-sm font-bold text-amber-400 hover:underline">
                    Ouvir locução das regras completas →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
                <div class="stitch-card border border-blue-500/30 rounded-3xl p-6">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 font-black text-xs uppercase">1ª
                        Fase</span>
                    <h3 class="text-xl font-black text-white mt-3">6 Perguntas Iniciais</h3>
                    <p class="text-sm text-slate-400 mt-1">Cada resposta certa soma <strong class="text-emerald-400">+10
                            pontos</strong> ao placar.</p>
                </div>
                <div class="stitch-card border border-blue-500/30 rounded-3xl p-6">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 font-black text-xs uppercase">2ª
                        Fase</span>
                    <h3 class="text-xl font-black text-white mt-3">6 Perguntas Médias</h3>
                    <p class="text-sm text-slate-400 mt-1">Cada resposta certa soma <strong class="text-emerald-400">+20
                            pontos</strong> ao placar.</p>
                </div>
                <div class="stitch-card border border-blue-500/30 rounded-3xl p-6">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 font-black text-xs uppercase">3ª
                        Fase</span>
                    <h3 class="text-xl font-black text-white mt-3">6 Perguntas Difíceis</h3>
                    <p class="text-sm text-slate-400 mt-1">Cada resposta certa soma <strong class="text-emerald-400">+30
                            pontos</strong> ao placar.</p>
                </div>
                <div class="stitch-card border-2 border-amber-500/50 rounded-3xl p-6">
                    <span class="px-3 py-1 rounded-full bg-amber-500 text-slate-950 font-black text-xs uppercase">4ª
                        Fase Desafio</span>
                    <h3 class="text-xl font-black text-amber-300 mt-3">6 Perguntas Finais</h3>
                    <p class="text-sm text-slate-300 mt-1">Cada acerto vale <strong class="text-amber-300">+50
                            pontos</strong> para os mestres da Palavra.</p>
                </div>
            </div>

            <!-- Modos de Jogo Bento Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="stitch-card border border-slate-800 hover:border-amber-500/50 rounded-3xl p-7 transition flex flex-col justify-between">
                    <div>
                        <div
                            class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center text-amber-400 mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-white mb-2">Modo Individual Offline</h3>
                        <p class="text-sm text-slate-400 leading-relaxed mb-6">
                            Jogue sozinho ou em família sem precisar de internet nem cadastro. Utilize as 3 Dicas
                            Bíblicas, consulte os 3 Convidados Bíblicos, Cartas 50/50 e Pulos.
                        </p>
                    </div>
                    <a href="{{ route('game.play', ['novo' => 1]) }}"
                        class="inline-flex items-center gap-1.5 text-sm font-black text-amber-400 hover:text-amber-300">
                        Iniciar Partida Individual →
                    </a>
                </div>

                <div
                    class="stitch-card border border-slate-800 hover:border-emerald-500/50 rounded-3xl p-7 transition flex flex-col justify-between">
                    <div>
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center text-emerald-400 mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-white mb-2">Salas Ao Vivo & Telão da Igreja</h3>
                        <p class="text-sm text-slate-400 leading-relaxed mb-6">
                            O apresentador controla as perguntas, revela dicas, aciona convidados bíblicos e exibe o
                            gabarito no telão enquanto a igreja responde pelo celular.
                        </p>
                    </div>
                    <a href="{{ route('apresentador.painel') }}"
                        class="inline-flex items-center gap-1.5 text-sm font-black text-emerald-400 hover:text-emerald-300">
                        Abrir Estúdio do Apresentador →
                    </a>
                </div>

                <div
                    class="stitch-card border border-slate-800 hover:border-purple-500/50 rounded-3xl p-7 transition flex flex-col justify-between">
                    <div>
                        <div
                            class="w-12 h-12 rounded-2xl bg-purple-500/20 border border-purple-400/40 flex items-center justify-center text-purple-400 mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-white mb-2">Enciclopédia Ilustrada & Admin</h3>
                        <p class="text-sm text-slate-400 leading-relaxed mb-6">
                            Explore os 10 artigos históricos do CD-ROM original (Manuscritos do Mar Morto, Septuaginta,
                            Traduções) e gerencie tudo no Painel Administrativo.
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('curiosidades') }}"
                            class="text-sm font-black text-purple-400 hover:text-purple-300">Enciclopédia →</a>
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-sm font-black text-amber-400 hover:text-amber-300">Painel Admin →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SEÇÃO: PREVIEW DA ENCICLOPÉDIA BÍBLICA CLÁSSICA -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 border-t border-slate-800/80">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <div>
                    <span class="text-xs font-black uppercase tracking-widest text-amber-400">História e Arqueologia
                        Bíblica</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-white mt-1">Acervo Ilustrado do Clássico</h2>
                </div>
                <a href="{{ route('curiosidades') }}"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-amber-300 border border-slate-700 transition">
                    Ver todos os 10 estudos →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach([
                        ['img' => '/images/classico/Sabia.jpg', 'titulo' => 'Você Sabia? Curiosidades', 'desc' => 'A Bíblia de Gutenberg, divisão em capítulos e fatos históricos.'],
                        ['img' => '/images/classico/Morto.jpg', 'titulo' => 'Manuscritos do Mar Morto', 'desc' => 'As cavernas de Qumran e a preservação milenar das Escrituras.'],
                        ['img' => '/images/classico/Septuaginta.jpg', 'titulo' => 'A Septuaginta', 'desc' => 'A histórica tradução grega do Antigo Testamento em Alexandria.'],
                        ['img' => '/images/classico/Portugues.jpg', 'titulo' => 'A Bíblia em Português', 'desc' => 'A trajetória de João Ferreira de Almeida e as traduções lusófonas.'],
                    ] as $card)
                    <a href="{{ route('curiosidades') }}"
                        class="stitch-card border border-slate-800 hover:border-amber-500/50 rounded-3xl overflow-hidden group transition">
                        <div class="h-40 overflow-hidden relative bg-slate-950">
                            <img src="{{ $card['img'] }}" alt="{{ $card['titulo'] }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500 opacity-90">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent">
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-black text-white text-base group-hover:text-amber-400 transition">
                                {{ $card['titulo'] }}</h3>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">{{ $card['desc'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2.5">
                <div
                    class="w-7 h-7 rounded-lg bg-amber-500 flex items-center justify-center text-slate-950 font-black text-xs">
                    B</div>
                <span class="font-black text-white">Bíblia Show 2.0</span>
                <span class="text-xs text-slate-500">• Clássico Recriado</span>
            </div>
            <div class="flex flex-wrap justify-center gap-5 text-xs font-bold text-slate-400">
                <a href="{{ route('game.play', ['novo' => 1]) }}" class="hover:text-amber-400">Jogar Individual</a>
                <a href="{{ route('entrar.view') }}" class="hover:text-amber-400">Entrar em Sala</a>
                <a href="{{ route('apresentador.painel') }}" class="hover:text-amber-400">Apresentador</a>
                <a href="{{ route('curiosidades') }}" class="hover:text-amber-400">Enciclopédia</a>
                <a href="{{ route('sobre') }}" class="hover:text-amber-400 text-amber-300">Sobre o Projeto</a>
                <a href="{{ route('perguntas.index') }}" class="hover:text-amber-400">Perguntas</a>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-amber-400">Admin</a>
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
                // Se o navegador suspender o autoplay inicial, toca no primeiríssimo gesto (clique/toque)
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