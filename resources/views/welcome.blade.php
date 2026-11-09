<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Bíblia Show') }} - Jogo Bíblico para Igrejas</title>
    <meta name="description" content="Bíblia Show 2.0 - Jogo bíblico interativo para igrejas, células e famílias. Jogue offline, crie salas e apresente em projetores.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .biblia-gradient { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        .biblia-accent { color: #f59e0b; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xs">B</span>
                    </div>
                    <span class="font-bold text-xl text-slate-900 tracking-tight">Bíblia Show</span>
                </div>
                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <a href="#" class="hover:text-amber-600 transition">Como funciona</a>
                    <a href="{{ route('game.play') }}" class="hover:text-amber-600 transition">Jogar individual</a>
                    <a href="{{ route('apresentador.painel') }}" class="hover:text-amber-600 transition">Criar sala</a>
                    <a href="{{ route('perguntas.index') }}" class="hover:text-amber-600 transition">Banco de perguntas</a>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('game.play') }}" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-lg font-medium shadow-md transition">
                        Jogar agora
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
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-sm font-medium">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        AO VIVO EM SUA IGREJA
                    </div>
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
                        O jogo bíblico que <br>
                        <span class="biblia-accent">transforma</span> sua congregação
                    </h1>
                    <p class="text-lg text-slate-300 max-w-lg leading-relaxed">
                        Bíblia Show 2.0 é uma plataforma completa de quiz bíblico para igrejas, células e famílias.
                        Jogue offline, crie salas em tempo real, projete o ranking e aprenda com explicações contextuais
                        após cada pergunta.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="{{ route('game.play') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                            Jogar individual (grátis)
                        </a>
                        <a href="{{ route('apresentador.painel') }}" class="border border-amber-500 hover:bg-amber-500 hover:text-white text-amber-500 px-8 py-4 rounded-xl font-bold text-lg transition">
                            Criar sala de apresentação
                        </a>
                    </div>
                    <div class="mt-8 flex items-center gap-6 text-sm text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="w-1 h-1 bg-green-500 rounded-full"></span>
                            <span>Sem conta necessária</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-1 h-1 bg-green-500 rounded-full"></span>
                            <span>Funciona offline</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-1 h-1 bg-green-500 rounded-full"></span>
                            <span>Projeção para TV/Projetor</span>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-amber-500/10 rounded-2xl blur-3xl"></div>
                    <div class="relative bg-white/5 backdrop-blur-sm border border-amber-200/30 rounded-2xl p-8 shadow-2xl">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-amber-500/10 p-4 rounded-xl border border-amber-200/30 text-center">
                                <div class="text-2xl font-bold text-amber-300">10+</div>
                                <div class="text-xs text-amber-300/70">Perguntas por livro</div>
                            </div>
                            <div class="bg-amber-500/10 p-4 rounded-xl border border-amber-200/30 text-center">
                                <div class="text-2xl font-bold text-amber-300">Ilimitado</div>
                                <div class="text-xs text-amber-300/70">Salas simultâneas</div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-amber-200/30">
                                <span class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-amber-900 font-bold">1</span>
                                <div>
                                    <div class="text-sm font-semibold text-slate-100">Escolha as perguntas</div>
                                    <div class="text-xs text-amber-200/80">Por livro, capítulo ou tema</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-amber-200/30">
                                <span class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-amber-900 font-bold">2</span>
                                <div>
                                    <div class="text-sm font-semibold text-slate-100">Crie a sala</div>
                                    <div class="text-xs text-amber-200/80">Gere código de acesso e QR Code</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-amber-200/30">
                                <span class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-amber-900 font-bold">3</span>
                                <div>
                                    <div class="text-sm font-semibold text-slate-100">Jogue e projete</div>
                                    <div class="text-xs text-amber-200/80">Ranking em tempo real no projetor</div>
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
                <h2 class="text-3xl font-bold text-slate-900 mb-4">Tudo o que sua igreja precisa</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Recursos pensados para pastores, líderes de células e apresentadores</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-300 transition group">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-200 transition">
                        <span class="text-amber-600 text-2xl">🎮</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Modo Individual Offline</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Jogue sozinho sem criar conta e sem internet. Perfeito para estudo pessoal e familiares.
                    </p>
                    <a href="{{ route('game.play') }}" class="text-amber-600 font-medium hover:underline">Começar a jogar →</a>
                </div>

                <!-- Feature 2 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-300 transition group">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-200 transition">
                        <span class="text-amber-600 text-2xl">📱</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Salas em Tempo Real</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Crie salas, gere código de acesso e QR Code. Participantes entram pelo smartphone.
                    </p>
                    <a href="{{ route('apresentador.painel') }}" class="text-amber-600 font-medium hover:underline">Criar sala →</a>
                </div>

                <!-- Feature 3 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-300 transition group">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-200 transition">
                        <span class="text-amber-600 text-2xl">📺</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Projeção Independente</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Ecrã de projeção separado dos controles do apresentador. Ideal para projetores e TVs.
                    </p>
                    <a href="#" class="text-amber-600 font-medium hover:underline">Ver demonstração →</a>
                </div>

                <!-- Feature 4 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-300 transition group">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-200 transition">
                        <span class="text-amber-600 text-2xl">📖</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Banco de Perguntas</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Perguntas por livro, capítulo, versículo, categoria e nível de dificuldade.
                    </p>
                    <a href="{{ route('perguntas.index') }}" class="text-amber-600 font-medium hover:underline">Explorar perguntas →</a>
                </div>

                <!-- Feature 5 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-300 transition group">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-200 transition">
                        <span class="text-amber-600 text-2xl">🎓</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Aprendizagem Contextual</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Depois de cada pergunta: resposta correta, referência, explicação e contexto da passagem.
                    </p>
                    <a href="#" class="text-amber-600 font-medium hover:underline">Saiba mais →</a>
                </div>

                <!-- Feature 6 -->
                <div class="bg-slate-50 p-8 rounded-2xl border border-slate-200 hover:border-amber-300 transition group">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-amber-200 transition">
                        <span class="text-amber-600 text-2xl">🏆</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Ranking Dinâmico</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Pontuações atualizadas em tempo real com ranking projetado para todos verem.
                    </p>
                    <a href="#" class="text-amber-600 font-medium hover:underline">Ver ranking →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="py-20 px-4 bg-slate-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900 mb-4">Como funciona em 3 passos</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Simples para pastores, líderes e participantes sem conhecimento técnico</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white font-bold text-2xl shadow-lg">1</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Crie a sala</h3>
                    <p class="text-slate-600 leading-relaxed">
                        O apresentador cria uma sala e gera um código de acesso de 6 caracteres.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white font-bold text-2xl shadow-lg">2</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Convide os participantes</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Os participantes entram pelo smartphone usando o código ou escaneando o QR Code.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white font-bold text-2xl shadow-lg">3</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Jogue e projete</h3>
                    <p class="text-slate-600 leading-relaxed">
                        O apresentador controla o jogo enquanto o ranking é projetado no ecrã principal.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4">
        <div class="max-w-4xl mx-auto biblia-gradient rounded-3xl p-10 text-center text-white shadow-2xl">
            <h2 class="text-3xl font-bold mb-4">Pronto para começar?</h2>
            <p class="text-amber-100 mb-8 text-lg">
                O Bíblia Show é gratuito para uso individual. Sem conta, sem cartão, sem complicação.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('game.play') }}" class="bg-white text-amber-600 hover:bg-amber-50 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl transition transform hover:-translate-y-1">
                    Jogar agora grátis
                </a>
                <a href="{{ route('apresentador.painel') }}" class="border border-white/30 hover:bg-white/10 text-white px-8 py-4 rounded-xl font-bold text-lg transition">
                    Criar sala de apresentação
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-8 px-4">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded bg-amber-500 flex items-center justify-center text-white font-bold text-xs">B</div>
                <span class="font-bold text-slate-900">Bíblia Show 2.0</span>
            </div>
            <div class="flex gap-6 text-sm text-slate-500">
                <a href="#" class="hover:text-amber-600">Privacidade</a>
                <a href="#" class="hover:text-amber-600">Termos</a>
                <a href="#" class="hover:text-amber-600">Licenciamento</a>
                <a href="#" class="hover:text-amber-600">Contato</a>
            </div>
            <div class="text-xs text-slate-400">
                Conteúdo bíblico baseado em traduções de domínio público. Verifique o status legal em seu território.
            </div>
        </div>
    </footer>

</body>
</html>