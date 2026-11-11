<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Participar ao Vivo — Bíblia Show</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <div class="max-w-2xl sm:max-w-3xl lg:max-w-4xl mx-auto px-4 py-6 sm:py-8">

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
                    <div id="containerSelecaoEquipe" class="space-y-2 mb-2">
                        <select id="selectEquipe" name="equipe_id"
                                onchange="aoMudarSelectEquipe(this)"
                                class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-white text-sm font-semibold focus:ring-2 focus:ring-amber-500 outline-none">
                            <option value="">Nenhuma equipe (Jogar individualmente na sala)</option>
                            @if($sala && $sala->equipes->isNotEmpty())
                                <optgroup label="Equipes Criadas na Sala">
                                    @foreach($sala->equipes as $eq)
                                        <option value="{{ $eq->id }}">{{ $eq->nome }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            <option value="__nova__">+ Criar ou Digitar Outra Equipe...</option>
                        </select>
                        <div id="campoNovaEquipe" class="hidden">
                            <input type="text" id="inputEquipe" name="equipe_nome"
                                   placeholder="Digite o nome da nova equipe ou célula"
                                   class="w-full px-4 py-2.5 rounded-xl border border-amber-500/50 bg-slate-950 text-amber-300 text-sm font-bold focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                    </div>
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
            <!-- Barra do Jogador Profissional com Avatar e Equipe -->
            <div class="bg-slate-900/95 border border-slate-800 rounded-3xl p-4 shadow-xl flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-xl flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0" id="jogadorAvatarDisplay">
                        P
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Participante no Palco</p>
                        <h3 class="font-black text-white text-base truncate" id="jogadorNomeDisplay">-</h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-amber-400 font-bold truncate" id="jogadorEquipeDisplay">Individual</span>
                            <button type="button" onclick="abrirModalTrocarEquipe()" class="text-[10px] font-black uppercase px-2 py-0.5 rounded-lg bg-blue-600/30 hover:bg-blue-600 text-blue-300 hover:text-white border border-blue-500/40 transition shrink-0">
                                Equipes ▾
                            </button>
                        </div>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-[10px] text-slate-400 uppercase font-black">Seus Pontos</p>
                    <p class="text-2xl font-black text-amber-400 font-mono"><span id="jogadorPontosDisplay">0</span> <span class="text-xs font-normal text-slate-400">pts</span></p>
                </div>
            </div>

            <!-- Placar Clássico de Valores da Pergunta (Errar, Parar, Acertar) -->
            <div class="grid grid-cols-3 gap-2">
                <div class="p-2.5 rounded-2xl bg-rose-950/40 border border-rose-500/30 text-center">
                    <p class="text-[9px] font-black uppercase text-rose-300">Se Errar</p>
                    <p class="text-sm font-black text-rose-400 font-mono" id="placarErrarDisplay">0 <span class="text-[9px]">pts</span></p>
                </div>
                <div class="p-2.5 rounded-2xl bg-slate-900 border border-slate-700 text-center">
                    <p class="text-[9px] font-black uppercase text-amber-300">Parar (Atual)</p>
                    <p class="text-sm font-black text-amber-400 font-mono" id="placarPararDisplay">0 <span class="text-[9px]">pts</span></p>
                </div>
                <div class="p-2.5 rounded-2xl bg-emerald-950/50 border border-emerald-500/40 text-center">
                    <p class="text-[9px] font-black uppercase text-emerald-300">Se Acertar</p>
                    <p class="text-sm font-black text-emerald-400 font-mono" id="placarAcertarDisplay">10 <span class="text-[9px]">pts</span></p>
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

            <!-- Card da Pergunta Ao Vivo (Estilo TV Show do Milhão Bíblico) -->
            <div id="cardPerguntaViva" class="bg-slate-900/95 border-2 border-amber-500/40 rounded-3xl p-5 sm:p-6 shadow-2xl">
                <!-- Topo Bíblico + Fase + Cronômetro -->
                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <span id="progressoPergunta" class="text-xs font-black uppercase tracking-wider px-3 py-1 rounded-full bg-amber-500 text-slate-950">
                            1ª Fase • Pergunta 1
                        </span>
                        <span id="nivelPergunta" class="text-xs text-amber-300 font-extrabold px-2.5 py-0.5 rounded-full bg-amber-500/15 border border-amber-500/30">
                            Vale 10 pts
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-mono font-black text-amber-400 bg-slate-950 px-2.5 py-1 rounded-xl border border-slate-800 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span id="tempoRestanteDisplay">60</span>s
                        </span>
                    </div>
                </div>

                <!-- Crachá das Escrituras Sagradas (Livro / Categoria) -->
                <div id="badgeBiblicoCard" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs font-black uppercase tracking-wider mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span id="textoBiblicoCard">Sagradas Escrituras</span>
                </div>

                <!-- Dica Bíblica / Convidado Bíblico revelados pelo Apresentador -->
                <div id="dicaBiblicaViva" class="hidden mb-3 p-3 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-xs font-bold text-amber-200"></div>
                <div id="dicaConvidadoViva" class="hidden mb-3 p-3 rounded-2xl bg-blue-950/90 border border-blue-400/40 text-xs font-bold text-blue-100"></div>

                <!-- Banner Clássico Vermelho Escuro com Borda Dourada -->
                <div class="question-banner rounded-2xl p-4 sm:p-5 mb-4 shadow-xl">
                    <p id="textoPerguntaViva" class="text-base sm:text-lg font-black text-white leading-relaxed">
                        Carregando pergunta da sala...
                    </p>
                </div>

                <div id="opcoesBotoes" class="space-y-2.5"></div>
            </div>

            <!-- Ranking da Sala com Alternância Jogadores e Equipes -->
            <div class="bg-slate-900/95 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3 border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <button type="button" id="tabJogadores" onclick="alternarTabRanking('jogadores')"
                                class="text-xs font-black px-3 py-1.5 rounded-xl bg-amber-500 text-slate-950 transition">
                            Jogadores
                        </button>
                        <button type="button" id="tabEquipes" onclick="alternarTabRanking('equipes')"
                                class="text-xs font-black px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition">
                            Equipes / Células
                        </button>
                    </div>
                    <button type="button" onclick="sairDaSala()" class="text-xs text-slate-400 hover:text-rose-400 font-bold transition">
                        Sair da Sala
                    </button>
                </div>
                <div id="miniRankingJogadores" class="space-y-2 text-sm"></div>
                <div id="miniRankingEquipes" class="hidden space-y-2 text-sm"></div>
            </div>
        </div>

    </div>

    <!-- Modal para Juntar-se ou Trocar de Equipe na Sala -->
    <div id="modalTrocarEquipe" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border-2 border-amber-400 rounded-3xl max-w-md w-full p-6 text-slate-100 shadow-2xl relative">
            <button type="button" onclick="fecharModalTrocarEquipe()" class="absolute top-4 right-4 text-slate-400 hover:text-white font-black text-lg">✕</button>
            <div class="flex items-center gap-2.5 mb-2">
                <span class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </span>
                <h3 class="text-lg font-black text-white">Equipes / Células da Sala</h3>
            </div>
            <p class="text-xs text-slate-400 mb-4">Escolha uma equipe criada pelo apresentador para pontuar em conjunto:</p>

            <div id="listaEquipesModal" class="space-y-2 max-h-56 overflow-y-auto pr-1 mb-4">
                <p class="text-xs text-slate-500">Carregando equipes...</p>
            </div>

            <div class="border-t border-slate-800 pt-3 space-y-2">
                <p class="text-[11px] font-bold text-slate-400 uppercase">Ou crie uma nova equipe:</p>
                <div class="flex gap-2">
                    <input type="text" id="inputNovaEquipeModal" placeholder="Ex.: Tribo de Judá"
                           class="flex-1 px-3 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white outline-none focus:border-amber-400 font-bold">
                    <button type="button" onclick="criarEquipeModal()"
                            class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs uppercase shadow transition">
                        Criar & Entrar
                    </button>
                </div>
                <button type="button" onclick="sairEquipeModal()"
                        class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition">
                    Jogar Individualmente (Sem Equipe)
                </button>
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
            opcaoEscolhida: null,
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

        function aoMudarSelectEquipe(sel) {
            const campo = document.getElementById('campoNovaEquipe');
            if (!campo) return;
            if (sel.value === '__nova__') {
                campo.classList.remove('hidden');
                document.getElementById('inputEquipe')?.focus();
            } else {
                campo.classList.add('hidden');
            }
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
            const selEq = document.getElementById('selectEquipe');
            let equipeId = null;
            let equipeNome = null;

            if (selEq && selEq.value === '__nova__') {
                equipeNome = document.getElementById('inputEquipe')?.value.trim() || null;
            } else if (selEq && selEq.value) {
                equipeId = selEq.value;
            }

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
                        equipe_id: equipeId,
                        equipe_nome: equipeNome,
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
            const avatarEl = document.getElementById('jogadorAvatarDisplay');
            if (avatarEl && estadoSessao.participanteNome) {
                avatarEl.innerText = estadoSessao.participanteNome.trim().charAt(0).toUpperCase();
            }
            document.getElementById('jogadorEquipeDisplay').innerText = estadoSessao.equipeNome ? 'Equipe: ' + estadoSessao.equipeNome : 'Individual';
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

                if (data.tempo_restante !== undefined) {
                    const timerEl = document.getElementById('tempoRestanteDisplay');
                    if (timerEl) timerEl.innerText = data.tempo_restante;
                }

                const placar = data.placar_milhao || {};
                const errarEl = document.getElementById('placarErrarDisplay');
                const pararEl = document.getElementById('placarPararDisplay');
                const acertarEl = document.getElementById('placarAcertarDisplay');
                if (errarEl && placar.errar !== undefined) errarEl.innerHTML = `${placar.errar} <span class="text-[9px]">pts</span>`;
                if (pararEl && placar.parar !== undefined) pararEl.innerHTML = `${placar.parar} <span class="text-[9px]">pts</span>`;
                if (acertarEl && placar.acertar !== undefined) acertarEl.innerHTML = `${placar.acertar} <span class="text-[9px]">pts</span>`;

                estadoSessao.equipes = data.equipes || [];
                atualizarRanking(data.ranking || [], data.equipes || []);

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

                // Atualiza se o servidor já registrou a resposta desta rodada
                if (data.respostas_rodada && estadoSessao.participanteId && data.respostas_rodada[estadoSessao.participanteId]) {
                    const minhaResp = data.respostas_rodada[estadoSessao.participanteId];
                    estadoSessao.jaRespondeuAtual = true;
                    if (estadoSessao.opcaoEscolhida === null && typeof minhaResp.indice !== 'undefined' && minhaResp.indice !== null) {
                        estadoSessao.opcaoEscolhida = minhaResp.indice;
                    }
                }

                if (estadoSessao.questaoAtualId !== q.id) {
                    estadoSessao.questaoAtualId = q.id;
                    estadoSessao.jaRespondeuAtual = !!data.ja_respondeu;
                    estadoSessao.opcaoEscolhida = null;
                    estadoSessao.reveladoNaPerguntaAtual = false;
                    estadoSessao.inicioPerguntaTimestamp = Date.now();
                    document.getElementById('feedbackResposta').classList.add('hidden');
                    fecharModalCerteza();
                    renderizarPergunta(q);
                } else {
                    const gabaritoRevelado = !!(data.ajudas && data.ajudas.gabarito_revelado);
                    if (gabaritoRevelado && !estadoSessao.reveladoNaPerguntaAtual) {
                        estadoSessao.reveladoNaPerguntaAtual = true;
                        revelarCoresOpcaoMobile(estadoSessao.opcaoEscolhida, data.pergunta_atual?.resposta_correta, null, q);
                    } else if (mudouEliminadas && !estadoSessao.jaRespondeuAtual) {
                        renderizarPergunta(q);
                    }
                }
            } catch (e) {}
        }

        function renderizarPergunta(q) {
            document.getElementById('textoPerguntaViva').innerText = q.texto;

            const badgeBiblico = document.getElementById('badgeBiblicoCard');
            const textoBiblico = document.getElementById('textoBiblicoCard');
            if (badgeBiblico && textoBiblico) {
                if (q.livro_nome || q.categoria_nome) {
                    textoBiblico.textContent = (q.livro_nome ? `LIVRO: ${q.livro_nome}` : 'SAGRADAS ESCRITURAS') + (q.categoria_nome ? ` • ${q.categoria_nome}` : '');
                    badgeBiblico.classList.remove('hidden');
                } else {
                    badgeBiblico.classList.add('hidden');
                }
            }

            const container = document.getElementById('opcoesBotoes');

            const eliminadas = estadoSessao.opcoesEliminadas || [];
            container.innerHTML = (q.opcoes || []).map((opcao, idx) => {
                const isEliminada = eliminadas.includes(idx);
                const isEscolhida = estadoSessao.opcaoEscolhida !== null && Number(estadoSessao.opcaoEscolhida) === idx;
                const estaBloqueada = estadoSessao.jaRespondeuAtual || (estadoSessao.opcaoEscolhida !== null);

                let classesBtn = 'btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 ';
                let tagHtml = '';

                if (isEliminada) {
                    classesBtn += 'opacity-30 border-slate-800 bg-slate-950 line-through cursor-not-allowed';
                } else if (isEscolhida) {
                    classesBtn += 'opcao-bloqueada cursor-not-allowed';
                    tagHtml = '<span class="shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-amber-400 text-slate-950">BLOQUEADA</span>';
                } else if (estaBloqueada) {
                    classesBtn += 'opacity-40 border-slate-800 bg-slate-950 cursor-not-allowed';
                } else {
                    classesBtn += 'border-blue-500/40 bg-blue-950/70 hover:border-amber-400 active:scale-[0.99] cursor-pointer';
                }

                return `
                    <button type="button"
                            id="btnOpcaoMobile_${idx}"
                            data-indice="${idx}"
                            ${isEliminada || estaBloqueada ? 'disabled' : ''}
                            onclick="confirmarOpcao(${idx}, '${String(opcao).replace(/'/g, "\\'")}')"
                            class="${classesBtn}">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <span id="badgeMobile_${idx}" class="w-8 h-8 rounded-xl ${isEscolhida ? 'bg-amber-400 text-slate-950' : 'bg-amber-500 text-slate-950'} font-black flex items-center justify-center shrink-0 text-sm">
                                ${letras[idx] || (idx + 1)}
                            </span>
                            <span class="font-bold text-white text-sm sm:text-base">${opcao}</span>
                        </div>
                        <span id="tagMobile_${idx}" class="shrink-0">${tagHtml}</span>
                    </button>
                `;
            }).join('');
        }

        async function confirmarOpcao(idx, textoOpcao) {
            if (estadoSessao.jaRespondeuAtual) return;
            new Audio('/audio/classico/tecla.wav').play().catch(() => {});
            const letra = letras[idx] || (idx + 1);

            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: 'Você tem certeza?',
                    html: `
                        <p class="text-xs text-slate-300 uppercase tracking-wider font-bold mb-2">Alternativa Escolhida:</p>
                        <div class="p-3.5 rounded-2xl bg-slate-950 border-2 border-amber-400 text-amber-300 font-black text-base text-left flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-amber-400 text-slate-950 font-black flex items-center justify-center shrink-0">${letra}</span>
                            <span>${textoOpcao}</span>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'SIM, CONFIRMAR!',
                    cancelButtonText: 'Trocar Opção',
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#334155',
                    background: '#0f172a',
                    color: '#f8fafc',
                    customClass: {
                        popup: 'rounded-3xl border-2 border-amber-400/80 shadow-2xl',
                        confirmButton: 'font-black rounded-xl px-5 py-3 text-slate-950 text-sm shadow-lg',
                        cancelButton: 'font-bold rounded-xl px-4 py-3 text-sm'
                    }
                });

                if (result.isConfirmed) {
                    enviarResposta(idx);
                }
            } else {
                indicePendenteConfirmacao = idx;
                document.getElementById('textoOpcaoConfirmar').innerText = `${letra}) ${textoOpcao}`;
                document.getElementById('modalCerteza').classList.remove('hidden');
                document.getElementById('btnConfirmarFinal').onclick = () => {
                    const escolhido = indicePendenteConfirmacao;
                    fecharModalCerteza();
                    enviarResposta(escolhido);
                };
            }
        }

        function fecharModalCerteza() {
            const m = document.getElementById('modalCerteza');
            if (m) m.classList.add('hidden');
            indicePendenteConfirmacao = null;
        }

        function bloquearOpcaoMobile(indiceEscolhido) {
            document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                btn.disabled = true;
                const idx = String(btn.dataset.indice);
                const tag = document.getElementById(`tagMobile_${idx}`);
                if (idx === String(indiceEscolhido)) {
                    btn.className = 'btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 opcao-bloqueada cursor-not-allowed';
                    if (tag) {
                        tag.innerHTML = '<span class="shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-amber-400 text-slate-950">BLOQUEADA</span>';
                    }
                } else {
                    btn.classList.add('opacity-40');
                }
            });
        }

        function revelarCoresOpcaoMobile(indiceEscolhido, respostaCorreta, acertouParam, questaoObj) {
            const corretaTexto = String(respostaCorreta || (questaoObj?.resposta_correta ?? '')).trim().toLowerCase();

            document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                const idx = String(btn.dataset.indice);
                btn.classList.remove('opcao-bloqueada', 'opacity-40', 'opacity-50');
                const tag = document.getElementById(`tagMobile_${idx}`);
                const badge = document.getElementById(`badgeMobile_${idx}`);
                const textoBtn = String(btn.querySelector('.font-bold')?.textContent || '').trim().toLowerCase();

                const ehCorreta = corretaTexto && textoBtn === corretaTexto;
                const ehEscolhida = indiceEscolhido !== null && idx === String(indiceEscolhido);
                const acertou = acertouParam !== null ? acertouParam : (ehEscolhida && ehCorreta);

                if (ehCorreta) {
                    btn.className = 'btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 opcao-correta-reveal';
                    if (badge) badge.className = 'w-8 h-8 rounded-xl bg-white text-emerald-700 font-black flex items-center justify-center shrink-0 text-sm';
                    if (tag) {
                        tag.innerHTML = '<span class="shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-white text-emerald-800">' + (acertou ? 'CERTA!' : 'CORRETA') + '</span>';
                    }
                } else if (ehEscolhida && !acertou) {
                    btn.className = 'btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 opcao-errada-reveal';
                    if (badge) badge.className = 'w-8 h-8 rounded-xl bg-white text-rose-700 font-black flex items-center justify-center shrink-0 text-sm';
                    if (tag) {
                        tag.innerHTML = '<span class="shrink-0 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-white text-rose-800">ERROU</span>';
                    }
                } else {
                    btn.classList.add('opacity-30');
                }
            });
        }

        async function enviarResposta(indiceOpcao) {
            if (estadoSessao.jaRespondeuAtual) return;
            estadoSessao.jaRespondeuAtual = true;
            estadoSessao.opcaoEscolhida = indiceOpcao;

            const tempoSegundos = Math.max(1, Math.min(60, Math.round((Date.now() - estadoSessao.inicioPerguntaTimestamp) / 1000)));

            // 1. Bloquear imediatamente a opção escolhida na tela (permanece na tela)
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
                        questao_id: estadoSessao.questaoAtualId || null,
                        resposta: String(indiceOpcao),
                        tempo_resposta: tempoSegundos,
                    }),
                });

                const data = await resp.json();
                if (!resp.ok) {
                    const msgErro = data.error || (data.errors ? Object.values(data.errors).flat()[0] : null) || 'Não foi possível enviar a resposta.';
                    throw new Error(msgErro);
                }

                // 2. Após pausa dramática (1.2s), revelar verde/vermelho nas opções e tocar som se ativo nas Definições!
                setTimeout(() => {
                    const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
                    if (ativo) {
                        const fx = new Audio(data.acertou ? '/audio/classico/certa1.wav' : '/audio/classico/errou1.wav');
                        fx.volume = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85;
                        fx.play().catch(() => {});
                    }

                    revelarCoresOpcaoMobile(indiceOpcao, data.resposta_correta, !!data.acertou);

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
                        ${!data.acertou && data.resposta_correta ? `<div class="text-xs text-rose-200">Correta: <strong class="text-emerald-300 underline">${data.resposta_correta}</strong></div>` : ''}
                        ${data.referencia_biblica || data.explicacao ? `<div class="text-xs mt-1.5 text-slate-300">${data.referencia_biblica ? '[' + data.referencia_biblica + '] ' : ''}${data.explicacao || ''}</div>` : ''}
                        <div class="text-[11px] mt-2 text-amber-300 font-semibold">Sua resposta prevalece na tela até o apresentador chamar a próxima pergunta.</div>
                    `;
                    fb.classList.remove('hidden');
                }, 1200);
            } catch (err) {
                // Em caso de erro, reabilita as opções e notifica via SweetAlert2
                estadoSessao.jaRespondeuAtual = false;
                estadoSessao.opcaoEscolhida = null;
                document.querySelectorAll('.btn-opcao-mobile').forEach(btn => {
                    const idx = Number(btn.dataset.indice);
                    const isEliminada = (estadoSessao.opcoesEliminadas || []).includes(idx);
                    if (!isEliminada) {
                        btn.disabled = false;
                        btn.className = 'btn-opcao-mobile w-full text-left p-4 rounded-2xl border-2 transition flex items-center justify-between gap-3.5 border-blue-500/40 bg-blue-950/70 hover:border-amber-400 active:scale-[0.99] cursor-pointer';
                        const tag = document.getElementById(`tagMobile_${idx}`);
                        if (tag) tag.innerHTML = '';
                    }
                });

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Falha no Envio',
                        text: err.message,
                        confirmButtonText: 'Tentar Novamente',
                        confirmButtonColor: '#f59e0b',
                        background: '#0f172a',
                        color: '#f8fafc',
                        customClass: {
                            popup: 'rounded-3xl border-2 border-rose-500 shadow-2xl',
                            confirmButton: 'font-black rounded-xl px-5 py-3 text-slate-950 text-sm'
                        }
                    });
                } else {
                    alert(err.message);
                }
            }
        }

        function alternarTabRanking(tab) {
            const tabJ = document.getElementById('tabJogadores');
            const tabE = document.getElementById('tabEquipes');
            const boxJ = document.getElementById('miniRankingJogadores');
            const boxE = document.getElementById('miniRankingEquipes');
            if (tab === 'jogadores') {
                if (tabJ) tabJ.className = 'text-xs font-black px-3 py-1.5 rounded-xl bg-amber-500 text-slate-950 transition';
                if (tabE) tabE.className = 'text-xs font-black px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition';
                if (boxJ) boxJ.classList.remove('hidden');
                if (boxE) boxE.classList.add('hidden');
            } else {
                if (tabE) tabE.className = 'text-xs font-black px-3 py-1.5 rounded-xl bg-amber-500 text-slate-950 transition';
                if (tabJ) tabJ.className = 'text-xs font-black px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition';
                if (boxE) boxE.classList.remove('hidden');
                if (boxJ) boxJ.classList.add('hidden');
            }
        }

        function atualizarRanking(ranking, equipes = []) {
            const boxJ = document.getElementById('miniRankingJogadores');
            const boxE = document.getElementById('miniRankingEquipes');

            const meuItem = ranking.find(r => Number(r.id) === Number(estadoSessao.participanteId));
            if (meuItem) {
                estadoSessao.pontuacao = meuItem.pontuacao;
                document.getElementById('jogadorPontosDisplay').innerText = meuItem.pontuacao;
                if (meuItem.equipe) {
                    estadoSessao.equipeNome = meuItem.equipe;
                    document.getElementById('jogadorEquipeDisplay').textContent = meuItem.equipe;
                }
            }

            if (boxJ) {
                if (ranking.length === 0) {
                    boxJ.innerHTML = '<p class="text-slate-500 text-xs">Aguardando participantes entrarem...</p>';
                } else {
                    boxJ.innerHTML = ranking.slice(0, 8).map((p, idx) => {
                        const souEu = Number(p.id) === Number(estadoSessao.participanteId);
                        return `
                            <div class="flex items-center justify-between p-2.5 rounded-xl ${souEu ? 'bg-amber-500/20 border border-amber-500/40 text-amber-200' : 'bg-slate-950/70 text-slate-200'}">
                                <span class="font-bold truncate">
                                    <strong class="text-amber-400 mr-1.5">${idx + 1}º</strong> ${p.nome} ${souEu ? '(Você)' : ''}
                                    ${p.equipe ? `<span class="text-[10px] text-slate-400 block">${p.equipe}</span>` : ''}
                                </span>
                                <span class="font-mono font-black text-amber-300 text-xs">${p.pontuacao} pts</span>
                            </div>
                        `;
                    }).join('');
                }
            }

            if (boxE) {
                if (!equipes || equipes.length === 0) {
                    boxE.innerHTML = '<p class="text-slate-500 text-xs">Nenhuma equipe pontuando nesta sala ainda.</p>';
                } else {
                    const equipesOrd = [...equipes].sort((a, b) => (b.pontuacao_total || 0) - (a.pontuacao_total || 0));
                    boxE.innerHTML = equipesOrd.map((eq, idx) => `
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/70 border border-slate-800">
                            <span class="font-bold text-white truncate">
                                <strong class="text-emerald-400 mr-1.5">${idx + 1}º</strong> ${eq.nome}
                            </span>
                            <span class="font-mono font-black text-emerald-300 text-xs">${eq.pontuacao_total || 0} pts</span>
                        </div>
                    `).join('');
                }
            }
        }

        function abrirModalTrocarEquipe() {
            const m = document.getElementById('modalTrocarEquipe');
            const list = document.getElementById('listaEquipesModal');
            if (!m || !list) return;

            const equipes = estadoSessao.equipes || [];
            if (equipes.length === 0) {
                list.innerHTML = '<p class="text-xs text-slate-400 p-3 bg-slate-950 rounded-xl">Nenhuma equipe criada na sala ainda. Digite um nome abaixo para fundar a primeira equipe!</p>';
            } else {
                list.innerHTML = equipes.map(eq => `
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-amber-400 transition">
                        <div>
                            <p class="font-black text-sm text-white">${eq.nome}</p>
                            <p class="text-[10px] text-amber-400 font-bold">${eq.pontuacao_total || 0} pts acumulados</p>
                        </div>
                        <button type="button" onclick="submeterTrocaEquipe(${eq.id}, null)"
                                class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-black text-xs transition">
                            Juntar-se →
                        </button>
                    </div>
                `).join('');
            }

            m.classList.remove('hidden');
        }

        function fecharModalTrocarEquipe() {
            const m = document.getElementById('modalTrocarEquipe');
            if (m) m.classList.add('hidden');
        }

        async function submeterTrocaEquipe(equipeId, equipeNome) {
            if (!estadoSessao.salaId || !estadoSessao.participanteId) return;

            try {
                const resp = await fetch(`/api/salas/${estadoSessao.salaId}/participantes/${estadoSessao.participanteId}/equipe`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        equipe_id: equipeId,
                        equipe_nome: equipeNome,
                    }),
                });

                const data = await resp.json();
                if (!resp.ok) throw new Error(data.error || 'Erro ao alterar equipe.');

                estadoSessao.equipeNome = data.equipe_nome || '';
                document.getElementById('jogadorEquipeDisplay').textContent = data.equipe_nome ? data.equipe_nome : 'Individual';
                sessionStorage.setItem('biblia_show_participante', JSON.stringify(estadoSessao));
                fecharModalTrocarEquipe();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Equipe Atualizada!',
                        text: data.mensagem,
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#0f172a',
                        color: '#f8fafc',
                    });
                }
            } catch (e) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Erro', text: e.message, background: '#0f172a', color: '#f8fafc' });
                } else {
                    alert(e.message);
                }
            }
        }

        function criarEquipeModal() {
            const inp = document.getElementById('inputNovaEquipeModal');
            const nome = inp ? inp.value.trim() : '';
            if (!nome) return;
            submeterTrocaEquipe(null, nome);
            if (inp) inp.value = '';
        }

        function sairEquipeModal() {
            submeterTrocaEquipe(null, null);
        }

        function sairDaSala() {
            sessionStorage.removeItem('biblia_show_participante');
            location.reload();
        }
    </script>
</body>
</html>
