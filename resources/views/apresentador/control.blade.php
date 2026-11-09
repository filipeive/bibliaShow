<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bíblia Show - Controle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/pusher-js@8/dist/web/pusher.js"></script>
    <script src="https://unpkg.com/laravel-echo@1/dist/echo.iife.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-5xl mx-auto px-4 py-8">
        <header class="flex justify-between items-center mb-6">
            <div>
                <a href="{{ route('apresentador.painel') }}" class="text-slate-500 hover:text-slate-700">← Salas</a>
                <h1 class="text-2xl font-bold text-slate-900">{{ $sala->nome }}</h1>
                <p class="text-slate-600 text-sm font-mono">Código: {{ $sala->codigo_acesso }}</p>
            </div>
            <a href="{{ route('projecao.tela', $sala->id) }}" class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md" target="_blank">
                Abrir Projeção
            </a>
        </header>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-6">
                <p class="text-green-800 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-6">
                <p class="text-red-800 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if($estado === 'aguardando' || $estado === 'em_curso' || $estado === 'pausado')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Controles Principais -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Pergunta Atual</h2>
                                <p class="text-slate-500 text-sm">{{ ucfirst($estado) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-slate-400 uppercase">Temporizador</p>
                                <p class="text-3xl font-bold text-slate-900 font-mono">{{ $tempo_restante }}s</p>
                                @if($tempo_esgotado)
                                    <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">Tempo esgotado</span>
                                @endif
                            </div>
                        </div>

                        @if($pergunta)
                            <div class="bg-slate-50 rounded-lg p-6 mb-6">
                                <p class="text-xl font-medium text-slate-800 leading-relaxed">{{ $pergunta['texto'] ?? $pergunta->texto }}</p>
                            </div>

                            <div class="flex gap-3">
                                @if($estado !== 'pausado')
                                    <form action="{{ route('apresentador.pausar', $sala->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-4 rounded-lg transition shadow-md flex items-center justify-center gap-2">
                                            <span>⏸</span> Pausar
                                        </button>
                                    </form>
                                    <form action="{{ route('apresentador.proxima', $sala->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-lg transition shadow-md flex items-center justify-center gap-2">
                                            <span>▶</span> Próxima Pergunta
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('apresentador.retomar', $sala->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-lg transition shadow-md flex items-center justify-center gap-2">
                                            <span>▶</span> Retomar
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('apresentador.finalizar', $sala->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-lg transition shadow-md flex items-center justify-center gap-2">
                                        <span>⏹</span> Finalizar
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Painel Lateral -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">Informações</h3>
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pontuação Total</span>
                                <span class="font-semibold text-slate-900">{{ $pontuacao }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Status</span>
                                <span class="font-semibold text-slate-900">{{ ucfirst($estado) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-sm font-semibold text-slate-900 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Participantes ao Vivo
                        </h3>
                        <div id="participantesList" class="space-y-2 max-h-60 overflow-y-auto pr-2">
                            @foreach($participantes as $participante)
                                <div id="participante-{{ $participante->id }}"
                                     class="flex items-center justify-between p-3 bg-slate-50 rounded border border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-sm font-bold">
                                            {{ strtoupper(substr($participante->nome, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-slate-900 block">{{ $participante->nome }}</span>
                                            @if($participante->equipe)
                                                <span class="text-xs text-slate-500">{{ $participante->equipe }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-xs font-mono font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded">
                                        {{ $participante->pontuacao_total }} pts
                                    </span>
                                </div>
                            @endforeach
                            @if($participantes->isEmpty())
                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded">
                                    <span class="text-sm text-slate-400">Nenhum participante conectado</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 bg-amber-50 rounded-xl shadow-lg p-6 border border-amber-100">
                        <h3 class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-3">QR Code de Entrada</h3>
                        <div class="flex flex-col items-center gap-3">
                            <div id="qrcode" class="w-32 h-32 bg-white rounded p-2 border border-amber-200"></div>
                            <p class="text-xs font-mono font-bold text-amber-700 bg-amber-100 px-2 py-1 rounded">
                                {{ $sala->codigo_acesso }}
                            </p>
                            <p class="text-[10px] text-amber-500/70 text-center leading-tight">
                                Escaneie com o celular para entrar
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($estado === 'terminado')
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Jogo Finalizado</h2>
                <p class="text-slate-600 mb-6">Pontuação total: <span class="font-bold text-xl">{{ $pontuacao }}</span></p>

                <a href="{{ route('apresentador.control', $sala->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Nova Partida
                </a>
            </div>
        @endif
    </div>

    <script>
        // Gerar QR Code para entrada da sala
        new QRCode(document.getElementById('qrcode'), {
            text: window.location.origin + '/entrar/{{ $sala->codigo_acesso }}',
            width: 160,
            height: 160,
            colorDark: '#0f172a',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H,
        });

        // Conexão Reverb via Laravel Echo
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ env('REVERB_APP_KEY') }}',
            wsHost: '{{ env('REVERB_HOST') }}',
            wsPort: {{ env('REVERB_PORT') }},
            wssPort: {{ env('REVERB_PORT') }},
            forceTLS: {{ env('REVERB_SCHEME') === 'https' ? 'true' : 'false' }},
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });

        const channel = window.Echo.private('game.{{ $sala->id }}');

        channel.listen('.participante-entrou', (e) => {
            if (e.saiu) {
                const el = document.getElementById('participante-' + e.id);
                if (el) el.remove();
                if (document.querySelectorAll('#participantesList > div').length === 0) {
                    document.getElementById('participantesList').innerHTML = '<div class="flex items-center justify-between p-3 bg-slate-50 rounded"><span class="text-sm text-slate-400">Nenhum participante conectado</span></div>';
                }
                return;
            }

            // Atualizar ou adicionar participante
            const list = document.getElementById('participantesList');
            const emptyMsg = list.querySelector('.text-slate-400');
            if (emptyMsg) emptyMsg.remove();

            const existing = document.getElementById('participante-' + e.id);
            if (existing) {
                existing.querySelector('.text-slate-900').innerText = e.nome;
            } else {
                const el = document.createElement('div');
                el.id = 'participante-' + e.id;
                el.className = 'flex items-center justify-between p-3 bg-slate-50 rounded border border-slate-100 animate-in fade-in slide-in-from-right-2 duration-300';
                el.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-sm font-bold">${e.nome.charAt(0).toUpperCase()}</div>
                        <div>
                            <span class="text-sm font-medium text-slate-900 block">${e.nome}</span>
                            ${e.equipe ? '<span class="text-xs text-slate-500">' + e.equipe + '</span>' : ''}
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded">0 pts</span>
                `;
                list.prepend(el);
            }
        });

        channel.listen('.resposta-enviada', (e) => {
            // Atualizar pontos do participante na lista
            const el = document.getElementById('participante-' + e.participanteId);
            if (el) {
                const ptsEl = el.querySelector('.font-mono');
                if (ptsEl) ptsEl.innerText = e.pontos + ' pts';
            }

            // Notificação visual breve
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-white border border-slate-200 rounded-lg p-4 shadow-xl flex items-center gap-3 z-50 animate-in fade-in slide-in-from-right-2 duration-300';
            toast.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold text-lg">✓</div>
                <div>
                    <div class="text-sm font-semibold text-slate-900">${e.nomeParticipante}</div>
                    <div class="text-xs text-slate-500">${e.correta ? 'Acertou!' : 'Errou'} • ${e.pontos} pts</div>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        });

        channel.listen('.ranking-atualizado', (e) => {
            // Ranking é atualizado via lista de participantes acima
            console.log('Ranking atualizado:', e.ranking);
        });

        channel.listen('.jogo-estado-alterado', (e) => {
            console.log('Estado alterado:', e.estado);
            if (e.estado === 'terminado') {
                document.getElementById('participantesList').innerHTML = '<div class="flex items-center justify-between p-3 bg-slate-50 rounded"><span class="text-sm text-slate-400">Jogo finalizado</span></div>';
            }
        });

        // Tratamento de erro de conexão
        window.Echo.connector.pusher.connection.bind('error', (err) => {
            console.error('Erro Reverb:', err);
        });
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('Conectado ao Reverb');
        });
    </script>

</body>
</html>