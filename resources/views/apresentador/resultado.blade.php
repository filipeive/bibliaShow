<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Pódio e Resultado da Sala</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
    </style>
</head>
<body class="milhao-bg text-slate-100 min-h-screen">

    @include('partials.navbar', ['dark' => true])

    <div class="max-w-4xl mx-auto px-4 py-8">
        <header class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('apresentador.control', $salaId) }}" class="text-xs font-bold text-amber-400 hover:underline">← Voltar ao Estúdio da Sala</a>
                <h1 class="text-3xl font-black text-white mt-1">Pódio & Relatório Final — {{ $sala->nome ?? ('Sala #' . $salaId) }}</h1>
            </div>
            <div class="flex gap-2.5">
                <a href="{{ route('apresentador.control', ['sala' => $salaId, 'nova_partida' => 1]) }}"
                   class="bg-amber-500 hover:bg-amber-400 text-slate-950 px-5 py-2.5 rounded-xl font-black text-xs sm:text-sm shadow">
                    Nova Partida na Sala
                </a>
                <a href="{{ route('apresentador.painel') }}"
                   class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm border border-slate-700">
                    Painel de Salas
                </a>
            </div>
        </header>

        <!-- Classificação dos Participantes -->
        <div class="bg-slate-900/95 border border-amber-500/40 rounded-3xl shadow-2xl p-6 sm:p-8 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <h2 class="text-lg font-black text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                    </svg>
                    Classificação Geral e Premiação dos Vencedores
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
