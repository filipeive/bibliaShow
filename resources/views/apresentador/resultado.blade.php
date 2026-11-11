<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Pódio e Resultado da Sala</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="sm-stage text-slate-100 min-h-screen">

    @include('partials.navbar', ['dark' => true])

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
        <header class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('apresentador.control', $salaId) }}" class="text-xs font-bold text-amber-300 hover:underline">← Voltar ao Estúdio da Sala</a>
                <div class="flex items-center gap-2 mt-2">
                    <span class="sm-pill !px-3 !py-0.5 !text-[10px] uppercase">ENCERRAMENTO</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-white sm-logo-title">Pódio & Relatório Oficial — {{ $sala->nome ?? ('Sala #' . $salaId) }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('apresentador.control', ['sala' => $salaId, 'nova_partida' => 1]) }}"
                   class="sm-cta !w-auto !py-2.5 !px-5 !text-xs cursor-pointer shadow-lg">
                    <span>Nova Partida na Sala</span>
                </a>
                <a href="{{ route('apresentador.painel') }}"
                   class="bg-blue-950 hover:bg-blue-900 border border-blue-400/30 text-blue-200 px-4 py-2.5 rounded-full font-bold text-xs transition">
                    Painel de Salas
                </a>
            </div>
        </header>

        <!-- Classificação dos Participantes (Show do Milhão Pódio) -->
        <div class="sm-panel p-6 sm:p-8 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-3 border-b border-blue-400/20">
                <h2 class="text-lg font-black text-amber-300 flex items-center gap-2 uppercase tracking-wider">
                    <span>🏆</span> Classificação Oficial & Premiação
                </h2>
                @if($sala && ($sala->premio_primeiro_lugar || $sala->premio_segundo_lugar))
                    <div class="flex flex-wrap gap-2 text-xs">
                        @if($sala->premio_primeiro_lugar)
                            <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/50 text-amber-300 font-bold">
                                1º Lugar: {{ $sala->premio_primeiro_lugar }}
                            </span>
                        @endif
                        @if($sala->premio_segundo_lugar)
                            <span class="px-3 py-1 rounded-full bg-slate-800 border border-slate-600 text-slate-200 font-bold">
                                2º Lugar: {{ $sala->premio_segundo_lugar }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            @if(isset($participantes) && $participantes->isNotEmpty())
                <div class="space-y-2.5">
                    @foreach($participantes as $idx => $p)
                        <div class="flex items-center justify-between p-4 rounded-2xl {{ $idx === 0 ? 'bg-amber-500/20 border-2 border-amber-400/60' : ($idx === 1 ? 'bg-slate-800/80 border-2 border-slate-500/50' : 'bg-slate-950/80 border border-slate-800') }}">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl {{ $idx === 0 ? 'bg-amber-500 text-slate-950' : ($idx === 1 ? 'bg-slate-300 text-slate-950' : 'bg-slate-800 text-slate-300') }} font-black flex items-center justify-center text-sm">
                                    {{ $idx + 1 }}º
                                </span>
                                <div>
                                    <p class="font-bold text-white">{{ $p->nome }}</p>
                                    @if($p->equipe)
                                        <p class="text-xs text-slate-400">Equipe: {{ $p->equipe->nome }}</p>
                                    @endif
                                    @if($idx === 0 && !empty($sala?->premio_primeiro_lugar))
                                        <p class="text-xs font-extrabold text-amber-300 mt-0.5">Prêmio 1º Lugar: {{ $sala->premio_primeiro_lugar }}</p>
                                    @elseif($idx === 1 && !empty($sala?->premio_segundo_lugar))
                                        <p class="text-xs font-extrabold text-slate-300 mt-0.5">Prêmio 2º Lugar: {{ $sala->premio_segundo_lugar }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="font-mono font-black text-xl text-amber-400">{{ $p->pontuacao_total }} pts</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-400">Pontuação total registrada na sessão: <strong class="text-amber-400">{{ $resultados['pontuacao_total'] ?? 0 }} pts</strong></p>
            @endif
        </div>

        <!-- Histórico de Perguntas Jogadas -->
        @if(!empty($resultados['historico_perguntas']))
            <div class="bg-slate-900/95 border border-slate-800 rounded-3xl p-6 sm:p-8">
                <h3 class="text-lg font-black text-white mb-4">Perguntas Apresentadas na Partida</h3>
                <div class="space-y-3">
                    @foreach($resultados['historico_perguntas'] as $index => $pergunta)
                        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-950/80">
                            <div class="flex justify-between items-start gap-3 mb-1.5">
                                <span class="font-bold text-white text-sm">
                                    {{ $index + 1 }}. {{ $pergunta['texto'] ?? $pergunta->texto ?? '' }}
                                </span>
                                @if(!empty($pergunta['resposta_correta']))
                                    <span class="shrink-0 text-xs font-black px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300">
                                        {{ $pergunta['resposta_correta'] }}
                                    </span>
                                @endif
                            </div>
                            @if(!empty($pergunta['referencia_biblica']) || !empty($pergunta['explicacao']))
                                <p class="text-xs text-slate-400">
                                    {{ !empty($pergunta['referencia_biblica']) ? '[' . $pergunta['referencia_biblica'] . '] ' : '' }}
                                    {{ $pergunta['explicacao'] ?? '' }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const som = new Audio('/audio/classico/ranking.wav');
            som.volume = 0.45;
            som.play().catch(() => {});
        });
    </script>
</body>
</html>
