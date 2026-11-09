<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show - Resultado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-3xl mx-auto px-4 py-8">
        <header class="mb-8">
            <a href="{{ route('apresentador.control', $salaId) }}" class="text-slate-500 hover:text-slate-700">← Voltar ao Controle</a>
            <h1 class="text-3xl font-bold text-slate-900">Resultado Final</h1>
        </header>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Pontuação Final</h2>
            <p class="text-4xl font-extrabold text-slate-900 mb-6">{{ $resultados['pontuacao_total'] }}</p>

            <div class="mt-8">
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Histórico de Perguntas</h3>
                <div class="space-y-3">
                    @foreach($resultados['historico_perguntas'] as $index => $pergunta)
                        <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-semibold text-slate-800">Pergunta {{ $index + 1 }}</span>
                                <span class="text-xs text-slate-500">{{ $pergunta['texto'] ?? $pergunta->texto }}</span>
                            </div>
                            <p class="text-sm text-slate-600 mt-1">{{ $pergunta['explicacao'] ?? 'Sem explicação disponível.' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('apresentador.control', $salaId) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold">
                    Nova Partida
                </a>
                <a href="{{ route('apresentador.painel') }}" class="text-slate-500 hover:text-slate-700 mt-4 inline-block">Voltar ao Painel</a>
            </div>
        </div>
    </div>

</body>
</html>