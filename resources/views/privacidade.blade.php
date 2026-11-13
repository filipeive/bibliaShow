<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade e Cookies — Bíblia Show</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
</head>
<body class="sm-stage min-h-screen text-slate-100">
    @include('partials.navbar', ['dark' => true])

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
        <div class="sm-card p-6 sm:p-10 space-y-6">
            <div class="border-b border-blue-400/20 pb-5">
                <span class="sm-badge-gold inline-block mb-3">TRANSPARÊNCIA &amp; LGPD</span>
                <h1 class="text-2xl sm:text-3xl font-black text-white">Política de Privacidade e Cookies</h1>
                <p class="text-sm text-blue-200 mt-2">
                    Entenda como o <strong class="text-amber-300">Bíblia Show 2.0</strong> utiliza cookies e armazenamento local para garantir uma experiência rápida, segura e sem sobrecarga.
                </p>
            </div>

            <section class="space-y-2">
                <h2 class="text-lg font-extrabold text-amber-300">1. Cookies Essenciais de Sessão</h2>
                <p class="text-sm text-blue-100/90 leading-relaxed">
                    Utilizamos apenas cookies técnicos indispensáveis para manter a sua partida ativa, proteger formulários contra ataques (CSRF) e autenticar apresentadores ou administradores quando realizam login.
                </p>
                <ul class="list-disc list-inside text-xs sm:text-sm text-blue-200 space-y-1 pl-2">
                    <li><code class="text-amber-300">laravel_session</code> — mantém o estado seguro da sua sessão de jogo.</li>
                    <li><code class="text-amber-300">XSRF-TOKEN</code> — proteção contra requisições forjadas.</li>
                    <li><code class="text-amber-300">biblia_show_cookie_consent</code> — registra que você já visualizou e aceitou este aviso.</li>
                </ul>
            </section>

            <section class="space-y-2">
                <h2 class="text-lg font-extrabold text-amber-300">2. Armazenamento Local (Preferências do Jogador)</h2>
                <p class="text-sm text-blue-100/90 leading-relaxed">
                    Para deixar o jogo mais ágil e evitar consultas repetidas ao servidor, guardamos localmente no seu navegador preferências de áudio (mudo/locução), modo de tela cheia e código de participante na Arena Ao Vivo.
                </p>
            </section>

            <section class="space-y-2">
                <h2 class="text-lg font-extrabold text-amber-300">3. Dados de Partidas e Ranking</h2>
                <p class="text-sm text-blue-100/90 leading-relaxed">
                    Os nomes informados nas partidas individuais ou nas salas da igreja são exibidos exclusivamente nos placares e telões do jogo. Não vendemos nem compartilhamos seus dados com redes de publicidade ou terceiros.
                </p>
            </section>

            <div class="pt-4 border-t border-blue-400/20 flex flex-wrap gap-3">
                <a href="{{ url('/') }}" class="sm-cta !w-auto !px-6 !py-2.5 !text-xs">
                    Voltar ao Palco Inicial
                </a>
                <a href="{{ route('game.play', ['novo' => 1]) }}" class="px-5 py-2.5 rounded-full bg-blue-900/70 hover:bg-blue-800 border border-blue-400/40 text-xs font-bold text-white transition">
                    Jogar Agora
                </a>
            </div>
        </div>
    </main>
</body>
</html>
