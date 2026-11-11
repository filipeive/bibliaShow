<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Telão ({{ $sala->codigo_acesso }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="sm-stage text-white min-h-screen p-5 sm:p-8">

    @php
        $faseAtual = $fase_atual ?? 1;
        $placar = $placar_milhao ?? ['errar' => 0, 'parar' => 0, 'acertar' => 10, 'valor_pergunta' => 10, 'fase' => 1];
        $ajudasMotor = $ajudas_motor ?? [];
        $opcoesEliminadas = $ajudasMotor['opcoes_eliminadas'] ?? [];
        $gabaritoRevelado = !empty($ajudasMotor['gabarito_revelado']);
        $dicaBiblica = $ajudasMotor['dica_biblica'] ?? null;
        $dicaConvidado = $ajudasMotor['dica_convidado'] ?? null;
    @endphp

    <div class="max-w-7xl mx-auto">
        <!-- Header da Projeção -->
        <header class="mb-6 flex flex-wrap justify-between items-center gap-4 border-b border-white/10 pb-5">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs font-black text-emerald-400 uppercase tracking-widest">AO VIVO • BÍBLIA SHOW ARENA</span>
                    <span id="faseBadgeProj" class="px-3 py-0.5 rounded-full bg-amber-500 text-slate-950 text-xs font-black uppercase">
                        {{ $faseAtual }}ª FASE • VALE {{ $placar['valor_pergunta'] ?? 10 }} PTS
                    </span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-white">{{ $sala->nome }}</h1>
                @if($sala->premio_primeiro_lugar || $sala->premio_segundo_lugar)
                    <div class="flex flex-wrap items-center gap-3 mt-1.5 text-xs">
                        @if($sala->premio_primeiro_lugar)
                            <span class="px-2.5 py-0.5 rounded-lg bg-amber-500/20 border border-amber-400/40 text-amber-300 font-bold">
                                1º Lugar: {{ $sala->premio_primeiro_lugar }}
                            </span>
                        @endif
                        @if($sala->premio_segundo_lugar)
                            <span class="px-2.5 py-0.5 rounded-lg bg-white/10 border border-white/20 text-slate-200 font-bold">
                                2º Lugar: {{ $sala->premio_segundo_lugar }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Áudio do Telão Discreto & Ativo por Padrão -->
                <button type="button" onclick="alternarAudioProjecao()" id="btnAudioProj" title="Áudio do Telão (Ativo por Padrão)"
                        class="p-2.5 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/20 text-amber-300 transition shrink-0">
                    <svg id="iconeAudioProj" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                    </svg>
                </button>
                <div class="bg-amber-500/15 border border-amber-400/40 px-5 py-2 rounded-2xl text-center">
                    <p class="text-[10px] font-black uppercase tracking-wider text-amber-300">Código da Sala</p>
                    <p class="text-2xl sm:text-3xl font-mono font-black text-amber-400" id="salaCodigo">{{ $sala->codigo_acesso }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pergunta</p>
                    <p class="text-3xl sm:text-4xl font-black text-white">
                        <span id="numPergunta">{{ $numero_pergunta ?? 1 }}</span><span class="text-slate-500 text-xl">/{{ $total_perguntas ?? 24 }}</span>
                    </p>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-7 items-start">
            <!-- Área Principal da Pergunta (2 colunas) -->
            <div class="lg:col-span-2 space-y-5" id="painelPergunta">
                @if($estado === 'terminado')
                    <div class="bg-slate-900/90 backdrop-blur-md rounded-3xl p-10 border-2 border-amber-400/50 text-center shadow-2xl">
                        <div class="w-16 h-16 rounded-2xl bg-amber-500/20 border-2 border-amber-400/50 flex items-center justify-center mx-auto mb-4 text-amber-400">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                            </svg>
                        </div>
                        <h2 class="text-4xl font-black text-white mb-2">Partida Finalizada!</h2>
                        <p class="text-amber-300 text-xl font-bold">Confira a classificação oficial ao lado</p>
                    </div>
                @elseif($pergunta)
                    <div class="bg-slate-900/90 backdrop-blur-md rounded-3xl p-7 border border-white/15 shadow-2xl">
                        <div class="flex justify-between items-center mb-5">
                            <span class="px-4 py-1.5 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-black uppercase tracking-wider" id="nivelBadge">
                                {{ $faseAtual }}ª Fase • {{ $pergunta['dificuldade_nome'] ?? 'Bíblia Show' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-2xl sm:text-3xl font-mono font-black text-amber-400" id="tempoRestante">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span id="timerSegundos">{{ $tempo_restante }}</span>s
                            </span>
                        </div>

                        <!-- Caixa Vermelha/Dourada Clássica da Pergunta -->
                        <div class="question-banner rounded-3xl p-6 sm:p-8 mb-6 shadow-xl">
                            <p class="text-2xl sm:text-3xl font-black text-white leading-snug" id="textoPergunta">
                                {{ $pergunta['texto'] ?? $pergunta->texto }}
                            </p>
                        </div>

                        <!-- Banner de Dica Bíblica e Convidado Bíblico no Telão -->
                        <div id="boxDicaBiblicaProj" class="{{ $dicaBiblica ? '' : 'hidden' }} mb-4 p-4 rounded-2xl bg-amber-500/20 border border-amber-400/50 text-amber-200 font-bold text-base">
                            {{ $dicaBiblica }}
                        </div>

                        <div id="boxConvidadoProj" class="{{ $dicaConvidado ? '' : 'hidden' }} mb-4 p-4 rounded-2xl bg-blue-950/90 border border-blue-400/50 text-blue-100 font-bold text-base">
                            @if($dicaConvidado)
                                <strong class="text-amber-300">{{ $dicaConvidado['convidado'] }}:</strong> {{ $dicaConvidado['mensagem'] }}
                            @endif
                        </div>

                        @php
                            $opcoes = $pergunta['opcoes'] ?? [];
                            $opcoesArray = is_string($opcoes) ? json_decode($opcoes, true) : $opcoes;
                            $letras = ['A', 'B', 'C', 'D'];
                            $corretaTexto = $gabaritoRevelado ? trim((string) ($pergunta['resposta_correta'] ?? '')) : '';
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="opcoesContainer">
                            @foreach($opcoesArray as $idx => $opcao)
                                @php
                                    $eliminada = in_array($idx, $opcoesEliminadas);
                                    $ehCorretaRevelada = $gabaritoRevelado && mb_strtolower(trim((string) $opcao), 'UTF-8') === mb_strtolower($corretaTexto, 'UTF-8');
                                @endphp
                                <div class="rounded-2xl p-4 flex items-center gap-4 border-2 transition {{ $eliminada ? 'opacity-25 bg-slate-950 border-slate-800 line-through' : ($ehCorretaRevelada ? 'bg-emerald-900/80 border-emerald-400 shadow-lg shadow-emerald-500/20' : 'bg-blue-950/80 border-blue-400/40') }}">
                                    <span class="w-11 h-11 rounded-xl {{ $ehCorretaRevelada ? 'bg-emerald-400 text-slate-950' : 'bg-amber-500 text-slate-950' }} font-black text-xl flex items-center justify-center shrink-0">
                                        {{ $letras[$idx] ?? ($idx + 1) }}
                                    </span>
                                    <span class="text-lg sm:text-xl font-bold text-white">{{ $opcao }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Coluna Lateral: Ranking em Tempo Real + QR Code -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-slate-900/90 backdrop-blur-md rounded-3xl p-6 border border-white/15 shadow-xl">
                    <h3 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                        </svg>
                        Classificação Ao Vivo
                    </h3>

                    <div class="space-y-2.5" id="rankingList">
                        @forelse(array_slice($ranking ?? [], 0, 8) as $index => $participante)
                            <div class="flex items-center gap-3 p-3.5 rounded-2xl {{ $index === 0 ? 'bg-amber-500/25 border border-amber-400/50' : 'bg-white/5 border border-white/10' }}">
                                <span class="text-xl font-black w-7 {{ $index === 0 ? 'text-amber-400' : 'text-slate-300' }}">
                                    {{ $index + 1 }}º
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-white truncate">{{ $participante['nome'] ?? 'Participante' }}</p>
                                    @if(!empty($participante['equipe']))
                                        <p class="text-xs text-slate-300 truncate">{{ $participante['equipe'] }}</p>
                                    @endif
                                </div>
                                <span class="font-mono font-black text-amber-300">{{ $participante['pontuacao'] }} pts</span>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-sm">
                                Escaneie o QR Code abaixo ou acesse <strong>/entrar/{{ $sala->codigo_acesso }}</strong>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- QR Code no Telão -->
                <div class="bg-slate-900/90 backdrop-blur-md rounded-3xl p-5 border border-white/15 text-center flex items-center justify-center gap-4">
                    <div id="qrcodeProj" class="p-2 bg-white rounded-xl shrink-0"></div>
                    <div class="text-left">
                        <p class="text-xs font-black uppercase tracking-wider text-amber-300">Participe pelo Celular</p>
                        <p class="text-2xl font-mono font-black text-white mt-0.5">{{ $sala->codigo_acesso }}</p>
                        <p class="text-xs text-slate-400 mt-1">/entrar/{{ $sala->codigo_acesso }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- SEÇÃO INFERIOR DO TELÃO: CORRIDA AO VIVO & RESPOSTAS     -->
        <!-- ======================================================== -->
        <div class="mt-7 grid grid-cols-1 lg:grid-cols-12 gap-7 items-start">
            <!-- 1. Corrida do Bíblia Show (Disputa de Posições estilo Race Track) -->
            <div class="lg:col-span-6 bg-slate-900/90 backdrop-blur-md rounded-3xl p-6 border border-white/15 shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-white uppercase tracking-wider">Corrida do Bíblia Show</h3>
                            <p class="text-[11px] text-slate-400">Disputa em tempo real rumo ao topo</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold text-amber-400 px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/30">
                        🏁 Rumo ao Milhão
                    </span>
                </div>

                <div id="corridaContainer" class="space-y-3">
                    <p class="text-xs text-slate-400 text-center py-4">Carregando posições da corrida...</p>
                </div>
            </div>

            <!-- 2. Participantes na Rodada & Letra Escolhida (Ao Bloquear / Revelar) -->
            <div class="lg:col-span-6 bg-slate-900/90 backdrop-blur-md rounded-3xl p-6 border border-white/15 shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-500/20 border border-blue-500/40 text-blue-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-white uppercase tracking-wider">Respostas dos Jogadores na Rodada</h3>
                            <p class="text-[11px] text-slate-400">Letras bloqueadas ao vivo pelos participantes</p>
                        </div>
                    </div>
                    <span id="contadorRespostasRodada" class="text-xs font-bold text-amber-300">
                        0 responderam
                    </span>
                </div>

                <div id="gridParticipantesRodada" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-72 overflow-y-auto pr-1">
                    <p class="text-xs text-slate-400 col-span-2 text-center py-4">Aguardando participantes...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const letras = ['A', 'B', 'C', 'D'];
        let questaoAtualId = {{ $pergunta['id'] ?? 'null' }};
        let estadoAtual = '{{ $estado }}';
        let gabaritoEstavaRevelado = {{ $gabaritoRevelado ? 'true' : 'false' }};
        let dicaEstavaAtiva = {{ $dicaBiblica ? 'true' : 'false' }};
        let convidadoEstavaAtivo = {{ $dicaConvidado ? 'true' : 'false' }};
        if (localStorage.getItem('biblia_show_som') === null) {
            localStorage.setItem('biblia_show_som', 'on');
        }
        let somAtivo = localStorage.getItem('biblia_show_som') !== 'off';

        const trilhaSuspense = new Audio('/audio/classico/suspense.wav');
        trilhaSuspense.loop = true;
        trilhaSuspense.volume = 0.3;

        // Desbloqueia áudio caso o navegador bloqueie autoplay inicial
        function desbloquearAudioTelao() {
            if (somAtivo && estadoAtual === 'em_curso' && trilhaSuspense.paused) {
                trilhaSuspense.play().catch(() => {});
            }
        }
        ['click', 'touchstart', 'keydown'].forEach(evt => {
            document.addEventListener(evt, desbloquearAudioTelao, { once: true, passive: true });
        });

        let ultimoSegundoTocado = null;
        function tocarTickTelao(segundos) {
            if (segundos > 10 || segundos < 0 || !somAtivo || estadoAtual !== 'em_curso') return;
            if (ultimoSegundoTocado === segundos) return;
            ultimoSegundoTocado = segundos;

            try {
                if (segundos > 0) {
                    const audioSrc = segundos <= 3 ? '/audio/classico/tempo_alerta.wav' : '/audio/classico/tempo_tick.wav';
                    const fx = new Audio(audioSrc);
                    fx.volume = 0.8;
                    fx.play().catch(() => {});
                } else if (segundos === 0) {
                    const fx = new Audio('/audio/classico/ks.wav');
                    fx.volume = 0.8;
                    fx.play().catch(() => {});
                }
            } catch (e) {}
        }

        function atualizarIconeAudioTelao() {
            const btn = document.getElementById('btnAudioProj');
            const icone = document.getElementById('iconeAudioProj');
            if (!btn || !icone) return;

            if (somAtivo) {
                btn.title = 'Áudio do Telão: Ativo (Clique para silenciar)';
                btn.className = 'p-2.5 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 hover:bg-emerald-500/30 transition shrink-0';
                icone.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />';
            } else {
                btn.title = 'Áudio do Telão: Silenciado (Clique para ativar)';
                btn.className = 'p-2.5 rounded-2xl bg-rose-500/20 border border-rose-400/40 text-rose-300 hover:bg-rose-500/30 transition shrink-0';
                icone.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-1.5l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />';
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            atualizarIconeAudioTelao();
            if (somAtivo && estadoAtual === 'em_curso') {
                trilhaSuspense.play().catch(() => {});
            }
        });

        const qrEl = document.getElementById('qrcodeProj');
        if (qrEl && typeof QRCode !== 'undefined') {
            new QRCode(qrEl, {
                text: window.location.origin + '/entrar/{{ $sala->codigo_acesso }}',
                width: 96,
                height: 96,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });
        }

        function alternarAudioProjecao() {
            somAtivo = !somAtivo;
            localStorage.setItem('biblia_show_som', somAtivo ? 'on' : 'off');
            atualizarIconeAudioTelao();
            if (somAtivo) {
                if (estadoAtual === 'em_curso') {
                    trilhaSuspense.play().catch(() => {});
                }
            } else {
                trilhaSuspense.pause();
            }
        }

        function renderizarRanking(ranking) {
            const list = document.getElementById('rankingList');
            if (!list) return;

            if (!ranking || ranking.length === 0) {
                list.innerHTML = '<div class="text-center py-6 text-slate-400 text-sm">Escaneie o QR Code abaixo ou acesse <strong>/entrar/{{ $sala->codigo_acesso }}</strong></div>';
                return;
            }

            list.innerHTML = ranking.slice(0, 8).map((p, index) => `
                <div class="flex items-center gap-3 p-3.5 rounded-2xl ${index === 0 ? 'bg-amber-500/25 border border-amber-400/50' : 'bg-white/5 border border-white/10'}">
                    <span class="text-xl font-black w-7 ${index === 0 ? 'text-amber-400' : 'text-slate-300'}">${index + 1}º</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-white truncate">${p.nome || 'Participante'}</p>
                        ${p.equipe ? `<p class="text-xs text-slate-300 truncate">${p.equipe}</p>` : ''}
                    </div>
                    <span class="font-mono font-black text-amber-300">${p.pontuacao} pts</span>
                </div>
            `).join('');
        }

        function renderizarCorrida(ranking) {
            const container = document.getElementById('corridaContainer');
            if (!container) return;

            if (!ranking || ranking.length === 0) {
                container.innerHTML = '<p class="text-xs text-slate-400 text-center py-4">Aguardando participantes entrarem na corrida...</p>';
                return;
            }

            const maxPontos = Math.max(100, ...ranking.map(r => Number(r.pontuacao) || 0));

            container.innerHTML = ranking.slice(0, 5).map((p, idx) => {
                const pts = Number(p.pontuacao) || 0;
                const perc = Math.min(100, Math.max(5, Math.round((pts / maxPontos) * 94)));
                const medalhas = ['🥇 1º', '🥈 2º', '🥉 3º', '4º', '5º'];

                return `
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-white flex items-center gap-1.5 truncate">
                                <span class="font-black text-amber-400">${medalhas[idx] || (idx + 1 + 'º')}</span>
                                <span class="truncate">${p.nome || 'Participante'}</span>
                                ${p.equipe ? `<span class="text-[10px] text-slate-400">(${p.equipe})</span>` : ''}
                            </span>
                            <span class="font-mono text-amber-300 font-black">${pts} pts</span>
                        </div>
                        <div class="h-4 w-full bg-slate-950/80 rounded-full overflow-hidden p-0.5 border border-slate-800 relative">
                            <div class="h-full bg-gradient-to-r ${idx === 0 ? 'from-amber-500 to-yellow-400 shadow-md shadow-amber-500/40' : 'from-blue-600 to-indigo-500'} rounded-full transition-all duration-700 relative"
                                 style="width: ${perc}%">
                                <span class="absolute right-1 top-0 bottom-0 flex items-center text-[9px] font-black text-slate-950 select-none">
                                    🏃
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderizarRespostasRodada(participantes, respostas, gabaritoRevelado) {
            const grid = document.getElementById('gridParticipantesRodada');
            const contadorEl = document.getElementById('contadorRespostasRodada');
            if (!grid) return;

            if (!participantes || participantes.length === 0) {
                grid.innerHTML = '<p class="text-xs text-slate-400 col-span-2 text-center py-4">Nenhum jogador conectado no momento.</p>';
                if (contadorEl) contadorEl.textContent = '0 responderam';
                return;
            }

            const total = participantes.length;
            let respCount = 0;

            grid.innerHTML = participantes.map(p => {
                const resp = respostas && respostas[p.id];
                const jaRespondeu = !!resp;
                if (jaRespondeu) respCount++;

                let badgeHtml = '';
                if (!jaRespondeu) {
                    badgeHtml = `
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-lg bg-slate-800 text-slate-400 border border-slate-700 animate-pulse">
                            Pensando...
                        </span>
                    `;
                } else if (gabaritoRevelado) {
                    const acertou = !!resp.correta;
                    badgeHtml = `
                        <span class="text-xs font-black px-2.5 py-1 rounded-lg ${acertou ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/40' : 'bg-rose-600 text-white shadow-md shadow-rose-600/40'}">
                            ${acertou ? '✓ Letra ' + resp.letra : '✗ Letra ' + resp.letra}
                        </span>
                    `;
                } else {
                    badgeHtml = `
                        <span class="text-xs font-black px-2.5 py-1 rounded-lg bg-gradient-to-r from-amber-400 to-amber-500 text-slate-950 shadow-md shadow-amber-500/30">
                            Bloqueou [ ${resp.letra} ]
                        </span>
                    `;
                }

                return `
                    <div class="flex items-center justify-between p-2.5 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                        <div class="flex items-center gap-2.5 min-w-0 pr-2">
                            <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-300 font-black text-xs flex items-center justify-center shrink-0">
                                ${(p.nome || 'P').charAt(0).toUpperCase()}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-white truncate">${p.nome || 'Participante'}</p>
                                ${p.equipe ? `<p class="text-[10px] text-slate-400 truncate">${p.equipe.nome || p.equipe}</p>` : ''}
                            </div>
                        </div>
                        <div class="shrink-0">
                            ${badgeHtml}
                        </div>
                    </div>
                `;
            }).join('');

            if (contadorEl) {
                contadorEl.textContent = `${respCount} de ${total} responderam`;
            }
        }

        function atualizarPergunta(data) {
            const numEl = document.getElementById('numPergunta');
            if (numEl && data.numero_pergunta) {
                numEl.textContent = data.numero_pergunta;
            }

            const timerEl = document.getElementById('timerSegundos');
            if (timerEl && typeof data.tempo_restante !== 'undefined') {
                const seg = Number(data.tempo_restante);
                timerEl.textContent = seg;
                if (seg <= 10) {
                    tocarTickTelao(seg);
                }
            }

            if (data.estado === 'terminado' && estadoAtual !== 'terminado') {
                location.reload();
                return;
            }

            estadoAtual = data.estado;
            const q = data.pergunta_atual;
            if (!q) return;

            const fase = data.fase_atual || 1;
            const placar = data.placar_milhao || {};
            const faseBadge = document.getElementById('faseBadgeProj');
            if (faseBadge) {
                faseBadge.textContent = `${fase}ª FASE • VALE ${placar.valor_pergunta || 10} PTS`;
            }

            const ajudas = data.ajudas || {};
            const eliminadas = ajudas.opcoes_eliminadas || [];
            const gabaritoRevelado = !!ajudas.gabarito_revelado;

            if (questaoAtualId !== q.id) {
                questaoAtualId = q.id;
                gabaritoEstavaRevelado = gabaritoRevelado;
                dicaEstavaAtiva = !!ajudas.dica_biblica;
                convidadoEstavaAtivo = !!ajudas.dica_convidado;
                if (somAtivo) {
                    new Audio(`/audio/classico/fase${Math.min(4, fase)}.wav`).play().catch(() => {});
                }
            } else {
                if (gabaritoRevelado && !gabaritoEstavaRevelado && somAtivo) {
                    new Audio('/audio/classico/certa1.wav').play().catch(() => {});
                }
                if (ajudas.dica_biblica && !dicaEstavaAtiva && somAtivo) {
                    new Audio('/audio/classico/dica.wav').play().catch(() => {});
                }
                if (ajudas.dica_convidado && !convidadoEstavaAtivo && somAtivo) {
                    new Audio('/audio/classico/sabe1.wav').play().catch(() => {});
                }
                gabaritoEstavaRevelado = gabaritoRevelado;
                dicaEstavaAtiva = !!ajudas.dica_biblica;
                convidadoEstavaAtivo = !!ajudas.dica_convidado;
            }

            const boxDica = document.getElementById('boxDicaBiblicaProj');
            if (boxDica) {
                if (ajudas.dica_biblica) {
                    boxDica.textContent = ajudas.dica_biblica;
                    boxDica.classList.remove('hidden');
                } else {
                    boxDica.classList.add('hidden');
                }
            }

            const boxConv = document.getElementById('boxConvidadoProj');
            if (boxConv) {
                if (ajudas.dica_convidado) {
                    boxConv.innerHTML = `<strong class="text-amber-300">${ajudas.dica_convidado.convidado}:</strong> ${ajudas.dica_convidado.mensagem}`;
                    boxConv.classList.remove('hidden');
                } else {
                    boxConv.classList.add('hidden');
                }
            }

            const textoEl = document.getElementById('textoPergunta');
            if (textoEl) textoEl.textContent = q.texto;

            const nivelEl = document.getElementById('nivelBadge');
            if (nivelEl) {
                nivelEl.textContent = `${fase}ª Fase • ${q.dificuldade_nome || 'Bíblia Show'}`;
            }

            const opcoesEl = document.getElementById('opcoesContainer');
            if (opcoesEl && Array.isArray(q.opcoes)) {
                const respCorreta = (q.resposta_correta || '').trim().toLowerCase();
                opcoesEl.innerHTML = q.opcoes.map((op, i) => {
                    const isEliminada = eliminadas.includes(i);
                    const isCorreta = gabaritoRevelado && respCorreta && String(op).trim().toLowerCase() === respCorreta;
                    return `
                        <div class="rounded-2xl p-4 flex items-center gap-4 border-2 transition ${isEliminada ? 'opacity-25 bg-slate-950 border-slate-800 line-through' : (isCorreta ? 'bg-emerald-900/80 border-emerald-400 shadow-lg shadow-emerald-500/20' : 'bg-blue-950/80 border-blue-400/40')}">
                            <span class="w-11 h-11 rounded-xl ${isCorreta ? 'bg-emerald-400 text-slate-950' : 'bg-amber-500 text-slate-950'} font-black text-xl flex items-center justify-center shrink-0">
                                ${letras[i] || (i + 1)}
                            </span>
                            <span class="text-lg sm:text-xl font-bold text-white">${op}</span>
                        </div>
                    `;
                }).join('');
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
                renderizarCorrida(data.ranking || []);
                renderizarRespostasRodada(data.participantes || [], data.respostas_rodada || {}, !!data.ajudas?.gabarito_revelado);
                atualizarPergunta(data);
            } catch (e) {}
        }

        // Execução imediata e sincronização contínua de alta fidelidade em tempo real (1.2 segundos)
        sincronizarProjecao();
        setInterval(sincronizarProjecao, 1200);
    </script>
</body>
</html>
