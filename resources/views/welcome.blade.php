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
        content="Bíblia Show 2.0 — O clássico jogo de perguntas e respostas bíblicas com 1.000 questões originais, locuções clássicas, 4 fases, salas multiplayer por QR Code e projeção para igrejas.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes spotlightPulse {
            0%, 100% { opacity: 0.35; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.06); }
        }
        .stage-glow { animation: spotlightPulse 5s ease-in-out infinite; }
    </style>
</head>

<body class="sm-stage text-slate-100 min-h-screen flex flex-col selection:bg-amber-500 selection:text-slate-950 overflow-x-hidden">

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

    <!-- PALCO PRINCIPAL DO JOGO (LOBBY ARCADE / AUDITÓRIO) -->
    <main class="relative flex-1 pt-5 sm:pt-8 pb-10 px-4 sm:px-6 lg:px-8">
        <!-- Holofotes decorativos de fundo -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden -z-10">
            <div class="stage-glow absolute -top-24 left-1/4 w-96 h-96 rounded-full bg-amber-400/15 blur-3xl"></div>
            <div class="stage-glow absolute top-20 right-1/4 w-96 h-96 rounded-full bg-blue-400/15 blur-3xl" style="animation-delay: 2.5s;"></div>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-12 gap-6 lg:gap-10 items-center">

                <!-- COLUNA ESQUERDA: LOBBY DE JOGO -->
                <div class="lg:col-span-6 space-y-5 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 sm-pill !px-4 !py-1 !text-[11px] uppercase tracking-widest shadow-lg">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping inline-block"></span>
                        <span>PALCO OFICIAL • 1.000 QUESTÕES & SONS</span>
                    </div>

                    <div>
                        <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white sm-logo-title leading-none">
                            BÍBLIA <span class="text-amber-400">SHOW</span>
                        </h1>
                        <p class="text-xs sm:text-sm font-bold uppercase tracking-[0.22em] text-amber-300/90 mt-2">
                            Edição Clássica • Show do Milhão Bíblico
                        </p>
                    </div>

                    <!-- AÇÃO PRINCIPAL DE JOGO -->
                    <div class="space-y-3.5 max-w-lg mx-auto lg:mx-0">
                        @if($jogoAtivoInfo)
                            <div class="sm-panel p-4 sm:p-5 text-left shadow-2xl border-2 border-amber-400/70">
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
                                            {{ $jogoAtivoInfo['jogador'] }} • Pergunta {{ $jogoAtivoInfo['numero'] }}/{{ $jogoAtivoInfo['total'] }}
                                        </p>
                                        <p class="text-xs text-blue-200 font-mono font-bold">{{ $jogoAtivoInfo['pontos'] }} pontos</p>
                                    </div>
                                </div>
                                <a href="{{ route('game.play') }}" class="sm-cta !text-sm sm:!text-base cursor-pointer shadow-xl !py-3.5">
                                    ▶ CONTINUAR PARTIDA ({{ $jogoAtivoInfo['numero'] }}/{{ $jogoAtivoInfo['total'] }})
                                </a>
                                <div class="text-center pt-2">
                                    <a href="{{ route('game.play', ['novo' => 1]) }}"
                                       onclick="return confirm('Deseja iniciar uma nova partida? O progresso atual será reiniciado.');"
                                       class="text-xs font-bold text-blue-300 hover:text-amber-300 hover:underline transition">
                                        ⟳ Iniciar nova partida do zero
                                    </a>
                                </div>
                            </div>
                        @else
                            <button type="button" onclick="abrirModalJogarAgora()"
                                    class="sm-cta !text-base sm:!text-lg cursor-pointer shadow-2xl !py-4 border-2 border-amber-200/80 hover:scale-[1.01] transition">
                                <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24">
                                    <path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                </svg>
                                <span>JOGAR AGORA</span>
                            </button>
                        @endif

                        <!-- ENTRADA RÁPIDA NA ARENA AO VIVO -->
                        <form action="{{ route('entrar.view') }}" method="GET"
                              onsubmit="event.preventDefault(); const c = this.codigo.value.trim().toUpperCase(); window.location.href = '{{ url('/entrar') }}' + (c ? '/' + c : '');"
                              class="sm-panel !p-2 flex items-center gap-2 shadow-xl border border-emerald-400/40 bg-blue-950/90">
                            <span class="pl-2.5 text-[11px] font-black uppercase tracking-wider text-emerald-300 shrink-0 hidden sm:inline">
                                🏟️ PIN DA SALA
                            </span>
                            <input type="text" name="codigo" maxlength="6" placeholder="CÓDIGO DA SALA (EX: AB12CD)"
                                   class="flex-1 bg-transparent px-2.5 py-2 text-amber-300 font-mono font-black uppercase tracking-widest text-xs sm:text-sm outline-none placeholder:text-blue-300/40">
                            <button type="submit"
                                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-400 to-emerald-600 hover:from-emerald-300 hover:to-emerald-500 text-slate-950 font-black text-xs uppercase tracking-wider shadow cursor-pointer shrink-0 transition">
                                Entrar →
                            </button>
                        </form>

                        <!-- TILES DE LOBBY (MENU DO JOGO) -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-1">
                            <a href="{{ route('apresentador.painel') }}"
                               class="sm-panel !p-3 flex flex-col items-center justify-center text-center gap-1.5 hover:border-emerald-400/70 hover:scale-[1.02] transition cursor-pointer">
                                <span class="text-xl">🎤</span>
                                <span class="text-[11px] font-black uppercase tracking-wide text-white">Apresentador</span>
                                <span class="text-[10px] text-emerald-300 font-bold">Criar Sala</span>
                            </a>
                            <a href="{{ route('curiosidades') }}"
                               class="sm-panel !p-3 flex flex-col items-center justify-center text-center gap-1.5 hover:border-amber-400/70 hover:scale-[1.02] transition cursor-pointer">
                                <span class="text-xl">📖</span>
                                <span class="text-[11px] font-black uppercase tracking-wide text-white">Regras</span>
                                <span class="text-[10px] text-amber-300 font-bold">4 Fases</span>
                            </a>
                            <a href="{{ route('sobre') }}"
                               class="sm-panel !p-3 flex flex-col items-center justify-center text-center gap-1.5 hover:border-blue-300/70 hover:scale-[1.02] transition cursor-pointer">
                                <span class="text-xl">📱</span>
                                <span class="text-[11px] font-black uppercase tracking-wide text-white">App Android</span>
                                <span class="text-[10px] text-blue-200 font-bold">APK Offline</span>
                            </a>
                            <button type="button" onclick="abrirModalDefinicoes()"
                                    class="sm-panel !p-3 flex flex-col items-center justify-center text-center gap-1.5 hover:border-amber-400/70 hover:scale-[1.02] transition cursor-pointer">
                                <span class="text-xl">⚙️</span>
                                <span class="text-[11px] font-black uppercase tracking-wide text-white">Definições</span>
                                <span class="text-[10px] text-amber-300 font-bold">Som & Tela</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- COLUNA DIREITA: SIMULADOR INTERATIVO DO PALCO -->
                <div class="lg:col-span-6">
                    <div class="sm-panel p-5 sm:p-6 shadow-2xl relative border-2 border-amber-400/50">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="sm-pill !px-3 !py-0.5 !text-[10px] uppercase font-black">
                                Teste Rápido • 1ª Fase
                            </span>
                            <div class="sm-pill text-xs">
                                <span>10 PTS • <small>1 / 24</small></span>
                            </div>
                            <span class="sm-timer !w-9 !h-9 !text-xs !border-amber-400">60s</span>
                        </div>

                        <div class="sm-question pt-4 pb-3.5 px-4 mb-3 shadow-lg">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">Gênesis 6:14 • Pergunta #001</p>
                            <p class="sm-question-text text-base sm:text-lg leading-snug">
                                De qual madeira Deus ordenou que Noé construísse a arca?
                            </p>
                        </div>

                        <!-- Ajudas Visuais -->
                        <div class="flex items-center justify-center gap-2.5 mb-3">
                            <span class="sm-lifeline !w-10 !h-8 !text-xs font-black" title="Cartas 50/50">−2<span class="sm-badge">1</span></span>
                            <span class="sm-lifeline !w-10 !h-8 !text-xs flex items-center justify-center" title="Convidados">👥<span class="sm-badge">3</span></span>
                            <span class="sm-lifeline !w-10 !h-8 !text-xs flex items-center justify-center" title="Dica Bíblica">💡<span class="sm-badge">3</span></span>
                            <span class="sm-lifeline !w-10 !h-8 !text-xs font-black" title="Pular">⇄<span class="sm-badge">3</span></span>
                        </div>

                        <!-- 4 Alternativas Interativas no Preview -->
                        <div class="space-y-2 mb-4" id="previewOpcoes">
                            @foreach(['A' => 'Madeira de Cedro', 'B' => 'Madeira de Gofer', 'C' => 'Madeira de Acácia', 'D' => 'Madeira de Oliveira'] as $letra => $textoOp)
                                <button type="button" onclick="testarPreviewOpcao('{{ $letra }}', this)"
                                        class="sm-option w-full text-left cursor-pointer transition hover:border-amber-300">
                                    <span class="flex items-center gap-2">
                                        <span class="sm-letter text-sm">{{ $letra }}:</span>
                                        <span class="text-xs sm:text-sm font-bold">{{ $textoOp }}</span>
                                    </span>
                                    <span class="preview-tag hidden text-[9px] font-black uppercase px-2 py-0.5 rounded-full"></span>
                                </button>
                            @endforeach
                        </div>

                        <!-- Lingotes ERRAR | PARAR | ACERTAR -->
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

                        <div id="previewFeedback" class="sm-banner-red py-1.5 px-3 rounded-xl text-[10px] uppercase tracking-wider text-center mt-3">
                            Clique em uma alternativa acima para testar!
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAIXA DE PROGRESSÃO DAS 4 FASES -->
            <div class="mt-8 sm:mt-10 grid grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach([
                    ['fase' => '1ª FASE', 'qs' => 'Perguntas 1 a 6', 'pts' => '+10 pts', 'cor' => 'border-blue-400/40 text-blue-200'],
                    ['fase' => '2ª FASE', 'qs' => 'Perguntas 7 a 12', 'pts' => '+20 pts', 'cor' => 'border-emerald-400/40 text-emerald-300'],
                    ['fase' => '3ª FASE', 'qs' => 'Perguntas 13 a 18', 'pts' => '+30 pts', 'cor' => 'border-amber-400/50 text-amber-300'],
                    ['fase' => '4ª FASE • MILHÃO', 'qs' => 'Perguntas 19 a 24', 'pts' => '+50 pts (Tudo ou Nada)', 'cor' => 'border-rose-400/60 text-rose-300'],
                ] as $f)
                    <div class="sm-panel !p-3.5 border {{ $f['cor'] }} flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-wider text-amber-300">{{ $f['fase'] }}</p>
                            <p class="text-xs font-bold text-white truncate">{{ $f['qs'] }}</p>
                        </div>
                        <span class="text-xs font-mono font-black shrink-0">{{ $f['pts'] }}</span>
                    </div>
                @endforeach
            </div>

            <!-- ENCICLOPÉDIA BÍBLICA ILUSTRADA (COMPACTA) -->
            <section class="mt-8 pt-6 border-t border-blue-400/20">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📜</span>
                        <h2 class="text-lg sm:text-xl font-black text-white sm-logo-title">Enciclopédia Ilustrada</h2>
                    </div>
                    <a href="{{ route('curiosidades') }}" class="text-xs font-bold text-amber-300 hover:underline">
                        Ver os 10 artigos →
                    </a>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    @foreach([
                            ['img' => asset('images/classico/Sabia.jpg'), 'titulo' => 'Você Sabia?', 'desc' => 'Gutenberg e fatos bíblicos.'],
                            ['img' => asset('images/classico/Morto.jpg'), 'titulo' => 'Mar Morto', 'desc' => 'Cavernas de Qumran.'],
                            ['img' => asset('images/classico/Septuaginta.jpg'), 'titulo' => 'A Septuaginta', 'desc' => 'Tradução grega no Egito.'],
                            ['img' => asset('images/classico/Portugues.jpg'), 'titulo' => 'Em Português', 'desc' => 'João Ferreira de Almeida.'],
                        ] as $card)
                        <a href="{{ route('curiosidades') }}" class="sm-panel overflow-hidden group block transition hover:scale-[1.02] shadow-xl">
                            <div class="h-24 sm:h-28 overflow-hidden relative bg-blue-950">
                                <img src="{{ $card['img'] }}" alt="{{ $card['titulo'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 opacity-90">
                            </div>
                            <div class="p-3">
                                <h3 class="font-black text-white text-xs sm:text-sm truncate">{{ $card['titulo'] }}</h3>
                                <p class="text-[10px] text-blue-200 mt-0.5 truncate">{{ $card['desc'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </main>

    <!-- Silhueta da Plateia no Rodapé -->
    <div class="sm-crowd w-full mt-auto"></div>

    <!-- Footer Clássico -->
    <footer class="border-t-2 border-blue-400/20 bg-[#060e33] py-5 px-4 sm:px-6 lg:px-8 text-xs text-blue-200">
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
        const BASE_AUDIO_WELCOME = "{{ asset('audio/classico') }}";
        let musicaHome = null;

        function testarPreviewOpcao(letra, btn) {
            const container = document.getElementById('previewOpcoes');
            const feedback = document.getElementById('previewFeedback');
            if (!container) return;

            container.querySelectorAll('.sm-option').forEach(el => {
                el.classList.remove('opcao-correta-reveal', 'border-rose-400', 'bg-rose-950/60');
                const tag = el.querySelector('.preview-tag');
                if (tag) tag.classList.add('hidden');
            });

            const correta = letra === 'B';
            const tag = btn.querySelector('.preview-tag');
            if (correta) {
                btn.classList.add('opcao-correta-reveal');
                if (tag) {
                    tag.textContent = 'Certa!';
                    tag.className = 'preview-tag text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-700 text-white';
                }
                if (feedback) {
                    feedback.textContent = '✓ Resposta Certa! +10 pontos em Gênesis 6:14 (Madeira de Gofer)!';
                    feedback.className = 'py-1.5 px-3 rounded-xl text-[10px] uppercase tracking-wider text-center mt-3 bg-emerald-600/30 border border-emerald-400/50 text-emerald-200 font-black';
                }
                if (localStorage.getItem('biblia_show_som') !== 'off') {
                    const fx = new Audio(`${BASE_AUDIO_WELCOME}/certa1.wav`);
                    fx.volume = (typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85);
                    fx.play().catch(() => {});
                }
            } else {
                btn.classList.add('border-rose-400', 'bg-rose-950/60');
                if (tag) {
                    tag.textContent = 'Errou';
                    tag.className = 'preview-tag text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-rose-700 text-white';
                }
                if (feedback) {
                    feedback.textContent = '✕ Não foi dessa vez! Tente novamente ou clique em JOGAR AGORA.';
                    feedback.className = 'sm-banner-red py-1.5 px-3 rounded-xl text-[10px] uppercase tracking-wider text-center mt-3';
                }
                if (localStorage.getItem('biblia_show_som') !== 'off') {
                    const fx = new Audio(`${BASE_AUDIO_WELCOME}/errou1.wav`);
                    fx.volume = (typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85);
                    fx.play().catch(() => {});
                }
            }
        }

        function iniciarAudioHome() {
            if (localStorage.getItem('biblia_show_som') === 'off') return;

            if (!musicaHome) {
                musicaHome = new Audio(`${BASE_AUDIO_WELCOME}/abertura.wav`);
                const vol = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85;
                musicaHome.volume = vol * 0.70;
            }

            musicaHome.play().catch(() => {
                const tocarNoPrimeiroGesto = () => {
                    if (localStorage.getItem('biblia_show_som') !== 'off' && musicaHome && musicaHome.paused) {
                        musicaHome.play().catch(() => {});
                    }
                };
                ['click', 'keydown'].forEach(evt => {
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
