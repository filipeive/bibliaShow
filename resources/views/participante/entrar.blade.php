<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Participar ao Vivo — Bíblia Show</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen">

    <div class="max-w-lg mx-auto px-4 py-6 sm:py-10">

        <!-- Cabeçalho -->
        <header class="flex items-center justify-between mb-6">
            <a href="/" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-slate-950 font-extrabold text-sm">B</div>
                <span class="font-bold text-lg text-white">Bíblia Show</span>
            </a>
            <span id="badgeSala" class="text-xs font-mono font-bold px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 {{ $sala ? '' : 'hidden' }}">
                Sala: <span id="codigoSalaTexto">{{ $sala?->codigo_acesso }}</span>
            </span>
        </header>

        <!-- ETAPA 1: Formulário de Entrada na Sala -->
        <div id="etapaEntrada" class="bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl p-6 sm:p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-extrabold text-white">Entrar na Sala</h1>
                <p class="text-slate-400 text-sm mt-1">
                    {{ $sala ? $sala->nome : 'Digite o código exibido no projetor para participar' }}
                </p>
            </div>

            <form id="entrarForm" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Código da Sala (6 letras/números)</label>
                    <input type="text" id="inputCodigo" name="codigo_acesso" required maxlength="6"
                           value="{{ $sala?->codigo_acesso ?? $codigo ?? '' }}"
                           placeholder="Ex.: AB12CD"
                           class="w-full px-4 py-3 rounded-xl border border-slate-600 bg-slate-900 text-amber-400 font-mono font-bold uppercase text-lg tracking-widest focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Seu Nome</label>
                    <input type="text" id="inputNome" name="nome" required
                           placeholder="Digite seu nome"
                           class="w-full px-4 py-3 rounded-xl border border-slate-600 bg-slate-900 text-white focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Equipe / Célula (Opcional)</label>
                    <input type="text" id="inputEquipe" name="equipe_nome"
                           placeholder="Nome da sua equipe ou célula"
                           class="w-full px-4 py-3 rounded-xl border border-slate-600 bg-slate-900 text-white focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <button type="submit" id="btnEntrar"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold py-4 rounded-xl transition shadow-lg text-base">
                    Entrar e Jogar →
                </button>
            </form>

            <div id="errorEntrada" class="hidden bg-rose-950/80 border border-rose-500/40 p-3.5 rounded-xl text-rose-200 text-sm mt-4"></div>
        </div>

        <!-- ETAPA 2: Tela de Jogo ao Vivo no Smartphone -->
        <div id="etapaJogo" class="hidden space-y-5">
            <!-- Barra do Jogador -->
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400">Jogador(a)</p>
                    <p class="font-extrabold text-white text-base" id="jogadorNomeDisplay">-</p>
                    <p class="text-xs text-amber-400 font-medium" id="jogadorEquipeDisplay"></p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400 uppercase font-semibold">Seus Pontos</p>
                    <p class="text-2xl font-extrabold text-amber-400"><span id="jogadorPontosDisplay">0</span> <span class="text-xs font-normal text-slate-400">pts</span></p>
                </div>
            </div>

            <!-- Feedback da Resposta na Rodada -->
            <div id="feedbackResposta" class="hidden rounded-2xl p-4 border"></div>

            <!-- Card da Pergunta Ao Vivo -->
            <div id="cardPerguntaViva" class="bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <span id="progressoPergunta" class="text-xs font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300">
                        Pergunta 1
                    </span>
                    <span id="nivelPergunta" class="text-xs text-slate-400 font-medium"></span>
                </div>

                <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-4 mb-4">
                    <p id="textoPerguntaViva" class="text-base sm:text-lg font-bold text-white leading-relaxed">
                        Carregando pergunta da sala...
                    </p>
                </div>

                <div id="opcoesBotoes" class="space-y-2.5"></div>
            </div>

            <!-- Ranking Compacto -->
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                        </svg>
                        Classificação da Sala
                    </h2>
                    <button type="button" onclick="sairDaSala()" class="text-xs text-slate-400 hover:text-rose-400">
                        Trocar de jogador
                    </button>
                </div>
                <div id="miniRanking" class="space-y-2 text-sm"></div>
            </div>
        </div>

    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const letras = ['A', 'B', 'C', 'D'];

        let estadoSessao = {
            salaId: {{ $sala?->id ?? 'null' }},
            codigoSala: '{{ $sala?->codigo_acesso ?? $codigo ?? '' }}',
            participanteId: null,
            participanteNome: '',
            equipeNome: '',
            pontuacao: 0,
            questaoAtualId: null,
            jaRespondeuAtual: false,
            inicioPerguntaTimestamp: Date.now(),
        };

        const salvo = sessionStorage.getItem('biblia_show_participante');
        if (salvo) {
            try {
                const parsed = JSON.parse(salvo);
                if (!estadoSessao.codigoSala || parsed.codigoSala === estadoSessao.codigoSala.toUpperCase()) {
                    estadoSessao = { ...estadoSessao, ...parsed };
                    iniciarModoJogo();
                }
            } catch (e) {}
        }

        document.getElementById('entrarForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = document.getElementById('btnEntrar');
            const errBox = document.getElementById('errorEntrada');
            errBox.classList.add('hidden');
            btn.disabled = true;
            btn.innerText = 'Conectando...';

            const codigo = document.getElementById('inputCodigo').value.trim().toUpperCase();
            const nome = document.getElementById('inputNome').value.trim();
            const equipe = document.getElementById('inputEquipe').value.trim();

            try {
                const response = await fetch('/api/salas/entrar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        codigo_acesso: codigo,
                        nome: nome,
                        equipe_nome: equipe || null,
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    const msg = data.error || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Erro ao entrar na sala.');
                    throw new Error(msg);
                }

                estadoSessao.salaId = data.sala.id;
                estadoSessao.codigoSala = data.sala.codigo_acesso;
                estadoSessao.participanteId = data.participante.id;
                estadoSessao.participanteNome = data.participante.nome;
                estadoSessao.equipeNome = data.participante.equipe_nome || '';
                estadoSessao.pontuacao = data.participante.pontuacao_total || 0;

                sessionStorage.setItem('biblia_show_participante', JSON.stringify(estadoSessao));
                iniciarModoJogo();
            } catch (err) {
                errBox.innerText = err.message;
                errBox.classList.remove('hidden');
                btn.disabled = false;
                btn.innerText = 'Entrar e Jogar →';
            }
        });

        function iniciarModoJogo() {
            document.getElementById('etapaEntrada').classList.add('hidden');
            document.getElementById('etapaJogo').classList.remove('hidden');
            document.getElementById('badgeSala').classList.remove('hidden');
            document.getElementById('codigoSalaTexto').innerText = estadoSessao.codigoSala;
            document.getElementById('jogadorNomeDisplay').innerText = estadoSessao.participanteNome;
            document.getElementById('jogadorEquipeDisplay').innerText = estadoSessao.equipeNome ? 'Equipe: ' + estadoSessao.equipeNome : '';
            document.getElementById('jogadorPontosDisplay').innerText = estadoSessao.pontuacao;

            sincronizarEstadoSala();
            setInterval(sincronizarEstadoSala, 2500);
        }

        async function sincronizarEstadoSala() {
            if (!estadoSessao.salaId) return;

            try {
                const url = `/api/salas/${estadoSessao.salaId}?participante_id=${estadoSessao.participanteId || ''}`;
                const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!resp.ok) return;
                const data = await resp.json();

                atualizarRanking(data.ranking || []);

                if (data.estado === 'terminado') {
                    document.getElementById('textoPerguntaViva').innerText = 'Partida encerrada pelo apresentador! Confira sua colocação abaixo.';
                    document.getElementById('opcoesBotoes').innerHTML = '';
                    return;
                }

                const q = data.pergunta_atual;
                if (!q) {
                    document.getElementById('textoPerguntaViva').innerText = 'Aguardando o apresentador iniciar a próxima pergunta...';
                    document.getElementById('opcoesBotoes').innerHTML = '';
                    return;
                }

                document.getElementById('progressoPergunta').innerText = `Pergunta ${data.numero_pergunta || 1} de ${data.total_perguntas || 12}`;
                document.getElementById('nivelPergunta').innerText = `Nível ${q.dificuldade_nivel || 1} (${q.dificuldade_nome || 'Iniciante'})`;

                if (estadoSessao.questaoAtualId !== q.id) {
                    estadoSessao.questaoAtualId = q.id;
                    estadoSessao.jaRespondeuAtual = !!data.ja_respondeu;
                    estadoSessao.inicioPerguntaTimestamp = Date.now();
                    document.getElementById('feedbackResposta').classList.add('hidden');
                    renderizarPergunta(q, estadoSessao.jaRespondeuAtual);
                } else if (data.ja_respondeu && !estadoSessao.jaRespondeuAtual) {
                    estadoSessao.jaRespondeuAtual = true;
                    renderizarPergunta(q, true);
                }
            } catch (e) {}
        }

        function renderizarPergunta(q, bloqueado) {
            document.getElementById('textoPerguntaViva').innerText = q.texto;
            const container = document.getElementById('opcoesBotoes');

            if (bloqueado) {
                container.innerHTML = `
                    <div class="p-4 rounded-xl bg-slate-900/70 border border-slate-700 text-center text-sm text-slate-300">
                        Resposta registrada! Aguarde o apresentador avançar para a próxima pergunta.
                    </div>
                `;
                return;
            }

            container.innerHTML = (q.opcoes || []).map((opcao, idx) => `
                <button type="button"
                        onclick="enviarResposta(${idx})"
                        class="w-full text-left p-4 rounded-xl border-2 border-slate-700 bg-slate-900 hover:border-amber-400 active:scale-[0.99] transition flex items-center gap-3.5">
                    <span class="w-8 h-8 rounded-lg bg-amber-500 text-slate-950 font-extrabold flex items-center justify-center shrink-0 text-sm">
                        ${letras[idx] || (idx + 1)}
                    </span>
                    <span class="font-semibold text-white text-sm sm:text-base">${opcao}</span>
                </button>
            `).join('');
        }

        async function enviarResposta(indiceOpcao) {
            if (estadoSessao.jaRespondeuAtual || !estadoSessao.questaoAtualId) return;
            estadoSessao.jaRespondeuAtual = true;

            new Audio('/audio/classico/tecla.wav').play().catch(() => {});

            const tempoSegundos = Math.max(1, Math.min(60, Math.round((Date.now() - estadoSessao.inicioPerguntaTimestamp) / 1000)));
            const container = document.getElementById('opcoesBotoes');
            container.innerHTML = `<div class="p-4 rounded-xl bg-slate-900 text-center text-sm text-amber-300">Enviando resposta...</div>`;

            try {
                const resp = await fetch(`/api/salas/${estadoSessao.salaId}/responder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        participante_id: estadoSessao.participanteId,
                        questao_id: estadoSessao.questaoAtualId,
                        resposta: String(indiceOpcao),
                        tempo_resposta: tempoSegundos,
                    }),
                });

                const data = await resp.json();
                if (!resp.ok) {
                    throw new Error(data.error || 'Não foi possível enviar a resposta.');
                }

                new Audio(data.acertou ? '/audio/classico/certa1.wav' : '/audio/classico/errou1.wav').play().catch(() => {});

                estadoSessao.pontuacao = data.participante?.pontuacao_total ?? estadoSessao.pontuacao;
                document.getElementById('jogadorPontosDisplay').innerText = estadoSessao.pontuacao;
                sessionStorage.setItem('biblia_show_participante', JSON.stringify(estadoSessao));

                const fb = document.getElementById('feedbackResposta');
                fb.className = 'rounded-2xl p-4 border ' + (data.acertou
                    ? 'bg-emerald-950/70 border-emerald-500/40 text-emerald-100'
                    : 'bg-rose-950/70 border-rose-500/40 text-rose-100');

                fb.innerHTML = `
                    <div class="font-extrabold text-base mb-1">
                        ${data.acertou ? `Certa Resposta! (+${data.pontos} pts)` : `Resposta Incorreta`}
                    </div>
                    ${!data.acertou ? `<div class="text-xs text-rose-200">Correta: <strong>${data.resposta_correta}</strong></div>` : ''}
                    ${data.referencia_biblica || data.explicacao ? `<div class="text-xs mt-1.5 text-slate-300">${data.referencia_biblica ? '[' + data.referencia_biblica + '] ' : ''}${data.explicacao || ''}</div>` : ''}
                `;
                fb.classList.remove('hidden');

                container.innerHTML = `
                    <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-700 text-center text-xs text-slate-400">
                        Aguardando o apresentador chamar a próxima pergunta...
                    </div>
                `;
                sincronizarEstadoSala();
            } catch (err) {
                estadoSessao.jaRespondeuAtual = false;
                alert(err.message);
            }
        }

        function atualizarRanking(ranking) {
            const box = document.getElementById('miniRanking');
            if (!box) return;

            const meuItem = ranking.find(r => Number(r.id) === Number(estadoSessao.participanteId));
            if (meuItem) {
                estadoSessao.pontuacao = meuItem.pontuacao;
                document.getElementById('jogadorPontosDisplay').innerText = meuItem.pontuacao;
            }

            if (ranking.length === 0) {
                box.innerHTML = '<p class="text-slate-500 text-xs">Nenhum participante listado ainda.</p>';
                return;
            }

            box.innerHTML = ranking.slice(0, 6).map((p, idx) => {
                const souEu = Number(p.id) === Number(estadoSessao.participanteId);
                return `
                    <div class="flex items-center justify-between p-2.5 rounded-xl ${souEu ? 'bg-amber-500/20 border border-amber-500/40' : 'bg-slate-900/70'}">
                        <span class="font-semibold text-white truncate">
                            <strong class="text-amber-400 mr-1.5">${idx + 1}º</strong> ${p.nome} ${souEu ? '(Você)' : ''}
                        </span>
                        <span class="font-mono font-bold text-amber-300 text-xs">${p.pontuacao} pts</span>
                    </div>
                `;
            }).join('');
        }

        function sairDaSala() {
            sessionStorage.removeItem('biblia_show_participante');
            location.reload();
        }
    </script>
</body>
</html>
