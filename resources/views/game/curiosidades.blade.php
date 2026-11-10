<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Enciclopédia Bíblica & Regras Clássicas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
    </style>
</head>
<body class="milhao-bg text-slate-100 min-h-screen">

    @include('partials.navbar', ['dark' => true])

    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Navegação Superior -->
        <header class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <span class="text-xs font-black uppercase tracking-widest text-amber-400">Acervo Cultural & Regras</span>
                <h1 class="text-2xl sm:text-3xl font-black text-white">Enciclopédia Bíblica & Regras Clássicas</h1>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="tocarAudioRegras()" id="btnOuvirRegras"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-900/60 hover:bg-blue-800 border border-blue-500/40 text-amber-300 font-bold text-xs uppercase tracking-wider transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                    </svg>
                    <span id="txtOuvirRegras">Ouvir Locução das Regras</span>
                </button>
                <a href="{{ route('game.play', ['novo' => 1]) }}"
                   class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black px-5 py-2.5 rounded-xl text-sm shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                    </svg>
                    Jogar Agora
                </a>
            </div>
        </header>

        <!-- Regras Oficiais do Clássico Bíblia Show -->
        <section class="bg-slate-900/90 border-2 border-amber-500/40 rounded-3xl p-6 sm:p-8 shadow-2xl mb-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center text-amber-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white">Regras Gerais — Bíblia Show Clássico</h1>
                    <p class="text-xs sm:text-sm text-amber-300/80">Mecânica original extraída do clássico Bíblia Show</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
                <div class="bg-blue-950/60 border border-blue-500/30 rounded-2xl p-4">
                    <span class="text-[11px] font-black uppercase tracking-wider text-amber-400">1ª Fase</span>
                    <p class="text-lg font-extrabold text-white mt-0.5">6 Perguntas</p>
                    <p class="text-xs text-slate-300 mt-1">Nível Fácil • <strong class="text-emerald-400">10 pontos</strong> por acerto</p>
                </div>
                <div class="bg-blue-950/60 border border-blue-500/30 rounded-2xl p-4">
                    <span class="text-[11px] font-black uppercase tracking-wider text-amber-400">2ª Fase</span>
                    <p class="text-lg font-extrabold text-white mt-0.5">6 Perguntas</p>
                    <p class="text-xs text-slate-300 mt-1">Nível Médio • <strong class="text-emerald-400">20 pontos</strong> por acerto</p>
                </div>
                <div class="bg-blue-950/60 border border-blue-500/30 rounded-2xl p-4">
                    <span class="text-[11px] font-black uppercase tracking-wider text-amber-400">3ª Fase</span>
                    <p class="text-lg font-extrabold text-white mt-0.5">6 Perguntas</p>
                    <p class="text-xs text-slate-300 mt-1">Nível Difícil • <strong class="text-emerald-400">30 pontos</strong> por acerto</p>
                </div>
                <div class="bg-amber-500/15 border border-amber-400/40 rounded-2xl p-4">
                    <span class="text-[11px] font-black uppercase tracking-wider text-amber-300">4ª Fase (Desafio)</span>
                    <p class="text-lg font-extrabold text-white mt-0.5">6 Perguntas</p>
                    <p class="text-xs text-amber-100 mt-1">Nível Desafio • <strong class="text-amber-300">50 pontos</strong> por acerto</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-slate-300">
                <div class="flex items-start gap-3 bg-slate-950/60 p-4 rounded-2xl border border-slate-800">
                    <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <strong class="text-white block">Tempo de 60 Segundos</strong>
                        Você tem 60 segundos para responder cada pergunta. Caso o tempo se esgote ou erre a resposta, o jogo encerra com metade dos pontos acumulados.
                    </div>
                </div>
                <div class="flex items-start gap-3 bg-slate-950/60 p-4 rounded-2xl border border-slate-800">
                    <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    <div>
                        <strong class="text-white block">Dicas Bíblicas, Convidados, Cartas e Pulos</strong>
                        Você dispõe de <strong>3 Dicas Bíblicas</strong> (livro, capítulo e versículo), <strong>3 Ajudas de Personagens Bíblicos</strong>, <strong>Cartas 50/50</strong> e <strong>3 Pulos</strong> durante a partida.
                    </div>
                </div>
            </div>
        </section>

        <!-- Enciclopédia Bíblica Ilustrada (Acervo Clássico) -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-black text-white">Enciclopédia & Curiosidades da Bíblia</h2>
                    <p class="text-sm text-slate-400">Artigos históricos e ilustrações restauradas do acervo clássico</p>
                </div>
                <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    {{ count($artigos) }} Estudos Clássicos
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($artigos as $artigo)
                    <article class="bg-slate-900/90 border border-slate-700/80 hover:border-amber-500/50 rounded-3xl overflow-hidden shadow-xl transition flex flex-col">
                        @if(!empty($artigo['imagem']))
                            <div class="h-52 bg-slate-950 overflow-hidden relative">
                                <img src="{{ $artigo['imagem'] }}" alt="{{ $artigo['titulo'] }}"
                                     class="w-full h-full object-cover opacity-90 hover:scale-105 transition duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent"></div>
                                <h3 class="absolute bottom-3 left-5 right-5 text-xl font-black text-amber-300 drop-shadow">
                                    {{ $artigo['titulo'] }}
                                </h3>
                            </div>
                        @endif

                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div class="text-sm text-slate-300 leading-relaxed space-y-2 max-h-64 overflow-y-auto pr-2">
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
    </div>

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

