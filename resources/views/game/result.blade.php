<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Resultado e Ranking Geral</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
    </style>
</head>
<body class="sm-stage text-slate-100 min-h-screen flex flex-col">
    @include('partials.navbar', ['dark' => true])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 flex-1 w-full">
        @php
            $respostas = $resultados['respostas'] ?? [];
            $totalRespondidas = count($respostas);
            $totalAcertos = $resultados['total_acertos'] ?? count(array_filter($respostas, fn ($r) => !empty($r['acertou'])));
            $totalErros = $resultados['total_erros'] ?? count(array_filter($respostas, fn ($r) => empty($r['acertou']) && empty($r['tempo_esgotado'])));
            $percentual = $totalRespondidas > 0 ? round(($totalAcertos / $totalRespondidas) * 100) : 0;
            $nomeParticipante = $jogador_nome ?? ($resultados['jogador_nome'] ?? 'Participante');
            $mensagemAmigavel = $resultados['mensagem_amigavel'] ?? "Parabéns pela sua dedicação ao estudo da Palavra, {$nomeParticipante}! Confira abaixo sua pontuação no Ranking.";
            $perdeuPartida = !empty($resultados['perdeu_partida']);
            $pontosTotal = $resultados['pontuacao_total'] ?? 0;
        @endphp

        <!-- Painel Central de Vitória / Pódio (Estilo Show do Milhão Imagem 2) -->
        <div class="sm-panel p-6 sm:p-10 mb-8 text-center relative overflow-hidden">
            <!-- Troféu Dourado com Louros -->
            <div class="relative inline-flex flex-col items-center justify-center mb-3">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border-4 border-amber-200 shadow-2xl shadow-amber-500/50 text-slate-950">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 fill-current" viewBox="0 0 24 24">
                        <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1c1.94-.37 3.5-1.99 3.61-3.96C19.08 11.63 21 9.55 21 7V5c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/>
                    </svg>
                </div>
                <div class="absolute -top-2 -right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-amber-500 border border-amber-200 flex items-center justify-center text-xs sm:text-sm font-black text-slate-950 shadow-lg shadow-amber-500/40">
                    {{ $totalAcertos }}
                </div>
            </div>

            <!-- Pílula Ouro de Pontuação Final -->
            <div class="mt-4 mb-3">
                <span class="sm-pill !px-6 !py-2 !text-base sm:!text-lg inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <span>{{ $pontosTotal }} PONTOS ACUMULADOS</span>
                    <svg class="w-4 h-4 text-amber-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                </span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-black text-white sm-logo-title mb-2">
                {{ $perdeuPartida ? 'Muito Bem, ' . $nomeParticipante . '!' : 'Parabéns pela Conquista, ' . $nomeParticipante . '!' }}
            </h1>

            <p class="text-xs sm:text-sm text-blue-200 max-w-xl mx-auto mb-6">
                {{ $mensagemAmigavel }}
            </p>

            <!-- 4 Lingotes de Ouro (KPIS Reais da Partida) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-2xl mx-auto mb-8">
                <div class="sm-ingot">
                    <div class="sm-ingot-label">PONTOS</div>
                    <div class="sm-ingot-value text-xl">{{ $pontosTotal }}</div>
                </div>
                <div class="sm-ingot is-acertar">
                    <div class="sm-ingot-label">ACERTOS</div>
                    <div class="sm-ingot-value text-xl">{{ $totalAcertos }} / {{ $totalRespondidas }}</div>
                </div>
                <div class="sm-ingot is-errar">
                    <div class="sm-ingot-label">ERROS</div>
                    <div class="sm-ingot-value text-xl">{{ $totalErros }} / 3</div>
                </div>
                <div class="sm-ingot">
                    <div class="sm-ingot-label">PRECISÃO</div>
                    <div class="sm-ingot-value text-xl">{{ $percentual }}%</div>
                </div>
            </div>

            <!-- Faixa Vermelha de Conquista Estilo Show do Milhão -->
            <div class="sm-banner-red py-2.5 px-4 rounded-2xl max-w-lg mx-auto text-sm uppercase tracking-wider mb-8 shadow-lg">
                {{ $perdeuPartida ? 'Bom jogo! Continue estudando as Sagradas Escrituras!' : 'Conhecimento Bíblico em Alto Nível!' }}
            </div>

            <!-- Ações Principais em Botões de Palco -->
            @php
                $classeBotaoCircular = 'w-12 h-12 inline-flex items-center justify-center rounded-full transition';
            @endphp
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 max-w-xl mx-auto">
                <a href="{{ route('game.play', ['novo' => 1]) }}"
                   class="sm-cta !text-sm cursor-pointer shadow-xl">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                    </svg>
                    <span>Jogar Novamente</span>
                </a>

                <button type="button" onclick="abrirModalJogarAgora()"
                        aria-label="Jogar Agora" title="Jogar Agora"
                    class="{{ $classeBotaoCircular }} bg-blue-900/80 hover:bg-blue-800 border-2 border-blue-400/50 text-blue-100 shadow">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M17.65 6.35A7.95 7.95 0 0012 4a8 8 0 100 16 8 8 0 007.93-7h-2.02A6 6 0 1112 6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z" />
                    </svg>
                </button>

                <a href="/"
                   aria-label="Início" title="Início"
                         class="{{ $classeBotaoCircular }} bg-blue-950/90 hover:bg-blue-900 border border-blue-400/30 text-blue-300">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Ranking Geral / Hall da Fama (2 cols) -->
            <div class="lg:col-span-2 sm-panel p-5 sm:p-6 shadow-xl h-fit">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-blue-400/20">
                    <h2 class="text-sm font-black text-amber-300 flex items-center gap-2 uppercase tracking-wider">
                        <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1c1.94-.37 3.5-1.99 3.61-3.96C19.08 11.63 21 9.55 21 7V5c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>
                        <span>Hall da Fama (Top 10)</span>
                    </h2>
                    <span class="sm-pill !px-2.5 !py-0.5 !text-[10px]">OFICIAL</span>
                </div>

                <div class="space-y-2">
                    @foreach(array_slice($ranking_geral ?? [], 0, 10) as $idx => $item)
                        @php
                            $ehJogadorAtual = mb_strtolower(trim($item['nome'] ?? ''), 'UTF-8') === mb_strtolower(trim($nomeParticipante), 'UTF-8')
                                && (int) ($item['pontuacao'] ?? -1) === (int) ($resultados['pontuacao_total'] ?? -2);
                        @endphp
                        <div class="flex items-center justify-between p-2.5 rounded-xl {{ $ehJogadorAtual ? 'bg-amber-500/25 border-2 border-amber-300 shadow-lg shadow-amber-500/20' : 'bg-blue-950/60 border border-blue-400/20' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-6 h-6 rounded-lg {{ $idx === 0 ? 'bg-amber-400 text-slate-950 font-black' : ($idx === 1 ? 'bg-slate-300 text-slate-950 font-bold' : ($idx === 2 ? 'bg-amber-700 text-amber-100 font-bold' : 'bg-blue-900 text-blue-200 font-bold')) }} text-[11px] flex items-center justify-center shrink-0">
                                    {{ $idx + 1 }}º
                                </span>
                                <div class="truncate">
                                    <p class="font-bold text-xs text-white truncate">
                                        {{ $item['nome'] ?? 'Participante' }}
                                        @if($ehJogadorAtual)
                                            <span class="text-[9px] font-black text-amber-300 uppercase ml-1">(Você)</span>
                                        @endif
                                    </p>
                                    <p class="text-[10px] text-blue-300/80">{{ $item['acertos'] ?? 0 }} acertos • {{ $item['data'] ?? '' }}</p>
                                </div>
                            </div>
                            <span class="font-mono font-black text-xs text-amber-300 shrink-0">{{ $item['pontuacao'] ?? 0 }} pts</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Revisão Bíblica (3 cols) -->
            <div class="lg:col-span-3 sm-panel p-5 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-blue-400/20">
                    <h2 class="text-sm font-black text-amber-300 flex items-center gap-2 uppercase tracking-wider">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        <span>Gabarito & Estudos da Partida</span>
                    </h2>
                    <span class="text-xs text-blue-200 font-bold">{{ count($respostas) }} respondida(s)</span>
                </div>

                @if(!empty($respostas))
                    <div class="space-y-3 max-h-[520px] overflow-y-auto pr-1">
                        @foreach($respostas as $idx => $resp)
                            <div class="p-3.5 rounded-xl border {{ !empty($resp['acertou']) ? 'border-emerald-500/40 bg-emerald-950/40' : 'border-rose-500/40 bg-rose-950/40' }}">
                                <div class="flex justify-between items-start gap-2 mb-1.5">
                                    <span class="font-bold text-xs sm:text-sm text-white">
                                        {{ $idx + 1 }}. {{ $resp['pergunta_texto'] ?? ('Pergunta #' . ($resp['questao_id'] ?? ($idx + 1))) }}
                                    </span>
                                    <span class="shrink-0 text-[10px] font-black px-2 py-0.5 rounded-full {{ !empty($resp['acertou']) ? 'bg-emerald-500 text-slate-950' : 'bg-rose-500 text-white' }}">
                                        {{ !empty($resp['acertou']) ? '+' . $resp['pontos'] . ' pts' : (!empty($resp['tempo_esgotado']) ? 'Tempo Esgotado' : 'Errou') }}
                                    </span>
                                </div>

                                <div class="text-xs text-blue-100 space-y-1 mt-2">
                                    @if(!empty($resp['resposta_correta']))
                                        <p>Resposta correta: <strong class="text-emerald-300">{{ $resp['resposta_correta'] }}</strong></p>
                                    @endif
                                    @if(!empty($resp['referencia_biblica']) || !empty($resp['explicacao']))
                                        <p class="text-blue-200/90 text-[11px] leading-relaxed">
                                            @if(!empty($resp['referencia_biblica']))
                                                <span class="text-amber-300 font-bold">[{{ $resp['referencia_biblica'] }}]</span>
                                            @endif
                                            {{ $resp['explicacao'] ?? '' }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-blue-200 text-center py-6">Nenhuma resposta registrada nesta sessão.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="sm-crowd w-full mt-auto"></div>

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
