<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Resultado Final</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
    </style>
</head>
<body class="milhao-bg text-slate-100 min-h-screen">

    <div class="max-w-3xl mx-auto px-4 py-8 sm:py-12">
        <header class="flex items-center justify-between mb-8">
            <a href="/" class="inline-flex items-center gap-2 text-slate-400 hover:text-white text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Voltar ao Início
            </a>
            <div class="flex items-center gap-2.5">
                <button type="button" onclick="alternarMusicaRanking()" id="btnSomRanking"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-600 text-amber-300 text-xs font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                    </svg>
                    <span>Tema do Ranking</span>
                </button>
                <a href="{{ route('game.play', ['novo' => 1]) }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 px-5 py-2.5 rounded-xl font-black text-sm shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Jogar Novamente
                </a>
            </div>
        </header>

        @php
            $respostas = $resultados['respostas'] ?? [];
            $totalRespondidas = count($respostas);
            $totalAcertos = $resultados['total_acertos'] ?? count(array_filter($respostas, fn ($r) => !empty($r['acertou'])));
            $percentual = $totalRespondidas > 0 ? round(($totalAcertos / $totalRespondidas) * 100) : 0;
        @endphp

        <div class="bg-slate-900/90 border-2 border-amber-500/40 rounded-3xl shadow-2xl p-6 sm:p-8 mb-8 text-center">
            <div class="w-16 h-16 rounded-2xl bg-amber-500/20 border-2 border-amber-400/50 flex items-center justify-center mx-auto mb-4 text-amber-400 shadow-lg">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white mb-1">Resultado da Partida</h1>
            <p class="text-slate-400 text-sm mb-6">Confira seu desempenho oficial no Bíblia Show</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-slate-950/80 border border-amber-500/30 rounded-2xl p-4">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Pontuação Final</p>
                    <p class="text-3xl font-black text-amber-400 mt-1">{{ $resultados['pontuacao_total'] ?? 0 }} <span class="text-xs font-bold">pts</span></p>
                </div>
                <div class="bg-slate-950/80 border border-emerald-500/30 rounded-2xl p-4">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Acertos</p>
                    <p class="text-3xl font-black text-emerald-400 mt-1">{{ $totalAcertos }} <span class="text-base font-normal text-slate-500">/ {{ $totalRespondidas }}</span></p>
                </div>
                <div class="bg-slate-950/80 border border-blue-500/30 rounded-2xl p-4">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Aproveitamento</p>
                    <p class="text-3xl font-black text-blue-400 mt-1">{{ $percentual }}%</p>
                </div>
            </div>

            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('game.play', ['novo' => 1]) }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 px-8 py-3.5 rounded-xl font-black shadow-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                    </svg>
                    Jogar Nova Partida
                </a>
                <a href="{{ route('curiosidades') }}" class="inline-flex items-center gap-2 bg-blue-900/70 hover:bg-blue-800 border border-blue-500/40 text-white px-6 py-3.5 rounded-xl font-bold transition">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    Enciclopédia & Regras
                </a>
            </div>
        </div>

        <!-- Revisão de Perguntas e Aprendizado Bíblico -->
        @if(!empty($respostas))
            <div class="bg-slate-900/90 border border-slate-700 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center gap-2.5 mb-4">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <h2 class="text-lg font-bold text-white">Revisão e Estudo Bíblico da Partida</h2>
                </div>
                <div class="space-y-3.5">
                    @foreach($respostas as $idx => $resp)
                        <div class="p-4 rounded-xl border {{ !empty($resp['acertou']) ? 'border-emerald-500/30 bg-emerald-950/30' : 'border-rose-500/30 bg-rose-950/30' }}">
                            <div class="flex justify-between items-start gap-3 mb-1.5">
                                <span class="font-bold text-sm sm:text-base text-white">
                                    {{ $idx + 1 }}. {{ $resp['pergunta_texto'] ?? ('Pergunta #' . ($resp['questao_id'] ?? ($idx + 1))) }}
                                </span>
                                <span class="shrink-0 text-xs font-extrabold px-2.5 py-1 rounded-full {{ !empty($resp['acertou']) ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                    {{ !empty($resp['acertou']) ? '+' . $resp['pontos'] . ' pts' : 'Errou' }}
                                </span>
                            </div>

                            <div class="text-xs sm:text-sm text-slate-300 space-y-1 mt-2">
                                @if(!empty($resp['resposta_correta']))
                                    <p>Resposta correta: <strong class="text-emerald-300">{{ $resp['resposta_correta'] }}</strong></p>
                                @endif
                                @if(!empty($resp['referencia_biblica']) || !empty($resp['explicacao']))
                                    <p class="text-slate-400">
                                        @if(!empty($resp['referencia_biblica']))
                                            <span class="text-amber-300 font-semibold">[{{ $resp['referencia_biblica'] }}]</span>
                                        @endif
                                        {{ $resp['explicacao'] ?? '' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        const somRanking = new Audio('/audio/classico/ranking.wav');
        somRanking.volume = 0.5;
        let tocandoRanking = false;

        window.addEventListener('DOMContentLoaded', () => {
            somRanking.play().then(() => { tocandoRanking = true; }).catch(() => {});
        });

        function alternarMusicaRanking() {
            if (tocandoRanking) {
                somRanking.pause();
                tocandoRanking = false;
            } else {
                somRanking.currentTime = 0;
                somRanking.play().catch(() => {});
                tocandoRanking = true;
            }
        }
    </script>
</body>
</html>
