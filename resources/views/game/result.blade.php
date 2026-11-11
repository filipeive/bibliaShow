<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Resultado e Ranking Geral</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
    </style>
</head>
<body class="milhao-bg text-slate-100 min-h-screen">
    @include('partials.navbar', ['dark' => true])

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        @php
            $respostas = $resultados['respostas'] ?? [];
            $totalRespondidas = count($respostas);
            $totalAcertos = $resultados['total_acertos'] ?? count(array_filter($respostas, fn ($r) => !empty($r['acertou'])));
            $totalErros = $resultados['total_erros'] ?? count(array_filter($respostas, fn ($r) => empty($r['acertou']) && empty($r['tempo_esgotado'])));
            $percentual = $totalRespondidas > 0 ? round(($totalAcertos / $totalRespondidas) * 100) : 0;
            $nomeParticipante = $jogador_nome ?? ($resultados['jogador_nome'] ?? 'Participante');
            $mensagemAmigavel = $resultados['mensagem_amigavel'] ?? "Parabéns pela sua dedicação ao estudo da Palavra, {$nomeParticipante}! Confira abaixo seu resultado e sua posição no Ranking.";
            $perdeuPartida = !empty($resultados['perdeu_partida']);
        @endphp

        <!-- Card Principal de Resultado e Mensagem Amigável -->
        <div class="bg-slate-900/95 border-2 {{ $perdeuPartida ? 'border-amber-400/60' : 'border-emerald-400/60' }} rounded-3xl shadow-2xl p-6 sm:p-8 mb-8 text-center">
            <div class="w-16 h-16 rounded-2xl bg-amber-500/20 border-2 border-amber-400/50 flex items-center justify-center mx-auto mb-4 text-amber-400 shadow-lg">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                </svg>
            </div>

            <span class="inline-block px-3.5 py-1 rounded-full bg-amber-500/20 border border-amber-400/40 text-amber-300 text-xs font-black uppercase tracking-widest mb-2">
                Participante: {{ $nomeParticipante }}
            </span>

            <h1 class="text-2xl sm:text-4xl font-black text-white mb-3">
                {{ $perdeuPartida ? 'Muito Bem, ' . $nomeParticipante . '!' : 'Parabéns pelo Desempenho, ' . $nomeParticipante . '!' }}
            </h1>

            <!-- Mensagem Amigável -->
            <div class="max-w-2xl mx-auto bg-slate-950/80 border border-amber-500/30 rounded-2xl p-4 sm:p-5 mb-6 text-slate-200 text-sm sm:text-base leading-relaxed">
                {{ $mensagemAmigavel }}
            </div>

            <!-- KPIs da Partida -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 mb-7">
                <div class="bg-slate-950/90 border border-amber-500/30 rounded-2xl p-4">
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-bold">Pontuação Final</p>
                    <p class="text-2xl sm:text-3xl font-black text-amber-400 mt-1">{{ $resultados['pontuacao_total'] ?? 0 }} <span class="text-xs font-bold">pts</span></p>
                </div>
                <div class="bg-slate-950/90 border border-emerald-500/30 rounded-2xl p-4">
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-bold">Acertos</p>
                    <p class="text-2xl sm:text-3xl font-black text-emerald-400 mt-1">{{ $totalAcertos }} <span class="text-sm font-normal text-slate-500">/ {{ $totalRespondidas }}</span></p>
                </div>
                <div class="bg-slate-950/90 border border-rose-500/30 rounded-2xl p-4">
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-bold">Erros</p>
                    <p class="text-2xl sm:text-3xl font-black text-rose-400 mt-1">{{ $totalErros }} <span class="text-sm font-normal text-slate-500">/ 3</span></p>
                </div>
                <div class="bg-slate-950/90 border border-blue-500/30 rounded-2xl p-4">
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-bold">Aproveitamento</p>
                    <p class="text-2xl sm:text-3xl font-black text-blue-400 mt-1">{{ $percentual }}%</p>
                </div>
            </div>

            <!-- O Usuário Escolhe Quando Reiniciar -->
            <div class="flex flex-wrap justify-center gap-3.5">
                <a href="{{ route('game.play', ['novo' => 1]) }}"
                   class="inline-flex items-center gap-2.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 px-8 py-4 rounded-2xl font-black text-sm sm:text-base shadow-xl shadow-amber-500/25 transition transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Reiniciar e Jogar Novamente
                </a>

                <button type="button" onclick="abrirModalJogarAgora()"
                        class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white px-6 py-4 rounded-2xl font-bold text-sm transition">
                    Trocar Jogador / Modo de Jogo
                </button>

                <a href="/"
                   class="inline-flex items-center gap-2 bg-slate-950 hover:bg-slate-800 border border-slate-800 text-slate-300 px-5 py-4 rounded-2xl font-bold text-sm transition">
                    Voltar ao Início
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Coluna 1 (2 cols): Ranking Geral (Hall da Fama do Bíblia Show) -->
            <div class="lg:col-span-2 bg-slate-900/95 border border-amber-500/30 rounded-3xl p-6 shadow-xl h-fit">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-black text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                        </svg>
                        Ranking Geral Clássico
                    </h2>
                    <span class="text-[11px] font-bold text-amber-400 uppercase">Top 10</span>
                </div>

                <div class="space-y-2">
                    @foreach(array_slice($ranking_geral ?? [], 0, 10) as $idx => $item)
                        @php
                            $ehJogadorAtual = mb_strtolower(trim($item['nome'] ?? ''), 'UTF-8') === mb_strtolower(trim($nomeParticipante), 'UTF-8')
                                && (int) ($item['pontuacao'] ?? -1) === (int) ($resultados['pontuacao_total'] ?? -2);
                        @endphp
                        <div class="flex items-center justify-between p-3 rounded-2xl {{ $ehJogadorAtual ? 'bg-amber-500/20 border-2 border-amber-400' : 'bg-slate-950/80 border border-slate-800' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-7 h-7 rounded-lg {{ $idx === 0 ? 'bg-amber-500 text-slate-950' : ($idx === 1 ? 'bg-slate-300 text-slate-950' : 'bg-slate-800 text-slate-300') }} font-black text-xs flex items-center justify-center shrink-0">
                                    {{ $idx + 1 }}º
                                </span>
                                <div class="truncate">
                                    <p class="font-bold text-sm text-white truncate">
                                        {{ $item['nome'] ?? 'Participante' }}
                                        @if($ehJogadorAtual)
                                            <span class="text-[10px] font-black text-amber-300 ml-1">(Sua Partida)</span>
                                        @endif
                                    </p>
                                    <p class="text-[11px] text-slate-400">{{ $item['acertos'] ?? 0 }} acertos • {{ $item['data'] ?? '' }}</p>
                                </div>
                            </div>
                            <span class="font-mono font-black text-sm text-amber-400 shrink-0">{{ $item['pontuacao'] ?? 0 }} pts</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Coluna 2 (3 cols): Revisão de Perguntas e Aprendizado Bíblico -->
            <div class="lg:col-span-3 bg-slate-900/95 border border-slate-800 rounded-3xl p-6 shadow-xl">
                <div class="flex items-center gap-2.5 mb-4">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <h2 class="text-base font-black text-white">Revisão Bíblica das Perguntas Respondidas</h2>
                </div>

                @if(!empty($respostas))
                    <div class="space-y-3">
                        @foreach($respostas as $idx => $resp)
                            <div class="p-4 rounded-2xl border {{ !empty($resp['acertou']) ? 'border-emerald-500/30 bg-emerald-950/30' : 'border-rose-500/30 bg-rose-950/30' }}">
                                <div class="flex justify-between items-start gap-3 mb-1.5">
                                    <span class="font-bold text-sm text-white">
                                        {{ $idx + 1 }}. {{ $resp['pergunta_texto'] ?? ('Pergunta #' . ($resp['questao_id'] ?? ($idx + 1))) }}
                                    </span>
                                    <span class="shrink-0 text-xs font-extrabold px-2.5 py-1 rounded-full {{ !empty($resp['acertou']) ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                        {{ !empty($resp['acertou']) ? '+' . $resp['pontos'] . ' pts' : (!empty($resp['tempo_esgotado']) ? 'Tempo Esgotado' : 'Errou') }}
                                    </span>
                                </div>

                                <div class="text-xs text-slate-300 space-y-1 mt-2">
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
                @else
                    <p class="text-sm text-slate-400">Nenhuma resposta registrada nesta sessão.</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        const somRanking = new Audio('/audio/classico/ranking.wav');

        window.addEventListener('DOMContentLoaded', () => {
            const ativo = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
            if (ativo) {
                somRanking.volume = typeof obterVolumeGlobal === 'function' ? obterVolumeGlobal() * 0.6 : 0.5;
                somRanking.play().catch(() => {});
            }
        });

        window.addEventListener('biblia-som-alterado', (ev) => {
            if (!ev.detail.ativo) {
                somRanking.pause();
            } else {
                somRanking.volume = ev.detail.volume * 0.6;
                somRanking.play().catch(() => {});
            }
        });
    </script>
</body>
</html>
