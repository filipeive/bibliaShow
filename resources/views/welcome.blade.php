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
            background: linear-gradient(180deg, rgba(14, 33, 105, 0.80) 0%, rgba(10, 22, 74, 0.92) 50%, rgba(6, 14, 51, 0.88) 100%);
            border-top: 2px solid rgba(251, 191, 36, 0.45);
            border-bottom: 2px solid rgba(251, 191, 36, 0.45);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.55), inset 0 0 80px rgba(37, 99, 235, 0.18);
        }

        /* Botões em Cápsula Conectados ao Medalhão */
        .sm-capsule-btn {
            background: linear-gradient(180deg, #1d4ed8 0%, #1e3a8a 52%, #0f172a 100%);
            border: 2px solid rgba(147, 197, 253, 0.65);
            box-shadow:
                inset 0 2px 4px rgba(255, 255, 255, 0.25),
                0 8px 20px rgba(2, 6, 23, 0.65);
            transition: transform 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease, filter 0.16s ease;
        }
        .sm-capsule-btn:hover {
            transform: translateX(6px) scale(1.02);
            border-color: #fbbf24;
            filter: brightness(1.12);
            box-shadow:
                inset 0 2px 6px rgba(255, 255, 255, 0.35),
                0 12px 26px rgba(2, 6, 23, 0.75),
                0 0 18px rgba(251, 191, 36, 0.35);
        }
        .sm-capsule-btn.is-primary {
            background: linear-gradient(180deg, #fbbf24 0%, #f59e0b 55%, #b45309 100%);
            border-color: #fef3c7;
            box-shadow:
                inset 0 2px 6px rgba(255, 255, 255, 0.6),
                0 10px 26px rgba(245, 158, 11, 0.45);
        }

        /* Anel Metálico Azul + Ouro ao Redor do Medalhão */
        .sm-medallion-ring {
            background: conic-gradient(from 135deg, #fbbf24, #2563eb, #1e3a8a, #d97706, #fbbf24);
            box-shadow:
                0 0 45px rgba(251, 191, 36, 0.26),
                0 22px 50px rgba(2, 6, 23, 0.85);
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
    <main class="relative z-10 flex-1 flex flex-col justify-center py-8 sm:py-12">

        <!-- LOGO DE PALCO NO TOPO -->
        <div class="text-center px-4 mb-8 sm:mb-10">
            <div class="inline-flex items-center gap-2 sm-pill !px-4 !py-1.5 !text-[11px] uppercase tracking-widest mb-3 shadow-lg">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping inline-block"></span>
                <span>1.000 Questões • Sons Originais • Arena Ao Vivo</span>
            </div>
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white sm-logo-title leading-none">
                BÍBLIA <span class="text-amber-400">SHOW</span>
            </h1>
            <p class="text-xs sm:text-sm font-extrabold uppercase tracking-[0.26em] text-blue-200 mt-2">
                A Nova Maneira de Aprender a Bíblia
            </p>
        </div>

        <!-- FAIXA CENTRAL COM CÁPSULAS CONECTADAS AO MEDALHÃO -->
        <div class="sm-stage-band w-full py-10 sm:py-12 px-4 sm:px-6 backdrop-blur-md">
            <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-center gap-10 md:gap-12">

                <!-- LADO ESQUERDO: CÁPSULAS EM ARCO AO REDOR DO MEDALHÃO -->
                <div class="w-full max-w-sm md:w-72 flex flex-col gap-4 z-20">
                    @if($jogoAtivoInfo)
                        <a href="{{ route('game.play') }}" onmouseenter="tocarHoverClassico()"
                           class="sm-capsule-btn is-primary md:translate-x-6 rounded-full px-6 py-4 flex items-center justify-between gap-3 text-left cursor-pointer">
                            <span class="flex items-center gap-3 font-black text-slate-950 text-sm sm:text-base tracking-wider uppercase">
                                <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                                <span>Continuar ({{ $jogoAtivoInfo['numero'] }}/{{ $jogoAtivoInfo['total'] }})</span>
                            </span>
                            <span class="text-slate-950 font-black text-xs shrink-0">{{ $jogoAtivoInfo['pontos'] }} pts</span>
                        </a>
                    @else
                        <button type="button" onclick="abrirModalJogarAgora()" onmouseenter="tocarHoverClassico()"
                                class="sm-capsule-btn is-primary md:translate-x-6 rounded-full px-6 py-4 flex items-center justify-between gap-3 text-left cursor-pointer">
                            <span class="flex items-center gap-3 font-black text-slate-950 text-base sm:text-lg tracking-wider uppercase">
                                <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                                <span>Jogar</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-950/15 text-slate-950 font-black text-[10px] uppercase shrink-0">Solo / Arena</span>
                        </button>
                    @endif

                    <a href="{{ route('curiosidades') }}" onmouseenter="tocarHoverClassico()"
                       class="sm-capsule-btn md:translate-x-0 rounded-full px-6 py-3.5 flex items-center justify-between gap-3 text-left cursor-pointer">
                        <span class="flex items-center gap-3 font-black text-white text-sm sm:text-base tracking-wider uppercase">
                            <svg class="w-5 h-5 text-amber-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                            <span>Instruções</span>
                        </span>
                        <span class="text-amber-300 text-xs font-bold shrink-0">4 Fases</span>
                    </a>

                    <a href="{{ route('game.result') }}" onmouseenter="tocarHoverClassico()"
                       class="sm-capsule-btn md:translate-x-0 rounded-full px-6 py-3.5 flex items-center justify-between gap-3 text-left cursor-pointer">
                        <span class="flex items-center gap-3 font-black text-white text-sm sm:text-base tracking-wider uppercase">
                            <svg class="w-5 h-5 text-amber-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/></svg>
                            <span>Ranking</span>
                        </span>
                        <span class="text-amber-300 text-xs font-bold shrink-0">Geral</span>
                    </a>

                    <a href="{{ route('apresentador.painel') }}" onmouseenter="tocarHoverClassico()"
                       class="sm-capsule-btn md:translate-x-6 rounded-full px-6 py-3.5 flex items-center justify-between gap-3 text-left cursor-pointer">
                        <span class="flex items-center gap-3 font-black text-emerald-300 text-sm sm:text-base tracking-wider uppercase">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z"/></svg>
                            <span>Apresentador</span>
                        </span>
                        <span class="text-emerald-200 text-xs font-bold shrink-0">Telão</span>
                    </a>
                </div>

                <!-- CENTRO/DIREITA: MEDALHÃO DE PALCO + BOTÃO ENTRAR NA SALA CENTRALIZADO -->
                <div class="relative z-10 flex flex-col items-center pb-4 md:pb-0">
                    <div class="sm-medallion-ring w-72 h-72 sm:w-80 sm:h-80 rounded-full p-2.5 flex items-center justify-center relative">
                        <div class="w-full h-full rounded-full overflow-hidden border-2 border-amber-200/80 shadow-inner relative bg-slate-950">
                            <img id="medallionImg" src="{{ asset('images/classico/Sabia.jpg') }}" alt="Bíblia Show"
                                 class="w-full h-full object-cover transition-opacity duration-700 opacity-90">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#060e33]/95 via-[#060e33]/30 to-transparent flex items-end justify-center pb-10 px-6 text-center">
                                <div>
                                    <span id="medallionBadge" class="sm-pill !px-3 !py-0.5 !text-[9px] uppercase mb-1.5 inline-block">Modo Clássico</span>
                                    <p id="medallionCaption" class="text-sm sm:text-base font-black text-amber-300 drop-shadow">1.000 Perguntas Bíblicas</p>
                                    <p class="text-[11px] text-blue-100/90 font-semibold mt-0.5">4 Fases • Cartas 50/50 • Convidados</p>
                                </div>
                            </div>
                        </div>

                        <!-- Aba Centralizada na Base do Medalhão (Mesma identidade visual das cápsulas) -->
                        <a href="{{ route('entrar.view') }}" onmouseenter="tocarHoverClassico()"
                           class="sm-capsule-btn absolute -bottom-4 left-1/2 -translate-x-1/2 hover:!translate-x-[-50%] whitespace-nowrap px-6 py-2.5 rounded-full text-white font-black text-xs sm:text-sm uppercase tracking-wider inline-flex items-center gap-2.5 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                            <span>Entrar na Sala</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ENTRADA RÁPIDA POR CÓDIGO PIN + BARRA DE AÇÕES -->
        <div class="max-w-2xl w-full mx-auto px-4 mt-10 space-y-4">
            <form action="{{ route('entrar.view') }}" method="GET"
                  onsubmit="event.preventDefault(); const c = this.codigo.value.trim().toUpperCase(); window.location.href = '{{ url('/entrar') }}' + (c ? '/' + c : '');"
                  class="sm-panel !p-2.5 flex items-center gap-3 shadow-2xl border border-amber-400/50">
                <div class="pl-2.5 flex items-center gap-2 text-amber-300 font-black text-xs uppercase tracking-wider shrink-0">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                    <span class="hidden sm:inline">PIN da Sala</span>
                </div>
                <input type="text" name="codigo" maxlength="6" placeholder="DIGITE O CÓDIGO DA SALA (EX: AB12CD)"
                       class="flex-1 bg-transparent px-2 py-2 text-amber-300 font-mono font-black uppercase tracking-widest text-xs sm:text-sm outline-none placeholder:text-blue-300/40">
                <button type="submit"
                        class="sm-cta !w-auto !py-2.5 !px-5 !text-xs cursor-pointer shrink-0 inline-flex items-center gap-1.5">
                    <span>Entrar</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </button>
            </form>

            <div class="flex flex-wrap items-center justify-center gap-3 text-xs font-bold">
                <a href="{{ route('sobre') }}"
                   class="px-4 py-2 rounded-full bg-blue-950/80 border border-blue-400/40 text-blue-200 hover:text-white hover:border-amber-400 transition inline-flex items-center gap-2 shadow">
                    <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                    <span>Baixar App Android (APK)</span>
                </a>
                <button type="button" onclick="abrirModalDefinicoes()"
                        class="px-4 py-2 rounded-full bg-blue-950/80 border border-blue-400/40 text-amber-300 hover:text-white hover:border-amber-400 transition inline-flex items-center gap-2 shadow cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Definições & Tela Cheia</span>
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
