<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">
    <title>Bíblia Show — Resultado e Ranking Geral</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Ecrã inteiro: resumo sempre visível; só as listas rolam dentro do painel */
        @media (max-height: 540px) and (orientation: landscape) and (max-width: 1023px) {
            .result-layout { display: grid !important; grid-template-columns: 5fr 7fr; grid-template-rows: minmax(0, 1fr); }
            .result-layout > section { min-height: 0 !important; overflow-y: auto; }
            .result-layout .result-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .result-layout h1 { font-size: 1.05rem !important; }
            .result-layout .result-actions svg { display: none; }
            .result-layout .result-actions > * { font-size: .7rem !important; white-space: nowrap; }
            .result-layout .sm-tab-count { display: none; }
        }

    </style>
</head>
<body class="sm-stage text-slate-100 sm-viewport flex flex-col">
    @include('partials.navbar', ['dark' => true, 'compact' => true])

    <div class="sm-shell max-w-6xl mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-6 w-full flex flex-col">
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
            $rankingTop = array_slice($ranking_geral ?? [], 0, 10);
            $classeBotaoAcao = 'w-full h-11 lg:h-12 inline-flex items-center justify-center gap-1.5 sm:gap-2 rounded-xl transition font-bold text-xs sm:text-sm whitespace-nowrap';
        @endphp

        <div class="result-layout flex-1 min-h-0 flex flex-col gap-3 sm:gap-4 lg:grid lg:grid-cols-12 lg:grid-rows-[minmax(0,1fr)] lg:gap-6">

            <!-- Painel de Vitória / Pódio -->
            <section class="lg:col-span-5 sm-panel p-4 sm:p-6 lg:p-8 text-center relative overflow-hidden shrink-0 flex flex-col justify-center">
                <div class="flex items-center justify-center gap-4 lg:flex-col lg:gap-0">
                    <!-- Troféu Dourado -->
                    <div class="relative inline-flex flex-col items-center justify-center shrink-0 lg:mb-4">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-24 lg:h-24 rounded-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center border-4 border-amber-200 shadow-2xl shadow-amber-500/50 text-slate-950">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 lg:w-12 lg:h-12 fill-current" viewBox="0 0 24 24">
                                <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1c1.94-.37 3.5-1.99 3.61-3.96C19.08 11.63 21 9.55 21 7V5c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/>
                            </svg>
                        </div>
                        <div class="absolute -top-1.5 -right-1.5 w-6 h-6 lg:w-8 lg:h-8 rounded-full bg-amber-500 border border-amber-200 flex items-center justify-center text-[11px] lg:text-sm font-black text-slate-950 shadow-lg shadow-amber-500/40" title="Acertos">
                            {{ $totalAcertos }}
                        </div>
                    </div>

                    <div class="min-w-0 text-left lg:text-center">
                        <span class="sm-pill !px-3 !py-1 lg:!px-5 lg:!py-1.5 !text-xs sm:!text-sm lg:!text-base inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <span>{{ $pontosTotal }} PONTOS</span>
                        </span>
                        <h1 class="text-lg sm:text-2xl lg:text-3xl font-black text-white sm-logo-title mt-1.5 lg:mt-3 leading-tight">
                            {{ $perdeuPartida ? 'Muito Bem, ' . $nomeParticipante . '!' : 'Parabéns pela Conquista, ' . $nomeParticipante . '!' }}
                        </h1>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-blue-200 max-w-xl mx-auto mt-2 lg:mt-3 sm-hide-short">
                    {{ $mensagemAmigavel }}
                </p>

                <!-- 4 Lingotes (KPIs Reais da Partida) -->
                <div class="result-kpis grid grid-cols-4 gap-2 sm:gap-3 max-w-2xl mx-auto w-full mt-3 lg:mt-6 lg:grid-cols-2">
                    <div class="sm-ingot">
                        <div class="sm-ingot-label">PONTOS</div>
                        <div class="sm-ingot-value sm:text-xl">{{ $pontosTotal }}</div>
                    </div>
                    <div class="sm-ingot is-acertar">
                        <div class="sm-ingot-label">ACERTOS</div>
                        <div class="sm-ingot-value sm:text-xl">{{ $totalAcertos }}/{{ $totalRespondidas }}</div>
                    </div>
                    <div class="sm-ingot is-errar">
                        <div class="sm-ingot-label">ERROS</div>
                        <div class="sm-ingot-value sm:text-xl">{{ $totalErros }}/3</div>
                    </div>
                    <div class="sm-ingot">
                        <div class="sm-ingot-label">PRECISÃO</div>
                        <div class="sm-ingot-value sm:text-xl">{{ $percentual }}%</div>
                    </div>
                </div>

                <!-- Faixa Vermelha de Conquista -->
                <div class="sm-banner-red py-2 px-4 rounded-2xl max-w-lg mx-auto w-full text-xs sm:text-sm uppercase tracking-wider mt-3 lg:mt-6 shadow-lg sm-hide-short">
                    {{ $perdeuPartida ? 'Bom jogo! Continue estudando as Sagradas Escrituras!' : 'Conhecimento Bíblico em Alto Nível!' }}
                </div>

                <!-- Ações Principais -->
                <div class="result-actions grid grid-cols-3 gap-2 sm:gap-3 max-w-xl mx-auto w-full mt-3 lg:mt-6">
                    <a href="{{ route('game.play', ['novo' => 1]) }}"
                       class="sm-cta {{ $classeBotaoAcao }} !h-11 lg:!h-12 !px-2 cursor-pointer shadow-xl">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 5V2L7 7l5 5V8a6 6 0 11-5.65 8h-2.1A8 8 0 1012 5z" />
                        </svg>
                        <span>Repetir</span>
                    </a>

                    <button type="button" onclick="abrirModalJogarAgora()"
                            class="{{ $classeBotaoAcao }} bg-blue-900/80 hover:bg-blue-800 border-2 border-blue-400/50 text-blue-100 shadow">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-none stroke-current shrink-0" viewBox="0 0 24 24" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                        </svg>
                        <span>Novo Jogo</span>
                    </button>

                    <a href="{{ url('/') }}"
                       class="{{ $classeBotaoAcao }} bg-blue-950/90 hover:bg-blue-900 border border-blue-400/30 text-blue-300">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                        </svg>
                        <span>Início</span>
                    </a>
                </div>

                <a href="{{ config('app.igreja_url') }}" class="sm-hide-short mt-3 lg:mt-4 inline-flex items-center justify-center gap-1.5 text-xs font-bold text-blue-200 hover:text-amber-300 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                    Voltar à Life Church
                </a>
            </section>

            <!-- Ranking & Gabarito em separadores (lista rola dentro do painel) -->
            <section class="lg:col-span-7 sm-panel p-3 sm:p-5 shadow-xl flex-1 min-h-[15rem] lg:min-h-0 flex flex-col">
                <div class="grid grid-cols-2 gap-2 mb-3 shrink-0" role="tablist" aria-label="Ranking e gabarito">
                    <button type="button" role="tab" id="tabRanking" aria-controls="painelRanking" aria-selected="true"
                            onclick="mostrarSeparadorResultado('Ranking')"
                            class="sm-tab is-active">
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1c1.94-.37 3.5-1.99 3.61-3.96C19.08 11.63 21 9.55 21 7V5c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>
                        <span class="sm:hidden">Ranking</span>
                        <span class="hidden sm:inline">Hall da Fama</span>
                        <span class="sm-tab-count">Top 10</span>
                    </button>
                    <button type="button" role="tab" id="tabGabarito" aria-controls="painelGabarito" aria-selected="false"
                            onclick="mostrarSeparadorResultado('Gabarito')"
                            class="sm-tab">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        <span>Gabarito</span>
                        <span class="sm-tab-count">{{ $totalRespondidas }}</span>
                    </button>
                </div>

                <!-- Ranking Geral / Hall da Fama -->
                <div id="painelRanking" role="tabpanel" aria-labelledby="tabRanking" data-fit-scroll class="flex-1 min-h-0 overflow-y-auto pr-1 space-y-1.5 sm:space-y-2">
                    @forelse($rankingTop as $idx => $item)
                        @php
                            $ehJogadorAtual = mb_strtolower(trim($item['nome'] ?? ''), 'UTF-8') === mb_strtolower(trim($nomeParticipante), 'UTF-8')
                                && (int) ($item['pontuacao'] ?? -1) === (int) ($resultados['pontuacao_total'] ?? -2);
                        @endphp
                        <div class="flex items-center justify-between p-2 sm:p-2.5 rounded-xl {{ $ehJogadorAtual ? 'bg-amber-500/25 border-2 border-amber-300 shadow-lg shadow-amber-500/20' : 'bg-blue-950/60 border border-blue-400/20' }}">
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
                    @empty
                        <p class="text-xs text-blue-200 text-center py-6">Ainda não há pontuações no ranking.</p>
                    @endforelse
                </div>

                <!-- Revisão Bíblica / Gabarito -->
                <div id="painelGabarito" role="tabpanel" aria-labelledby="tabGabarito" data-fit-scroll class="hidden flex-1 min-h-0 overflow-y-auto pr-1 space-y-2 sm:space-y-3">
                    @forelse($respostas as $idx => $resp)
                        <div class="p-3 rounded-xl border {{ !empty($resp['acertou']) ? 'border-emerald-500/40 bg-emerald-950/40' : 'border-rose-500/40 bg-rose-950/40' }}">
                            <div class="flex justify-between items-start gap-2 mb-1">
                                <span class="font-bold text-xs sm:text-sm text-white">
                                    {{ $idx + 1 }}. {{ $resp['pergunta_texto'] ?? ('Pergunta #' . ($resp['questao_id'] ?? ($idx + 1))) }}
                                </span>
                                <span class="shrink-0 text-[10px] font-black px-2 py-0.5 rounded-full {{ !empty($resp['acertou']) ? 'bg-emerald-500 text-slate-950' : 'bg-rose-500 text-white' }}">
                                    {{ !empty($resp['acertou']) ? '+' . $resp['pontos'] . ' pts' : (!empty($resp['tempo_esgotado']) ? 'Tempo Esgotado' : 'Errou') }}
                                </span>
                            </div>

                            <div class="text-xs text-blue-100 space-y-1 mt-1.5">
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
                    @empty
                        <p class="text-xs text-blue-200 text-center py-6">Nenhuma resposta registrada nesta sessão.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <script>
        function mostrarSeparadorResultado(nome) {
            ['Ranking', 'Gabarito'].forEach((sep) => {
                const ativo = sep === nome;
                const tab = document.getElementById('tab' + sep);
                const painel = document.getElementById('painel' + sep);
                if (tab) {
                    tab.classList.toggle('is-active', ativo);
                    tab.setAttribute('aria-selected', ativo ? 'true' : 'false');
                }
                if (painel) {
                    painel.classList.toggle('hidden', !ativo);
                }
            });
        }

        const somRanking = new Audio("{{ asset('audio/classico/ranking.wav') }}");

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
