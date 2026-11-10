<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Projeção ({{ $sala->codigo_acesso }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .projection-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
    </style>
</head>
<body class="projection-bg text-white min-h-screen p-6 sm:p-10">

    <div class="max-w-6xl mx-auto">
        <!-- Header da Projeção -->
        <header class="mb-8 flex flex-wrap justify-between items-center gap-4 border-b border-white/10 pb-6">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs font-extrabold text-emerald-400 uppercase tracking-widest">AO VIVO • PROJEÇÃO</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white">{{ $sala->nome }}</h1>
            </div>

            <div class="flex items-center gap-5">
                <button type="button" onclick="alternarAudioProjecao()" id="btnAudioProj"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/20 text-amber-300 text-xs font-bold uppercase tracking-wider transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                    </svg>
                    <span id="txtAudioProj">Ativar Trilha Sonora</span>
                </button>
                <div class="bg-amber-500/15 border border-amber-400/30 px-5 py-2.5 rounded-2xl text-center">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-amber-300">Código para Entrar</p>
                    <p class="text-2xl sm:text-3xl font-mono font-extrabold text-amber-400" id="salaCodigo">{{ $sala->codigo_acesso }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pergunta</p>
                    <p class="text-3xl sm:text-4xl font-extrabold text-white">
                        <span id="numPergunta">{{ $numero_pergunta ?? 1 }}</span><span class="text-slate-500 text-xl">/{{ $total_perguntas ?? 12 }}</span>
                    </p>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <!-- Área Principal da Pergunta (2 colunas) -->
            <div class="lg:col-span-2" id="painelPergunta">
                @if($estado === 'terminado')
                    <div class="bg-white/10 backdrop-blur-md rounded-3xl p-10 border border-white/20 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-amber-500/20 border-2 border-amber-400/50 flex items-center justify-center mx-auto mb-4 text-amber-400">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                            </svg>
                        </div>
                        <h2 class="text-4xl font-extrabold text-white mb-2">Jogo Finalizado!</h2>
                        <p class="text-amber-300 text-xl">Confira a classificação final ao lado</p>
                    </div>
                @elseif($pergunta)
                    <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20 shadow-2xl">
                        <div class="flex justify-between items-center mb-6">
                            <span class="px-3.5 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-extrabold uppercase tracking-wider" id="nivelBadge">
                                Nível {{ $pergunta['dificuldade_nivel'] ?? 1 }} • {{ $pergunta['dificuldade_nome'] ?? 'Iniciante' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-2xl font-mono font-extrabold text-amber-400" id="tempoRestante">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $tempo_restante }}s
                            </span>
                        </div>

                        <p class="text-2xl sm:text-3xl font-bold text-white leading-relaxed mb-8" id="textoPergunta">
                            {{ $pergunta['texto'] ?? $pergunta->texto }}
                        </p>

                        @php
                            $opcoes = $pergunta['opcoes'] ?? [];
                            $opcoesArray = is_string($opcoes) ? json_decode($opcoes, true) : $opcoes;
                            $letras = ['A', 'B', 'C', 'D'];
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="opcoesContainer">
                            @foreach($opcoesArray as $idx => $opcao)
                                <div class="bg-slate-900/80 border-2 border-white/15 rounded-2xl p-4 flex items-center gap-4">
                                    <span class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 font-extrabold text-lg flex items-center justify-center shrink-0">
                                        {{ $letras[$idx] ?? ($idx + 1) }}
                                    </span>
                                    <span class="text-lg sm:text-xl font-semibold text-white">{{ $opcao }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white/10 backdrop-blur-md rounded-3xl p-10 border border-white/20 text-center">
                        <h2 class="text-3xl font-bold text-white">Aguardando Início</h2>
                        <p class="text-slate-300 mt-2">O apresentador iniciará a partida em instantes...</p>
                    </div>
                @endif
            </div>

            <!-- Coluna Lateral: Ranking em Tempo Real -->
            <div class="lg:col-span-1 bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/20 shadow-xl">
                <h3 class="text-lg font-extrabold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                    </svg>
                    Ranking ao Vivo
                </h3>

                <div class="space-y-2.5" id="rankingList">
                    @forelse(array_slice($ranking ?? [], 0, 8) as $index => $participante)
                        <div class="flex items-center gap-3 p-3.5 rounded-xl {{ $index === 0 ? 'bg-amber-500/25 border border-amber-400/50' : 'bg-white/5 border border-white/10' }}">
                            <span class="text-xl font-extrabold w-7 {{ $index === 0 ? 'text-amber-400' : 'text-slate-300' }}">
                                {{ $index + 1 }}º
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-white truncate">{{ $participante['nome'] ?? 'Participante' }}</p>
                                @if(!empty($participante['equipe']))
                                    <p class="text-xs text-slate-300 truncate">{{ $participante['equipe'] }}</p>
                                @endif
                            </div>
                            <span class="font-mono font-extrabold text-amber-300">{{ $participante['pontuacao'] }} pts</span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-sm">
                            Entre pelo smartphone em <strong>/entrar/{{ $sala->codigo_acesso }}</strong>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        const letras = ['A', 'B', 'C', 'D'];
        let questaoAtualId = {{ $pergunta['id'] ?? 'null' }};
        let estadoAtual = '{{ $estado }}';
        let somAtivo = false;
        const trilhaSuspense = new Audio('/audio/classico/suspense.wav');
        trilhaSuspense.loop = true;
        trilhaSuspense.volume = 0.35;

        function alternarAudioProjecao() {
            somAtivo = !somAtivo;
            const txt = document.getElementById('txtAudioProj');
            if (somAtivo) {
                if (txt) txt.textContent = 'Silenciar Trilha';
                if (estadoAtual === 'em_curso') {
                    trilhaSuspense.play().catch(() => {});
                }
            } else {
                if (txt) txt.textContent = 'Ativar Trilha Sonora';
                trilhaSuspense.pause();
            }
        }

        function renderizarRanking(ranking) {
            const list = document.getElementById('rankingList');
            if (!list) return;

            if (!ranking || ranking.length === 0) {
                list.innerHTML = '<div class="text-center py-8 text-slate-400 text-sm">Entre pelo smartphone em <strong>/entrar/{{ $sala->codigo_acesso }}</strong></div>';
                return;
            }

            list.innerHTML = ranking.slice(0, 8).map((p, index) => `
                <div class="flex items-center gap-3 p-3.5 rounded-xl ${index === 0 ? 'bg-amber-500/25 border border-amber-400/50' : 'bg-white/5 border border-white/10'}">
                    <span class="text-xl font-extrabold w-7 ${index === 0 ? 'text-amber-400' : 'text-slate-300'}">${index + 1}º</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-white truncate">${p.nome || 'Participante'}</p>
                        ${p.equipe ? `<p class="text-xs text-slate-300 truncate">${p.equipe}</p>` : ''}
                    </div>
                    <span class="font-mono font-extrabold text-amber-300">${p.pontuacao} pts</span>
                </div>
            `).join('');
        }

        function atualizarPergunta(data) {
            const numEl = document.getElementById('numPergunta');
            if (numEl && data.numero_pergunta) {
                numEl.textContent = data.numero_pergunta;
            }

            if (data.estado === 'terminado' && estadoAtual !== 'terminado') {
                location.reload();
                return;
            }

            estadoAtual = data.estado;
            const q = data.pergunta_atual;
            if (!q) return;

            if (questaoAtualId !== q.id) {
                questaoAtualId = q.id;
                if (somAtivo) {
                    new Audio('/audio/classico/fase1.wav').play().catch(() => {});
                }
            }

            const textoEl = document.getElementById('textoPergunta');
            if (textoEl) textoEl.textContent = q.texto;

            const nivelEl = document.getElementById('nivelBadge');
            if (nivelEl) {
                nivelEl.textContent = `Nível ${q.dificuldade_nivel || 1} • ${q.dificuldade_nome || 'Iniciante'}`;
            }

            const opcoesEl = document.getElementById('opcoesContainer');
            if (opcoesEl && Array.isArray(q.opcoes)) {
                opcoesEl.innerHTML = q.opcoes.map((op, i) => `
                    <div class="bg-slate-900/80 border-2 border-white/15 rounded-2xl p-4 flex items-center gap-4">
                        <span class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 font-extrabold text-lg flex items-center justify-center shrink-0">
                            ${letras[i] || (i + 1)}
                        </span>
                        <span class="text-lg sm:text-xl font-semibold text-white">${op}</span>
                    </div>
                `).join('');
            }
        }

        async function sincronizarProjecao() {
            try {
                const resp = await fetch('/api/salas/{{ $sala->id }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!resp.ok) return;
                const data = await resp.json();

                renderizarRanking(data.ranking || []);
                atualizarPergunta(data);
            } catch (e) {}
        }

        setInterval(sincronizarProjecao, 2500);
    </script>
</body>
</html>
