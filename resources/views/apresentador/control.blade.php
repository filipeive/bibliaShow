<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show - Controle</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">Participantes</h3>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded">
                                <span class="text-sm text-slate-700">Aguardando participantes...</span>
                            </div>
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

</body>
</html>