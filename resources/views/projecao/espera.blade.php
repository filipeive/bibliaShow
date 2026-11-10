<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Aguardando Início ({{ $sala->codigo_acesso ?? $salaId }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .projection-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
    </style>
</head>
<body class="projection-bg text-white min-h-screen flex flex-col justify-between p-6 sm:p-10">

    <!-- Topo -->
    <header class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 pb-4 max-w-6xl mx-auto w-full">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs font-black tracking-widest text-emerald-400 uppercase">Lobby Ao Vivo • Bíblia Show Arena</span>
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Sala Oficial</span>
            <span class="text-lg font-black text-amber-300">{{ $sala->nome ?? 'Arena Bíblica' }}</span>
        </div>
    </header>

    <!-- Conteúdo Central -->
    <main class="max-w-4xl mx-auto w-full my-auto py-8">
        <div class="bg-slate-900/90 border-2 border-amber-500/40 rounded-3xl p-8 sm:p-12 text-center shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/15 border border-amber-400/30 text-amber-300 text-xs font-black uppercase tracking-wider mb-4">
                Aponte a câmera do celular para entrar
            </div>

            <h1 class="text-3xl sm:text-5xl font-black text-white mb-2">Bíblia Show</h1>
            <p class="text-slate-300 text-base sm:text-lg mb-8">Preparem seus celulares, o Show do Milhão Bíblico vai começar!</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center justify-items-center mb-8">
                <!-- QR Code e Link -->
                <div class="flex flex-col items-center gap-3">
                    <div id="qrcodeLobby" class="p-4 bg-white rounded-2xl shadow-2xl border-4 border-amber-400"></div>
                    <p class="text-xs text-slate-400 mt-1">
                        Ou acesse no navegador: <strong class="text-amber-300">/entrar/{{ $sala->codigo_acesso }}</strong>
                    </p>
                </div>

                <!-- Código Gigante e Status -->
                <div class="space-y-4 text-center md:text-left w-full max-w-sm">
                    <div class="p-6 rounded-2xl bg-slate-950/80 border border-slate-800">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Código de Acesso</span>
                        <span class="text-4xl sm:text-5xl font-mono font-black text-amber-400 tracking-wider block">
                            {{ $sala->codigo_acesso }}
                        </span>
                    </div>

                    @if($sala && ($sala->premio_primeiro_lugar || $sala->premio_segundo_lugar))
                        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-left">
                            <span class="text-[11px] font-black uppercase tracking-wider text-amber-400 block mb-1">Prêmios da Partida:</span>
                            @if($sala->premio_primeiro_lugar)
                                <p class="text-xs text-white font-bold">🥇 1º: <span class="text-amber-300">{{ $sala->premio_primeiro_lugar }}</span></p>
                            @endif
                            @if($sala->premio_segundo_lugar)
                                <p class="text-xs text-slate-300 font-semibold mt-0.5">🥈 2º: {{ $sala->premio_segundo_lugar }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Participantes Conectados em Tempo Real -->
            <div class="pt-6 border-t border-slate-800">
                <div class="flex items-center justify-center gap-2 mb-4">
                    <div class="w-3 h-3 rounded-full border-2 border-amber-400 border-t-transparent animate-spin"></div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-300">
                        <span id="contadorLobby">0</span> participante(s) na sala de espera
                    </span>
                </div>

                <div id="participantesLobbyGrid" class="flex flex-wrap items-center justify-center gap-2 max-h-36 overflow-y-auto">
                    <span class="text-xs text-slate-500 italic">Aguardando participantes digitarem o código no smartphone...</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Rodapé -->
    <footer class="text-center text-xs text-slate-400 max-w-6xl mx-auto w-full pt-4 border-t border-white/5">
        Bíblia Show • Arena Interativa em Tempo Real • Conectando a Igreja através do Conhecimento Bíblico
    </footer>

    <script>
        const salaId = {{ $sala->id ?? $salaId }};
        const codigoAcesso = '{{ $sala->codigo_acesso ?? "" }}';

        // Gerar QR Code
        const qrBox = document.getElementById('qrcodeLobby');
        if (qrBox && typeof QRCode !== 'undefined' && codigoAcesso) {
            new QRCode(qrBox, {
                text: window.location.origin + '/entrar/' + codigoAcesso,
                width: 170,
                height: 170,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H,
            });
        }

        let jogoIniciou = false;

        async function verificarStatusSalaLobby() {
            if (jogoIniciou) return;

            try {
                const resp = await fetch(`/api/salas/${salaId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!resp.ok) return;
                const data = await resp.json();

                // 1. Atualizar lista de participantes conectados em tempo real
                const grid = document.getElementById('participantesLobbyGrid');
                const contador = document.getElementById('contadorLobby');
                const ranking = data.ranking || [];

                if (contador) contador.textContent = ranking.length;

                if (grid) {
                    if (ranking.length === 0) {
                        grid.innerHTML = '<span class="text-xs text-slate-500 italic">Aguardando participantes digitarem o código no smartphone...</span>';
                    } else {
                        grid.innerHTML = ranking.map(p => `
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-950/80 border border-slate-800 text-xs font-bold text-white shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                ${p.nome}
                                ${p.equipe ? `<span class="text-slate-400 text-[10px]">(${p.equipe})</span>` : ''}
                            </span>
                        `).join('');
                    }
                }

                // 2. Transição automática em tempo real para a tela do jogo quando o apresentador iniciar
                if (data.estado === 'em_curso' && data.pergunta_atual) {
                    jogoIniciou = true;
                    // Redireciona imediatamente para a tela de projeção sem reload em loop
                    window.location.href = `/projecao/${salaId}`;
                }
            } catch (err) {}
        }

        // Checagem em tempo real a cada 1.2 segundos (sem refresh de tela!)
        setInterval(verificarStatusSalaLobby, 1200);
        verificarStatusSalaLobby();
    </script>
</body>
</html>
