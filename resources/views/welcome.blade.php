<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Bíblia Show') }} - Jogo Bíblico Interativo</title>
    <meta name="description" content="Bíblia Show - O clássico jogo de perguntas e respostas bíblicas recriado com 1.000 perguntas originais, locuções clássicas, projeção para igrejas e modo multiplayer.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .biblia-gradient { background: radial-gradient(circle at top right, #1e3a8a 0%, #0f172a 60%, #020617 100%); }
        .biblia-accent { color: #f59e0b; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 flex items-center justify-center shadow-md text-slate-950">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <span class="font-black text-xl text-slate-900 tracking-tight">Bíblia Show</span>
                </div>
                <div class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
                    <a href="{{ route('game.play', ['novo' => 1]) }}" class="hover:text-amber-600 transition">Jogar Individual</a>
                    <a href="{{ route('entrar.view') }}" class="hover:text-amber-600 transition">Entrar em Sala</a>
                    <a href="{{ route('apresentador.painel') }}" class="hover:text-amber-600 transition">Painel do Apresentador</a>
                    <a href="{{ route('curiosidades') }}" class="hover:text-amber-600 transition">Enciclopédia & Regras</a>
                    <a href="{{ route('perguntas.index') }}" class="hover:text-amber-600 transition">Banco (1.000+ Perguntas)</a>
                </div>
                <div class="flex items-center gap-2.5">
                    <button type="button" onclick="tocarAbertura()" id="btnTemaAbertura" title="Ouvir tema clássico de abertura"
                            class="inline-flex items-center gap-1.5 border border-slate-300 hover:border-amber-500 text-slate-700 px-3.5 py-2 rounded-xl text-xs font-bold transition">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                        </svg>
                        <span id="txtTemaAbertura" class="hidden sm:inline">Tema Clássico</span>
                    </button>
                    <a href="{{ route('game.play', ['novo' => 1]) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md transition">
                        Jogar Agora
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="biblia-gradient text-white py-20 px-4 overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-xs font-extrabold uppercase tracking-wider">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        CLÁSSICO RECRIADO • 1.000 PERGUNTAS E SONS ORIGINAIS
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black leading-tight tracking-tight">
                        O clássico <span class="biblia-accent">Bíblia Show</span> <br>
                        para sua igreja e família
                    </h1>
                    <p class="text-lg text-slate-300 max-w-lg leading-relaxed">
                        Reviva a emoção do clássico jogo bíblico estilo Show do Milhão com as 4 fases originais, locuções clássicas, dicas de referências bíblicas, convidados bíblicos e modo ao vivo para projetores e smartphones.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 pt-2">
                        <a href="{{ route('game.play', ['novo' => 1]) }}" class="inline-flex items-center justify-center gap-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 px-8 py-4 rounded-xl font-black text-lg shadow-lg transition transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                            </svg>
                            Jogar Modo Show do Milhão
                        </a>
                        <a href="{{ route('apresentador.painel') }}" class="inline-flex items-center justify-center gap-2 border-2 border-amber-500/70 hover:bg-amber-500 hover:text-slate-950 text-amber-400 px-7 py-4 rounded-xl font-extrabold text-lg transition">
                            Criar Sala para Igreja
                        </a>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center gap-6 text-sm text-slate-300">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>Locuções e Trilhas Originais</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>Funciona Offline</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>Projeção em Telão</span>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-amber-500/10 rounded-2xl blur-3xl"></div>
                    <div class="relative bg-white/5 backdrop-blur-sm border border-amber-200/30 rounded-3xl p-8 shadow-2xl">
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <div class="bg-rose-950/60 p-3.5 rounded-2xl border border-rose-500/40 text-center">
                                <div class="text-[10px] font-black uppercase text-rose-300">Errar</div>
                                <div class="text-xl font-black text-rose-400">Metade</div>
                            </div>
                            <div class="bg-amber-500/20 p-3.5 rounded-2xl border border-amber-400/50 text-center">
                                <div class="text-[10px] font-black uppercase text-amber-200">Parar</div>
                                <div class="text-xl font-black text-amber-300">100%</div>
                            </div>
                            <div class="bg-emerald-950/60 p-3.5 rounded-2xl border border-emerald-500/40 text-center">
                                <div class="text-[10px] font-black uppercase text-emerald-300">Acertar</div>
                                <div class="text-xl font-black text-emerald-400">+10 a +50</div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3.5">
                            <div class="flex items-center gap-3.5 p-3.5 bg-white/5 rounded-xl border border-amber-200/20">
                                <span class="w-9 h-9 bg-amber-500 rounded-xl flex items-center justify-center text-slate-950 font-black">1</span>
                                <div>
                                    <div class="text-sm font-bold text-slate-100">4 Fases Clássicas (10, 20, 30 e 50 pts)</div>
                                    <div class="text-xs text-amber-200/80">1.000 perguntas originais revisadas nos 66 livros</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3.5 p-3.5 bg-white/5 rounded-xl border border-amber-200/20">
                                <span class="w-9 h-9 bg-amber-500 rounded-xl flex items-center justify-center text-slate-950 font-black">2</span>
                                <div>
                                    <div class="text-sm font-bold text-slate-100">Dicas Bíblicas, Convidados, Cartas e Pulos</div>
                                    <div class="text-xs text-amber-200/80">Consulte personagens bíblicos e referências de versículos</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3.5 p-3.5 bg-white/5 rounded-xl border border-amber-200/20">
                                <span class="w-9 h-9 bg-amber-500 rounded-xl flex items-center justify-center text-slate-950 font-black">3</span>
                                <div>
                                    <div class="text-sm font-bold text-slate-100">Enciclopédia Ilustrada & Salas ao Vivo</div>
                                    <div class="text-xs text-amber-200/80">Estudos históricos do clássico e competição por QR Code</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-4 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-black text-slate-900 mb-3">Recursos Completos do Bíblia Show</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Ideal para estudo individual, escolas bíblicas dominicais, gincanas de jovens e cultos</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-400 transition group">
                    <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-500 group-hover:text-slate-950 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Modo Clássico Individual</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Jogue as 4 fases com cronômetro de 60s, confirmação "Você tem certeza?", trilha de suspense e efeitos sonoros originais.
                    </p>
                    <a href="{{ route('game.play', ['novo' => 1]) }}" class="text-amber-600 font-bold hover:underline">Começar partida →</a>
                </div>

                <!-- Feature 2 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-400 transition group">
                    <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-500 group-hover:text-slate-950 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Salas em Tempo Real</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Crie salas para sua igreja com código de acesso e QR Code. Os participantes respondem direto pelo smartphone.
                    </p>
                    <a href="{{ route('apresentador.painel') }}" class="text-amber-600 font-bold hover:underline">Abrir painel →</a>
                </div>

                <!-- Feature 3 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-400 transition group">
                    <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-500 group-hover:text-slate-950 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Projeção para Telão / TV</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Tela exclusiva para o projetor da igreja com perguntas em destaque, cronômetro sincronizado e ranking ao vivo.
                    </p>
                    <a href="{{ route('apresentador.painel') }}" class="text-amber-600 font-bold hover:underline">Configurar projeção →</a>
                </div>

                <!-- Feature 4 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-400 transition group">
                    <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-500 group-hover:text-slate-950 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">1.000+ Perguntas Bíblicas</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Acervo completo importado do clássico Bíblia Show com referências de livro, capítulo, versículo e convidados bíblicos.
                    </p>
                    <a href="{{ route('perguntas.index') }}" class="text-amber-600 font-bold hover:underline">Explorar perguntas →</a>
                </div>

                <!-- Feature 5 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-400 transition group">
                    <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-500 group-hover:text-slate-950 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Enciclopédia & Curiosidades</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Artigos ilustrados sobre a história da Bíblia, manuscritos, traduções, Bíblia de Gutenberg e locução das regras.
                    </p>
                    <a href="{{ route('curiosidades') }}" class="text-amber-600 font-bold hover:underline">Ler enciclopédia →</a>
                </div>

                <!-- Feature 6 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-400 transition group">
                    <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-500 group-hover:text-slate-950 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Placar Estilo Show do Milhão</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Sistema fiel de pontuação com valores de Errar, Parar e Acertar, além de ranking ao vivo para competições em grupo.
                    </p>
                    <a href="{{ route('entrar.view') }}" class="text-amber-600 font-bold hover:underline">Entrar com código →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-8 px-4">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-500 flex items-center justify-center text-slate-950 font-black text-xs">B</div>
                <span class="font-bold text-slate-900">Bíblia Show</span>
            </div>
            <div class="flex gap-6 text-sm text-slate-500">
                <a href="{{ route('game.play', ['novo' => 1]) }}" class="hover:text-amber-600">Jogar</a>
                <a href="{{ route('curiosidades') }}" class="hover:text-amber-600">Enciclopédia & Regras</a>
                <a href="{{ route('apresentador.painel') }}" class="hover:text-amber-600">Apresentador</a>
                <a href="{{ route('perguntas.index') }}" class="hover:text-amber-600">Banco de Questões</a>
            </div>
            <div class="text-xs text-slate-400">
                Acervo bíblico e sonoro restaurado do clássico Bíblia Show.
            </div>
        </div>
    </footer>

    <script>
        let audioAbertura = null;
        let tocandoAbertura = false;

        function tocarAbertura() {
            const txt = document.getElementById('txtTemaAbertura');
            if (!audioAbertura) {
                audioAbertura = new Audio('/audio/classico/abertura.wav');
                audioAbertura.onended = () => {
                    tocandoAbertura = false;
                    if (txt) txt.textContent = 'Tema Clássico';
                };
            }
            if (tocandoAbertura) {
                audioAbertura.pause();
                audioAbertura.currentTime = 0;
                tocandoAbertura = false;
                if (txt) txt.textContent = 'Tema Clássico';
            } else {
                audioAbertura.play().catch(() => {});
                tocandoAbertura = true;
                if (txt) txt.textContent = 'Parar Música';
            }
        }
    </script>
</body>
</html>
