<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bíblia Show — Estúdio da Sala {{ $sala->codigo_acesso }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* ===== Consola do Estúdio (classes partilhadas entre Blade e JS) ===== */
        .st-question-text { font-size: clamp(1.05rem, 2.5vh + .2rem, 1.8rem); line-height: 1.3; }
        .st-opcao {
            display: flex; align-items: center; gap: .65rem; padding: .55rem .75rem;
            border-radius: 1rem; border: 2px solid rgba(96, 165, 250, .3); background: rgba(23, 37, 84, .7);
            color: #f1f5f9; transition: all .2s ease;
        }
        .st-opcao .st-letra {
            width: 1.9rem; height: 1.9rem; border-radius: .65rem; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: .85rem;
            background: linear-gradient(180deg, #fde68a 0%, #f59e0b 55%, #b45309 100%); color: #3b1d00; border: 2px solid #fff3c4;
        }
        .st-opcao .st-tag { font-size: .65rem; font-weight: 900; text-transform: uppercase; letter-spacing: .05em; padding: .1rem .5rem; border-radius: .5rem; flex-shrink: 0; }
        .st-opcao.is-correta { border-color: #34d399; background: linear-gradient(90deg, rgba(6, 78, 59, .75), rgba(23, 37, 84, .9)); color: #d1fae5; box-shadow: 0 0 15px rgba(16, 185, 129, .25); }
        .st-opcao.is-correta .st-letra { background: #34d399; border-color: #a7f3d0; color: #022c22; }
        .st-opcao.is-correta .st-tag { color: #6ee7b7; background: rgba(16, 185, 129, .2); border: 1px solid rgba(52, 211, 153, .4); }
        .st-opcao.is-eliminada { opacity: .35; text-decoration: line-through; border-color: rgba(30, 58, 138, .6); box-shadow: none; }
        .st-opcao.is-eliminada .st-tag { color: #fb7185; background: rgba(244, 63, 94, .1); border: 1px solid rgba(244, 63, 94, .3); }

        .st-btn {
            width: 100%; height: 100%; min-height: 2.9rem; display: flex; align-items: center; justify-content: center; gap: .45rem;
            padding: .55rem .75rem; border-radius: .9rem; border: 2px solid transparent;
            font-size: .75rem; font-weight: 900; text-transform: uppercase; letter-spacing: .03em; line-height: 1.1;
            transition: filter .12s ease, transform .12s ease;
        }
        .st-btn:hover:not(:disabled) { filter: brightness(1.12); }
        .st-btn:active:not(:disabled) { transform: scale(.97); }
        .st-btn:disabled { opacity: .4; cursor: not-allowed; }
        .st-btn.is-emerald { background: #059669; border-color: rgba(110, 231, 183, .5); color: #fff; }
        .st-btn.is-blue { background: rgba(23, 37, 84, .85); border-color: rgba(96, 165, 250, .45); color: #fcd34d; }
        .st-btn.is-amber { background: #f59e0b; border-color: #fde68a; color: #1c0f00; }
        .st-btn.is-go { background: #10b981; border-color: #a7f3d0; color: #022c22; }
        .st-btn.is-rose { background: rgba(190, 18, 60, .85); border-color: rgba(253, 164, 175, .5); color: #fff; }
        @media (max-width: 639px) {
            .st-btn { font-size: .62rem; letter-spacing: 0; padding: .5rem .2rem; gap: .2rem; }
            .st-question-text { font-size: 1.1rem; }
        }

        .st-ajuda { width: 100%; padding: .45rem .5rem; border-radius: .8rem; border: 1px solid rgba(96, 165, 250, .4); background: rgba(23, 37, 84, .8); text-align: center; transition: all .15s ease; }
        .st-ajuda:hover:not(:disabled) { background: rgba(30, 58, 138, .9); }
        .st-ajuda:active:not(:disabled) { transform: scale(.97); }
        .st-ajuda:disabled { opacity: .4; cursor: not-allowed; }
        .st-ajuda.is-sabe { border-color: rgba(16, 185, 129, .45); background: rgba(6, 78, 59, .5); }
        .st-ajuda.is-nao-sabe { border-color: rgba(244, 63, 94, .45); background: rgba(76, 5, 25, .5); }

        .st-som { display: inline-flex; align-items: center; gap: .35rem; padding: .4rem .6rem; border-radius: .7rem; font-size: .7rem; font-weight: 800; background: rgba(23, 37, 84, .8); border: 1px solid rgba(96, 165, 250, .3); transition: all .15s ease; }
        .st-som:hover { background: rgba(30, 58, 138, .9); }
        .st-som:active { transform: scale(.95); }
        .st-som.is-on { background: rgba(245, 158, 11, .25); border-color: #fbbf24; color: #fde68a; }

        .st-fase { padding: .2rem .55rem; border-radius: .6rem; font-size: .65rem; font-weight: 900; text-transform: uppercase; letter-spacing: .04em; border: 1px solid rgba(30, 58, 138, .7); background: rgba(23, 37, 84, .5); color: rgba(147, 197, 253, .6); white-space: nowrap; }
        .st-fase.is-done { color: #6ee7b7; border-color: rgba(52, 211, 153, .35); }
        .st-fase.is-current { color: #3b1d00; border-color: #fff3c4; background: linear-gradient(180deg, #fde68a, #f59e0b); box-shadow: 0 0 12px rgba(245, 158, 11, .35); }

        .st-timer { transition: color .2s ease; }
        .st-timer.is-critico { color: #fb7185 !important; animation: sm-pulse .8s ease-in-out infinite; }

        details.st-collapse > summary { list-style: none; cursor: pointer; }
        details.st-collapse > summary::-webkit-details-marker { display: none; }
        details.st-collapse[open] .st-chevron { transform: rotate(180deg); }
    </style>
</head>
<body class="sm-stage sm-viewport-lg min-h-screen lg:min-h-0 text-slate-100 flex flex-col selection:bg-amber-400 selection:text-slate-950">

    @include('partials.navbar', ['dark' => true, 'compact' => true, 'hideJogarAgora' => true])

    @php
        $faseAtual = $fase_atual ?? 1;
        $placar = $placar_milhao ?? ['errar' => 0, 'parar' => 0, 'acertar' => 10, 'valor_pergunta' => 10, 'fase' => 1];
        $ajudasMotor = $ajudas_motor ?? [];
        $opcoesEliminadas = $ajudasMotor['opcoes_eliminadas'] ?? [];
        $gabaritoRevelado = !empty($ajudasMotor['gabarito_revelado']);
        $dicaBiblicaAtiva = $ajudasMotor['dica_biblica'] ?? null;
        $dicaConvidadoAtiva = $ajudasMotor['dica_convidado'] ?? null;
        $jogoAtivo = in_array($estado, ['aguardando', 'em_curso', 'pausado'], true);
    @endphp

    <div class="sm-shell-lg max-w-[1600px] mx-auto w-full px-3 sm:px-5 py-3 flex flex-col gap-3">
        <!-- ===== Barra Superior do Estúdio ===== -->
        <header class="sm-panel px-4 py-3 shadow-2xl flex flex-wrap items-center justify-between gap-3 shrink-0">
            <div class="min-w-0">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-2.5 h-2.5 rounded-full {{ $jogoAtivo ? 'bg-emerald-400 animate-pulse' : 'bg-slate-500' }} shrink-0"></span>
                    <h1 class="text-base sm:text-lg font-black text-white tracking-tight truncate">{{ $sala->nome }}</h1>
                </div>
                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                    <span id="badgeFaseTopo" class="sm-pill !px-2.5 !py-0.5 !text-[10px] uppercase">
                        {{ $faseAtual }}ª FASE • VALE {{ $placar['valor_pergunta'] ?? 10 }} PTS
                    </span>
                    <span class="font-mono font-black text-[11px] bg-blue-950/90 text-amber-300 px-2.5 py-0.5 rounded-lg border border-blue-400/40">
                        PIN {{ $sala->codigo_acesso }}
                    </span>
                    <span id="badgeEstadoSala" class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-lg border {{ $estado === 'pausado' ? 'border-amber-400/50 bg-amber-400/15 text-amber-300' : 'border-emerald-400/50 bg-emerald-500/15 text-emerald-300' }}">
                        {{ $estado === 'pausado' ? 'Pausado' : ($estado === 'terminado' ? 'Terminado' : ($estado === 'aguardando' ? 'Aguardando' : 'Em curso')) }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                <!-- Mesa de som -->
                <div class="flex flex-wrap items-center gap-1.5 pr-2 mr-0.5 border-r border-blue-400/20">
                    <button type="button" onclick="tocarSomEstudio('abertura')" title="Tema de abertura" class="st-som text-amber-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" /></svg>
                        <span class="hidden sm:inline">Abertura</span>
                    </button>
                    <button type="button" onclick="alternarSuspenseEstudio()" id="btnSuspenseEstudio" title="Trilha de suspense (tecla S)" class="st-som text-blue-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z" /></svg>
                        <span id="txtSuspenseEstudio" class="hidden sm:inline">Suspense</span>
                        <span class="sm-kbd hidden lg:inline-flex">S</span>
                    </button>
                    <button type="button" onclick="tocarSomEstudio('certa1')" title="Efeito de acerto / aplausos" class="st-som text-emerald-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span class="hidden sm:inline">Acerto</span>
                    </button>
                    <button type="button" onclick="tocarSomEstudio('errou1')" title="Efeito de erro" class="st-som text-rose-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        <span class="hidden sm:inline">Erro</span>
                    </button>
                    <button type="button" onclick="tocarSomEstudio('inicio')" title="Toque de início" class="st-som text-purple-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        <span class="hidden sm:inline">Chime</span>
                    </button>
                </div>
                <a href="{{ route('entrar.view', $sala->codigo_acesso) }}" target="_blank" title="Abrir a vista do participante"
                   class="st-som text-white !bg-blue-600/90 hover:!bg-blue-500 !border-blue-300/50">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                    <span class="hidden sm:inline">Smartphone</span>
                </a>
                <a href="{{ route('projecao.tela', $sala->id) }}" target="_blank"
                   class="sm-cta !w-auto !py-1.5 !px-3.5 !text-[11px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" /></svg>
                    Abrir Telão
                </a>
            </div>
        </header>

        @if($jogoAtivo)
            <div class="flex-1 lg:min-h-0 grid grid-cols-1 lg:grid-cols-12 lg:grid-rows-[minmax(0,1fr)] gap-3">
                <!-- ===== Palco: pergunta, gabarito, ajudas e comandos ===== -->
                <section class="lg:col-span-8 sm-panel p-3 sm:p-4 shadow-2xl flex flex-col gap-3 lg:min-h-0">
                    <!-- Linha de estado: pergunta, fases e cronómetro -->
                    <div class="flex flex-wrap items-center justify-between gap-2 shrink-0">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span id="badgePerguntaNum" class="sm-pill !px-3 !py-1 !text-[11px] uppercase">
                                Pergunta {{ $numero_pergunta }} de {{ $total_perguntas }}
                            </span>
                            <span id="badgeLivro" class="text-[11px] font-bold px-2.5 py-1 rounded-lg border border-blue-400/30 bg-blue-950/80 text-blue-200 {{ empty($pergunta['livro_nome']) ? 'hidden' : '' }}">
                                <span id="badgeLivroNome" class="text-white">{{ $pergunta['livro_nome'] ?? '' }}</span>
                            </span>
                            <span id="badgeGabaritoVisivel" class="text-[10px] font-black uppercase px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-400/50 {{ $gabaritoRevelado ? '' : 'hidden' }}">
                                Gabarito no telão
                            </span>
                        </div>
                        <div class="flex items-center gap-1 order-last sm:order-none w-full sm:w-auto">
                            @foreach([1 => 10, 2 => 20, 3 => 30, 4 => 50] as $numFase => $ptsFase)
                                <span class="st-fase {{ $faseAtual === $numFase ? 'is-current' : ($faseAtual > $numFase ? 'is-done' : '') }}" title="{{ $numFase }}ª fase • {{ $ptsFase }} pts por pergunta">
                                    {{ $numFase }}ª • {{ $ptsFase }}
                                </span>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-2 bg-blue-950/80 px-3 py-1 rounded-xl border border-blue-400/30">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-2xl font-black text-amber-400 font-mono leading-none">
                                <span id="presenterTimer" class="st-timer">{{ $tempo_restante }}</span><span class="text-sm">s</span>
                            </p>
                        </div>
                    </div>

                    @if($pergunta)
                        <!-- Monitor de respostas -->
                        <div class="flex items-center gap-3 px-3 py-2 rounded-xl bg-blue-950/70 border border-blue-400/30 shrink-0">
                            <svg class="w-4 h-4 text-amber-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-xs font-bold text-white flex-1 min-w-0 truncate" id="txtRespostasMonitor">A aguardar participantes...</p>
                            <div class="w-24 sm:w-40 bg-blue-950 h-2.5 rounded-full overflow-hidden border border-blue-400/40 shrink-0">
                                <div id="barraProgressoRespostas" class="bg-gradient-to-r from-amber-400 via-amber-300 to-emerald-400 h-full rounded-full w-0 transition-all duration-500"></div>
                            </div>
                        </div>

                        <!-- Enunciado -->
                        <div class="sm-question px-4 py-4 sm:px-6 lg:flex-1 lg:min-h-[90px] flex items-center justify-center text-center">
                            <p id="textoPerguntaEstudio" class="st-question-text font-black text-white drop-shadow">
                                {{ $pergunta['texto'] ?? $pergunta->texto }}
                            </p>
                        </div>

                        <!-- Gabarito do apresentador -->
                        @php
                            $opcoes = $pergunta['opcoes'] ?? [];
                            $opcoesArray = is_string($opcoes) ? json_decode($opcoes, true) : $opcoes;
                            $respostaCorreta = trim((string) ($pergunta['resposta_correta'] ?? ''));
                            $letras = ['A', 'B', 'C', 'D'];
                        @endphp
                        <div id="containerAlternativasEstudio" class="grid grid-cols-1 sm:grid-cols-2 gap-2 shrink-0">
                            @foreach($opcoesArray as $idx => $opcao)
                                @php
                                    $ehCorreta = mb_strtolower(trim((string) $opcao), 'UTF-8') === mb_strtolower($respostaCorreta, 'UTF-8');
                                    $eliminada = in_array($idx, $opcoesEliminadas);
                                @endphp
                                <div id="opcaoEstudio_{{ $idx }}" class="st-opcao {{ $eliminada ? 'is-eliminada' : ($ehCorreta ? 'is-correta' : '') }}">
                                    <span class="st-letra">{{ $letras[$idx] ?? ($idx + 1) }}</span>
                                    <span class="font-bold text-sm flex-1 leading-snug">{{ $opcao }}</span>
                                    @if($eliminada)
                                        <span class="st-tag">Eliminada</span>
                                    @elseif($ehCorreta)
                                        <span class="st-tag">Correta</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Ajudas para telão e smartphones -->
                        @php
                            $ajudasQuestao = $pergunta['ajudas'] ?? [];
                            if (is_string($ajudasQuestao)) {
                                $ajudasQuestao = json_decode($ajudasQuestao, true) ?: [];
                            }
                            $convidadosNomes = $ajudasQuestao['convidados'] ?? ['Paulo', 'Pedro', 'Moisés'];
                        @endphp
                        <div class="rounded-xl bg-blue-950/50 border border-amber-500/30 p-2.5 shrink-0">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                <span class="text-[10px] font-black uppercase tracking-wider text-amber-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                                    Ajudas
                                </span>
                                <span class="text-[10px] text-blue-200/80 truncate">
                                    Referência: <strong id="referenciaBiblicaAjuda" class="text-amber-300">{{ $pergunta['referencia_biblica'] ?? 'Contexto Bíblico' }}</strong>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                                <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST" class="form-ajax-control">
                                    @csrf
                                    <input type="hidden" name="tipo" value="dica">
                                    <button type="submit" id="btnAjudaDica" class="st-ajuda">
                                        <span class="text-xs font-black text-white block">Dica Bíblica</span>
                                        <span id="txtDicasRestantes" class="text-[10px] font-bold text-amber-300">{{ $ajudasMotor['dicas_restantes'] ?? 3 }} restantes</span>
                                    </button>
                                </form>
                                <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST" class="form-ajax-control">
                                    @csrf
                                    <input type="hidden" name="tipo" value="cartas">
                                    <button type="submit" id="btnAjudaCartas" {{ !empty($ajudasMotor['cartas_usada']) ? 'disabled' : '' }} class="st-ajuda">
                                        <span class="text-xs font-black text-white block">Cartas 50/50</span>
                                        <span id="txtCartasStatus" class="text-[10px] font-bold text-amber-300">{{ !empty($ajudasMotor['cartas_usada']) ? 'Usada' : 'Eliminar 2' }}</span>
                                    </button>
                                </form>
                                @foreach($convidadosNomes as $idxConv => $nomeConv)
                                    @php
                                        $naoSabe = (($idxConv + 1) === (int) ($ajudasQuestao['errado'] ?? 2));
                                    @endphp
                                    <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST" class="form-ajax-control">
                                        @csrf
                                        <input type="hidden" name="tipo" value="convidado">
                                        <input type="hidden" name="indice_convidado" value="{{ $idxConv }}">
                                        <button type="submit" class="st-ajuda {{ $naoSabe ? 'is-nao-sabe' : 'is-sabe' }}">
                                            <span class="text-xs font-black text-white block truncate">{{ $nomeConv }}</span>
                                            <span class="text-[10px] font-bold {{ $naoSabe ? 'text-rose-300' : 'text-emerald-300' }}">{{ $naoSabe ? 'Não sabe' : 'Sabe a certa' }}</span>
                                        </button>
                                    </form>
                                @endforeach
                            </div>

                            <div id="boxDicasAtivasContainer" class="mt-2 text-xs space-y-1.5 text-amber-200 {{ ($dicaBiblicaAtiva || $dicaConvidadoAtiva) ? '' : 'hidden' }}">
                                <p id="boxTextoDicaBiblica" class="bg-blue-950/80 px-2.5 py-1.5 rounded-lg border border-amber-400/30 {{ $dicaBiblicaAtiva ? '' : 'hidden' }}">
                                    <strong class="text-amber-300">No telão:</strong> <span id="conteudoDicaBiblica">{{ $dicaBiblicaAtiva }}</span>
                                </p>
                                <p id="boxTextoDicaConvidado" class="bg-blue-950/80 px-2.5 py-1.5 rounded-lg border border-amber-400/30 {{ $dicaConvidadoAtiva ? '' : 'hidden' }}">
                                    <strong id="nomeConvidadoDica" class="text-amber-300">{{ $dicaConvidadoAtiva['convidado'] ?? '' }}:</strong> <span id="conteudoDicaConvidado">{{ $dicaConvidadoAtiva['mensagem'] ?? '' }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Barra de comando da rodada (fixa no fundo no telemóvel) -->
                        <div class="sticky bottom-0 z-30 -mx-3 sm:-mx-4 lg:mx-0 px-3 sm:px-4 lg:px-0 py-2 lg:py-0 bg-[#0b1a4d]/95 lg:bg-transparent backdrop-blur-md lg:backdrop-blur-none border-t border-blue-400/30 lg:border-0 grid grid-cols-5 gap-2 shrink-0">
                            <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST" class="form-ajax-control">
                                @csrf
                                <input type="hidden" name="tipo" value="revelar">
                                <button type="submit" id="btnToggleGabarito" class="st-btn is-emerald" title="Mostrar a resposta certa no telão e encerrar a ronda (tecla R)">
                                    <svg class="w-4 h-4 shrink-0 hidden sm:block" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span id="txtBtnGabarito">{{ $gabaritoRevelado ? 'Ocultar resposta' : 'Mostrar resposta' }}</span>
                                    <span class="sm-kbd hidden lg:inline-flex">R</span>
                                </button>
                            </form>
                            <form action="{{ route('apresentador.ajuda', $sala->id) }}" method="POST" class="form-ajax-control">
                                @csrf
                                <input type="hidden" name="tipo" value="pulo">
                                <button type="submit" id="btnPulo" class="st-btn is-blue" title="Trocar por outra pergunta">
                                    <span id="txtBtnPulo">Pular ({{ $ajudasMotor['pulos_restantes'] ?? 3 }})</span>
                                </button>
                            </form>
                            <div id="containerBtnPausarRetomar">
                                @if($estado !== 'pausado')
                                    <form action="{{ route('apresentador.pausar', $sala->id) }}" method="POST" class="form-ajax-control h-full">
                                        @csrf
                                        <button type="submit" class="st-btn is-amber" title="Pausar (tecla P)">Pausar <span class="sm-kbd hidden lg:inline-flex">P</span></button>
                                    </form>
                                @else
                                    <form action="{{ route('apresentador.retomar', $sala->id) }}" method="POST" class="form-ajax-control h-full">
                                        @csrf
                                        <button type="submit" class="st-btn is-go" title="Retomar (tecla P)">Retomar <span class="sm-kbd hidden lg:inline-flex">P</span></button>
                                    </form>
                                @endif
                            </div>
                            <form action="{{ route('apresentador.proxima', $sala->id) }}" method="POST" class="form-ajax-control">
                                @csrf
                                <button type="submit" id="btnProximaPergunta" class="sm-cta st-btn !text-xs" title="Avançar para a próxima pergunta (tecla N)">
                                    Próxima
                                    <span class="sm-kbd hidden lg:inline-flex">N</span>
                                </button>
                            </form>
                            <form action="{{ route('apresentador.finalizar', $sala->id) }}" method="POST" class="form-ajax-control" data-confirmar="Encerrar a partida agora para todos os participantes?">
                                @csrf
                                <button type="submit" class="st-btn is-rose" title="Terminar a partida">Encerrar</button>
                            </form>
                        </div>
                    @else
                        <div class="lg:flex-1 flex items-center justify-center py-10 text-blue-200 text-sm font-bold">
                            Nenhuma pergunta carregada. Clique em “Próxima” para começar.
                        </div>
                    @endif
                </section>

                <!-- ===== Lateral: ranking, entrada, prémios e equipas ===== -->
                <aside class="lg:col-span-4 flex flex-col gap-3 lg:min-h-0">
                    <div class="sm-panel p-3 sm:p-4 shadow-2xl flex flex-col lg:flex-1 lg:min-h-0">
                        <div class="flex items-center justify-between mb-2.5 pb-2 border-b border-blue-900/50 shrink-0">
                            <h3 class="text-sm font-black text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                Ranking ao vivo
                            </h3>
                            <span id="contadorParticipantes" class="sm-pill !px-2.5 !py-0.5 !text-[10px]">
                                {{ $participantes->count() }} jogadores
                            </span>
                        </div>
                        <div id="participantesList" class="space-y-2 max-h-80 lg:max-h-none lg:flex-1 lg:min-h-0 overflow-y-auto pr-1">
                            @forelse($participantes as $idx => $participante)
                                <div class="flex items-center justify-between p-2.5 bg-blue-950/60 rounded-xl border border-blue-400/20">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-7 h-7 rounded-lg sm-ingot flex items-center justify-center text-xs font-black shrink-0">{{ $idx + 1 }}º</div>
                                        <div class="truncate">
                                            <span class="text-sm font-bold text-white block truncate">{{ $participante->nome }}</span>
                                            @if($participante->equipe)
                                                <span class="text-xs text-blue-300/80">{{ $participante->equipe->nome }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-xs font-mono font-black text-amber-300 bg-amber-500/15 border border-amber-500/30 px-2 py-0.5 rounded-lg shrink-0">{{ $participante->pontuacao_total }} pts</span>
                                </div>
                            @empty
                                <div class="p-4 bg-blue-950/50 rounded-xl text-center text-xs text-blue-200/70 border border-blue-900/40">
                                    A aguardar participantes com o código <strong class="text-amber-300">{{ $sala->codigo_acesso }}</strong>...
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Entrada por QR Code -->
                    <div class="sm-panel p-3 shadow-2xl flex items-center gap-3 shrink-0">
                        <div id="qrcode" class="p-1.5 bg-white rounded-xl border-2 border-amber-400/50 shrink-0"></div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-wider text-amber-300">Entrar pelo celular</p>
                            <p class="text-2xl font-mono font-black text-amber-300 leading-tight">{{ $sala->codigo_acesso }}</p>
                            <a href="{{ route('entrar.view', $sala->codigo_acesso) }}" target="_blank" class="text-[11px] text-blue-200 underline hover:text-amber-300 truncate block">/entrar/{{ $sala->codigo_acesso }}</a>
                        </div>
                    </div>

                    <!-- Prémios -->
                    <details class="st-collapse sm-panel shadow-2xl shrink-0">
                        <summary class="px-4 py-2.5 flex items-center justify-between gap-2">
                            <span class="text-xs font-black text-amber-400 uppercase tracking-wider">Prêmios (1º e 2º)</span>
                            <svg class="st-chevron w-4 h-4 text-blue-200 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <form action="{{ route('apresentador.premios', $sala->id) }}" method="POST" class="px-4 pb-3 space-y-2 form-ajax-control">
                            @csrf
                            <input type="text" name="premio_primeiro_lugar" value="{{ $sala->premio_primeiro_lugar }}" placeholder="1º lugar — Ex.: Bíblia de Estudo + Troféu" aria-label="Prêmio do 1º lugar" class="sm-game-input !text-xs !py-2">
                            <input type="text" name="premio_segundo_lugar" value="{{ $sala->premio_segundo_lugar }}" placeholder="2º lugar — Ex.: Livro Devocional + Medalha" aria-label="Prêmio do 2º lugar" class="sm-game-input !text-xs !py-2">
                            <button type="submit" class="st-btn is-blue !min-h-0 !py-2">Salvar prêmios</button>
                        </form>
                    </details>

                    <!-- Equipes -->
                    <details class="st-collapse sm-panel shadow-2xl shrink-0">
                        <summary class="px-4 py-2.5 flex items-center justify-between gap-2">
                            <span class="text-xs font-black text-amber-300 uppercase tracking-wider">Equipes / Células ({{ $equipes->count() }})</span>
                            <svg class="st-chevron w-4 h-4 text-blue-200 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-4 pb-3">
                            <form action="{{ route('apresentador.equipe.criar', $sala->id) }}" method="POST" class="flex gap-2 form-ajax-control">
                                @csrf
                                <input type="text" name="nome" required placeholder="Ex.: Tribo de Judá" aria-label="Nome da equipe" class="sm-game-input !text-xs !py-2 flex-1 min-w-0">
                                <button type="submit" class="sm-cta !w-auto !px-3 !py-2 !text-[11px] shrink-0">+ Equipe</button>
                            </form>
                            <div id="listaEquipesContainer" class="flex flex-wrap gap-1.5 mt-2">
                                @foreach($equipes as $eq)
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-blue-950/80 border border-blue-400/20 text-blue-200">{{ $eq->nome }} ({{ $eq->pontuacao_total }} pts)</span>
                                @endforeach
                            </div>
                        </div>
                    </details>
                </aside>
            </div>
        @elseif($estado === 'terminado')
            <div class="flex-1 flex items-center justify-center">
                <div class="sm-panel p-8 text-center max-w-2xl w-full shadow-2xl border-amber-400/50">
                    <div class="w-16 h-16 rounded-full sm-ingot flex items-center justify-center mx-auto mb-4 text-2xl font-black shadow-lg">🏆</div>
                    <h2 class="text-2xl sm:text-3xl font-black text-white mb-2 tracking-tight sm-logo-title">Partida Finalizada</h2>
                    <p class="text-blue-200 mb-6">Pontuação acumulada na sala: <span class="font-black text-amber-400 text-2xl">{{ $pontuacao }} pts</span></p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ route('apresentador.control', ['sala' => $sala->id, 'nova_partida' => 1]) }}" class="sm-cta !w-auto !px-6 !py-3 !text-sm">Nova partida nesta sala</a>
                        <a href="{{ route('apresentador.resultado', $sala->id) }}" class="sm-arcade-btn">Ver pódio e relatório</a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        const BASE_APP_URL = (() => {
            const subpath = "{{ parse_url(url('/'), PHP_URL_PATH) }}" || '';
            return (window.location.origin + subpath).replace(/\/+$/, '');
        })();
        const qrContainer = document.getElementById('qrcode');
        if (qrContainer && typeof QRCode !== 'undefined') {
            new QRCode(qrContainer, {
                text: `${BASE_APP_URL}/entrar/{{ $sala->codigo_acesso }}`,
                width: 96,
                height: 96,
                colorDark: '#0a192f',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H,
            });
        }

        const BASE_AUDIO_ESTUDIO = "{{ asset('audio/classico') }}";
        const audioSuspense = new Audio(`${BASE_AUDIO_ESTUDIO}/suspense.wav`);
        audioSuspense.loop = true;
        audioSuspense.volume = 0.3;
        let suspenseTocando = false;

        function tocarSomEstudio(nome) {
            const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
            if (!ativo) return;
            const fx = new Audio(`${BASE_AUDIO_ESTUDIO}/${nome}.wav`);
            fx.volume = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85;
            fx.play().catch(() => {});
        }

        function alternarSuspenseEstudio() {
            const txt = document.getElementById('txtSuspenseEstudio');
            if (suspenseTocando) {
                audioSuspense.pause();
                suspenseTocando = false;
                if (txt) txt.textContent = 'Suspense';
                document.getElementById('btnSuspenseEstudio')?.classList.remove('is-on');
            } else {
                audioSuspense.volume = (typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() : 0.85) * 0.35;
                audioSuspense.play().catch(() => {});
                suspenseTocando = true;
                if (txt) txt.textContent = 'Parar';
                document.getElementById('btnSuspenseEstudio')?.classList.add('is-on');
            }
        }

        window.addEventListener('biblia-som-alterado', (ev) => {
            if (!ev.detail.ativo && suspenseTocando) {
                audioSuspense.pause();
                suspenseTocando = false;
                const txt = document.getElementById('txtSuspenseEstudio');
                if (txt) txt.textContent = 'Suspense';
                document.getElementById('btnSuspenseEstudio')?.classList.remove('is-on');
            }
        });

        window.addEventListener('DOMContentLoaded', () => {
            @if(session('som_efeito'))
                tocarSomEstudio('{{ session('som_efeito') }}');
            @endif
            @if(session('success'))
                mostrarToastEstudio(@js(session('success')), true);
            @endif
            @if(session('error'))
                mostrarToastEstudio(@js(session('error')), false);
            @endif
        });

        // Interceptador assíncrono para os botões do estúdio
        const letrasAlfabeto = ['A', 'B', 'C', 'D'];
        let gabaritoVisivelTelao = {{ $gabaritoRevelado ? 'true' : 'false' }};
        let estadoSalaAtual = '{{ $estado }}';
        let respostaCorretaAtual = '{{ addslashes($respostaCorreta ?? '') }}';
        const monitorRespostas = { total: 0, respondidos: 0 };

        function mostrarToastEstudio(mensagem, sucesso = true) {
            const toast = document.getElementById('toastEstudio');
            const content = document.getElementById('toastEstudioContent');
            const msg = document.getElementById('toastEstudioMsg');
            if (!toast || !content || !msg) return;

            msg.textContent = mensagem;
            content.className = sucesso
                ? 'px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-3 border bg-emerald-950/95 border-emerald-400 text-emerald-200'
                : 'px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-3 border bg-rose-950/95 border-rose-400 text-rose-200';

            toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
            }, 3000);
        }

        document.addEventListener('submit', async (e) => {
            const form = e.target.closest('.form-ajax-control');
            if (!form) return;
            e.preventDefault();
            if (form.dataset.confirmar && !window.confirm(form.dataset.confirmar)) return;

            // Mostrar a resposta fecha a ronda: avisar se ainda há jogadores sem responder
            const tipoAcao = form.querySelector('input[name="tipo"]')?.value;
            const faltam = monitorRespostas.total - monitorRespostas.respondidos;
            if (tipoAcao === 'revelar' && !gabaritoVisivelTelao && monitorRespostas.total > 0 && faltam > 0
                && !window.confirm(`Ainda falta${faltam > 1 ? 'm' : ''} ${faltam} de ${monitorRespostas.total} jogador(es) responder.\nMostrar a resposta agora encerra a ronda para eles. Continuar?`)) {
                return;
            }

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
                        span.className = 'text-[11px] font-bold px-2.5 py-1 rounded-lg bg-blue-950/80 border border-blue-400/20 text-blue-200';
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
                        <div id="opcaoEstudio_${idx}" class="st-opcao ${ehCorreta ? 'is-correta' : ''}">
                            <span class="st-letra">${letra}</span>
                            <span class="font-bold text-sm flex-1 leading-snug">${opcao}</span>
                            ${ehCorreta ? '<span class="st-tag">Correta</span>' : ''}
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
            if (txtGab) txtGab.textContent = 'Mostrar resposta';
            gabaritoVisivelTelao = false;

            if (typeof data.tempo_restante !== 'undefined') {
                atualizarCronometroEstudio(data.tempo_restante);
            }
        }

        function atualizarDOMEstadoEstudio(estado) {
            estadoSalaAtual = estado;
            const badgeEstado = document.getElementById('badgeEstadoSala');
            if (badgeEstado) {
                badgeEstado.textContent = estado === 'pausado' ? 'Pausado' : 'Em curso';
                badgeEstado.className = 'text-[10px] font-black uppercase px-2.5 py-0.5 rounded-lg border ' + (estado === 'pausado' ? 'border-amber-400/50 bg-amber-400/15 text-amber-300' : 'border-emerald-400/50 bg-emerald-500/15 text-emerald-300');
            }

            const containerBtn = document.getElementById('containerBtnPausarRetomar');
            if (containerBtn) {
                const pausado = estado === 'pausado';
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const acao = pausado ? @js(route('apresentador.retomar', $sala->id)) : @js(route('apresentador.pausar', $sala->id));
                containerBtn.innerHTML = `
                    <form action="${acao}" method="POST" class="form-ajax-control h-full">
                        <input type="hidden" name="_token" value="${token}">
                        <button type="submit" class="st-btn ${pausado ? 'is-go' : 'is-amber'}" title="${pausado ? 'Retomar' : 'Pausar'} (tecla P)">
                            ${pausado ? 'Retomar' : 'Pausar'} <span class="sm-kbd hidden lg:inline-flex">P</span>
                        </button>
                    </form>
                `;
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
                    txtGab.textContent = gabaritoVisivelTelao ? 'Ocultar resposta' : 'Mostrar resposta';
                }
            }

            // 2. Cartas 50/50 eliminadas
            if (Array.isArray(ajudas.opcoes_eliminadas)) {
                ajudas.opcoes_eliminadas.forEach(idx => {
                    const el = document.getElementById(`opcaoEstudio_${idx}`);
                    if (el) {
                        el.classList.remove('is-correta');
                        el.classList.add('is-eliminada');
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
                const resp = await fetch(`${BASE_APP_URL}/api/salas/{{ $sala->id }}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!resp.ok) return;
                const data = await resp.json();

                const list = document.getElementById('participantesList');
                const contador = document.getElementById('contadorParticipantes');
                const monitorTxt = document.getElementById('txtRespostasMonitor');
                const barraProgresso = document.getElementById('barraProgressoRespostas');

                const totalPart = data.ranking ? data.ranking.length : 0;
                const respostasMap = data.respostas_rodada || {};
                const respondidosCount = Object.keys(respostasMap).length;
                const pct = totalPart > 0 ? Math.min(100, Math.round((respondidosCount / totalPart) * 100)) : 0;
                monitorRespostas.total = totalPart;
                monitorRespostas.respondidos = respondidosCount;

                if (monitorTxt) {
                    if (totalPart === 0) {
                        monitorTxt.textContent = 'A aguardar participantes com o código {{ $sala->codigo_acesso }}...';
                    } else if (respondidosCount === 0) {
                        monitorTxt.textContent = `0 de ${totalPart} responderam (0%) • Aguardando respostas...`;
                    } else if (respondidosCount < totalPart) {
                        monitorTxt.textContent = `${respondidosCount} de ${totalPart} responderam (${pct}%) • Bloqueando respostas...`;
                    } else {
                        monitorTxt.textContent = `Todos os ${totalPart} responderam! (100%) • Pronto para revelar`;
                    }
                }
                if (barraProgresso) {
                    barraProgresso.style.width = `${pct}%`;
                }

                if (contador) {
                    contador.textContent = totalPart + ' jogadores';
                }

                if (typeof data.tempo_restante !== 'undefined') {
                    const tempoServidor = Number(data.tempo_restante) || 0;
                    if (Math.abs(tempoServidor - tempoEstudio) > 1) {
                        atualizarCronometroEstudio(tempoServidor);
                    }
                }

                if (!list || !data.ranking) return;

                if (data.ranking.length === 0) {
                    list.innerHTML = '<div class="p-4 bg-blue-950/50 rounded-xl text-center text-xs text-blue-200/70 border border-blue-900/40">A aguardar participantes com o código <strong class="text-amber-300">{{ $sala->codigo_acesso }}</strong>...</div>';
                    return;
                }

                const gabaritoRevelado = data.ajudas && data.ajudas.gabarito_revelado;

                list.innerHTML = data.ranking.map((p, idx) => {
                    const r = respostasMap[p.id];
                    let statusBadge = '<span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-lg bg-blue-950 border border-blue-400/20 text-blue-300">Pensando...</span>';

                    if (r) {
                        if (gabaritoRevelado) {
                            if (r.correta) {
                                statusBadge = `<span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-400/40">Acertou: ${r.letra} (+${r.pontos || 10} pts)</span>`;
                            } else {
                                statusBadge = `<span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-lg bg-rose-500/20 text-rose-300 border border-rose-400/40">Errou: ${r.letra}</span>`;
                            }
                        } else {
                            statusBadge = `<span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-400/40 animate-pulse">Bloqueou: ${r.letra}</span>`;
                        }
                    }

                    return `
                        <div class="flex items-center justify-between p-2.5 bg-blue-950/60 rounded-xl border border-blue-400/20">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-7 h-7 rounded-lg sm-ingot flex items-center justify-center text-xs font-black shrink-0">
                                    ${idx + 1}º
                                </div>
                                <div class="truncate">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-white truncate">${p.nome}</span>
                                        ${statusBadge}
                                    </div>
                                    ${p.equipe ? `<span class="text-xs text-blue-300/80 block">${p.equipe}</span>` : ''}
                                </div>
                            </div>
                            <span class="text-xs font-mono font-black text-amber-300 bg-amber-500/15 border border-amber-500/30 px-2.5 py-1 rounded-lg shrink-0 ml-2">
                                ${p.pontuacao} pts
                            </span>
                        </div>
                    `;
                }).join('');
            } catch (e) {}
        }

        // Cronómetro do apresentador: sincronizado pela API e suavizado localmente a cada segundo
        let tempoEstudio = {{ (int) ($tempo_restante ?? 0) }};
        function atualizarCronometroEstudio(segundos) {
            tempoEstudio = Math.max(0, parseInt(segundos, 10) || 0);
            const el = document.getElementById('presenterTimer');
            if (!el) return;
            el.textContent = tempoEstudio;
            el.classList.toggle('is-critico', tempoEstudio > 0 && tempoEstudio <= 10);
        }
        setInterval(() => {
            if (estadoSalaAtual === 'em_curso' && !gabaritoVisivelTelao && tempoEstudio > 0) {
                atualizarCronometroEstudio(tempoEstudio - 1);
            }
        }, 1000);

        // Atalhos de teclado do estúdio: R revelar, N próxima, P pausar/retomar, S suspense
        document.addEventListener('keydown', (ev) => {
            if (ev.ctrlKey || ev.metaKey || ev.altKey || ev.repeat) return;
            const alvo = ev.target;
            if (alvo && (alvo.tagName === 'INPUT' || alvo.tagName === 'TEXTAREA' || alvo.tagName === 'SELECT' || alvo.isContentEditable)) return;
            const atalhos = {
                r: () => document.getElementById('btnToggleGabarito'),
                n: () => document.getElementById('btnProximaPergunta'),
                p: () => document.querySelector('#containerBtnPausarRetomar button'),
            };
            const tecla = (ev.key || '').toLowerCase();
            if (tecla === 's') {
                ev.preventDefault();
                alternarSuspenseEstudio();
                return;
            }
            const btn = atalhos[tecla]?.();
            if (btn && !btn.disabled) {
                ev.preventDefault();
                btn.click();
            }
        });

        // Sincronização em tempo real ágil (1.5 segundos)
        sincronizarParticipantes();
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
