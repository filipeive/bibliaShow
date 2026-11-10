<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bíblia Show — Estúdio da Sala {{ $sala->codigo_acesso }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
    </style>
</head>
<body class="milhao-bg min-h-screen text-slate-100">

    @include('partials.navbar', ['dark' => true])

    @php
        $faseAtual = $fase_atual ?? 1;
        $placar = $placar_milhao ?? ['errar' => 0, 'parar' => 0, 'acertar' => 10, 'valor_pergunta' => 10, 'fase' => 1];
        $ajudasMotor = $ajudas_motor ?? [];
        $opcoesEliminadas = $ajudasMotor['opcoes_eliminadas'] ?? [];
        $gabaritoRevelado = !empty($ajudasMotor['gabarito_revelado']);
        $dicaBiblicaAtiva = $ajudasMotor['dica_biblica'] ?? null;
        $dicaConvidadoAtiva = $ajudasMotor['dica_convidado'] ?? null;
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
        <!-- Barra de Comando Superior da Sala -->
        <header class="flex flex-wrap justify-between items-center gap-4 mb-6 bg-slate-900/95 p-5 rounded-3xl shadow-xl border border-amber-500/30">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <span id="badgeFaseTopo" class="px-3 py-1 rounded-full bg-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider">
                        {{ $faseAtual }}ª FASE • VALE {{ $placar['valor_pergunta'] ?? 10 }} PTS
                    </span>
                    <span class="font-mono font-black text-sm bg-blue-950 text-amber-300 px-3 py-1 rounded-xl border border-blue-500/40">
                        SALA: {{ $sala->codigo_acesso }}
                    </span>
                    <span id="badgeEstadoSala" class="text-xs font-bold px-2.5 py-1 rounded-full {{ $estado === 'pausado' ? 'bg-yellow-500/20 text-yellow-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                        {{ ucfirst($estado) }}
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-white mt-1.5">{{ $sala->nome }}</h1>
            </div>

            <!-- Mesa de Áudio Clássico + Telas -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="tocarSomEstudio('abertura')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-amber-300 text-xs font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                    </svg>
                    Abertura
                </button>
                <button type="button" onclick="alternarSuspenseEstudio()" id="btnSuspenseEstudio"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-blue-300 text-xs font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z" />
                    </svg>
                    <span id="txtSuspenseEstudio">Suspense</span>
                </button>
                <a href="{{ route('entrar.view', $sala->codigo_acesso) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-500 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                    Smartphone
                </a>
                <a href="{{ route('projecao.tela', $sala->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 px-4 py-2 rounded-xl text-xs font-black shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    Abrir Telão (Projeção)
                </a>
            </div>
        </header>

        @if(session('success'))
            <div class="bg-emerald-950/90 border border-emerald-500/40 p-4 rounded-2xl mb-6 text-emerald-200 text-sm font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-950/90 border border-rose-500/40 p-4 rounded-2xl mb-6 text-rose-200 text-sm font-bold">
                {{ session('error') }}
            </div>
        @endif

        @if($estado === 'aguardando' || $estado === 'em_curso' || $estado === 'pausado')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Coluna Principal (2/3): Pergunta, Alternativas, Ajudas do Show do Milhão e Controles -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-slate-900/95 rounded-3xl shadow-2xl border border-slate-800 p-6">
                        <!-- Barra de Progresso das 4 Fases -->
                        <div class="grid grid-cols-4 gap-2 mb-5">
                            @foreach([1 => '10 pts', 2 => '20 pts', 3 => '30 pts', 4 => '50 pts'] as $numFase => $ptsTexto)
                                <div class="p-2.5 rounded-xl border text-center {{ $faseAtual === $numFase ? 'bg-amber-500/20 border-amber-400 text-amber-300' : 'bg-slate-950/70 border-slate-800 text-slate-500' }}">
                                    <span class="text-[10px] font-black uppercase block">{{ $numFase }}ª Fase</span>
                                    <span class="text-xs font-extrabold">{{ $ptsTexto }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-between items-center mb-5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span id="badgePerguntaNum" class="text-xs font-black uppercase tracking-wider px-3 py-1 rounded-full bg-blue-950 text-amber-300 border border-blue-500/30">
                                    Pergunta {{ $numero_pergunta }} de {{ $total_perguntas }}
                                </span>
                                <span id="badgeLivro" class="text-xs font-bold px-3 py-1 rounded-full bg-slate-800 text-slate-300 {{ empty($pergunta['livro_nome']) ? 'hidden' : '' }}">
                                    Livro: <span id="badgeLivroNome">{{ $pergunta['livro_nome'] ?? '' }}</span>
                                </span>
                                <span id="badgeGabaritoVisivel" class="text-xs font-black px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 {{ $gabaritoRevelado ? '' : 'hidden' }}">
                                    Gabarito Visível no Telão
                                </span>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Cronômetro</p>
                                <p class="text-2xl sm:text-3xl font-black text-amber-400 font-mono"><span id="presenterTimer">{{ $tempo_restante }}</span>s</p>
                            </div>
                        </div>

                        @if($pergunta)
                            <!-- Enunciado estilo Show do Milhão -->
                            <div class="bg-gradient-to-r from-red-950 via-red-900 to-red-950 border-2 border-amber-400/60 text-white rounded-2xl p-5 mb-5 shadow-lg">
                                <p id="textoPerguntaEstudio" class="text-lg sm:text-xl font-black leading-relaxed">
                                    {{ $pergunta['texto'] ?? $pergunta->texto }}
                                </p>
                            </div>

                            <!-- Alternativas com Gabarito para o Apresentador -->
                            @php
                                $opcoes = $pergunta['opcoes'] ?? [];
                                $opcoesArray = is_string($opcoes) ? json_decode($opcoes, true) : $opcoes;
                                $respostaCorreta = trim((string) ($pergunta['resposta_correta'] ?? ''));
                                $letras = ['A', 'B', 'C', 'D'];
                            @endphp
                            <div class="mb-5">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">
                                    Alternativas (Gabarito do Apresentador destacado em verde):
                                </p>
                                <div id="containerAlternativasEstudio" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($opcoesArray as $idx => $opcao)
                                        @php
                                            $ehCorreta = mb_strtolower(trim((string) $opcao), 'UTF-8') === mb_strtolower($respostaCorreta, 'UTF-8');
                                            $eliminada = in_array($idx, $opcoesEliminadas);
                                            $letra = $letras[$idx] ?? ($idx + 1);
                                        @endphp
                                        <div id="opcaoEstudio_{{ $idx }}" class="p-3.5 rounded-2xl border-2 flex items-center gap-3 {{ $eliminada ? 'opacity-40 border-slate-800 bg-slate-950 line-through' : ($ehCorreta ? 'border-emerald-500 bg-emerald-950/50 text-emerald-100' : 'border-slate-800 bg-slate-950/80 text-slate-200') }}">
                                            <span class="w-8 h-8 rounded-xl font-black flex items-center justify-center shrink-0 text-sm {{ $ehCorreta ? 'bg-emerald-500 text-slate-950' : 'bg-amber-500 text-slate-950' }}">
                                                {{ $letra }}
                                            </span>
                                            <span class="font-bold text-sm flex-1">{{ $opcao }}</span>
                                            @if($ehCorreta)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-black text-emerald-300 bg-emerald-500/20 border border-emerald-500/30 px-2 py-0.5 rounded-lg">
                                                    Correta
                                                </span>
                                            @elseif($eliminada)
                                                <span class="text-[10px] font-bold text-rose-400">50/50</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Ajudas do Clássico Bíblia Show (Ativáveis pelo Apresentador para o Telão/Smartphones) -->
                            @php
                                $ajudasQuestao = $pergunta['ajudas'] ?? [];
                                if (is_string($ajudasQuestao)) {
                                    $ajudasQuestao = json_decode($ajudasQuestao, true) ?: [];
                                }
                                $convidadosNomes = $ajudasQuestao['convidados'] ?? ['Paulo', 'Pedro', 'Moisés'];
                            @endphp
                            <div class="bg-slate-950/90 border border-amber-500/30 rounded-2xl p-4 mb-5">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                    <span class="text-xs font-black uppercase tracking-wider text-amber-400">
                                        Ajudas Clássicas para Telão & Smartphones
                                    </span>
                                    <span class="text-[11px] text-slate-400">
                                        Dica: <strong id="referenciaBiblicaAjuda">{{ $pergunta['referencia_biblica'] ?? 'Contexto Bíblico' }}</strong>
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                                    <!-- 1. Revelar Dica Bíblica -->
                                    <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST" class="form-ajax-control">
                                        @csrf
                                        <input type="hidden" name="tipo" value="dica">
                                        <button type="submit" id="btnAjudaDica"
                                                class="w-full p-2.5 rounded-xl border border-blue-500/40 bg-blue-950/70 hover:bg-blue-900 text-center transition">
                                            <span class="text-xs font-black text-white block">Dica Bíblica</span>
                                            <span id="txtDicasRestantes" class="text-[10px] text-amber-300">{{ $ajudasMotor['dicas_restantes'] ?? 3 }} restantes</span>
                                        </button>
                                    </form>

                                    <!-- 2. Cartas 50/50 -->
                                    <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST" class="form-ajax-control">
                                        @csrf
                                        <input type="hidden" name="tipo" value="cartas">
                                        <button type="submit" id="btnAjudaCartas" {{ !empty($ajudasMotor['cartas_usada']) ? 'disabled' : '' }}
                                                class="w-full p-2.5 rounded-xl border border-blue-500/40 bg-blue-950/70 hover:bg-blue-900 disabled:opacity-40 text-center transition">
                                            <span class="text-xs font-black text-white block">Cartas 50/50</span>
                                            <span id="txtCartasStatus" class="text-[10px] text-amber-300">{{ !empty($ajudasMotor['cartas_usada']) ? 'Usada' : 'Eliminar 2' }}</span>
                                        </button>
                                    </form>

                                    <!-- 3. Convidados Bíblicos -->
                                    @foreach($convidadosNomes as $idxConv => $nomeConv)
                                        @php
                                            $naoSabe = (($idxConv + 1) === (int) ($ajudasQuestao['errado'] ?? 2));
                                        @endphp
                                        <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST" class="form-ajax-control">
                                            @csrf
                                            <input type="hidden" name="tipo" value="convidado">
                                            <input type="hidden" name="indice_convidado" value="{{ $idxConv }}">
                                            <button type="submit"
                                                    class="w-full p-2.5 rounded-xl border {{ $naoSabe ? 'border-rose-500/30 bg-rose-950/40 hover:bg-rose-900/60' : 'border-emerald-500/30 bg-emerald-950/40 hover:bg-emerald-900/60' }} text-center transition">
                                                <span class="text-xs font-black text-white block truncate">{{ $nomeConv }}</span>
                                                <span class="text-[10px] {{ $naoSabe ? 'text-rose-300' : 'text-emerald-300' }}">
                                                    {{ $naoSabe ? 'Não sabe' : 'Sabe a certa' }}
                                                </span>
                                            </button>
                                        </form>
                                    @endforeach
                                </div>

                                <div id="boxDicasAtivasContainer" class="mt-3 pt-3 border-t border-slate-800 text-xs space-y-1 text-amber-200 {{ ($dicaBiblicaAtiva || $dicaConvidadoAtiva) ? '' : 'hidden' }}">
                                    <p id="boxTextoDicaBiblica" class="{{ $dicaBiblicaAtiva ? '' : 'hidden' }}">
                                        <strong>Dica exibida no telão:</strong> <span id="conteudoDicaBiblica">{{ $dicaBiblicaAtiva }}</span>
                                    </p>
                                    <p id="boxTextoDicaConvidado" class="{{ $dicaConvidadoAtiva ? '' : 'hidden' }}">
                                        <strong id="nomeConvidadoDica">{{ $dicaConvidadoAtiva['convidado'] ?? '' }}:</strong> <span id="conteudoDicaConvidado">{{ $dicaConvidadoAtiva['mensagem'] ?? '' }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Botões Principais de Controle da Rodada -->
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                                <!-- Revelar Gabarito no Telão -->
                                <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST" class="form-ajax-control">
                                    @csrf
                                    <input type="hidden" name="tipo" value="revelar">
                                    <button type="submit" id="btnToggleGabarito"
                                            class="w-full h-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-3 px-3 rounded-xl transition shadow text-xs flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span id="txtBtnGabarito">{{ $gabaritoRevelado ? 'Ocultar Gabarito' : 'Revelar no Telão' }}</span>
                                    </button>
                                </form>

                                <!-- Pular Pergunta -->
                                <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST" class="form-ajax-control">
                                    @csrf
                                    <input type="hidden" name="tipo" value="pulo">
                                    <button type="submit" id="btnPulo"
                                            class="w-full h-full bg-slate-800 hover:bg-slate-700 border border-slate-700 text-amber-300 font-bold py-3 px-3 rounded-xl transition text-xs flex items-center justify-center gap-1.5">
                                        <span id="txtBtnPulo">Pular ({{ $ajudasMotor['pulos_restantes'] ?? 3 }})</span>
                                    </button>
                                </form>

                                <div id="containerBtnPausarRetomar" class="w-full h-full">
                                    @if($estado !== 'pausado')
                                        <form action="{{ route('apresentador.pausar', $sala->id) }}" method="POST" class="form-ajax-control h-full">
                                            @csrf
                                            <button type="submit" class="w-full h-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-black py-3 px-3 rounded-xl transition shadow text-xs flex items-center justify-center gap-1.5">
                                                Pausar
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('apresentador.retomar', $sala->id) }}" method="POST" class="form-ajax-control h-full">
                                            @csrf
                                            <button type="submit" class="w-full h-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black py-3 px-3 rounded-xl transition shadow text-xs flex items-center justify-center gap-1.5">
                                                Retomar
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <form action="{{ route('apresentador.proxima', $sala->id) }}" method="POST" class="form-ajax-control">
                                    @csrf
                                    <button type="submit" id="btnProximaPergunta" class="w-full h-full bg-blue-600 hover:bg-blue-500 text-white font-black py-3 px-3 rounded-xl transition shadow text-xs flex items-center justify-center gap-1.5">
                                        Próxima →
                                    </button>
                                </form>

                                <form action="{{ route('apresentador.finalizar', $sala->id) }}" method="POST" class="form-ajax-control">
                                    @csrf
                                    <button type="submit" class="w-full h-full bg-rose-600 hover:bg-rose-500 text-white font-black py-3 px-3 rounded-xl transition shadow text-xs flex items-center justify-center gap-1.5">
                                        Encerrar
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Coluna Lateral (1/3): Ranking Ao Vivo, Equipes e QR Code -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Ranking de Participantes -->
                    <div class="bg-slate-900/95 rounded-3xl shadow-2xl border border-slate-800 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-black text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                Ranking Ao Vivo
                            </h3>
                            <span id="contadorParticipantes" class="text-xs font-black bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2.5 py-0.5 rounded-full">
                                {{ $participantes->count() }} jogadores
                            </span>
                        </div>

                        <div id="participantesList" class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            @forelse($participantes as $idx => $participante)
                                <div class="flex items-center justify-between p-3 bg-slate-950/80 rounded-2xl border border-slate-800">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-300 flex items-center justify-center text-xs font-black shrink-0">
                                            {{ $idx + 1 }}º
                                        </div>
                                        <div class="truncate">
                                            <span class="text-sm font-bold text-white block truncate">{{ $participante->nome }}</span>
                                            @if($participante->equipe)
                                                <span class="text-xs text-slate-400">{{ $participante->equipe->nome }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-xs font-mono font-black text-amber-300 bg-amber-500/15 px-2.5 py-1 rounded-lg shrink-0">
                                        {{ $participante->pontuacao_total }} pts
                                    </span>
                                </div>
                            @empty
                                <div class="p-4 bg-slate-950/60 rounded-2xl text-center text-xs text-slate-400">
                                    Aguardando participantes entrarem com o código <strong>{{ $sala->codigo_acesso }}</strong>...
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Prêmios para Vencedores (1º e 2º Lugar) -->
                    <div class="bg-slate-900/95 rounded-3xl shadow-2xl border border-amber-500/30 p-5">
                        <h3 class="text-xs font-black text-amber-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                            </svg>
                            Prêmios dos Vencedores (1º e 2º)
                        </h3>
                        <form action="{{ route('apresentador.premios', $sala->id) }}" method="POST" class="space-y-2.5 form-ajax-control">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-amber-300 mb-1">1º Lugar (Campeão)</label>
                                <input type="text" name="premio_primeiro_lugar" value="{{ $sala->premio_primeiro_lugar }}"
                                       placeholder="Ex.: Bíblia de Estudo + Troféu"
                                       class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white outline-none focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-300 mb-1">2º Lugar (Vice-Campeão)</label>
                                <input type="text" name="premio_segundo_lugar" value="{{ $sala->premio_segundo_lugar }}"
                                       placeholder="Ex.: Livro Devocional + Medalha"
                                       class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white outline-none focus:border-amber-500">
                            </div>
                            <button type="submit" class="w-full bg-amber-500/20 hover:bg-amber-500 text-amber-300 hover:text-slate-950 border border-amber-500/40 font-black py-2 rounded-xl text-xs transition">
                                Salvar Prêmios da Sala
                            </button>
                        </form>
                    </div>

                    <!-- Criar Equipe / Célula na Sala -->
                    <div class="bg-slate-900/95 rounded-3xl shadow-2xl border border-slate-800 p-5">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-3">Criar Equipe / Célula</h3>
                        <form action="{{ route('apresentador.equipe.criar', $sala->id) }}" method="POST" class="flex gap-2 form-ajax-control">
                            @csrf
                            <input type="text" name="nome" required placeholder="Ex.: Tribo de Judá"
                                   class="flex-1 px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white outline-none focus:border-amber-500">
                            <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-black px-3.5 py-2 rounded-xl text-xs">
                                + Equipe
                            </button>
                        </form>
                        <div id="listaEquipesContainer" class="flex flex-wrap gap-1.5 mt-3">
                            @foreach($equipes as $eq)
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-slate-300">
                                    {{ $eq->nome }} ({{ $eq->pontuacao_total }} pts)
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- QR Code de Entrada -->
                    <div class="bg-slate-900/95 rounded-3xl shadow-2xl border border-slate-800 p-6 text-center">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-3">
                            QR Code para Smartphones
                        </h3>
                        <div class="flex flex-col items-center gap-3">
                            <div id="qrcode" class="p-3 bg-white rounded-2xl shadow-inner"></div>
                            <div class="text-xl font-mono font-black text-amber-300 bg-amber-500/15 px-5 py-1.5 rounded-xl border border-amber-500/30">
                                {{ $sala->codigo_acesso }}
                            </div>
                            <p class="text-xs text-slate-400">
                                Link direto: <a href="{{ route('entrar.view', $sala->codigo_acesso) }}" target="_blank" class="text-amber-400 underline font-bold">/entrar/{{ $sala->codigo_acesso }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($estado === 'terminado')
            <div class="bg-slate-900/95 rounded-3xl shadow-2xl border border-amber-500/40 p-8 text-center">
                <h2 class="text-2xl font-black text-white mb-2">Partida Finalizada</h2>
                <p class="text-slate-300 mb-6">Pontuação acumulada na sala: <span class="font-black text-amber-400 text-xl">{{ $pontuacao }} pts</span></p>

                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('apresentador.control', ['sala' => $sala->id, 'nova_partida' => 1]) }}"
                       class="bg-amber-500 hover:bg-amber-400 text-slate-950 px-6 py-3 rounded-xl font-black shadow">
                        Iniciar Nova Partida nesta Sala
                    </a>
                    <a href="{{ route('apresentador.resultado', $sala->id) }}"
                       class="bg-slate-800 hover:bg-slate-700 text-white px-6 py-3 rounded-xl font-bold border border-slate-700">
                        Ver Relatório e Pódio Final
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        const qrContainer = document.getElementById('qrcode');
        if (qrContainer && typeof QRCode !== 'undefined') {
            new QRCode(qrContainer, {
                text: window.location.origin + '/entrar/{{ $sala->codigo_acesso }}',
                width: 145,
                height: 145,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H,
            });
        }

        const audioSuspense = new Audio('/audio/classico/suspense.wav');
        audioSuspense.loop = true;
        audioSuspense.volume = 0.3;
        let suspenseTocando = false;

        function tocarSomEstudio(nome) {
            const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
            if (!ativo) return;
            const fx = new Audio(`/audio/classico/${nome}.wav`);
            fx.volume = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85;
            fx.play().catch(() => {});
        }

        function alternarSuspenseEstudio() {
            const txt = document.getElementById('txtSuspenseEstudio');
            if (suspenseTocando) {
                audioSuspense.pause();
                suspenseTocando = false;
                if (txt) txt.textContent = 'Suspense';
            } else {
                audioSuspense.volume = (typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85) * 0.35;
                audioSuspense.play().catch(() => {});
                suspenseTocando = true;
                if (txt) txt.textContent = 'Parar Suspense';
            }
        }

        window.addEventListener('biblia-som-alterado', (ev) => {
            if (!ev.detail.ativo && suspenseTocando) {
                audioSuspense.pause();
                suspenseTocando = false;
                const txt = document.getElementById('txtSuspenseEstudio');
                if (txt) txt.textContent = 'Suspense';
            }
        });

        window.addEventListener('DOMContentLoaded', () => {
            @if(session('som_efeito'))
                tocarSomEstudio('{{ session('som_efeito') }}');
            @endif
        });

        // Interceptador assíncrono para os botões do estúdio
        const letrasAlfabeto = ['A', 'B', 'C', 'D'];
        let gabaritoVisivelTelao = {{ $gabaritoRevelado ? 'true' : 'false' }};
        let estadoSalaAtual = '{{ $estado }}';
        let respostaCorretaAtual = '{{ addslashes($respostaCorreta ?? '') }}';

        function mostrarToastEstudio(mensagem, sucesso = true) {
            const toast = document.getElementById('toastEstudio');
            const content = document.getElementById('toastEstudioContent');
            const msg = document.getElementById('toastEstudioMsg');
            if (!toast || !content || !msg) return;

            msg.textContent = mensagem;
            content.className = sucesso
                ? 'px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-3 border bg-emerald-950/95 border-emerald-500/50 text-emerald-200'
                : 'px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-3 border bg-rose-950/95 border-rose-500/50 text-rose-200';

            toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
            }, 3000);
        }

        document.addEventListener('submit', async (e) => {
            const form = e.target.closest('.form-ajax-control');
            if (!form) return;
            e.preventDefault();

            const btnSubmit = form.querySelector('button[type="submit"]');
            if (btnSubmit) btnSubmit.disabled = true;

            try {
                const formData = new FormData(form);
                const resp = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                const data = await resp.json().catch(() => ({}));

                if (btnSubmit) btnSubmit.disabled = false;

                if (!resp.ok) {
                    mostrarToastEstudio(data.mensagem || 'Ocorreu um erro na ação.', false);
                    return;
                }

                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                if (data.som_efeito) {
                    tocarSomEstudio(data.som_efeito);
                }

                if (data.mensagem) {
                    mostrarToastEstudio(data.mensagem, true);
                }

                // 1. Atualizar Próxima Pergunta
                if (data.pergunta) {
                    atualizarDOMPerguntaEstudio(data);
                }

                // 2. Atualizar Estado (Pausado / Em Curso)
                if (data.estado) {
                    atualizarDOMEstadoEstudio(data.estado);
                }

                // 3. Atualizar Ajudas
                if (data.ajudas) {
                    atualizarDOMAjudasEstudio(data.ajudas);
                }

                // 4. Nova Equipe criada
                if (data.equipe) {
                    const c = document.getElementById('listaEquipesContainer');
                    if (c) {
                        const span = document.createElement('span');
                        span.className = 'text-[11px] font-bold px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-slate-300';
                        span.textContent = `${data.equipe.nome} (0 pts)`;
                        c.appendChild(span);
                    }
                    form.reset();
                }

            } catch (err) {
                if (btnSubmit) btnSubmit.disabled = false;
                mostrarToastEstudio('Erro de conexão ao executar comando.', false);
            }
        });

        function atualizarDOMPerguntaEstudio(data) {
            const q = data.pergunta;
            respostaCorretaAtual = (q.resposta_correta || '').trim();

            const enunciado = document.getElementById('textoPerguntaEstudio');
            if (enunciado) enunciado.textContent = q.texto;

            const badgeNum = document.getElementById('badgePerguntaNum');
            if (badgeNum) badgeNum.textContent = `Pergunta ${data.numero_pergunta || 1} de ${data.total_perguntas || 24}`;

            const badgeFase = document.getElementById('badgeFaseTopo');
            if (badgeFase && data.placar_milhao) {
                badgeFase.textContent = `${data.fase_atual}ª FASE • VALE ${data.placar_milhao.valor_pergunta || 10} PTS`;
            }

            const badgeLivro = document.getElementById('badgeLivro');
            const badgeLivroNome = document.getElementById('badgeLivroNome');
            if (badgeLivro && badgeLivroNome) {
                if (q.livro_nome) {
                    badgeLivroNome.textContent = q.livro_nome;
                    badgeLivro.classList.remove('hidden');
                } else {
                    badgeLivro.classList.add('hidden');
                }
            }

            // Alternativas
            const containerAlt = document.getElementById('containerAlternativasEstudio');
            if (containerAlt && Array.isArray(q.opcoes)) {
                containerAlt.innerHTML = q.opcoes.map((opcao, idx) => {
                    const ehCorreta = String(opcao).trim().toLowerCase() === respostaCorretaAtual.toLowerCase();
                    const letra = letrasAlfabeto[idx] || (idx + 1);
                    return `
                        <div id="opcaoEstudio_${idx}" class="p-3.5 rounded-2xl border-2 flex items-center gap-3 ${ehCorreta ? 'border-emerald-500 bg-emerald-950/50 text-emerald-100' : 'border-slate-800 bg-slate-950/80 text-slate-200'}">
                            <span class="w-8 h-8 rounded-xl font-black flex items-center justify-center shrink-0 text-sm ${ehCorreta ? 'bg-emerald-500 text-slate-950' : 'bg-amber-500 text-slate-950'}">
                                ${letra}
                            </span>
                            <span class="font-bold text-sm flex-1">${opcao}</span>
                            ${ehCorreta ? '<span class="inline-flex items-center gap-1 text-[11px] font-black text-emerald-300 bg-emerald-500/20 border border-emerald-500/30 px-2 py-0.5 rounded-lg">Correta</span>' : ''}
                        </div>
                    `;
                }).join('');
            }

            // Resetar caixas de ajuda
            const refBiblica = document.getElementById('referenciaBiblicaAjuda');
            if (refBiblica) refBiblica.textContent = q.referencia_biblica || 'Contexto Bíblico';

            const boxAjudas = document.getElementById('boxDicasAtivasContainer');
            if (boxAjudas) boxAjudas.classList.add('hidden');

            const badgeGab = document.getElementById('badgeGabaritoVisivel');
            if (badgeGab) badgeGab.classList.add('hidden');

            const txtGab = document.getElementById('txtBtnGabarito');
            if (txtGab) txtGab.textContent = 'Revelar no Telão';
            gabaritoVisivelTelao = false;
        }

        function atualizarDOMEstadoEstudio(estado) {
            estadoSalaAtual = estado;
            const badgeEstado = document.getElementById('badgeEstadoSala');
            if (badgeEstado) {
                badgeEstado.textContent = estado === 'pausado' ? 'Pausado' : 'Em curso';
                badgeEstado.className = 'text-xs font-bold px-2.5 py-1 rounded-full ' + (estado === 'pausado' ? 'bg-yellow-500/20 text-yellow-300' : 'bg-emerald-500/20 text-emerald-300');
            }

            const containerBtn = document.getElementById('containerBtnPausarRetomar');
            if (containerBtn) {
                if (estado === 'pausado') {
                    containerBtn.innerHTML = `
                        <form action="/apresentador/control/{{ $sala->id }}/retomar" method="POST" class="form-ajax-control h-full">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                            <button type="submit" class="w-full h-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black py-3 px-3 rounded-xl transition shadow text-xs flex items-center justify-center gap-1.5">
                                Retomar
                            </button>
                        </form>
                    `;
                } else {
                    containerBtn.innerHTML = `
                        <form action="/apresentador/control/{{ $sala->id }}/pausar" method="POST" class="form-ajax-control h-full">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''}">
                            <button type="submit" class="w-full h-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-black py-3 px-3 rounded-xl transition shadow text-xs flex items-center justify-center gap-1.5">
                                Pausar
                            </button>
                        </form>
                    `;
                }
            }
        }

        function atualizarDOMAjudasEstudio(ajudas) {
            // 1. Revelar gabarito
            if (ajudas.gabarito_revelado !== undefined) {
                gabaritoVisivelTelao = !!ajudas.gabarito_revelado;
                const badgeGab = document.getElementById('badgeGabaritoVisivel');
                if (badgeGab) {
                    if (gabaritoVisivelTelao) badgeGab.classList.remove('hidden');
                    else badgeGab.classList.add('hidden');
                }
                const txtGab = document.getElementById('txtBtnGabarito');
                if (txtGab) {
                    txtGab.textContent = gabaritoVisivelTelao ? 'Ocultar Gabarito' : 'Revelar no Telão';
                }
            }

            // 2. Cartas 50/50 eliminadas
            if (Array.isArray(ajudas.opcoes_eliminadas)) {
                ajudas.opcoes_eliminadas.forEach(idx => {
                    const el = document.getElementById(`opcaoEstudio_${idx}`);
                    if (el) {
                        el.className = 'p-3.5 rounded-2xl border-2 flex items-center gap-3 opacity-40 border-slate-800 bg-slate-950 line-through';
                    }
                });
                const btnCartas = document.getElementById('btnAjudaCartas');
                const txtCartas = document.getElementById('txtCartasStatus');
                if (btnCartas && ajudas.cartas_usada) {
                    btnCartas.disabled = true;
                    if (txtCartas) txtCartas.textContent = 'Usada';
                }
            }

            // 3. Dica bíblica
            if (ajudas.dica_biblica) {
                const boxAjudas = document.getElementById('boxDicasAtivasContainer');
                const boxDica = document.getElementById('boxTextoDicaBiblica');
                const conteudo = document.getElementById('conteudoDicaBiblica');
                if (boxAjudas && boxDica && conteudo) {
                    conteudo.textContent = ajudas.dica_biblica;
                    boxDica.classList.remove('hidden');
                    boxAjudas.classList.remove('hidden');
                }
                const txtDicas = document.getElementById('txtDicasRestantes');
                if (txtDicas && ajudas.dicas_restantes !== undefined) {
                    txtDicas.textContent = `${ajudas.dicas_restantes} restantes`;
                }
            }

            // 4. Convidado bíblico
            if (ajudas.dica_convidado) {
                const boxAjudas = document.getElementById('boxDicasAtivasContainer');
                const boxConv = document.getElementById('boxTextoDicaConvidado');
                const nomeConv = document.getElementById('nomeConvidadoDica');
                const conteudoConv = document.getElementById('conteudoDicaConvidado');
                if (boxAjudas && boxConv && nomeConv && conteudoConv) {
                    nomeConv.textContent = ajudas.dica_convidado.convidado + ':';
                    conteudoConv.textContent = ajudas.dica_convidado.mensagem;
                    boxConv.classList.remove('hidden');
                    boxAjudas.classList.remove('hidden');
                }
            }

            // 5. Pulos restantes
            if (ajudas.pulos_restantes !== undefined) {
                const txtPulo = document.getElementById('txtBtnPulo');
                if (txtPulo) txtPulo.textContent = `Pular (${ajudas.pulos_restantes})`;
            }
        }

        async function sincronizarParticipantes() {
            try {
                const resp = await fetch('/api/salas/{{ $sala->id }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!resp.ok) return;
                const data = await resp.json();

                const list = document.getElementById('participantesList');
                const contador = document.getElementById('contadorParticipantes');
                if (!list || !data.ranking) return;

                if (contador) {
                    contador.textContent = data.ranking.length + ' jogadores';
                }

                if (data.ranking.length === 0) {
                    list.innerHTML = '<div class="p-4 bg-slate-950/60 rounded-2xl text-center text-xs text-slate-400">Aguardando participantes entrarem com o código <strong>{{ $sala->codigo_acesso }}</strong>...</div>';
                    return;
                }

                list.innerHTML = data.ranking.map((p, idx) => `
                    <div class="flex items-center justify-between p-3 bg-slate-950/80 rounded-2xl border border-slate-800">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-300 flex items-center justify-center text-xs font-black shrink-0">
                                ${idx + 1}º
                            </div>
                            <div class="truncate">
                                <span class="text-sm font-bold text-white block truncate">${p.nome}</span>
                                ${p.equipe ? `<span class="text-xs text-slate-400">${p.equipe}</span>` : ''}
                            </div>
                        </div>
                        <span class="text-xs font-mono font-black text-amber-300 bg-amber-500/15 px-2.5 py-1 rounded-lg shrink-0">
                            ${p.pontuacao} pts
                        </span>
                    </div>
                `).join('');
            } catch (e) {}
        }

        // Sincronização em tempo real ágil (1.5 segundos)
        setInterval(sincronizarParticipantes, 1500);
    </script>

    <!-- Toast de Notificação Estúdio em Tempo Real -->
    <div id="toastEstudio" class="fixed bottom-6 right-6 z-50 transform translate-y-24 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-3 border text-white" id="toastEstudioContent">
            <span id="toastEstudioMsg"></span>
        </div>
    </div>
</body>
</html>
