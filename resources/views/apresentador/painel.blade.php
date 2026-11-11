<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bíblia Show — Painel do Apresentador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 50%, #020617 100%); }
    </style>
</head>
<body class="milhao-bg min-h-screen text-slate-100">

    @include('partials.navbar', ['dark' => true])

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <header class="flex flex-wrap justify-between items-center gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-xs font-black uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Estúdio de Apresentação Ao Vivo
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white">Painel do Apresentador</h1>
                <p class="text-slate-400 text-sm">Crie salas no formato Show do Milhão Bíblico (4 Fases), projete no telão e controle dicas, convidados e cartas</p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 px-4 py-2.5 rounded-xl text-xs font-bold transition">
                    Painel Admin
                </a>
                <a href="{{ route('perguntas.index') }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 px-4 py-2.5 rounded-xl text-xs font-black shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    Banco de Perguntas
                </a>
            </div>
        </header>

        <!-- Criar Nova Sala -->
        <div class="bg-slate-900/90 rounded-3xl shadow-2xl border border-amber-500/30 p-6 sm:p-8 mb-8">
            <h2 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Criar Nova Sala de Apresentação Ao Vivo
            </h2>
            <form action="{{ route('apresentador.criar') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label for="nome" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Nome da Sala / Culto / Evento
                        </label>
                        <input type="text" id="nome" name="nome" required
                               placeholder="Ex.: Culto de Jovens, Gincana EBD, Célula Betel..."
                               class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                    </div>
                    <div>
                        <label for="quantidade_perguntas" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Formato da Partida
                        </label>
                        <select id="quantidade_perguntas" name="quantidade_perguntas"
                                class="w-full px-3.5 py-3 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                            <option value="12">12 Perguntas (3 por Fase)</option>
                            <option value="16">16 Perguntas (4 por Fase)</option>
                            <option value="24" selected>24 Perguntas (4 Fases Clássicas)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="premio_primeiro_lugar" class="block text-xs font-bold uppercase tracking-wider text-amber-400 mb-1.5">
                            Prêmio para o 1º Lugar (Opcional)
                        </label>
                        <input type="text" id="premio_primeiro_lugar" name="premio_primeiro_lugar"
                               placeholder="Ex.: Bíblia de Estudo + Troféu Ouro"
                               class="w-full px-4 py-3 rounded-xl border border-amber-500/40 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                    </div>
                    <div>
                        <label for="premio_segundo_lugar" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Prêmio para o 2º Lugar (Opcional)
                        </label>
                        <input type="text" id="premio_segundo_lugar" name="premio_segundo_lugar"
                               placeholder="Ex.: Livro Devocional + Medalha Prata"
                               class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                    </div>
                    <div>
                        <button type="submit"
                                class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-black py-3 px-6 rounded-xl shadow-lg shadow-amber-500/20 transition text-sm">
                            Criar e Abrir Estúdio →
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Salas Ativas -->
        <div class="bg-slate-900/90 rounded-3xl shadow-2xl border border-slate-800 p-6 sm:p-8">
            <h2 class="text-lg font-black text-white mb-4">Salas Ativas Recentes</h2>

            @if($salas->isEmpty())
                <div class="text-center py-12 text-slate-400 text-sm">
                    Nenhuma sala ativa no momento. Preencha o formulário acima para abrir sua primeira sala!
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800">
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase">Nome da Sala</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase">Código</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase">Participantes</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase">Status</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/70">
                            @foreach($salas as $sala)
                                <tr id="salaPainelRow_{{ $sala->id }}" class="hover:bg-slate-800/40 transition">
                                    <td class="py-4">
                                        <div class="text-white font-bold">{{ $sala->nome }}</div>
                                        @if($sala->premio_primeiro_lugar || $sala->premio_segundo_lugar)
                                            <div class="text-xs text-amber-300/90 mt-0.5">
                                                @if($sala->premio_primeiro_lugar)
                                                    <span>1º: {{ $sala->premio_primeiro_lugar }}</span>
                                                @endif
                                                @if($sala->premio_segundo_lugar)
                                                    <span class="ml-2 text-slate-400">• 2º: {{ $sala->premio_segundo_lugar }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4">
                                        <span class="font-mono font-black text-amber-300 bg-amber-500/15 border border-amber-500/30 px-3 py-1 rounded-lg text-sm">
                                            {{ $sala->codigo_acesso }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-sm text-slate-300">
                                        {{ $sala->participantes_count ?? 0 }} conectado(s)
                                    </td>
                                    <td class="py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                            {{ $sala->status === 'em_curso' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $sala->status === 'em_curso' ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                            {{ $sala->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right space-x-1.5 whitespace-nowrap">
                                        <a href="{{ route('apresentador.control', $sala->id) }}"
                                           class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 px-3.5 py-2 rounded-xl text-xs font-black shadow">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                                            </svg>
                                            Controlar
                                        </a>
                                        <a href="{{ route('projecao.tela', $sala->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold">
                                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                                            </svg>
                                            Telão
                                        </a>
                                        <a href="{{ route('entrar.view', $sala->codigo_acesso) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-500 text-white px-3.5 py-2 rounded-xl text-xs font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                            </svg>
                                            Smartphone
                                        </a>
                                        <button type="button" onclick="eliminarSalaPainel({{ $sala->id }}, '{{ addslashes($sala->nome) }}')"
                                                class="inline-flex items-center gap-1 bg-rose-500/20 hover:bg-rose-500 border border-rose-500/40 text-rose-300 hover:text-white px-3 py-2 rounded-xl text-xs font-bold transition">
                                            Excluir
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Toast Apresentador -->
    <div id="toastPainel" class="fixed bottom-6 right-6 z-50 transform translate-y-24 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-3 border text-white" id="toastPainelContent">
            <span id="toastPainelMsg"></span>
        </div>
    </div>

    <script>
        function mostrarToastPainel(mensagem, sucesso = true) {
            const toast = document.getElementById('toastPainel');
            const content = document.getElementById('toastPainelContent');
            const msg = document.getElementById('toastPainelMsg');
            if (!toast || !content || !msg) return;

            msg.textContent = mensagem;
            content.className = sucesso
                ? 'px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-3 border bg-emerald-950/95 border-emerald-500/50 text-emerald-200'
                : 'px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-3 border bg-rose-950/95 border-rose-500/50 text-rose-200';

            toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
            }, 3500);
        }

        async function eliminarSalaPainel(salaId, nomeSala) {
            if (!confirm(`Deseja excluir a sala «${nomeSala}»? Todos os participantes conectados e dados de jogo serão removidos.`)) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const resp = await fetch(`/apresentador/salas/${salaId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                    }
                });

                const data = await resp.json().catch(() => ({}));

                if (resp.ok && data.sucesso) {
                    const row = document.getElementById(`salaPainelRow_${salaId}`);
                    if (row) {
                        row.style.transition = 'all 0.4s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => row.remove(), 400);
                    }
                    mostrarToastPainel(data.mensagem || `Sala «${nomeSala}» excluída com sucesso!`, true);
                } else {
                    mostrarToastPainel(data.mensagem || 'Não foi possível excluir a sala.', false);
                }
            } catch (err) {
                mostrarToastPainel('Erro de conexão ao excluir sala.', false);
            }
        }
    </script>
</body>
</html>
