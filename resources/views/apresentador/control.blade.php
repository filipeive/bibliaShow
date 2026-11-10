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
                    <span class="px-3 py-1 rounded-full bg-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider">
                        {{ $faseAtual }}ª FASE • VALE {{ $placar['valor_pergunta'] ?? 10 }} PTS
                    </span>
                    <span class="font-mono font-black text-sm bg-blue-950 text-amber-300 px-3 py-1 rounded-xl border border-blue-500/40">
                        SALA: {{ $sala->codigo_acesso }}
                    </span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $estado === 'pausado' ? 'bg-yellow-500/20 text-yellow-300' : 'bg-emerald-500/20 text-emerald-300' }}">
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
                                <span class="text-xs font-black uppercase tracking-wider px-3 py-1 rounded-full bg-blue-950 text-amber-300 border border-blue-500/30">
                                    Pergunta {{ $numero_pergunta }} de {{ $total_perguntas }}
                                </span>
                                @if(!empty($pergunta['livro_nome']))
                                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-800 text-slate-300">
                                        Livro: {{ $pergunta['livro_nome'] }}
                                    </span>
                                @endif
                                @if($gabaritoRevelado)
                                    <span class="text-xs font-black px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                        Gabarito Visível no Telão
                                    </span>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Cronômetro</p>
                                <p class="text-2xl sm:text-3xl font-black text-amber-400 font-mono"><span id="presenterTimer">{{ $tempo_restante }}</span>s</p>
                            </div>
                        </div>

                        @if($pergunta)
                            <!-- Enunciado estilo Show do Milhão -->
                            <div class="bg-gradient-to-r from-red-950 via-red-900 to-red-950 border-2 border-amber-400/60 text-white rounded-2xl p-5 mb-5 shadow-lg">
                                <p class="text-lg sm:text-xl font-black leading-relaxed">
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
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($opcoesArray as $idx => $opcao)
                                        @php
                                            $ehCorreta = mb_strtolower(trim((string) $opcao), 'UTF-8') === mb_strtolower($respostaCorreta, 'UTF-8');
                                            $eliminada = in_array($idx, $opcoesEliminadas);
                                            $letra = $letras[$idx] ?? ($idx + 1);
                                        @endphp
                                        <div class="p-3.5 rounded-2xl border-2 flex items-center gap-3 {{ $eliminada ? 'opacity-40 border-slate-800 bg-slate-950 line-through' : ($ehCorreta ? 'border-emerald-500 bg-emerald-950/50 text-emerald-100' : 'border-slate-800 bg-slate-950/80 text-slate-200') }}">
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
                                        Dica: <strong>{{ $pergunta['referencia_biblica'] ?? 'Contexto Bíblico' }}</strong>
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                                    <!-- 1. Revelar Dica Bíblica -->
                                    <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tipo" value="dica">
                                        <button type="submit"
                                                class="w-full p-2.5 rounded-xl border border-blue-500/40 bg-blue-950/70 hover:bg-blue-900 text-center transition">
                                            <span class="text-xs font-black text-white block">Dica Bíblica</span>
                                            <span class="text-[10px] text-amber-300">{{ $ajudasMotor['dicas_restantes'] ?? 3 }} restantes</span>
                                        </button>
                                    </form>

                                    <!-- 2. Cartas 50/50 -->
                                    <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tipo" value="cartas">
                                        <button type="submit" {{ !empty($ajudasMotor['cartas_usada']) ? 'disabled' : '' }}
                                                class="w-full p-2.5 rounded-xl border border-blue-500/40 bg-blue-950/70 hover:bg-blue-900 disabled:opacity-40 text-center transition">
                                            <span class="text-xs font-black text-white block">Cartas 50/50</span>
                                            <span class="text-[10px] text-amber-300">{{ !empty($ajudasMotor['cartas_usada']) ? 'Usada' : 'Eliminar 2' }}</span>
                                        </button>
                                    </form>

                                    <!-- 3. Convidados Bíblicos -->
                                    @foreach($convidadosNomes as $idxConv => $nomeConv)
                                        @php
                                            $naoSabe = (($idxConv + 1) === (int) ($ajudasQuestao['errado'] ?? 2));
                                        @endphp
                                        <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST">
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

                                @if($dicaBiblicaAtiva || $dicaConvidadoAtiva)
                                    <div class="mt-3 pt-3 border-t border-slate-800 text-xs space-y-1 text-amber-200">
                                        @if($dicaBiblicaAtiva)
                                            <p><strong>Dica exibida no telão:</strong> {{ $dicaBiblicaAtiva }}</p>
                                        @endif
                                        @if($dicaConvidadoAtiva)
                                            <p><strong>{{ $dicaConvidadoAtiva['convidado'] }}:</strong> {{ $dicaConvidadoAtiva['mensagem'] }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Botões Principais de Controle da Rodada -->
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                                <!-- Revelar Gabarito no Telão -->
                                <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tipo" value="revelar">
                                    <button type="submit"
                                            class="w-full h-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-3 px-3 rounded-xl transition shadow text-xs flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ $gabaritoRevelado ? 'Ocultar Gabarito' : 'Revelar no Telão' }}</span>
                                    </button>
                                </form>

                                <!-- Pular Pergunta -->
                                <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tipo" value="pulo">
                                    <button type="submit"
                                            class="w-full h-full bg-slate-800 hover:bg-slate-700 border border-slate-700 text-amber-300 font-bold py-3 px-3 rounded-xl transition text-xs flex items-center justify-center gap-1.5">
                                        <span>Pular ({{ $ajudasMotor['pulos_restantes'] ?? 3 }})</span>
                                    </button>
                                </form>

                                @if($estado !== 'pausado')
                                    <form action="{{ route('apresentador.pausar', $sala->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full h-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-black py-3 px-3 rounded-xl transition shadow text-xs flex items-center justify-center gap-1.5">
                                            Pausar
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('apresentador.retomar', $sala->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full h-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black py-3 px-3 rounded-xl transition shadow text-xs flex items-center justify-center gap-1.5">
                                            Retomar
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('apresentador.proxima', $sala->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full h-full bg-blue-600 hover:bg-blue-500 text-white font-black py-3 px-3 rounded-xl transition shadow text-xs flex items-center justify-center gap-1.5">
                                        Próxima →
                                    </button>
                                </form>

                                <form action="{{ route('apresentador.finalizar', $sala->id) }}" method="POST">
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

                    <!-- Criar Equipe / Célula na Sala -->
                    <div class="bg-slate-900/95 rounded-3xl shadow-2xl border border-slate-800 p-5">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-3">Criar Equipe / Célula</h3>
                        <form action="{{ route('apresentador.equipe.criar', $sala->id) }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="text" name="nome" required placeholder="Ex.: Tribo de Judá"
                                   class="flex-1 px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white outline-none focus:border-amber-500">
                            <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-black px-3.5 py-2 rounded-xl text-xs">
                                + Equipe
                            </button>
                        </form>
                        @if($equipes->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @foreach($equipes as $eq)
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-slate-300">
                                        {{ $eq->nome }} ({{ $eq->pontuacao_total }} pts)
                                    </span>
                                @endforeach
                            </div>
                        @endif
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
        audioSuspense.volume = 0.35;
        let suspenseTocando = false;

        function tocarSomEstudio(nome) {
            new Audio(`/audio/classico/${nome}.wav`).play().catch(() => {});
        }

        function alternarSuspenseEstudio() {
            const txt = document.getElementById('txtSuspenseEstudio');
            if (suspenseTocando) {
                audioSuspense.pause();
                suspenseTocando = false;
                if (txt) txt.textContent = 'Suspense';
            } else {
                audioSuspense.play().catch(() => {});
                suspenseTocando = true;
                if (txt) txt.textContent = 'Parar Suspense';
            }
        }

        @if(session('som_efeito'))
            window.addEventListener('DOMContentLoaded', () => {
                tocarSomEstudio('{{ session('som_efeito') }}');
            });
        @endif

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

        setInterval(sincronizarParticipantes, 2500);
    </script>

</body>
</html>
