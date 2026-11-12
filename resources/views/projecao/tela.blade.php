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
        .sm-proj-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            align-items: start;
        }
        @media (min-width: 1024px) {
            .sm-proj-grid {
                grid-template-columns: minmax(0, 1fr) 400px;
                gap: 1.5rem;
            }
        }
        @media (min-width: 1440px) {
            .sm-proj-grid {
                grid-template-columns: minmax(0, 1fr) 480px;
                gap: 1.75rem;
            }
        }
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

        $temasParticipantes = [
            ['cor' => 'amber', 'avatar' => 'bg-gradient-to-br from-amber-300 via-amber-500 to-amber-600 text-slate-950 border-2 border-amber-200 shadow-amber-500/40', 'borda' => 'border-amber-400', 'icone' => '👑', 'barra' => 'from-amber-400 via-yellow-300 to-amber-500'],
            ['cor' => 'emerald', 'avatar' => 'bg-gradient-to-br from-emerald-300 via-emerald-500 to-emerald-700 text-slate-950 border-2 border-emerald-200 shadow-emerald-500/40', 'borda' => 'border-emerald-400', 'icone' => '🌿', 'barra' => 'from-emerald-400 to-teal-500'],
            ['cor' => 'cyan', 'avatar' => 'bg-gradient-to-br from-cyan-300 via-cyan-500 to-blue-600 text-slate-950 border-2 border-cyan-200 shadow-cyan-500/40', 'borda' => 'border-cyan-400', 'icone' => '🕊️', 'barra' => 'from-cyan-400 to-blue-500'],
            ['cor' => 'purple', 'avatar' => 'bg-gradient-to-br from-purple-300 via-purple-500 to-indigo-700 text-white border-2 border-purple-200 shadow-purple-500/40', 'borda' => 'border-purple-400', 'icone' => '✝️', 'barra' => 'from-purple-400 to-indigo-500'],
            ['cor' => 'rose', 'avatar' => 'bg-gradient-to-br from-pink-400 via-rose-500 to-rose-700 text-white border-2 border-pink-200 shadow-rose-500/40', 'borda' => 'border-rose-400', 'icone' => '🌟', 'barra' => 'from-pink-400 to-rose-600'],
            ['cor' => 'orange', 'avatar' => 'bg-gradient-to-br from-orange-300 via-amber-500 to-red-600 text-slate-950 border-2 border-orange-200 shadow-orange-500/40', 'borda' => 'border-orange-400', 'icone' => '🔥', 'barra' => 'from-orange-400 to-red-500'],
            ['cor' => 'blue', 'avatar' => 'bg-gradient-to-br from-blue-300 via-blue-500 to-indigo-800 text-white border-2 border-blue-200 shadow-blue-500/40', 'borda' => 'border-blue-400', 'icone' => '🛡️', 'barra' => 'from-blue-400 to-indigo-600'],
            ['cor' => 'lime', 'avatar' => 'bg-gradient-to-br from-lime-300 via-yellow-400 to-emerald-600 text-slate-950 border-2 border-lime-200 shadow-lime-500/40', 'borda' => 'border-lime-400', 'icone' => '⚡', 'barra' => 'from-lime-400 to-yellow-400'],
            ['cor' => 'red', 'avatar' => 'bg-gradient-to-br from-red-400 via-rose-600 to-red-800 text-white border-2 border-red-200 shadow-red-500/40', 'borda' => 'border-red-400', 'icone' => '📖', 'barra' => 'from-red-400 to-rose-600'],
            ['cor' => 'teal', 'avatar' => 'bg-gradient-to-br from-teal-300 via-teal-500 to-emerald-700 text-slate-950 border-2 border-teal-200 shadow-teal-500/40', 'borda' => 'border-teal-400', 'icone' => '⚓', 'barra' => 'from-teal-400 to-emerald-600'],
        ];
    @endphp

    <div class="max-w-[1880px] w-full mx-auto">
        <!-- Header da Projeção (Tema Show do Milhão Palco) -->
        <header class="mb-6 flex flex-wrap justify-between items-center gap-4 border-b border-blue-400/20 pb-5">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="sm-pill !px-3 !py-0.5 !text-[11px] uppercase tracking-widest">
                        AO VIVO • ARENA BÍBLIA SHOW
                    </span>
                    <span id="faseBadgeProj" class="sm-pill !px-3 !py-0.5 !text-[11px] font-black uppercase">
                        {{ $faseAtual }}ª FASE • VALE {{ $placar['valor_pergunta'] ?? 10 }} PTS
                    </span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-white sm-logo-title">{{ $sala->nome }}</h1>
                @if($sala->premio_primeiro_lugar || $sala->premio_segundo_lugar)
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-xs">
                        @if($sala->premio_primeiro_lugar)
                            <span class="px-3 py-1 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-300 font-bold flex items-center gap-1.5 shadow">
                                <span>🏆</span> 1º Lugar: {{ $sala->premio_primeiro_lugar }}
                            </span>
                        @endif
                        @if($sala->premio_segundo_lugar)
                            <span class="px-3 py-1 rounded-xl bg-blue-900/60 border border-blue-400/40 text-blue-200 font-bold flex items-center gap-1.5">
                                <span>🥈</span> 2º Lugar: {{ $sala->premio_segundo_lugar }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Áudio do Telão (Silenciado por Padrão para não conflitar com a igreja ou celulares) -->
                <button type="button" onclick="alternarAudioProjecao()" id="btnAudioProj" title="Áudio do Telão: Silenciado por padrão (Clique para ativar efeitos de palco)"
                        class="sm-lifeline !w-11 !h-11 flex items-center justify-center cursor-pointer border-rose-400/60 text-rose-300 opacity-60">
                    <svg id="iconeAudioProj" class="w-5 h-5 text-rose-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-1.5l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                    </svg>
                </button>
                <div class="sm-ingot !px-5 !py-2 text-center min-w-[130px]">
                    <div class="sm-ingot-label">CÓDIGO DA SALA</div>
                    <div class="sm-ingot-value !text-2xl sm:!text-3xl" id="salaCodigo">{{ $sala->codigo_acesso }}</div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-blue-200 uppercase tracking-wider">Pergunta</p>
                    <p class="text-3xl sm:text-4xl font-black text-white">
                        <span id="numPergunta" class="text-amber-300">{{ $numero_pergunta ?? 1 }}</span><span class="text-blue-300/60 text-xl font-bold">/{{ $total_perguntas ?? 24 }}</span>
                    </p>
                </div>
            </div>
        </header>

        <!-- ======================================================== -->
        <!-- GRADE PRINCIPAL DO TELÃO (2 COLUNAS):                    -->
        <!-- 1. ESQUERDA (1fr): Card Principal da Pergunta & Opções   -->
        <!-- 2. DIREITA  (480px): Classificação, Resultados & QR      -->
        <!-- ======================================================== -->
        <div class="sm-proj-grid">

            <!-- ===================================================== -->
            <!-- 1. COLUNA PRINCIPAL: CARD DA PERGUNTA & ALTERNATIVAS  -->
            <!-- ===================================================== -->
            <div class="space-y-6 min-w-0" id="painelPergunta">
                @if($estado === 'terminado')
                    <div class="sm-panel p-8 sm:p-12 text-center shadow-2xl relative overflow-hidden">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border-4 border-amber-200 shadow-2xl shadow-amber-500/50 mx-auto mb-4">
                            <span class="text-4xl sm:text-5xl">🏆</span>
                        </div>
                        <div class="inline-flex items-center justify-center mb-3">
                            <span class="sm-pill !px-6 !py-1.5 text-sm uppercase font-black">
                                ★ PARTIDA FINALIZADA ★
                            </span>
                        </div>
                        <h2 class="text-3xl sm:text-5xl font-black text-white sm-logo-title mb-2">Show Concluído!</h2>
                        <p class="text-blue-200 text-lg font-bold">Confira a classificação oficial dos participantes no ranking ao lado</p>
                    </div>
                @elseif($pergunta)
                    <div class="sm-panel p-6 sm:p-8 shadow-2xl">
                        <div class="flex justify-between items-center mb-6">
                            <span class="sm-pill !px-4 !py-1 text-xs font-black uppercase tracking-wider" id="nivelBadge">
                                {{ $faseAtual }}ª Fase • {{ $pergunta['dificuldade_nome'] ?? 'Bíblia Show' }}
                            </span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-black uppercase text-amber-300 tracking-wider">Tempo</span>
                                <div class="sm-timer !w-14 !h-14 !text-xl" id="tempoRestanteBox" title="Tempo Restante">
                                    <span id="timerSegundos">{{ $tempo_restante }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Caixa Dourada/Azul Clássica da Pergunta -->
                        <div class="sm-question p-6 sm:p-8 mb-6 shadow-2xl">
                            <p class="sm-question-text text-4xl sm:text-5xl font-black text-white leading-snug" id="textoPergunta">
                                {{ $pergunta['texto'] ?? $pergunta->texto }}
                            </p>
                        </div>

                        <!-- Banner de Dica Bíblica e Convidado Bíblico no Telão -->
                        <div id="boxDicaBiblicaProj" class="{{ $dicaBiblica ? '' : 'hidden' }} mb-4 p-4 rounded-2xl bg-amber-500/20 border-2 border-amber-400/50 text-amber-200 text-base font-bold flex items-center gap-3 shadow-lg">
                            <span class="text-xl">💡</span>
                            <span>{{ $dicaBiblica }}</span>
                        </div>

                        <div id="boxConvidadoProj" class="{{ $dicaConvidado ? '' : 'hidden' }} mb-4 p-4 rounded-2xl bg-blue-950/90 border-2 border-blue-400/50 text-blue-100 font-bold text-base shadow-lg">
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
                                <div class="sm-option min-h-[64px] sm:min-h-[72px] !px-5 !py-3.5 {{ $eliminada ? 'is-eliminada' : ($ehCorretaRevelada ? 'opcao-correta-reveal' : '') }}">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <span class="sm-avatar !w-11 !h-11 !text-lg !font-black shrink-0">
                                            {{ $letras[$idx] ?? ($idx + 1) }}
                                        </span>
                                        <span class="text-lg sm:text-xl font-black text-white truncate">{{ $opcao }}</span>
                                    </div>
                                    @if($ehCorretaRevelada)
                                        <span class="text-emerald-200 font-black text-xl shrink-0">✓</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- ===================================================== -->
            <!-- 2. COLUNA LATERAL DIREITA: CLASSIFICAÇÃO, RESULTADOS, QR -->
            <!-- ===================================================== -->
            <div class="space-y-5 min-w-0">
                <!-- 1. Classificação Ao Vivo -->
                <div class="sm-panel p-5 sm:p-6 shadow-xl">
                    <div class="flex items-center gap-2.5 mb-3.5 pb-2.5 border-b border-blue-400/20">
                        <svg class="w-5 h-5 text-amber-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                        </svg>
                        <h3 class="text-base sm:text-lg font-black text-white sm-logo-title uppercase tracking-wider">Classificação Ao Vivo</h3>
                    </div>

                    <div class="space-y-2.5 max-h-52 overflow-y-auto pr-1" id="rankingList">
                        @forelse(array_slice($ranking ?? [], 0, 6) as $index => $participante)
                            @php
                                $temaRank = $temasParticipantes[abs((int) ($participante['id'] ?? $index)) % count($temasParticipantes)];
                            @endphp
                            <div class="flex items-center gap-2.5 p-2.5 rounded-xl {{ $index === 0 ? 'bg-gradient-to-r from-amber-500/25 to-amber-600/15 border-2 border-amber-400/70 shadow-lg' : 'bg-blue-950/80 border border-blue-400/30' }}">
                                <span class="text-sm sm:text-base font-black w-6 {{ $index === 0 ? 'text-amber-300' : 'text-blue-200' }}">
                                    {{ $index + 1 }}º
                                </span>
                                <div class="w-7 h-7 rounded-lg {{ $temaRank['avatar'] }} text-xs font-black flex items-center justify-center shrink-0 shadow-sm">
                                    <span class="text-xs leading-none">{{ $temaRank['icone'] }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-white truncate text-xs sm:text-sm">{{ $participante['nome'] ?? 'Participante' }}</p>
                                    @if(!empty($participante['equipe']))
                                        <p class="text-[10px] sm:text-xs text-blue-200/70 truncate">{{ $participante['equipe'] }}</p>
                                    @endif
                                </div>
                                <span class="font-mono font-black text-amber-300 text-xs sm:text-sm">{{ $participante['pontuacao'] }} pts</span>
                            </div>
                        @empty
                            <div class="text-center py-4 text-blue-200 text-xs">
                                Escaneie o QR Code abaixo para entrar
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 2. Card de Resultado / Respostas Bloqueadas na Rodada -->
                <div class="sm-panel p-5 sm:p-6 shadow-2xl relative overflow-hidden border-2 sm:border-3 border-amber-400 shadow-amber-500/20 bg-gradient-to-b from-[#0e2270] via-[#0a1854] to-[#060e33]">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-transparent via-amber-300 to-transparent"></div>

                    <div class="flex items-center justify-between gap-3 mb-4 pb-3 border-b-2 border-amber-400/30">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-br from-amber-300 via-amber-500 to-amber-600 text-slate-950 flex items-center justify-center shadow-md shadow-amber-500/40 border-2 border-amber-200 shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base sm:text-lg font-black text-white uppercase tracking-wider sm-logo-title truncate">Respostas na Rodada</h3>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/50 text-[10px] font-black text-emerald-300 uppercase tracking-widest shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                        AO VIVO
                                    </span>
                                </div>
                                <p class="text-xs text-blue-200 font-semibold truncate">Letras bloqueadas ao vivo</p>
                            </div>
                        </div>
                        @php
                            $totalParticipantes = count($participantes ?? []);
                            $respondidosCount = count(array_filter(($participantes ?? collect())->all(), fn($p) => isset($respostas_rodada[$p->id])));
                        @endphp
                        <span id="contadorRespostasRodada" class="px-3 sm:px-3.5 py-1.5 rounded-full bg-gradient-to-r from-amber-300 via-amber-400 to-amber-500 text-slate-950 text-xs sm:text-sm font-black shadow-md shadow-amber-500/40 border border-white/80 tracking-wide shrink-0 whitespace-nowrap">
                            {{ $respondidosCount }}/{{ $totalParticipantes }} resp.
                        </span>
                    </div>

                    <div id="gridParticipantesRodada" class="space-y-3 max-h-64 overflow-y-auto pr-1">
                        @php
                            $tempoEsgotadoInicial = ($tempo_restante ?? 0) <= 0 || !empty($tempo_esgotado) || !empty($gabaritoRevelado);
                        @endphp
                        @forelse($participantes ?? [] as $p)
                            @php
                                $resp = $respostas_rodada[$p->id] ?? null;
                                $jaRespondeu = !empty($resp);
                                $acertou = !empty($resp['correta']);
                                $letraResp = $resp['letra'] ?? '?';
                                $tema = $temasParticipantes[abs((int) ($p->id ?? $loop->index)) % count($temasParticipantes)];

                                if ($jaRespondeu) {
                                    if ($gabaritoRevelado) {
                                        $cardClasses = $acertou
                                            ? 'bg-gradient-to-r from-emerald-950/40 via-blue-950/90 to-blue-900/90 border-2 border-emerald-400 shadow-xl shadow-emerald-500/20 ring-1 ring-emerald-300/40'
                                            : 'bg-gradient-to-r from-rose-950/40 via-blue-950/90 to-blue-900/90 border-2 border-rose-500/70 shadow-xl shadow-rose-600/20 ring-1 ring-rose-400/40';
                                        $avatarClasses = $acertou
                                            ? 'bg-gradient-to-br from-emerald-400 to-emerald-600 text-slate-950 border-2 border-emerald-200 shadow-emerald-500/40'
                                            : 'bg-gradient-to-br from-rose-500 to-rose-700 text-white border-2 border-rose-300 shadow-rose-600/40';
                                    } else {
                                        $cardClasses = 'bg-gradient-to-r from-amber-950/50 via-blue-950/90 to-blue-900/90 border-2 border-amber-400 shadow-xl shadow-amber-500/25 ring-1 ring-amber-300/50';
                                        $avatarClasses = $tema['avatar'];
                                    }
                                } elseif ($tempoEsgotadoInicial) {
                                    $cardClasses = 'bg-gradient-to-r from-rose-950/30 via-slate-900/90 to-blue-950/90 border border-rose-500/40 shadow-sm opacity-85';
                                    $avatarClasses = 'bg-rose-950/70 border border-rose-400/40 text-rose-300';
                                } else {
                                    $cardClasses = 'bg-blue-950/80 border border-blue-400/30';
                                    $avatarClasses = $tema['avatar'];
                                }
                            @endphp
                            <div class="flex items-center justify-between p-3 sm:p-3.5 rounded-2xl transition-all duration-300 {{ $cardClasses }}">
                                <div class="flex items-center gap-3 min-w-0 pr-2">
                                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl {{ $avatarClasses }} text-base sm:text-lg font-black flex items-center justify-center shadow-md shrink-0 relative">
                                        <span class="text-base sm:text-lg leading-none">{{ $tema['icone'] }}</span>
                                        <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-slate-950 border {{ $tema['borda'] }} text-[9px] font-black text-white flex items-center justify-center leading-none">{{ mb_strtoupper(mb_substr($p->nome ?? 'P', 0, 1)) }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm sm:text-base font-black text-white truncate drop-shadow-sm">{{ $p->nome ?? 'Participante' }}</p>
                                        @if(!empty($p->equipe))
                                            <p class="text-xs text-amber-200/80 font-bold truncate">{{ $p->equipe->nome ?? $p->equipe }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    @if(!$jaRespondeu)
                                        @if($tempoEsgotadoInicial)
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-950/80 text-rose-300 border border-rose-500/50 font-bold text-xs sm:text-sm tracking-wide shadow">
                                                <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>Tempo Esgotado</span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-900/80 text-blue-200 border border-blue-400/40 font-bold text-xs sm:text-sm tracking-wide shadow">
                                                <span class="relative flex h-2.5 w-2.5">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-400"></span>
                                                </span>
                                                <span>Pensando...</span>
                                            </div>
                                        @endif
                                    @elseif($gabaritoRevelado)
                                        @if($acertou)
                                            <div class="inline-flex items-center gap-2 px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-xl bg-gradient-to-r from-emerald-400 via-emerald-500 to-emerald-600 text-slate-950 shadow-lg shadow-emerald-500/50 border border-emerald-200 font-black">
                                                <span class="text-xs sm:text-sm uppercase tracking-wider text-emerald-950 flex items-center gap-1 font-extrabold">
                                                    <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                    Acertou
                                                </span>
                                                <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 text-emerald-300 font-mono font-black text-sm sm:text-base border border-emerald-300/60 shadow-inner">
                                                    [ {{ $letraResp }} ]
                                                </span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-2 px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 text-white shadow-lg shadow-rose-600/50 border border-rose-300 font-black">
                                                <span class="text-xs sm:text-sm uppercase tracking-wider text-rose-100 flex items-center gap-1 font-extrabold">
                                                    <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Errou
                                                </span>
                                                <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 text-rose-300 font-mono font-black text-sm sm:text-base border border-rose-400/60 shadow-inner">
                                                    [ {{ $letraResp }} ]
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="inline-flex items-center gap-2 px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 text-slate-950 shadow-lg shadow-amber-500/50 border border-amber-200 animate-pulse font-black">
                                            <span class="text-xs sm:text-sm uppercase tracking-wider text-amber-950 font-extrabold">Bloqueou</span>
                                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 text-amber-300 font-mono font-black text-sm sm:text-base border border-amber-400/60 shadow-inner">
                                                [ {{ $letraResp }} ]
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-blue-200 text-center py-6 font-bold">Aguardando participantes...</p>
                        @endforelse
                    </div>
                </div>

                <!-- 3. Participe pelo Celular -->
                <div class="sm-panel p-4 text-center flex items-center justify-center gap-4">
                    <div id="qrcodeProj" class="p-2 bg-white rounded-xl shadow-lg border-2 border-amber-400 shrink-0"></div>
                    <div class="text-left min-w-0">
                        <span class="sm-pill !px-2.5 !py-0.5 !text-[10px] uppercase tracking-wider">Participe pelo Celular</span>
                        <p class="text-2xl font-mono font-black text-amber-300 mt-1 leading-none">{{ $sala->codigo_acesso }}</p>
                        <p class="text-xs text-blue-200 mt-1 font-bold truncate">{{ parse_url(url('/'), PHP_URL_PATH) }}/entrar/{{ $sala->codigo_acesso }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const BASE_APP_URL = (() => {
            const subpath = "{{ parse_url(url('/'), PHP_URL_PATH) }}" || '';
            return (window.location.origin + subpath).replace(/\/+$/, '');
        })();
        const letras = ['A', 'B', 'C', 'D'];
        let questaoAtualId = {{ $pergunta['id'] ?? 'null' }};
        let estadoAtual = '{{ $estado }}';
        let gabaritoEstavaRevelado = {{ $gabaritoRevelado ? 'true' : 'false' }};
        let dicaEstavaAtiva = {{ $dicaBiblica ? 'true' : 'false' }};
        let convidadoEstavaAtivo = {{ $dicaConvidado ? 'true' : 'false' }};
        let tempoLocal = {{ max(0, (int) ($tempo_restante ?? 0)) }};
        // No Telão, áudio ativado por padrão para efeitos sonoros de palco (suspense, dicas e contagem regressiva)
        let somAtivo = localStorage.getItem('biblia_show_projecao_som') !== 'off';

        const BASE_AUDIO_URL = "{{ asset('audio/classico') }}";
        const trilhaSuspense = new Audio(`${BASE_AUDIO_URL}/suspense.wav`);
        trilhaSuspense.loop = true;
        trilhaSuspense.volume = 0.25;

        // Desbloqueia áudio caso o navegador bloqueie autoplay inicial
        function desbloquearAudioTelao() {
            if (somAtivo && estadoAtual === 'em_curso' && trilhaSuspense.paused && !gabaritoEstavaRevelado) {
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
                    const audioSrc = segundos <= 3 ? `${BASE_AUDIO_URL}/tempo_alerta.wav` : `${BASE_AUDIO_URL}/tempo_tick.wav`;
                    const fx = new Audio(audioSrc);
                    fx.volume = 0.9;
                    fx.play().catch(() => {});
                } else if (segundos === 0) {
                    const fx = new Audio(`${BASE_AUDIO_URL}/ks.wav`);
                    fx.volume = 0.95;
                    fx.play().catch(() => {});
                }
            } catch (e) {}
        }

        function atualizarTimerVisual(seg) {
            const timerEl = document.getElementById('timerSegundos');
            const timerBox = document.getElementById('tempoRestanteBox');
            if (timerEl) timerEl.textContent = Math.max(0, seg);
            if (timerBox) {
                if (seg <= 5 && seg > 0) {
                    timerBox.className = 'sm-timer !w-14 !h-14 !text-xl is-critical';
                } else if (seg <= 10 && seg > 0) {
                    timerBox.className = 'sm-timer !w-14 !h-14 !text-xl timer-box-warning';
                } else {
                    timerBox.className = 'sm-timer !w-14 !h-14 !text-xl';
                }
            }
        }

        // Relógio local regressivo contínuo de 1 em 1 segundo de alta fidelidade
        setInterval(() => {
            if (estadoAtual !== 'em_curso' || gabaritoEstavaRevelado) return;
            if (tempoLocal > 0) {
                tempoLocal--;
                atualizarTimerVisual(tempoLocal);
                if (tempoLocal <= 10) {
                    tocarTickTelao(tempoLocal);
                }
            }
        }, 1000);

        function atualizarIconeAudioTelao() {
            const btn = document.getElementById('btnAudioProj');
            const icone = document.getElementById('iconeAudioProj');
            if (!btn || !icone) return;

            if (somAtivo) {
                btn.title = 'Áudio do Telão: Ativo (Clique para silenciar)';
                btn.className = 'sm-lifeline !w-11 !h-11 flex items-center justify-center cursor-pointer border-emerald-400 text-emerald-300';
                icone.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />';
            } else {
                btn.title = 'Áudio do Telão: Silenciado (Clique para ativar)';
                btn.className = 'sm-lifeline !w-11 !h-11 flex items-center justify-center cursor-pointer border-rose-400/60 text-rose-300 opacity-60';
                icone.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-1.5l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />';
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            atualizarIconeAudioTelao();
            if (somAtivo && estadoAtual === 'em_curso' && !gabaritoEstavaRevelado) {
                trilhaSuspense.play().catch(() => {});
            }
        });

        const qrEl = document.getElementById('qrcodeProj');
        if (qrEl && typeof QRCode !== 'undefined') {
            new QRCode(qrEl, {
                text: `${BASE_APP_URL}/entrar/{{ $sala->codigo_acesso }}`,
                width: 80,
                height: 80,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });
        }

        function alternarAudioProjecao() {
            somAtivo = !somAtivo;
            localStorage.setItem('biblia_show_projecao_som', somAtivo ? 'on' : 'off');
            atualizarIconeAudioTelao();
            if (somAtivo) {
                if (estadoAtual === 'em_curso' && !gabaritoEstavaRevelado) {
                    trilhaSuspense.play().catch(() => {});
                }
            } else {
                trilhaSuspense.pause();
            }
        }

        const TEMAS_PARTICIPANTES = [
            { cor: 'amber', avatar: 'bg-gradient-to-br from-amber-300 via-amber-500 to-amber-600 text-slate-950 border-2 border-amber-200 shadow-amber-500/40', borda: 'border-amber-400', icone: '👑', barra: 'from-amber-400 via-yellow-300 to-amber-500' },
            { cor: 'emerald', avatar: 'bg-gradient-to-br from-emerald-300 via-emerald-500 to-emerald-700 text-slate-950 border-2 border-emerald-200 shadow-emerald-500/40', borda: 'border-emerald-400', icone: '🌿', barra: 'from-emerald-400 to-teal-500' },
            { cor: 'cyan', avatar: 'bg-gradient-to-br from-cyan-300 via-cyan-500 to-blue-600 text-slate-950 border-2 border-cyan-200 shadow-cyan-500/40', borda: 'border-cyan-400', icone: '🕊️', barra: 'from-cyan-400 to-blue-500' },
            { cor: 'purple', avatar: 'bg-gradient-to-br from-purple-300 via-purple-500 to-indigo-700 text-white border-2 border-purple-200 shadow-purple-500/40', borda: 'border-purple-400', icone: '✝️', barra: 'from-purple-400 to-indigo-500' },
            { cor: 'rose', avatar: 'bg-gradient-to-br from-pink-400 via-rose-500 to-rose-700 text-white border-2 border-pink-200 shadow-rose-500/40', borda: 'border-rose-400', icone: '🌟', barra: 'from-pink-400 to-rose-600' },
            { cor: 'orange', avatar: 'bg-gradient-to-br from-orange-300 via-amber-500 to-red-600 text-slate-950 border-2 border-orange-200 shadow-orange-500/40', borda: 'border-orange-400', icone: '🔥', barra: 'from-orange-400 to-red-500' },
            { cor: 'blue', avatar: 'bg-gradient-to-br from-blue-300 via-blue-500 to-indigo-800 text-white border-2 border-blue-200 shadow-blue-500/40', borda: 'border-blue-400', icone: '🛡️', barra: 'from-blue-400 to-indigo-600' },
            { cor: 'lime', avatar: 'bg-gradient-to-br from-lime-300 via-yellow-400 to-emerald-600 text-slate-950 border-2 border-lime-200 shadow-lime-500/40', borda: 'border-lime-400', icone: '⚡', barra: 'from-lime-400 to-yellow-400' },
            { cor: 'red', avatar: 'bg-gradient-to-br from-red-400 via-rose-600 to-red-800 text-white border-2 border-red-200 shadow-red-500/40', borda: 'border-red-400', icone: '📖', barra: 'from-red-400 to-rose-600' },
            { cor: 'teal', avatar: 'bg-gradient-to-br from-teal-300 via-teal-500 to-emerald-700 text-slate-950 border-2 border-teal-200 shadow-teal-500/40', borda: 'border-teal-400', icone: '⚓', barra: 'from-teal-400 to-emerald-600' }
        ];

        function obterTemaParticipante(p, idx = 0) {
            const seed = p && typeof p.id !== 'undefined' ? Number(p.id) : idx;
            return TEMAS_PARTICIPANTES[Math.abs(seed) % TEMAS_PARTICIPANTES.length];
        }

        function renderizarRanking(ranking) {
            const list = document.getElementById('rankingList');
            if (!list) return;

            if (!ranking || ranking.length === 0) {
                list.innerHTML = '<div class="text-center py-4 text-blue-200 text-xs">Escaneie o QR Code abaixo para entrar</div>';
                return;
            }

            list.innerHTML = ranking.slice(0, 6).map((p, index) => {
                const tema = obterTemaParticipante(p, index);
                return `
                    <div class="flex items-center gap-2.5 p-2 rounded-xl ${index === 0 ? 'bg-gradient-to-r from-amber-500/25 to-amber-600/15 border-2 border-amber-400/70 shadow-lg' : 'bg-blue-950/80 border border-blue-400/30'}">
                        <span class="text-sm font-black w-6 ${index === 0 ? 'text-amber-300' : 'text-blue-200'}">${index + 1}º</span>
                        <div class="w-6 h-6 rounded-lg ${tema.avatar} text-xs font-black flex items-center justify-center shrink-0 shadow-sm">
                            <span class="text-xs leading-none">${tema.icone}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-white truncate text-xs sm:text-sm">${p.nome || 'Participante'}</p>
                            ${p.equipe ? `<p class="text-[10px] text-blue-200/70 truncate">${p.equipe}</p>` : ''}
                        </div>
                        <span class="font-mono font-black text-amber-300 text-xs sm:text-sm">${p.pontuacao} pts</span>
                    </div>
                `;
            }).join('');
        }

        function renderizarCorrida(ranking) {
            const container = document.getElementById('corridaContainer');
            if (!container) return;

            if (!ranking || ranking.length === 0) {
                container.innerHTML = '<p class="text-[11px] text-blue-200 text-center py-3">Carregando posições da corrida...</p>';
                return;
            }

            const maxPontos = Math.max(100, ...ranking.map(r => Number(r.pontuacao) || 0));

            container.innerHTML = ranking.slice(0, 5).map((p, idx) => {
                const tema = obterTemaParticipante(p, idx);
                const pts = Number(p.pontuacao) || 0;
                const perc = Math.min(100, Math.max(5, Math.round((pts / maxPontos) * 94)));
                const medalhas = ['1º', '2º', '3º', '4º', '5º'];

                return `
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-white flex items-center gap-1.5 truncate">
                                <span class="font-black ${idx === 0 ? 'text-amber-300' : 'text-blue-200'}">${medalhas[idx] || (idx + 1 + 'º')}</span>
                                <span class="text-sm leading-none">${tema.icone}</span>
                                <span class="truncate">${p.nome || 'Participante'}</span>
                                ${p.equipe ? `<span class="text-[10px] text-blue-200/70">(${p.equipe})</span>` : ''}
                            </span>
                            <span class="font-mono text-amber-300 font-black">${pts} pts</span>
                        </div>
                        <div class="h-3.5 w-full bg-blue-950/90 rounded-full overflow-hidden p-0.5 border border-blue-400/30 relative">
                            <div class="h-full bg-gradient-to-r ${tema.barra} rounded-full transition-all duration-700 relative shadow-sm"
                                 style="width: ${perc}%">
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderizarRespostasRodada(participantes, respostas, gabaritoRevelado, tempoEsgotado) {
            ultimosParticipantes = participantes || ultimosParticipantes;
            ultimasRespostas = respostas || ultimasRespostas;

            const grid = document.getElementById('gridParticipantesRodada');
            const contadorEl = document.getElementById('contadorRespostasRodada');
            if (!grid) return;

            if (!participantes || participantes.length === 0) {
                grid.innerHTML = '<p class="text-xs text-blue-200 text-center py-6 font-bold">Nenhum jogador conectado no momento.</p>';
                if (contadorEl) contadorEl.textContent = '0 resp.';
                return;
            }

            const total = participantes.length;
            let respCount = 0;
            const isTempoEsgotado = typeof tempoEsgotado !== 'undefined' ? !!tempoEsgotado : (tempoLocal <= 0 || !!gabaritoRevelado);

            grid.innerHTML = participantes.map((p, idx) => {
                const resp = respostas && respostas[p.id];
                const jaRespondeu = !!resp;
                if (jaRespondeu) respCount++;
                const tema = obterTemaParticipante(p, idx);
                const inicial = (p.nome || 'P').charAt(0).toUpperCase();

                let badgeHtml = '';
                if (!jaRespondeu) {
                    if (isTempoEsgotado) {
                        badgeHtml = `
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-950/80 text-rose-300 border border-rose-500/50 font-bold text-xs sm:text-sm tracking-wide shadow">
                                <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Tempo Esgotado</span>
                            </div>
                        `;
                    } else {
                        badgeHtml = `
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-900/80 text-blue-200 border border-blue-400/40 font-bold text-xs sm:text-sm tracking-wide shadow">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-400"></span>
                                </span>
                                <span>Pensando...</span>
                            </div>
                        `;
                    }
                } else if (gabaritoRevelado) {
                    const acertou = !!resp.correta;
                    if (acertou) {
                        badgeHtml = `
                            <div class="inline-flex items-center gap-2 px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-xl bg-gradient-to-r from-emerald-400 via-emerald-500 to-emerald-600 text-slate-950 shadow-lg shadow-emerald-500/50 border border-emerald-200 font-black">
                                <span class="text-xs sm:text-sm uppercase tracking-wider text-emerald-950 flex items-center gap-1 font-extrabold">
                                    <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    Acertou
                                </span>
                                <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 text-emerald-300 font-mono font-black text-sm sm:text-base border border-emerald-300/60 shadow-inner">
                                    [ ${resp.letra} ]
                                </span>
                            </div>
                        `;
                    } else {
                        badgeHtml = `
                            <div class="inline-flex items-center gap-2 px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 text-white shadow-lg shadow-rose-600/50 border border-rose-300 font-black">
                                <span class="text-xs sm:text-sm uppercase tracking-wider text-rose-100 flex items-center gap-1 font-extrabold">
                                    <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Errou
                                </span>
                                <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 text-rose-300 font-mono font-black text-sm sm:text-base border border-rose-400/60 shadow-inner">
                                    [ ${resp.letra} ]
                                </span>
                            </div>
                        `;
                    }
                } else {
                    badgeHtml = `
                        <div class="inline-flex items-center gap-2 px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 text-slate-950 shadow-lg shadow-amber-500/50 border border-amber-200 animate-pulse font-black">
                            <span class="text-xs sm:text-sm uppercase tracking-wider text-amber-950 font-extrabold">Bloqueou</span>
                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 text-amber-300 font-mono font-black text-sm sm:text-base border border-amber-400/60 shadow-inner">
                                [ ${resp.letra} ]
                            </span>
                        </div>
                    `;
                }

                let cardClasses = 'bg-blue-950/80 border border-blue-400/30';
                let avatarClasses = tema.avatar;

                if (jaRespondeu) {
                    if (gabaritoRevelado) {
                        cardClasses = resp.correta
                            ? 'bg-gradient-to-r from-emerald-950/40 via-blue-950/90 to-blue-900/90 border-2 border-emerald-400 shadow-xl shadow-emerald-500/20 ring-1 ring-emerald-300/40'
                            : 'bg-gradient-to-r from-rose-950/40 via-blue-950/90 to-blue-900/90 border-2 border-rose-500/70 shadow-xl shadow-rose-600/20 ring-1 ring-rose-400/40';
                        avatarClasses = resp.correta
                            ? 'bg-gradient-to-br from-emerald-400 to-emerald-600 text-slate-950 border-2 border-emerald-200 shadow-emerald-500/40'
                            : 'bg-gradient-to-br from-rose-500 to-rose-700 text-white border-2 border-rose-300 shadow-rose-600/40';
                    } else {
                        cardClasses = 'bg-gradient-to-r from-amber-950/50 via-blue-950/90 to-blue-900/90 border-2 border-amber-400 shadow-xl shadow-amber-500/25 ring-1 ring-amber-300/50';
                    }
                } else if (isTempoEsgotado) {
                    cardClasses = 'bg-gradient-to-r from-rose-950/30 via-slate-900/90 to-blue-950/90 border border-rose-500/40 shadow-sm opacity-85';
                    avatarClasses = 'bg-rose-950/70 border border-rose-400/40 text-rose-300';
                }

                const avatarHtml = `
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl ${avatarClasses} text-base sm:text-lg font-black flex items-center justify-center shadow-md shrink-0 relative">
                        <span class="text-base sm:text-lg leading-none">${tema.icone}</span>
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-slate-950 border ${tema.borda} text-[9px] font-black text-white flex items-center justify-center leading-none">${inicial}</span>
                    </div>
                `;

                return `
                    <div class="flex items-center justify-between p-3 sm:p-3.5 rounded-2xl ${cardClasses} transition-all duration-300">
                        <div class="flex items-center gap-3 min-w-0 pr-2">
                            ${avatarHtml}
                            <div class="min-w-0">
                                <p class="text-sm sm:text-base font-black text-white truncate drop-shadow-sm">${p.nome || 'Participante'}</p>
                                ${p.equipe ? `<p class="text-xs text-amber-200/80 font-bold truncate">${p.equipe.nome || p.equipe}</p>` : ''}
                            </div>
                        </div>
                        <div class="shrink-0">
                            ${badgeHtml}
                        </div>
                    </div>
                `;
            }).join('');

            if (contadorEl) {
                contadorEl.textContent = `${respCount}/${total} resp.`;
            }
        }

        function atualizarPergunta(data) {
            const numEl = document.getElementById('numPergunta');
            if (numEl && data.numero_pergunta) {
                numEl.textContent = data.numero_pergunta;
            }

            if (typeof data.tempo_restante !== 'undefined') {
                const seg = Number(data.tempo_restante);
                if (Math.abs(tempoLocal - seg) > 1 || seg > tempoLocal) {
                    tempoLocal = seg;
                    if (seg > 10) {
                        ultimoSegundoTocado = null;
                    }
                }
                atualizarTimerVisual(tempoLocal);
                if (tempoLocal <= 10 && tempoLocal >= 0) {
                    tocarTickTelao(tempoLocal);
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
                ultimoSegundoTocado = null;
                tempoLocal = Number(data.tempo_restante || 30);
                gabaritoEstavaRevelado = gabaritoRevelado;
                dicaEstavaAtiva = !!ajudas.dica_biblica;
                convidadoEstavaAtivo = !!ajudas.dica_convidado;
                if (somAtivo) {
                    new Audio(`${BASE_AUDIO_URL}/fase${Math.min(4, fase)}.wav`).play().catch(() => {});
                    if (!gabaritoRevelado) {
                        trilhaSuspense.play().catch(() => {});
                    }
                }
            } else {
                // No Telão não tocamos áudio de acerto (certa1) nem de erro (errou1)
                // porque vários participantes jogam ao mesmo tempo com respostas distintas.
                // O feedback de acerto/erro é individual e toca direto no smartphone de cada um.
                if (ajudas.dica_biblica && !dicaEstavaAtiva && somAtivo) {
                    new Audio(`${BASE_AUDIO_URL}/dica.wav`).play().catch(() => {});
                }
                if (ajudas.dica_convidado && !convidadoEstavaAtivo && somAtivo) {
                    new Audio(`${BASE_AUDIO_URL}/sabe1.wav`).play().catch(() => {});
                }
                if (gabaritoRevelado && !trilhaSuspense.paused) {
                    trilhaSuspense.pause();
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
                    let classeOpcao = 'sm-option min-h-[64px] sm:min-h-[72px] !px-5 !py-3.5';
                    if (isEliminada) classeOpcao += ' is-eliminada';
                    if (isCorreta) classeOpcao += ' opcao-correta-reveal';
                    return `
                        <div class="${classeOpcao}">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <span class="sm-avatar !w-11 !h-11 !text-lg !font-black shrink-0">
                                    ${letras[i] || (i + 1)}
                                </span>
                                <span class="text-lg sm:text-xl font-black text-white truncate">${op}</span>
                            </div>
                            ${isCorreta ? '<span class="text-emerald-200 font-black text-xl shrink-0">✓</span>' : ''}
                        </div>
                    `;
                }).join('');
            }
        }

        async function sincronizarProjecao() {
            try {
                const resp = await fetch(`${BASE_APP_URL}/api/salas/{{ $sala->id }}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!resp.ok) return;
                const data = await resp.json();

                const isTempoEsgotado = Number(data.tempo_restante) <= 0 || !!data.tempo_esgotado || !!data.ajudas?.gabarito_revelado || tempoLocal <= 0;

                renderizarRanking(data.ranking || []);
                renderizarCorrida(data.ranking || []);
                renderizarRespostasRodada(data.participantes || [], data.respostas_rodada || {}, !!data.ajudas?.gabarito_revelado, isTempoEsgotado);
                atualizarPergunta(data);
            } catch (e) {}
        }

        // Execução imediata e sincronização contínua de alta fidelidade em tempo real (1.2 segundos)
        sincronizarProjecao();
        setInterval(sincronizarProjecao, 1200);
    </script>
</body>
</html>
