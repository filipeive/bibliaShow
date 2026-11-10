<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Painel do Apresentador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900">

    <div class="max-w-5xl mx-auto px-4 py-8">
        <header class="flex flex-wrap justify-between items-center gap-4 mb-8">
            <div>
                <a href="/" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Voltar ao Início
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 mt-1">Painel do Apresentador</h1>
                <p class="text-slate-600 text-sm">Crie salas ao vivo, projete no telão e acompanhe os participantes em tempo real</p>
            </div>
            <a href="{{ route('perguntas.index') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
                Banco de Perguntas
            </a>
        </header>

        <!-- Criar Nova Sala -->
        <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 mb-8">
            <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Criar Nova Sala de Apresentação
            </h2>
            <form action="{{ route('apresentador.criar') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <div class="md:col-span-2">
                    <label for="nome" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Nome da Sala / Evento
                    </label>
                    <input type="text" id="nome" name="nome" required
                           placeholder="Ex.: Culto de Jovens, Gincana EBD, Célula Betel..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-sm">
                </div>
                <div>
                    <label for="quantidade_perguntas" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Nº de Perguntas
                    </label>
                    <select id="quantidade_perguntas" name="quantidade_perguntas"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 outline-none text-sm bg-white">
                        <option value="10">10 perguntas</option>
                        <option value="12" selected>12 perguntas</option>
                        <option value="18">18 perguntas</option>
                        <option value="24">24 perguntas (4 Fases Completas)</option>
                    </select>
                </div>
                <div>
                    <button type="submit"
                            class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold py-2.5 px-6 rounded-xl shadow transition">
                        Criar e Iniciar Sala →
                    </button>
                </div>
            </form>
        </div>

        <!-- Salas Ativas -->
        <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Salas Ativas Recentes</h2>

            @if($salas->isEmpty())
                <div class="text-center py-10 text-slate-500">
                    Nenhuma sala ativa no momento. Preencha o formulário acima para criar sua primeira sala!
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="pb-3 text-xs font-bold text-slate-500 uppercase">Nome da Sala</th>
                                <th class="pb-3 text-xs font-bold text-slate-500 uppercase">Código</th>
                                <th class="pb-3 text-xs font-bold text-slate-500 uppercase">Participantes</th>
                                <th class="pb-3 text-xs font-bold text-slate-500 uppercase">Status</th>
                                <th class="pb-3 text-xs font-bold text-slate-500 uppercase text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salas as $sala)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="py-4 text-slate-900 font-semibold">{{ $sala->nome }}</td>
                                    <td class="py-4">
                                        <span class="font-mono font-extrabold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-lg text-sm">
                                            {{ $sala->codigo_acesso }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-sm text-slate-600">
                                        {{ $sala->participantes_count ?? 0 }} conectado(s)
                                    </td>
                                    <td class="py-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                            {{ $sala->status === 'em_curso' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $sala->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right space-x-1.5">
                                        <a href="{{ route('apresentador.control', $sala->id) }}"
                                           class="inline-flex items-center gap-1.5 bg-slate-900 hover:bg-slate-800 text-white px-3.5 py-2 rounded-lg text-xs font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                                            </svg>
                                            Controlar
                                        </a>
                                        <a href="{{ route('projecao.tela', $sala->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 px-3.5 py-2 rounded-lg text-xs font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                                            </svg>
                                            Projeção
                                        </a>
                                        <a href="{{ route('entrar.view', $sala->codigo_acesso) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-2 rounded-lg text-xs font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                            </svg>
                                            Entrar
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
