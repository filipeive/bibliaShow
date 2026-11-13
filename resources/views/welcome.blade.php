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
    <meta property="og:description" content="O clássico jogo bíblico estilo Show do Milhão: modo individual offline/online e arena ao vivo para igrejas!">
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
            50% { opacity: 0.65; transform: scale(1.06); }
        }
        .stage-glow { animation: spotlightPulse 5s ease-in-out infinite; }

        /* Faixa Central de Palco (Tema Azul Auditório + Borda Ouro) */
        .sm-stage-band {
            background: linear-gradient(180deg, rgba(14, 33, 105, 0.78) 0%, rgba(10, 22, 74, 0.90) 50%, rgba(6, 14, 51, 0.85) 100%);
            border-top: 2px solid rgba(251, 191, 36, 0.45);
            border-bottom: 2px solid rgba(251, 191, 36, 0.45);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.55), inset 0 0 60px rgba(37, 99, 235, 0.18);
        }

        /* Botões em Cápsula Conectados ao Medalhão */
        .sm-capsule-btn {
            background: linear-gradient(180deg, #1d4ed8 0%, #1e3a8a 52%, #0f172a 100%);
            border: 2px solid rgba(147, 197, 253, 0.65);
            box-shadow:
                inset 0 2px 4px rgba(255, 255, 255, 0.25),
                0 8px 18px rgba(2, 6, 23, 0.65);
            transition: transform 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease, filter 0.16s ease;
        }
        .sm-capsule-btn:hover {
            transform: translateX(6px) scale(1.03);
            border-color: #fbbf24;
            filter: brightness(1.12);
            box-shadow:
                inset 0 2px 6px rgba(255, 255, 255, 0.35),
                0 10px 24px rgba(2, 6, 23, 0.75),
                0 0 18px rgba(251, 191, 36, 0.35);
        }
        .sm-capsule-btn.is-primary {
            background: linear-gradient(180deg, #fbbf24 0%, #f59e0b 55%, #b45309 100%);
            border-color: #fef3c7;
            box-shadow:
                inset 0 2px 6px rgba(255, 255, 255, 0.6),
                0 10px 24px rgba(245, 158, 11, 0.45);
        }

        /* Anel Metálico Azul + Ouro ao Redor do Medalhão */
        .sm-medallion-ring {
            background: conic-gradient(from 135deg, #fbbf24, #2563eb, #1e3a8a, #d97706, #fbbf24);
            box-shadow:
                0 0 40px rgba(251, 191, 36, 0.28),
                0 20px 45px rgba(2, 6, 23, 0.85);
        }
    </style>
</head>

<body class="sm-stage text-slate-100 min-h-screen flex flex-col selection:bg-amber-500 selection:text-slate-950 overflow-x-hidden relative">

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

    <!-- Holofotes de Palco -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden -z-10">
        <div class="stage-glow absolute -top-24 left-1/4 w-96 h-96 rounded-full bg-amber-400/15 blur-3xl"></div>
        <div class="stage-glow absolute top-24 right-1/4 w-96 h-96 rounded-full bg-blue-500/20 blur-3xl" style="animation-delay: 2.5s;"></div>
    </div>

    <!-- Navbar Estilizada -->
    @include('partials.navbar', ['dark' => true])

    <!-- PALCO PRINCIPAL ESTILO MENU CLÁSSICO NO TEMA BÍBLIA SHOW 2.0 -->
    <main class="relative z-10 flex-1 flex flex-col justify-center py-5 sm:py-8">

        <!-- LOGO DE PALCO NO TOPO -->
        <div class="text-center px-4 mb-5 sm:mb-7">
            <div class="inline-flex items-center gap-2 sm-pill !px-4 !py-1 !text-[10px] sm:!text-[11px] uppercase tracking-widest mb-2.5 shadow-lg">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping inline-block"></span>
                <span>1.000 Questões • Sons Originais • Arena Ao Vivo</span>
            </div>
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white sm-logo-title leading-none">
                BÍBLIA <span class="text-amber-400">SHOW</span>
            </h1>
            <p class="text-[11px] sm:text-xs font-extrabold uppercase tracking-[0.26em] text-blue-200 mt-1.5">
                A Nova Maneira de Aprender a Bíblia
            </p>
        </div>

        <!-- FAIXA CENTRAL COM CÁPSULAS CONECTADAS AO MEDALHÃO -->
        <div class="sm-stage-band w-full py-7 sm:py-9 px-4 backdrop-blur-md">
            <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center justify-center gap-6 md:gap-0">

                <!-- LADO ESQUERDO: CÁPSULAS DO MENU PRINCIPAL -->
                <div class="w-full max-w-xs md:w-72 flex flex-col gap-3 md:-mr-8 z-20">
                    @if($jogoAtivoInfo)
                        <a href="{{ route('game.play') }}" onmouseenter="tocarHoverClassico()"
                           class="sm-capsule-btn is-primary rounded-full px-6 py-3.5 flex items-center justify-between text-left cursor-pointer">
                            <span class="font-black text-slate-950 text-sm sm:text-base tracking-wider uppercase">
                                ▶ CONTINUAR ({{ $jogoAtivoInfo['numero'] }}/{{ $jogoAtivoInfo['total'] }})
                            </span>
                            <span class="text-slate-950 font-black text-xs">{{ $jogoAtivoInfo['pontos'] }} pts</span>
                        </a>
                    @else
                        <button type="button" onclick="abrirModalJogarAgora()" onmouseenter="tocarHoverClassico()"
                                class="sm-capsule-btn is-primary rounded-full px-6 py-3.5 flex items-center justify-between text-left cursor-pointer">
                            <span class="font-black text-slate-950 text-base sm:text-lg tracking-wider uppercase">
                                ▶ INICIAR JOGO
                            </span>
                            <span class="px-2 py-0.5 rounded-full bg-slate-950/20 text-slate-950 font-black text-[10px] uppercase">Solo / Arena</span>
                        </button>
                    @endif

                    <a href="{{ route('curiosidades') }}" onmouseenter="tocarHoverClassico()"
                       class="sm-capsule-btn rounded-full px-6 py-2.5 flex items-center justify-between text-left cursor-pointer">
                        <span class="font-black text-white text-sm sm:text-base tracking-wider uppercase">
                            INSTRUÇÕES
                        </span>
                        <span class="text-amber-300 text-xs font-bold">4 Fases</span>
                    </a>

                    <a href="{{ route('game.result') }}" onmouseenter="tocarHoverClassico()"
                       class="sm-capsule-btn rounded-full px-6 py-2.5 flex items-center justify-between text-left cursor-pointer">
                        <span class="font-black text-white text-sm sm:text-base tracking-wider uppercase">
                            RANKING
                        </span>
                        <span class="text-amber-300 text-xs font-bold">🏆 Geral</span>
                    </a>

                    <a href="{{ route('apresentador.painel') }}" onmouseenter="tocarHoverClassico()"
                       class="sm-capsule-btn rounded-full px-6 py-2.5 flex items-center justify-between text-left cursor-pointer">
                        <span class="font-black text-emerald-300 text-sm sm:text-base tracking-wider uppercase">
                            APRESENTADOR
                        </span>
                        <span class="text-emerald-200 text-xs font-bold">Telão & Salas</span>
                    </a>
                </div>

                <!-- CENTRO/DIREITA: MEDALHÃO DE PALCO + ABA ENTRAR NA SALA -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="sm-medallion-ring w-64 h-64 sm:w-80 sm:h-80 rounded-full p-2 sm:p-2.5 flex items-center justify-center relative">
                        <div class="w-full h-full rounded-full overflow-hidden border-2 border-amber-200/80 shadow-inner relative bg-slate-950">
                            <img id="medallionImg" src="{{ asset('images/classico/Sabia.jpg') }}" alt="Bíblia Show"
                                 class="w-full h-full object-cover transition-opacity duration-700 opacity-90">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#060e33]/95 via-[#060e33]/25 to-transparent flex items-end justify-center p-5 text-center">
                                <div>
                                    <span id="medallionBadge" class="sm-pill !px-2.5 !py-0.5 !text-[9px] uppercase mb-1 inline-block">Modo Clássico</span>
                                    <p id="medallionCaption" class="text-xs sm:text-sm font-black text-amber-300 drop-shadow">1.000 Perguntas Bíblicas</p>
                                    <p class="text-[10px] text-blue-100/90 font-semibold">4 Fases • Cartas 50/50 • Convidados</p>
                                </div>
                            </div>
                        </div>

                        <!-- Aba Inferior Direita: Entrar na Arena -->
                        <a href="{{ route('entrar.view') }}" onmouseenter="tocarHoverClassico()"
                           class="absolute -bottom-3 right-2 sm:right-4 px-5 py-1.5 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 hover:from-emerald-300 hover:to-emerald-500 text-slate-950 font-black text-xs sm:text-sm tracking-wide shadow-xl border-2 border-emerald-200 transition">
                            🏟️ Entrar na Sala →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ENTRADA RÁPIDA POR CÓDIGO PIN + BARRA DE AÇÕES -->
        <div class="max-w-xl w-full mx-auto px-4 mt-7 space-y-3">
            <form action="{{ route('entrar.view') }}" method="GET"
                  onsubmit="event.preventDefault(); const c = this.codigo.value.trim().toUpperCase(); window.location.href = '{{ url('/entrar') }}' + (c ? '/' + c : '');"
                  class="sm-panel !p-2 flex items-center gap-2 shadow-2xl border border-amber-400/50">
                <span class="pl-2.5 text-[11px] font-black uppercase tracking-wider text-amber-300 shrink-0 hidden sm:inline">
                    🏟️ PIN da Sala:
                </span>
                <input type="text" name="codigo" maxlength="6" placeholder="DIGITE O CÓDIGO DA SALA (EX: AB12CD)"
                       class="flex-1 bg-transparent px-2.5 py-2 text-amber-300 font-mono font-black uppercase tracking-widest text-xs sm:text-sm outline-none placeholder:text-blue-300/40">
                <button type="submit"
                        class="sm-cta !w-auto !py-2 !px-4 !text-xs cursor-pointer shrink-0">
                    Entrar →
                </button>
            </form>

            <div class="flex flex-wrap items-center justify-center gap-2.5 text-xs font-bold">
                <a href="{{ route('sobre') }}"
                   class="px-3.5 py-1.5 rounded-full bg-blue-950/80 border border-blue-400/40 text-blue-200 hover:text-white hover:border-amber-400 transition inline-flex items-center gap-1.5 shadow">
                    <span>📱</span> Baixar App Android (APK)
                </a>
                <button type="button" onclick="abrirModalDefinicoes()"
                        class="px-3.5 py-1.5 rounded-full bg-blue-950/80 border border-blue-400/40 text-amber-300 hover:text-white hover:border-amber-400 transition inline-flex items-center gap-1.5 shadow cursor-pointer">
                    <span>⚙️</span> Definições & Tela Cheia
                </button>
            </div>
        </div>
    </main>

    <!-- Silhueta da Plateia no Rodapé -->
    <div class="sm-crowd w-full mt-auto"></div>

    <!-- Footer Clássico -->
    <footer class="border-t-2 border-blue-400/20 bg-[#060e33] py-4 px-4 sm:px-6 lg:px-8 text-xs text-blue-200 relative z-10">
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
                <a href="{{ route('sobre') }}" class="hover:text-amber-300 text-amber-300">Créditos & APK</a>
            </div>
        </div>
    </footer>

    <script>
        const BASE_AUDIO_WELCOME = "{{ asset('audio/classico') }}";
        let musicaHome = null;

        function tocarHoverClassico() {
            if (localStorage.getItem('biblia_show_som') === 'off') return;
            try {
                const fx = new Audio(`${BASE_AUDIO_WELCOME}/hover.wav`);
                fx.volume = (typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85) * 0.45;
                fx.play().catch(() => {});
            } catch (e) {}
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

        const slidesClassicos = [
            { img: "{{ asset('images/classico/Sabia.jpg') }}", badge: 'Modo Clássico', cap: '1.000 Perguntas Bíblicas' },
            { img: "{{ asset('images/classico/Morto.jpg') }}", badge: 'Enciclopédia', cap: 'Manuscritos & História' },
            { img: "{{ asset('images/classico/Portugues.jpg') }}", badge: '4 Fases', cap: 'Show do Milhão Bíblico' },
            { img: "{{ asset('images/classico/Septuaginta.jpg') }}", badge: 'Multijogador', cap: 'Arena Ao Vivo nas Igrejas' },
        ];
        let slideIdx = 0;
        setInterval(() => {
            const imgEl = document.getElementById('medallionImg');
            const capEl = document.getElementById('medallionCaption');
            const badgeEl = document.getElementById('medallionBadge');
            if (!imgEl || !capEl || !badgeEl) return;
            slideIdx = (slideIdx + 1) % slidesClassicos.length;
            imgEl.style.opacity = '0.25';
            setTimeout(() => {
                imgEl.src = slidesClassicos[slideIdx].img;
                badgeEl.textContent = slidesClassicos[slideIdx].badge;
                capEl.textContent = slidesClassicos[slideIdx].cap;
                imgEl.style.opacity = '0.90';
            }, 300);
        }, 4500);

        document.addEventListener('DOMContentLoaded', iniciarAudioHome);

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}").catch(() => {});
            });
        }
    </script>
</body>

</html>
