<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Participar ao Vivo — Bíblia Show</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
        .question-banner { background: linear-gradient(180deg, #991b1b 0%, #7f1d1d 100%); border: 2px solid #fbbf24; }
        .opcao-bloqueada {
            background: linear-gradient(90deg, rgba(217, 119, 6, 0.4) 0%, rgba(120, 53, 15, 0.55) 100%) !important;
            border-color: #fbbf24 !important;
        }
        .opcao-correta-reveal {
            background: linear-gradient(90deg, rgba(5, 150, 105, 0.92) 0%, rgba(6, 95, 70, 0.95) 100%) !important;
            border-color: #34d399 !important;
        }
        .opcao-errada-reveal {
            background: linear-gradient(90deg, rgba(225, 29, 72, 0.92) 0%, rgba(136, 19, 55, 0.95) 100%) !important;
            border-color: #fb7185 !important;
        }
    </style>
</head>
<body class="milhao-bg text-slate-100 min-h-screen">

    @include('partials.navbar', ['dark' => true])

    <div class="max-w-lg mx-auto px-4 py-6 sm:py-8">

        <!-- Badge da Sala Ativa -->
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-black uppercase tracking-wider text-amber-400">Arena Multiplayer</span>
            <span id="badgeSala" class="text-xs font-mono font-black px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 {{ $sala ? '' : 'hidden' }}">
                SALA: <span id="codigoSalaTexto">{{ $sala?->codigo_acesso }}</span>
            </span>
        </div>

        <!-- ETAPA 1: Formulário de Entrada na Sala -->
        <div id="etapaEntrada" class="bg-slate-900/95 border border-slate-700/80 rounded-3xl shadow-2xl p-6 sm:p-8">
            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center mx-auto mb-3 text-amber-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                </div>
                <h1 class="text-2xl font-black text-white">Entrar na Sala Ao Vivo</h1>
                <p class="text-slate-400 text-sm mt-1">
                    {{ $sala ? $sala->nome : 'Digite o código exibido no projetor da igreja para jogar' }}
                </p>
            </div>

            @if($sala && ($sala->premio_primeiro_lugar || $sala->premio_segundo_lugar))
                <div class="mb-5 p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 space-y-1">
                    <p class="text-[11px] font-black uppercase tracking-wider text-amber-400">Prêmios desta Sala:</p>
                    @if($sala->premio_primeiro_lugar)
                        <p class="text-xs text-white font-bold"><strong class="text-amber-300">1º Lugar:</strong> {{ $sala->premio_primeiro_lugar }}</p>
                    @endif
                    @if($sala->premio_segundo_lugar)
                        <p class="text-xs text-slate-300 font-semibold"><strong class="text-slate-200">2º Lugar:</strong> {{ $sala->premio_segundo_lugar }}</p>
                    @endif
                </div>
            @endif

            <form id="entrarForm" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Código da Sala (6 caracteres)</label>
                    <input type="text" id="inputCodigo" name="codigo_acesso" required maxlength="6"
                           value="{{ $sala?->codigo_acesso ?? $codigo ?? '' }}"
                           placeholder="Ex.: AB12CD"
                           class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-amber-400 font-mono font-black uppercase text-lg tracking-widest focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Seu Nome</label>
                    <input type="text" id="inputNome" name="nome" required
                           placeholder="Digite seu nome"
                           class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Equipe / Célula (Opcional)</label>
                    <input type="text" id="inputEquipe" name="equipe_nome"
                           placeholder="Nome da sua equipe ou célula"
                           class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none">
                </div>

                <button type="submit" id="btnEntrar"
                        class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-black py-4 rounded-xl transition shadow-lg text-base">
                    Entrar e Jogar →
                </button>
            </form>

            <div id="errorEntrada" class="hidden bg-rose-950/80 border border-rose-500/40 p-3.5 rounded-xl text-rose-200 text-sm mt-4"></div>
        </div>

        <!-- ETAPA 2: Tela de Jogo ao Vivo no Smartphone -->
        <div id="etapaJogo" class="hidden space-y-4">
            <!-- Barra do Jogador -->
            <div class="bg-slate-900/95 border border-slate-800 rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <p class="text-[11px] text-slate-400 uppercase font-bold">Participante</p>
                    <p class="font-black text-white text-base" id="jogadorNomeDisplay">-</p>
                    <p class="text-xs text-amber-400 font-semibold" id="jogadorEquipeDisplay"></p>
                </div>
                <div class="text-right">
                    <p class="text-[11px] text-slate-400 uppercase font-bold">Seus Pontos</p>
                    <p class="text-2xl font-black text-amber-400"><span id="jogadorPontosDisplay">0</span> <span class="text-xs font-normal text-slate-400">pts</span></p>
                </div>
            </div>

            <!-- Prêmios da Sala em disputa -->
            <div id="boxPremiosSala" class="hidden bg-amber-500/10 border border-amber-500/30 rounded-2xl p-3.5 flex flex-wrap items-center justify-between gap-2 text-xs">
                <span class="font-black uppercase tracking-wider text-amber-400">Prêmios em Disputa:</span>
                <div class="flex flex-wrap gap-3">
                    <span id="premio1Display" class="font-bold text-white"></span>
                    <span id="premio2Display" class="font-semibold text-slate-300"></span>
                </div>
            </div>

            <!-- Feedback da Resposta na Rodada -->
            <div id="feedbackResposta" class="hidden rounded-2xl p-4 border"></div>

            <!-- Card da Pergunta Ao Vivo -->
            <div id="cardPerguntaViva" class="bg-slate-900/95 border border-slate-800 rounded-3xl p-5 shadow-2xl">
                <div class="flex items-center justify-between mb-3">
                    <span id="progressoPergunta" class="text-xs font-black uppercase tracking-wider px-3 py-1 rounded-full bg-amber-500 text-slate-950">
                        1ª Fase • Pergunta 1
                    </span>
                    <span id="nivelPergunta" class="text-xs text-amber-300 font-bold">Vale 10 pts</span>
                </div>

                <!-- Dica Bíblica / Convidado Bíblico revelados pelo Apresentador -->
                <div id="dicaBiblicaViva" class="hidden mb-3 p-3 rounded-xl bg-amber-500/20 border border-amber-400/40 text-xs font-bold text-amber-200"></div>
                <div id="dicaConvidadoViva" class="hidden mb-3 p-3 rounded-xl bg-blue-950/90 border border-blue-400/40 text-xs font-bold text-blue-100"></div>

                <div class="question-banner rounded-2xl p-4 mb-4 shadow-md">
                    <p id="textoPerguntaViva" class="text-base sm:text-lg font-black text-white leading-relaxed">
                        Carregando pergunta da sala...
                    </p>
                </div>

                <div id="opcoesBotoes" class="space-y-2.5"></div>
            </div>

            <!-- Ranking Compacto -->
            <div class="bg-slate-900/95 border border-slate-800 rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-black text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                        </svg>
                        Classificação da Sala
                    </h2>
                    <button type="button" onclick="sairDaSala()" class="text-xs text-slate-400 hover:text-rose-400 font-semibold">
                        Trocar de jogador
                    </button>
                </div>
                <div id="miniRanking" class="space-y-2 text-sm"></div>
            </div>
        </div>

    </div>

    <!-- Modal "Você tem certeza?" estilo Show do Milhão -->
    <div id="modalCerteza" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl">
            <p class="text-xs font-black uppercase tracking-widest text-amber-400 mb-1">Confirmação Bíblia Show</p>
            <h3 class="text-2xl font-black text-white mb-2">Você tem certeza?</h3>
            <p id="textoOpcaoConfirmar" class="text-sm font-bold text-amber-200 bg-slate-950 p-3 rounded-xl border border-slate-800 mb-5"></p>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="fecharModalCerteza()"
                        class="py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-sm">
                    Trocar
                </button>
                <button type="button" id="btnConfirmarFinal"
                        class="py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm shadow-lg">
                    Sim, Confirmar!
                </button>
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
            reveladoNaPerguntaAtual: false,
            inicioPerguntaTimestamp: Date.now(),
            opcoesEliminadas: [],
        };

        let indicePendenteConfirmacao = null;

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
            setInterval(sincronizarEstadoSala, 1200);
        }

        async function sincronizarEstadoSala() {
            if (!estadoSessao.salaId) return;

            try {
                const url = `/api/salas/${estadoSessao.salaId}?participante_id=${estadoSessao.participanteId || ''}`;
                const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!resp.ok) return;
                const data = await resp.json();

                if (data.sala && (data.sala.premio_primeiro_lugar || data.sala.premio_segundo_lugar)) {
                    document.getElementById('boxPremiosSala').classList.remove('hidden');
                    document.getElementById('premio1Display').innerHTML = data.sala.premio_primeiro_lugar ? `1º: <strong class="text-amber-300">${data.sala.premio_primeiro_lugar}</strong>` : '';
                    document.getElementById('premio2Display').innerHTML = data.sala.premio_segundo_lugar ? `2º: <strong>${data.sala.premio_segundo_lugar}</strong>` : '';
                }

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

                const fase = data.fase_atual || 1;
                const placar = data.placar_milhao || {};
                document.getElementById('progressoPergunta').innerText = `${fase}ª Fase • Pergunta ${data.numero_pergunta || 1}/${data.total_perguntas || 24}`;
                document.getElementById('nivelPergunta').innerText = `Vale ${placar.valor_pergunta || 10} pts`;

                const ajudas = data.ajudas || {};
                const dicaEl = document.getElementById('dicaBiblicaViva');
                if (ajudas.dica_biblica) {
                    dicaEl.textContent = ajudas.dica_biblica;
                    dicaEl.classList.remove('hidden');
                } else {
                    dicaEl.classList.add('hidden');
                }

                const convEl = document.getElementById('dicaConvidadoViva');
                if (ajudas.dica_convidado) {
                    convEl.innerHTML = `<strong class="text-amber-300">${ajudas.dica_convidado.convidado}:</strong> ${ajudas.dica_convidado.mensagem}`;
                    convEl.classList.remove('hidden');
                } else {
                    convEl.classList.add('hidden');
                }

                const eliminadasStr = JSON.stringify(ajudas.opcoes_eliminadas || []);
                const mudouEliminadas = eliminadasStr !== JSON.stringify(estadoSessao.opcoesEliminadas);
                estadoSessao.opcoesEliminadas = ajudas.opcoes_eliminadas || [];

                if (estadoSessao.questaoAtualId !== q.id) {
                    estadoSessao.questaoAtualId = q.id;
                    estadoSessao.jaRespondeuAtual = !!data.ja_respondeu;
                    estadoSessao.reveladoNaPerguntaAtual = false;
                    estadoSessao.inicioPerguntaTimestamp = Date.now();
                    document.getElementById('feedbackResposta').classList.add('hidden');
                    fecharModalCerteza();
                    renderizarPergunta(q, estadoSessao.jaRespondeuAtual);
                } else if (mudouEliminadas && !estadoSessao.jaRespondeuAtual) {
                    renderizarPergunta(q, false);
                }
            } catch (e) {}
        }

        function renderizarPergunta(q, bloqueado) {
            document.getElementById('textoPerguntaViva').innerText = q.texto;
            const container = document.getElementById('opcoesBotoes');

            if (bloqueado && !estadoSessao.reveladoNaPerguntaAtual) {
                container.innerHTML = `
                    <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800 text-center text-sm text-slate-300">
                        Resposta registrada! Aguarde o apresentador chamar a próxima pergunta.
                    </div>
                `;
                return;
            }

            const eliminadas = estadoSessao.opcoesEliminadas || [];
            container.innerHTML = (q.opcoes || []).map((opcao, idx) => {
                const isEliminada = eliminadas.includes(idx);
                return `
                    <button type="button"
                            id="btnOpcaoMobile_${idx}"
                            data-indice="${idx}"
                            ${isEliminada ? 'disabled' : ''}
                            onclick="confirmarOpcao(${idx}, '${String(opcao).replace(/'/g, "\\'")}')"
                            class="btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 ${isEliminada ? 'opacity-30 border-slate-800 bg-slate-950 line-through' : 'border-blue-500/40 bg-blue-950/70 hover:border-amber-400 active:scale-[0.99]'}">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <span id="badgeMobile_${idx}" class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 font-black flex items-center justify-center shrink-0 text-sm">
                                ${letras[idx] || (idx + 1)}
                            </span>
                            <span class="font-bold text-white text-sm sm:text-base">${opcao}</span>
                        </div>
                        <span id="tagMobile_${idx}" class="hidden shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded"></span>
                    </button>
                `;
            }).join('');
        }

        function confirmarOpcao(idx, textoOpcao) {
            if (estadoSessao.jaRespondeuAtual) return;
            new Audio('/audio/classico/tecla.wav').play().catch(() => {});
            indicePendenteConfirmacao = idx;
            document.getElementById('textoOpcaoConfirmar').innerText = `${letras[idx] || (idx + 1)}) ${textoOpcao}`;
            document.getElementById('modalCerteza').classList.remove('hidden');
            document.getElementById('btnConfirmarFinal').onclick = () => {
                const escolhido = indicePendenteConfirmacao;
                fecharModalCerteza();
                enviarResposta(escolhido);
            };
        }

        function fecharModalCerteza() {
            document.getElementById('modalCerteza').classList.add('hidden');
            indicePendenteConfirmacao = null;
        }

        function bloquearOpcaoMobile(indiceEscolhido) {
            document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                btn.disabled = true;
                const idx = String(btn.dataset.indice);
                const tag = document.getElementById(`tagMobile_${idx}`);
                if (idx === String(indiceEscolhido)) {
                    btn.classList.add('opcao-bloqueada');
                    if (tag) {
                        tag.textContent = 'BLOQUEADA';
                        tag.className = 'shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-amber-400 text-slate-950';
                    }
                } else {
                    btn.classList.add('opacity-50');
                }
            });
        }

        function revelarCoresOpcaoMobile(indiceEscolhido, indiceCorreto, acertou) {
            document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                const idx = String(btn.dataset.indice);
                btn.classList.remove('opcao-bloqueada', 'opacity-50');
                const tag = document.getElementById(`tagMobile_${idx}`);
                const badge = document.getElementById(`badgeMobile_${idx}`);

                if (idx === String(indiceCorreto)) {
                    btn.classList.add('opcao-correta-reveal');
                    if (badge) badge.className = 'w-8 h-8 rounded-xl bg-white text-emerald-700 font-black flex items-center justify-center shrink-0 text-sm';
                    if (tag) {
                        tag.textContent = acertou ? 'CERTA!' : 'CORRETA';
                        tag.className = 'shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-white text-emerald-800';
                    }
                } else if (idx === String(indiceEscolhido) && !acertou) {
                    btn.classList.add('opcao-errada-reveal');
                    if (badge) badge.className = 'w-8 h-8 rounded-xl bg-white text-rose-700 font-black flex items-center justify-center shrink-0 text-sm';
                    if (tag) {
                        tag.textContent = 'ERROU';
                        tag.className = 'shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-white text-rose-800';
                    }
                } else {
                    btn.classList.add('opacity-35');
                }
            });
        }

        async function enviarResposta(indiceOpcao) {
            if (estadoSessao.jaRespondeuAtual || !estadoSessao.questaoAtualId) return;
            estadoSessao.jaRespondeuAtual = true;
            estadoSessao.reveladoNaPerguntaAtual = true;

            const tempoSegundos = Math.max(1, Math.min(60, Math.round((Date.now() - estadoSessao.inicioPerguntaTimestamp) / 1000)));

            // 1. Bloquear imediatamente a opção escolhida
            bloquearOpcaoMobile(indiceOpcao);

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

                // 2. Após pausa dramática (1.2s), revelar verde/vermelho nas opções e tocar som se ativo nas Definições!
                setTimeout(() => {
                    const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
                    if (ativo) {
                        const fx = new Audio(data.acertou ? '/audio/classico/certa1.wav' : '/audio/classico/errou1.wav');
                        fx.volume = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85;
                        fx.play().catch(() => {});
                    }

                    revelarCoresOpcaoMobile(indiceOpcao, data.indice_correto, !!data.acertou);

                    estadoSessao.pontuacao = data.participante?.pontuacao_total ?? estadoSessao.pontuacao;
                    document.getElementById('jogadorPontosDisplay').innerText = estadoSessao.pontuacao;
                    sessionStorage.setItem('biblia_show_participante', JSON.stringify(estadoSessao));

                    const fb = document.getElementById('feedbackResposta');
                    fb.className = 'rounded-2xl p-4 border ' + (data.acertou
                        ? 'bg-emerald-950/80 border-emerald-500/40 text-emerald-100'
                        : 'bg-rose-950/80 border-rose-500/40 text-rose-100');

                    fb.innerHTML = `
                        <div class="font-black text-base mb-1">
                            ${data.acertou ? `Certa Resposta! (+${data.pontos} pts)` : `Que pena, você errou!`}
                        </div>
                        ${!data.acertou ? `<div class="text-xs text-rose-200">Correta: <strong class="text-emerald-300 underline">${data.resposta_correta}</strong></div>` : ''}
                        ${data.referencia_biblica || data.explicacao ? `<div class="text-xs mt-1.5 text-slate-300">${data.referencia_biblica ? '[' + data.referencia_biblica + '] ' : ''}${data.explicacao || ''}</div>` : ''}
                        <div class="text-[11px] mt-2 text-amber-300 font-semibold">Aguardando o apresentador avançar para a próxima pergunta...</div>
                    `;
                    fb.classList.remove('hidden');
                }, 1200);
            } catch (err) {
                estadoSessao.jaRespondeuAtual = false;
                estadoSessao.reveladoNaPerguntaAtual = false;
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
                    <div class="flex items-center justify-between p-2.5 rounded-xl ${souEu ? 'bg-amber-500/20 border border-amber-500/40' : 'bg-slate-950/70'}">
                        <span class="font-bold text-white truncate">
                            <strong class="text-amber-400 mr-1.5">${idx + 1}º</strong> ${p.nome} ${souEu ? '(Você)' : ''}
                        </span>
                        <span class="font-mono font-black text-amber-300 text-xs">${p.pontuacao} pts</span>
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
