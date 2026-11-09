<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show - Painel do Apresentador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-4xl mx-auto px-4 py-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Bíblia Show</h1>
                <p class="text-slate-600">Painel do Apresentador</p>
            </div>
            <a href="{{ route('apresentador.criar') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md">
                Nova Sala
            </a>
        </header>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Salas Recentes</h2>

            @if($salas->isEmpty())
                <div class="text-center py-8 text-slate-500">
                    Nenhuma sala ativa. Crie uma nova sala para começar.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="pb-3 text-sm font-semibold text-slate-500 uppercase">Nome</th>
                                <th class="pb-3 text-sm font-semibold text-slate-500 uppercase">Código</th>
                                <th class="pb-3 text-sm font-semibold text-slate-500 uppercase">Status</th>
                                <th class="pb-3 text-sm font-semibold text-slate-500 uppercase text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salas as $sala)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="py-4 text-slate-800 font-medium">{{ $sala->nome }}</td>
                                    <td class="py-4 font-mono font-bold text-blue-600">{{ $sala->codigo_acesso }}</td>
                                    <td class="py-4">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                                            {{ $sala->status === 'em_curso' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $sala->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right">
                                        <a href="{{ route('apresentador.control', $sala->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-semibold">
                                            Controlar
                                        </a>
                                        <a href="{{ route('projecao.tela', $sala->id) }}" class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded text-sm font-semibold ml-2" target="_blank">
                                            Projeção
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</body>
</html>