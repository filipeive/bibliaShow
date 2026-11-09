<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show - Projeção</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/pusher-js@8/dist/web/pusher.js"></script>
    <script src="https://unpkg.com/laravel-echo@1/dist/echo.iife.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .projection-bg { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    </style>
</head>
<body class="projection-bg text-white min-h-screen p-8">

    <div class="max-w-4xl mx-auto">
        <!-- Header da projeção (apenas informações públicas) -->
        <header class="mb-10">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse"></div>
                        <span class="text-sm font-semibold text-green-400 uppercase tracking-wider">AO VIVO</span>
                    </div>
                    <h1 class="text-4xl font-extrabold text-white mb-1">Bíblia Show</h1>
                    <p class="text-blue-300 text-sm font-mono font-bold" id="salaCodigo">{{ $sala->codigo_acesso }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pontuação</p>
                    <p class="text-5xl font-bold text-white" id="pontuacaoTotal">{{ $pontuacao }}</p>
                </div>
            </div>
        </header>

        @if($estado === 'aguardando')
            <div class="flex flex-col items-center justify-center h-[50vh] text-center">
                <div class="w-20 h-20 rounded-full border-4 border-blue-500 border-t-transparent animate-spin mb-6"></div>
                <h2 class="text-3xl font-bold text-white">Aguardando Início</h2>
                <p class="text-blue-300">O apresentador irá iniciar em breve...</p>
            </div>

        @elseif($estado === 'em_curso' || $estado === 'pausado')
            <main class="flex flex-col items-center justify-center min-h-[60vh]">
                @if($pergunta)
                    <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20 shadow-2xl max-w-2xl">
                        <div class="flex justify-between items-center mb-8">
                            <div class="text-right">
                                <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Pergunta</p>
                                <p class="text-2xl font-bold text-white">1</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tempo</p>
                                <p class="text-3xl font-mono font-bold text-white" id="tempoRestante">{{ $tempo_restante }}s</p>
                                @if($tempo_esgotado)
                                    <p class="text-xs font-bold text-red-400 bg-red-500/20 px-3 py-1 rounded mt-2">Tempo esgotado</p>
                                @endif
                            </div>
                        </div>

                        <p class="text-3xl font-light text-white/90 leading-relaxed mb-8 italic" id="textoPergunta">
                            "{{ $pergunta['texto'] ?? $pergunta->texto }}"
                        </p>

                        <div class="flex gap-4">
                            <div class="flex-1 bg-white/5 p-4 rounded-xl border border-white/10">
                                <p class="text-xs font-semibold text-blue-300 uppercase">Resposta</p>
                                <p class="text-xl font-medium text-white" id="ultimaResposta">Aguardando...</p>
                            </div>
                            <div class="flex-1 bg-white/5 p-4 rounded-xl border border-white/10">
                                <p class="text-xs font-semibold text-blue-300 uppercase">Pontos</p>
                                <p class="text-xl font-medium text-white" id="pontosUltima">0 pts</p>
                            </div>
                        </div>
                    </div>
                @endif
            </main>

        @elseif($estado === 'terminado')
            <div class="flex flex-col items-center justify-center h-[50vh] text-center">
                <div class="text-6xl mb-6">🏆</div>
                <h2 class="text-4xl font-bold text-white">Jogo Finalizado</h2>
                <p class="text-blue-300 text-xl mb-8">Pontuação final: <span class="text-white font-bold" id="pontuacaoFinal">{{ $pontuacao }}</span></p>
            </div>
        @endif

        <!-- Ranking (atualizado em tempo real) -->
        <div id="rankingContainer" @if((count($ranking ?? []) === 0))class="hidden"@endif class="mt-10 bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/20 animate-in fade-in duration-500">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                Ranking
            </h3>
            <div class="space-y-2" id="rankingList">
                @foreach(array_slice($ranking ?? [], 0, 5) as $index => $participante)
                    <div id="rank-{{ $index + 1 }}" class="flex items-center gap-4 p-3 rounded-lg {{ $index === 0 ? 'bg-blue-500/30' : 'bg-white/5' }} border border-transparent hover:border-white/10 transition">
                        <span class="text-2xl font-bold {{ $index === 0 ? 'text-yellow-400' : 'text-white' }}">{{ $index + 1 }}.</span>
                        <span class="flex-1 text-white/90">{{ $participante['nome'] ?? 'Participante' }}</span>
                        <span class="font-mono font-bold text-white">{{ $participante['pontuacao'] }} pts</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Estado vazio quando sem ranking -->
        <div id="semRanking" @if((count($ranking ?? []) > 0))class="hidden"@endif class="mt-10 flex flex-col items-center justify-center h-[40vh] text-center">
            <div class="w-16 h-16 rounded-full border-4 border-white/20 flex items-center justify-center mb-4 text-slate-400">👥</div>
            <p class="text-white/60">Aguardando participantes...</p>
        </div>
    </div>

    <script>
        // Conexão Reverb via Laravel Echo
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ env('REVERB_APP_KEY') }}',
            wsHost: '{{ env('REVERB_HOST') }}',
            wsPort: {{ env('REVERB_PORT') }},
            wssPort: {{ env('REVERB_PORT') }},
            forceTLS: {{ env('REVERB_SCHEME') === 'https' ? 'true' : 'false' }},
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });

        const channel = window.Echo.private('game.{{ $sala->id }}');

        // Atualiza o ranking na projeção
        function atualizarRanking(ranking) {
            const container = document.getElementById('rankingContainer');
            const empty = document.getElementById('semRanking');
            const list = document.getElementById('rankingList');

            if (!ranking || ranking.length === 0) {
                if (container) container.classList.add('hidden');
                if (empty) empty.classList.remove('hidden');
                return;
            }

            if (container) container.classList.remove('hidden');
            if (empty) empty.classList.add('hidden');
            list.innerHTML = '';

            ranking.forEach((p, index) => {
                const el = document.createElement('div');
                el.className = 'flex items-center gap-4 p-3 rounded-lg ' + (index === 0 ? 'bg-blue-500/30 border border-blue-500/50' : 'bg-white/5 border border-transparent');
                el.innerHTML = `
                    <span class="text-2xl font-bold ${index === 0 ? 'text-yellow-400' : 'text-white'}">${index + 1}.</span>
                    <span class="flex-1 text-white/90 truncate">${p.nome || 'Participante'}</span>
                    <span class="font-mono font-bold text-white">${p.pontuacao} pts</span>
                `;
                list.appendChild(el);
            });
        }

        channel.listen('.pergunta-iniciada', (e) => {
            const texto = document.getElementById('textoPergunta');
            const tempo = document.getElementById('tempoRestante');
            const resposta = document.getElementById('ultimaResposta');
            const pontos = document.getElementById('pontosUltima');

            if (texto && e.questao.texto) texto.innerText = '"' + e.questao.texto + '"';
            if (tempo) tempo.innerText = e.tempoLimite + 's';
            if (resposta) resposta.innerText = 'Aguardando...';
            if (pontos) pontos.innerText = '0 pts';
        });

        channel.listen('.resposta-enviada', (e) => {
            const resposta = document.getElementById('ultimaResposta');
            const pontos = document.getElementById('pontosUltima');
            if (resposta) {
                resposta.innerText = e.nomeParticipante + ' — ' + (e.correta ? 'Acertou!' : 'Errou');
                resposta.className = 'text-xl font-medium text-white ' + (e.correta ? 'text-green-400' : 'text-red-400');
            }
            if (pontos) pontos.innerText = e.pontos + ' pts';
        });

        channel.listen('.ranking-atualizado', (e) => {
            atualizarRanking(e.ranking);
        });

        channel.listen('.jogo-estado-alterado', (e) => {
            const tempo = document.getElementById('tempoRestante');
            if (e.estado === 'terminado') {
                location.reload();
            } else if (e.tempoRestante && tempo) {
                tempo.innerText = e.tempoRestante + 's';
            }
        });

        // Inicializa ranking do estado carregado
        atualizarRanking(@json($ranking ?? []));
    </script>
</body>
</html>