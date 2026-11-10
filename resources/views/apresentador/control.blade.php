<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bíblia Show — Controle da Sala {{ $sala->codigo_acesso }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-900">

    <div class="max-w-6xl mx-auto px-4 py-6 sm:py-8">
        <header class="flex flex-wrap justify-between items-center gap-4 mb-6 bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div>
                <a href="{{ route('apresentador.painel') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Painel de Salas
                </a>
                <div class="flex items-center gap-3 mt-1">
                    <h1 class="text-2xl font-extrabold text-slate-900">{{ $sala->nome }}</h1>
                    <span class="font-mono font-extrabold text-sm bg-amber-100 text-amber-800 px-3 py-1 rounded-lg border border-amber-200">
                        Código: {{ $sala->codigo_acesso }}
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('entrar.view', $sala->codigo_acesso) }}" target="_blank"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                    Tela do Participante
                </a>
                <a href="{{ route('projecao.tela', $sala->id) }}" target="_blank"
                   class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    Abrir Projeção (Telão)
                </a>
            </div>
        </header>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl mb-6 text-emerald-800 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl mb-6 text-rose-800 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if($estado === 'aguardando' || $estado === 'em_curso' || $estado === 'pausado')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Coluna Principal: Pergunta, Alternativas, Gabarito e Controles -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6">
                        <div class="flex justify-between items-start mb-5">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">
                                        Pergunta {{ $numero_pergunta }} de {{ $total_perguntas }}
                                    </span>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $estado === 'pausado' ? 'bg-yellow-100 text-yellow-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ ucfirst($estado) }}
                                    </span>
                                    @if(!empty($pergunta['dificuldade_nome']))
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                                            Nível {{ $pergunta['dificuldade_nivel'] ?? 1 }} ({{ $pergunta['dificuldade_nome'] }})
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[11px] font-bold text-slate-400 uppercase">Temporizador</p>
                                <p class="text-3xl font-extrabold text-slate-900 font-mono"><span id="presenterTimer">{{ $tempo_restante }}</span>s</p>
                            </div>
                        </div>

                        @if($pergunta)
                            <!-- Enunciado -->
                            <div class="bg-slate-900 text-white rounded-xl p-5 mb-5">
                                <p class="text-lg sm:text-xl font-bold leading-relaxed">
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
                                            $letra = $letras[$idx] ?? ($idx + 1);
                                        @endphp
                                        <div class="p-3.5 rounded-xl border-2 flex items-center gap-3 {{ $ehCorreta ? 'border-emerald-500 bg-emerald-50 text-emerald-950' : 'border-slate-200 bg-slate-50 text-slate-700' }}">
                                            <span class="w-8 h-8 rounded-lg font-extrabold flex items-center justify-center shrink-0 text-sm {{ $ehCorreta ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                                {{ $letra }}
                                            </span>
                                            <span class="font-semibold text-sm flex-1">{{ $opcao }}</span>
                                            @if($ehCorreta)
                                                <span class="inline-flex items-center gap-1 text-xs font-extrabold text-emerald-700 bg-emerald-200/70 px-2 py-0.5 rounded">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                    </svg>
                                                    Correta
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Referência Bíblica e Dica dos Convidados Bíblicos -->
                            @php
                                $ajudas = $pergunta['ajudas'] ?? [];
                                if (is_string($ajudas)) {
                                    $ajudas = json_decode($ajudas, true) ?: [];
                                }
                            @endphp
                            <div class="bg-amber-50 border border-amber-200/80 rounded-xl p-4 mb-6 text-xs sm:text-sm text-amber-950 space-y-2">
                                @if(!empty($pergunta['referencia_biblica']) || !empty($pergunta['explicacao']))
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-amber-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                        </svg>
                                        <p>
                                            <strong>Referência / Comentário:</strong>
                                            {{ $pergunta['referencia_biblica'] ? '[' . $pergunta['referencia_biblica'] . '] ' : '' }}
                                            {{ $pergunta['explicacao'] ?? '' }}
                                        </p>
                                    </div>
                                @endif
                                @if(!empty($ajudas['convidados']))
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-amber-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                        </svg>
                                        <p>
                                            <strong>Convidados Bíblicos desta pergunta:</strong>
                                            @foreach($ajudas['convidados'] as $i => $conv)
                                                <span class="inline-block px-2 py-0.5 rounded mr-1 {{ ($i + 1) === (int)($ajudas['errado'] ?? 0) ? 'bg-rose-200 text-rose-900 font-semibold' : 'bg-emerald-200 text-emerald-900 font-semibold' }}">
                                                    {{ $conv }} {{ ($i + 1) === (int)($ajudas['errado'] ?? 0) ? '(Não sabe)' : '(Sabe)' }}
                                                </span>
                                            @endforeach
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Botões de Controle -->
                            <div class="flex flex-wrap gap-3">
                                @if($estado !== 'pausado')
                                    <form action="{{ route('apresentador.pausar', $sala->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold py-3.5 px-4 rounded-xl transition shadow flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                                            </svg>
                                            Pausar
                                        </button>
                                    </form>
                                    <form action="{{ route('apresentador.proxima', $sala->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 px-4 rounded-xl transition shadow flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.688c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062A1.125 1.125 0 013 16.81V8.688zM12.75 8.688c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062a1.125 1.125 0 01-1.683-.977V8.688z" />
                                            </svg>
                                            Próxima Pergunta
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('apresentador.retomar', $sala->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-3.5 px-4 rounded-xl transition shadow flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                            </svg>
                                            Retomar Jogo
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('apresentador.finalizar', $sala->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-extrabold py-3.5 px-4 rounded-xl transition shadow flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" />
                                        </svg>
                                        Encerrar Sala
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Coluna Lateral: Participantes, Ranking e QR Code -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                Participantes e Ranking
                            </h3>
                            <span id="contadorParticipantes" class="text-xs font-bold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full">
                                {{ $participantes->count() }}
                            </span>
                        </div>

                        <div id="participantesList" class="space-y-2 max-h-72 overflow-y-auto pr-1">
                            @forelse($participantes as $idx => $participante)
                                <div id="participante-{{ $participante->id }}"
                                     class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-800 flex items-center justify-center text-xs font-extrabold shrink-0">
                                            {{ $idx + 1 }}º
                                        </div>
                                        <div class="truncate">
                                            <span class="text-sm font-bold text-slate-900 block truncate">{{ $participante->nome }}</span>
                                            @if($participante->equipe)
                                                <span class="text-xs text-slate-500">{{ $participante->equipe->nome }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-xs font-mono font-extrabold text-amber-800 bg-amber-100 px-2.5 py-1 rounded-lg shrink-0">
                                        {{ $participante->pontuacao_total }} pts
                                    </span>
                                </div>
                            @empty
                                <div class="p-4 bg-slate-50 rounded-xl text-center text-sm text-slate-400">
                                    Aguardando participantes entrarem com o código <strong>{{ $sala->codigo_acesso }}</strong>...
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- QR Code de Entrada -->
                    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 text-center">
                        <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-3">
                            QR Code para Smartphones
                        </h3>
                        <div class="flex flex-col items-center gap-3">
                            <div id="qrcode" class="p-3 bg-white rounded-xl border border-slate-200 shadow-inner"></div>
                            <div class="text-lg font-mono font-extrabold text-amber-800 bg-amber-100 px-4 py-1.5 rounded-xl border border-amber-200">
                                {{ $sala->codigo_acesso }}
                            </div>
                            <p class="text-xs text-slate-500">
                                Link direto: <a href="{{ route('entrar.view', $sala->codigo_acesso) }}" target="_blank" class="text-blue-600 underline font-medium">/entrar/{{ $sala->codigo_acesso }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($estado === 'terminado')
            <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-8 text-center">
                <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Partida Finalizada</h2>
                <p class="text-slate-600 mb-6">Pontuação acumulada na sala: <span class="font-bold text-xl">{{ $pontuacao }} pts</span></p>

                <div class="flex justify-center gap-3">
                    <a href="{{ route('apresentador.control', ['sala' => $sala->id, 'nova_partida' => 1]) }}"
                       class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-slate-950 px-6 py-3 rounded-xl font-extrabold shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Iniciar Nova Partida nesta Sala
                    </a>
                    <a href="{{ route('apresentador.resultado', $sala->id) }}"
                       class="bg-slate-800 hover:bg-slate-700 text-white px-6 py-3 rounded-xl font-bold">
                        Ver Relatório Final
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
                width: 150,
                height: 150,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H,
            });
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
                    contador.textContent = data.ranking.length;
                }

                if (data.ranking.length === 0) {
                    list.innerHTML = '<div class="p-4 bg-slate-50 rounded-xl text-center text-sm text-slate-400">Aguardando participantes entrarem com o código <strong>{{ $sala->codigo_acesso }}</strong>...</div>';
                    return;
                }

                list.innerHTML = data.ranking.map((p, idx) => `
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-800 flex items-center justify-center text-xs font-extrabold shrink-0">
                                ${idx + 1}º
                            </div>
                            <div class="truncate">
                                <span class="text-sm font-bold text-slate-900 block truncate">${p.nome}</span>
                                ${p.equipe ? `<span class="text-xs text-slate-500">${p.equipe}</span>` : ''}
                            </div>
                        </div>
                        <span class="text-xs font-mono font-extrabold text-amber-800 bg-amber-100 px-2.5 py-1 rounded-lg shrink-0">
                            ${p.pontuacao} pts
                        </span>
                    </div>
                `).join('');
            } catch (e) {}
        }

        setInterval(sincronizarParticipantes, 3000);
    </script>

</body>
</html>
