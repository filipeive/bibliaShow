<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">
    <title>Bíblia Show — Pódio e Resultado da Sala</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="sm-stage sm-viewport-lg min-h-screen lg:min-h-0 text-slate-100 flex flex-col">

    @include('partials.navbar', ['dark' => true, 'compact' => true, 'hideJogarAgora' => true])

    @php
        $listaParticipantes = isset($participantes) ? $participantes->values() : collect();
        $podio = $listaParticipantes->take(3);
        $restantes = $listaParticipantes->slice(3);
        $historico = $resultados['historico_perguntas'] ?? [];
        $degraus = [
            1 => ['ordem' => 'order-2', 'altura' => 'h-28 sm:h-36 lg:h-48', 'cor' => 'from-amber-300 via-amber-500 to-amber-700 border-amber-200', 'medalha' => '👑', 'premio' => $sala?->premio_primeiro_lugar],
            2 => ['ordem' => 'order-1', 'altura' => 'h-20 sm:h-24 lg:h-32', 'cor' => 'from-slate-200 via-slate-400 to-slate-600 border-slate-100', 'medalha' => '🥈', 'premio' => $sala?->premio_segundo_lugar],
            3 => ['ordem' => 'order-3', 'altura' => 'h-14 sm:h-16 lg:h-20', 'cor' => 'from-orange-300 via-amber-700 to-amber-900 border-orange-200', 'medalha' => '🥉', 'premio' => null],
        ];
    @endphp

    <div class="sm-shell-lg max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 lg:py-6 w-full flex flex-col gap-4">
        <header class="flex flex-wrap items-end justify-between gap-3 shrink-0">
            <div class="min-w-0">
                <a href="{{ route('apresentador.control', $salaId) }}" class="text-xs font-bold text-amber-300 hover:underline">← Voltar ao estúdio</a>
                <h1 class="text-xl sm:text-3xl font-black text-white sm-logo-title mt-1 truncate">Pódio — {{ $sala->nome ?? ('Sala #' . $salaId) }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('apresentador.control', ['sala' => $salaId, 'nova_partida' => 1]) }}" class="sm-cta !w-auto !py-2 !px-4 !text-xs">Nova partida</a>
                <a href="{{ route('apresentador.painel') }}" class="sm-arcade-btn !py-2 !px-4 !text-xs">Salas</a>
            </div>
        </header>

        <div class="flex-1 lg:min-h-0 grid grid-cols-1 lg:grid-cols-12 lg:grid-rows-[minmax(0,1fr)] gap-4">
            <!-- Pódio + classificação -->
            <section class="lg:col-span-7 sm-panel p-4 sm:p-6 shadow-2xl flex flex-col lg:min-h-0 {{ $restantes->isEmpty() ? 'justify-center' : '' }}">
                @if($podio->isNotEmpty())
                    <div class="flex items-end justify-center gap-2 sm:gap-4 pt-2 shrink-0">
                        @foreach($podio as $idx => $p)
                            @php $pos = $idx + 1; $d = $degraus[$pos]; @endphp
                            <div class="{{ $d['ordem'] }} flex-1 max-w-[12rem] flex flex-col items-center text-center min-w-0">
                                <span class="text-2xl sm:text-3xl {{ $pos === 1 ? 'animate-bounce' : '' }}">{{ $d['medalha'] }}</span>
                                <p class="font-black text-white truncate max-w-full {{ $pos === 1 ? 'text-base sm:text-lg' : 'text-sm' }}">{{ $p->nome }}</p>
                                @if($p->equipe)
                                    <p class="text-[10px] text-blue-200/80 truncate max-w-full">{{ $p->equipe->nome }}</p>
                                @endif
                                <p class="font-mono font-black text-amber-300 text-sm">{{ $p->pontuacao_total }} pts</p>
                                <div class="w-full {{ $d['altura'] }} mt-1.5 rounded-t-2xl bg-gradient-to-b {{ $d['cor'] }} border-2 border-b-0 flex flex-col items-center justify-start pt-2 shadow-2xl">
                                    <span class="text-2xl sm:text-3xl font-black text-slate-950/80">{{ $pos }}º</span>
                                    @if($d['premio'])
                                        <span class="text-[10px] font-black text-slate-950/80 px-1.5 leading-tight line-clamp-2">{{ $d['premio'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="h-1.5 rounded-full bg-gradient-to-r from-transparent via-amber-400/60 to-transparent shrink-0"></div>

                    @if($restantes->isNotEmpty())
                        <div class="mt-3 space-y-1.5 lg:flex-1 lg:min-h-0 overflow-y-auto pr-1">
                            @foreach($restantes as $idx => $p)
                                <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-blue-950/60 border border-blue-400/20">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-7 h-7 rounded-lg bg-blue-900 text-blue-200 font-black flex items-center justify-center text-xs shrink-0">{{ $idx + 1 }}º</span>
                                        <div class="min-w-0">
                                            <p class="font-bold text-white text-sm truncate">{{ $p->nome }}</p>
                                            @if($p->equipe)
                                                <p class="text-[10px] text-blue-200/70 truncate">{{ $p->equipe->nome }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="font-mono font-black text-amber-300 text-sm shrink-0">{{ $p->pontuacao_total }} pts</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="lg:flex-1 flex flex-col items-center justify-center text-center py-10">
                        <span class="text-5xl">🏆</span>
                        <p class="text-sm text-blue-200 mt-3">Nenhum participante pontuou nesta partida.</p>
                        <p class="text-xs text-blue-300/70 mt-1">Pontuação registada: <strong class="text-amber-400">{{ $resultados['pontuacao_total'] ?? 0 }} pts</strong></p>
                    </div>
                @endif
            </section>

            <!-- Histórico de perguntas -->
            <section class="lg:col-span-5 sm-panel p-4 sm:p-5 shadow-2xl flex flex-col lg:min-h-0">
                <h2 class="text-sm font-black text-amber-300 uppercase tracking-wider mb-3 flex items-center justify-between shrink-0">
                    <span>Perguntas da partida</span>
                    <span class="sm-pill !px-2.5 !py-0.5 !text-[10px]">{{ count($historico) }}</span>
                </h2>
                <div class="space-y-2 lg:flex-1 lg:min-h-0 overflow-y-auto pr-1">
                    @forelse($historico as $index => $pergunta)
                        <div class="p-3 rounded-xl border border-blue-400/20 bg-blue-950/60">
                            <p class="font-bold text-white text-xs sm:text-sm">{{ $index + 1 }}. {{ $pergunta['texto'] ?? '' }}</p>
                            @if(!empty($pergunta['resposta_correta']))
                                <p class="text-xs mt-1">Resposta: <strong class="text-emerald-300">{{ $pergunta['resposta_correta'] }}</strong></p>
                            @endif
                            @if(!empty($pergunta['referencia_biblica']) || !empty($pergunta['explicacao']))
                                <p class="text-[11px] text-blue-200/80 mt-0.5">
                                    @if(!empty($pergunta['referencia_biblica']))<span class="text-amber-300 font-bold">[{{ $pergunta['referencia_biblica'] }}]</span>@endif
                                    {{ $pergunta['explicacao'] ?? '' }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-blue-200/70 text-center py-6">Sem histórico de perguntas.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
            if (!ativo) return;
            const som = new Audio("{{ asset('audio/classico/ranking.wav') }}");
            som.volume = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() * 0.5 : 0.45;
            som.play().catch(() => {});
        });
    </script>
</body>
</html>
