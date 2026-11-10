<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show - Jogar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-2xl mx-auto px-4 py-8">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Bíblia Show</h1>
            <p class="text-slate-600">Modo Individual - Offline</p>
        </header>

        @php
            $resultado = session('resultado');
            $acertou = $resultado['acertou'] ?? false;
            $pontos = $resultado['pontos'] ?? 0;
            $respostaCorreta = $resultado['resposta_correta'] ?? '';
        @endphp

        @if($resultado)
            <div class="bg-{{ $acertou ? 'green' : 'red' }}-50 border-{{ $acertou ? 'green' : 'red' }}-200 p-4 rounded-lg mb-6">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">{{ $acertou ? '✓' : '✗' }}</span>
                    <div>
                        <p class="font-semibold text-{{ $acertou ? 'green' : 'red' }}-800">
                            {{ $acertou ? 'Correto!' : 'Errado!' }}
                        </p>
                        <p class="text-sm text-slate-600 mt-1">
                            {{ $acertou ? '+ ' . $pontos . ' pontos' : 'Resposta correta: ' . $respostaCorreta }}
                        </p>
                    </div>
                </div>
                @if($resultado['explicacao'] ?? null)
                    <div class="mt-3 p-3 bg-slate-100 rounded text-sm text-slate-700">
                        <strong>Explicação:</strong> {{ $resultado['explicacao'] }}
                    </div>
                @endif
            </div>
        @endif

        @if($estado === 'em_curso' && $pergunta)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Pergunta</p>
                        <p class="text-2xl font-semibold text-slate-900">1</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Pontuação</p>
                        <p class="text-2xl font-bold text-slate-900">{{ $pontuacao }}</p>
                    </div>
                </div>

                <div class="bg-slate-100 rounded-lg p-4 mb-6">
                    <p class="text-xl font-medium text-slate-800 leading-relaxed">{{ $pergunta['texto'] ?? $pergunta->texto }}</p>
                </div>

                <form action="{{ route('game.answer') }}" method="POST" id="respostaForm" class="space-y-3">
                    @csrf
                    <input type="hidden" name="tempo_resposta" id="tempo_resposta" value="0">
                    <input type="hidden" name="resposta" id="respostaSelecionada" value="">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @php
                            $opcoes = $pergunta['opcoes'] ?? [];
                            $opcoesArray = is_string($opcoes) ? json_decode($opcoes, true) : $opcoes;
                        @endphp
                        @foreach($opcoesArray as $indice => $opcao)
                            <label class="cursor-pointer block border-2 border-slate-200 rounded-xl p-4 hover:border-blue-400 hover:bg-blue-50 transition flex items-center gap-3 group">
                                <input type="radio" name="resposta" value="{{ $indice }}" class="w-5 h-5 text-blue-600 border-slate-300 focus:ring-blue-500" onchange="document.getElementById('respostaSelecionada').value = this.value; document.getElementById('respostaForm').requestSubmit();">
                                <span class="text-slate-700 font-medium">{{ $opcao }}</span>
                            </label>
                        @endforeach
                    </div>
                </form>
            </div>

            <div class="flex justify-between items-center text-sm text-slate-500">
                <span>Tempo restante: <span id="timer">{{ $tempo_restante }}</span>s</span>
                <a href="{{ route('game.play') }}" class="hover:text-blue-600">Sair do jogo</a>
            </div>

            <script>
                let tempo = {{ $tempo_restante }};
                const timerEl = document.getElementById('timer');
                const form = document.getElementById('respostaForm');
                const hiddenTempo = form.querySelector('input[name="tempo_resposta"]');

                let timerInterval = setInterval(() => {
                    tempo--;
                    timerEl.textContent = tempo;
                    hiddenTempo.value = Math.max(0, {{ $tempo_restante }} - tempo);

                    if (tempo <= 0) {
                        clearInterval(timerInterval);
                        hiddenTempo.value = Math.max(0, {{ $tempo_restante }});
                        form.submit();
                    }
                }, 1000);
            </script>

        @elseif($estado === 'terminado')
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Jogo Finalizado</h2>
                <p class="text-slate-600 mb-6">Pontuação total: <span class="font-bold text-xl">{{ $pontuacao }}</span></p>

                <a href="{{ route('game.play') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Jogar Novamente
                </a>
                <a href="/" class="text-slate-500 hover:text-slate-700 mt-4 inline-block">Voltar ao início</a>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">Bem-vindo ao Bíblia Show</h2>
                <p class="text-slate-600 mb-6">Teste seus conhecimentos bíblicos de forma individual e offline.</p>

                <a href="{{ route('game.play') }}" class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg font-bold text-lg shadow-lg">
                    Iniciar Jogo
                </a>
            </div>
        @endif

    </div>

</html>