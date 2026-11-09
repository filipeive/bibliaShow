<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show - Resultado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-3xl mx-auto px-4 py-8">
        <header class="mb-8">
            <a href="/" class="text-slate-500 hover:text-slate-700">← Voltar</a>
            <h1 class="text-3xl font-bold text-slate-900">Resultado</h1>
        </header>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Pontuação Final</h2>
            <p class="text-4xl font-extrabold text-slate-900 mb-6">{{ $resultados['pontuacao_total'] }}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-slate-50 p-4 rounded-lg">
                    <p class="text-sm text-slate-500 uppercase tracking-wider">Perguntas</p>
                    <p class="text-xl font-semibold text-slate-900">{{ $resultados['historico_perguntas']|length }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <p class="text-sm text-slate-500 uppercase tracking-wider">Respostas</p>
                    <p class="text-xl font-semibold text-slate-900">{{ count($resultados['respostas']) }}</p>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Respostas</h3>
                <div class="space-y-3">
                    @foreach($resultados['respostas'] as $resp)
                        <div class="p-3 rounded-lg border {{ $resp['acertou'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-medium text-slate-800">{{ $resp['questao_id'] }}</span>
                                <span class="text-xs font-bold {{ $resp['acertou'] ? 'text-green-700' : 'text-red-700' }} uppercase">
                                    {{ $resp['acertou'] ? 'Acerto' : 'Erro' }}
                                </span>
                                <span class="text-xs text-slate-500">+{{ $resp['pontos'] }} pts</span>
                            </div>
                            <p class="text-sm text-slate-700">Tempo: {{ $resp['tempo_resposta'] }}s</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('game.play') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold">
                    Jogar Novamente
                </a>
            </div>
        </div>
    </div>

</html>