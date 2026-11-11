<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Manual do Jogo & Enciclopédia Bíblica</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="sm-stage text-slate-100 min-h-screen flex flex-col justify-between">

    @include('partials.navbar', ['dark' => true])

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full relative z-10">
        <!-- Navegação Superior / HUD -->
        <header class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 mb-2">
                    <span class="sm-pill text-xs uppercase tracking-wider">
                        ★ Manual Oficial de Regras ★
                    </span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-white sm-logo-title">Enciclopédia & Regras do Show</h1>
                <p class="text-xs sm:text-sm text-blue-200 mt-1">Conheça as 4 fases do jogo, as ajudas bíblicas e o acervo histórico</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="tocarAudioRegras()" id="btnOuvirRegras"
                        class="sm-arcade-btn text-xs py-2.5 px-4 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                    </svg>
                    <span id="txtOuvirRegras">Ouvir Locução das Regras</span>
                </button>
                <a href="{{ route('game.play', ['novo' => 1]) }}"
                   class="sm-arcade-btn is-gold text-xs py-2.5 px-5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                    </svg>
                    <span>Jogar Modo Individual</span>
                </a>
            </div>
        </header>

        <!-- Regras Oficiais do Show do Milhão Bíblico -->
        <section class="sm-panel p-6 sm:p-8 mb-10 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/50 flex items-center justify-center text-amber-400 text-xl font-black shadow-lg">
                    📜
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-white sm-logo-title">Estrutura das 4 Fases Clássicas</h2>
                    <p class="text-xs sm:text-sm text-blue-200">A pontuação cresce a cada fase rumo ao prêmio máximo</p>
                </div>
            </div>

            <!-- 4 Fases em Estilo Arcade HUD -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="p-5 rounded-2xl bg-blue-950/70 border border-blue-400/30 relative">
                    <div class="sm-badge-stage stage-1 mb-2">1ª FASE • FÁCIL</div>
                    <p class="text-xl font-black text-white">6 Perguntas</p>
                    <p class="text-xs text-blue-200 mt-1 font-semibold">
                        <strong class="text-amber-300">10 pontos</strong> por acerto
                    </p>
                    <p class="text-[11px] text-blue-300/70 mt-2">Questões essenciais sobre personagens, fatos e histórias centrais da Bíblia.</p>
                </div>

                <div class="p-5 rounded-2xl bg-blue-950/70 border border-blue-400/30 relative">
                    <div class="sm-badge-stage stage-2 mb-2">2ª FASE • MÉDIO</div>
                    <p class="text-xl font-black text-white">6 Perguntas</p>
                    <p class="text-xs text-blue-200 mt-1 font-semibold">
                        <strong class="text-amber-300">20 pontos</strong> por acerto
                    </p>
                    <p class="text-[11px] text-blue-300/70 mt-2">Detalhes sobre reis, profetas, livros poéticos e cartas apostólicas.</p>
                </div>

                <div class="p-5 rounded-2xl bg-blue-950/70 border border-blue-400/30 relative">
                    <div class="sm-badge-stage stage-3 mb-2">3ª FASE • DIFÍCIL</div>
                    <p class="text-xl font-black text-white">6 Perguntas</p>
                    <p class="text-xs text-blue-200 mt-1 font-semibold">
                        <strong class="text-amber-300">30 pontos</strong> por acerto
                    </p>
                    <p class="text-[11px] text-blue-300/70 mt-2">Versículos profundos, genealogias e profecias do Antigo e Novo Testamento.</p>
                </div>

                <div class="p-5 rounded-2xl bg-amber-500/10 border-2 border-amber-400/60 relative">
                    <div class="sm-badge-stage stage-4 mb-2">4ª FASE • DESAFIO</div>
                    <p class="text-xl font-black text-amber-300">6 Perguntas</p>
                    <p class="text-xs text-amber-100 mt-1 font-semibold">
                        <strong class="text-amber-300">50 pontos</strong> por acerto
                    </p>
                    <p class="text-[11px] text-amber-200/80 mt-2">O teste final para os mestres bíblicos! Pontuação máxima.</p>
                </div>
            </div>

            <!-- Mecânicas de Jogo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs sm:text-sm">
                <div class="bg-blue-950/50 p-4 rounded-2xl border border-blue-400/25 flex items-start gap-3">
                    <span class="text-2xl">⏱️</span>
                    <div>
                        <strong class="text-white block font-bold text-sm mb-1">Tempo de 60 Segundos</strong>
                        <span class="text-blue-200 text-xs leading-relaxed">
                            O participante tem 60 segundos por rodada para analisar e cravar a alternativa certa.
                        </span>
                    </div>
                </div>

                <div class="bg-blue-950/50 p-4 rounded-2xl border border-blue-400/25 flex items-start gap-3">
                    <span class="text-2xl">🛑</span>
                    <div>
                        <strong class="text-white block font-bold text-sm mb-1">Errar vs Parar</strong>
                        <span class="text-blue-200 text-xs leading-relaxed">
                            Ao parar, garante-se a pontuação acumulada. Ao errar, perde-se metade dos pontos da fase anterior.
                        </span>
                    </div>
                </div>

                <div class="bg-blue-950/50 p-4 rounded-2xl border border-blue-400/25 flex items-start gap-3">
                    <span class="text-2xl">🃏</span>
                    <div>
                        <strong class="text-white block font-bold text-sm mb-1">4 Ajudas do Show</strong>
                        <span class="text-blue-200 text-xs leading-relaxed">
                            <strong>Dica Bíblica</strong>, <strong>Cartas 50/50</strong> (elimina até 3 opções), <strong>Convidados Bíblicos</strong> e <strong>3 Pulos</strong>.
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Enciclopédia Bíblica Ilustrada (Acervo Clássico) -->
        <section class="mb-12">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-xl sm:text-3xl font-black text-white sm-logo-title">Enciclopédia & Curiosidades da Bíblia</h2>
                    <p class="text-xs sm:text-sm text-blue-200 mt-0.5">Artigos históricos e ilustrações restauradas do acervo clássico</p>
                </div>
                <span class="sm-pill text-xs">
                    ★ {{ count($artigos) }} Estudos Bíblicos ★
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($artigos as $artigo)
                    <article class="sm-panel overflow-hidden flex flex-col hover:border-amber-400/60 transition">
                        @if(!empty($artigo['imagem']))
                            <div class="h-48 sm:h-56 bg-slate-950 overflow-hidden relative border-b border-blue-400/20">
                                <img src="{{ $artigo['imagem'] }}" alt="{{ $artigo['titulo'] }}"
                                     class="w-full h-full object-cover opacity-90 hover:scale-105 transition duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-blue-950 via-blue-950/30 to-transparent"></div>
                                <h3 class="absolute bottom-3 left-5 right-5 text-lg sm:text-xl font-black text-amber-300 drop-shadow">
                                    {{ $artigo['titulo'] }}
                                </h3>
                            </div>
                        @else
                            <div class="p-6 border-b border-blue-400/20">
                                <h3 class="text-lg sm:text-xl font-black text-amber-300">
                                    {{ $artigo['titulo'] }}
                                </h3>
                            </div>
                        @endif

                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div class="text-xs sm:text-sm text-blue-100 leading-relaxed space-y-2.5 max-h-64 overflow-y-auto pr-2">
                                @foreach(explode("\n", $artigo['conteudo'] ?? '') as $paragrafo)
                                    @if(trim($paragrafo) !== '')
                                        <p>{{ $paragrafo }}</p>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </main>

    <div class="sm-crowd w-full mt-auto"></div>

    <script>
        let audioRegras = null;
        let tocando = false;

        function tocarAudioRegras() {
            const txt = document.getElementById('txtOuvirRegras');
            if (!audioRegras) {
                audioRegras = new Audio('/audio/classico/regras.wav');
                audioRegras.onended = () => {
                    tocando = false;
                    txt.textContent = 'Ouvir Locução das Regras';
                };
            }

            if (tocando) {
                audioRegras.pause();
                audioRegras.currentTime = 0;
                tocando = false;
                txt.textContent = 'Ouvir Locução das Regras';
            } else {
                audioRegras.play().catch(() => {});
                tocando = true;
                txt.textContent = 'Parar Locução';
            }
        }
    </script>
</body>
</html>