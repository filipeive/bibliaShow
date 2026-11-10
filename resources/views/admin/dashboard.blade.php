<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bíblia Show — Painel de Administração</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .admin-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 45%, #020617 100%); }
    </style>
</head>
<body class="admin-bg text-slate-100 min-h-screen">

    @include('partials.navbar', ['dark' => true])

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Cabeçalho do Painel Admin -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-xs font-black uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Central Administrativa & Editorial
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white">Painel de Administração — Bíblia Show</h1>
                <p class="text-slate-400 text-sm">Gestão global de perguntas, salas em tempo real, igrejas, usuários e sincronização do acervo clássico</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <form action="{{ route('admin.reimportar') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-900/70 hover:bg-blue-800 border border-blue-500/40 text-blue-100 text-xs font-bold transition">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Sincronizar Clássico (1.000 Q)
                    </button>
                </form>

                <a href="{{ route('admin.exportar', ['formato' => 'csv']) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-bold transition">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Exportar CSV
                </a>

                <a href="{{ route('perguntas.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nova Pergunta
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-950/80 border border-emerald-500/40 rounded-2xl p-4 mb-6 text-emerald-200 text-sm font-semibold flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- KPIs Principais -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Perguntas</p>
                <p class="text-2xl sm:text-3xl font-black text-amber-400 mt-1">{{ $stats['total_questoes'] }}</p>
                <p class="text-[11px] text-slate-500 mt-1">{{ $stats['questoes_aprovadas'] }} aprovadas</p>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Livros Bíblicos</p>
                <p class="text-2xl sm:text-3xl font-black text-emerald-400 mt-1">{{ $stats['total_livros'] }}</p>
                <p class="text-[11px] text-slate-500 mt-1">{{ $stats['total_capitulos'] }} capítulos</p>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Salas Ao Vivo</p>
                <p class="text-2xl sm:text-3xl font-black text-blue-400 mt-1">{{ $stats['salas_ativas'] }}</p>
                <p class="text-[11px] text-slate-500 mt-1">{{ $stats['total_salas'] }} criadas no total</p>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Participantes</p>
                <p class="text-2xl sm:text-3xl font-black text-purple-400 mt-1">{{ $stats['total_participantes'] }}</p>
                <p class="text-[11px] text-slate-500 mt-1">{{ $stats['total_respostas'] }} respostas</p>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Igrejas</p>
                <p class="text-2xl sm:text-3xl font-black text-amber-300 mt-1">{{ $stats['total_igrejas'] }}</p>
                <p class="text-[11px] text-slate-500 mt-1">Multi-tenant ativo</p>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Usuários</p>
                <p class="text-2xl sm:text-3xl font-black text-rose-400 mt-1">{{ $stats['total_usuarios'] }}</p>
                <p class="text-[11px] text-slate-500 mt-1">Equipes & Líderes</p>
            </div>
        </div>

        <!-- Distribuição pelas 4 Fases Clássicas -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <h2 class="text-base font-black text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    Distribuição de Questões por Fase (Show do Milhão Bíblico)
                </h2>
                <a href="{{ route('perguntas.index') }}" class="text-xs font-bold text-amber-400 hover:underline">Ver todas as questões →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($distribuicaoFases as $f)
                    <a href="{{ route('perguntas.index', ['dificuldade_id' => $f['nivel']]) }}"
                       class="bg-slate-950/80 hover:bg-slate-950 border border-slate-800 hover:border-amber-500/40 rounded-2xl p-4 transition flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-black uppercase tracking-wider text-amber-400">{{ $f['nivel'] }}ª Fase • {{ $f['pontos'] }} pts</span>
                            <p class="text-sm font-bold text-white mt-0.5">{{ $f['nome'] }}</p>
                        </div>
                        <span class="text-2xl font-black text-white font-mono">{{ $f['total'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna Esquerda (2/3): Salas Ao Vivo e Revisão Editorial -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Salas Recentes e Controles Rápidos -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-black text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            Salas de Apresentação
                        </h2>
                        <a href="{{ route('apresentador.painel') }}" class="text-xs font-bold text-amber-400 hover:underline">
                            + Criar Nova Sala
                        </a>
                    </div>

                    @if($salasRecentes->isEmpty())
                        <p class="text-sm text-slate-400 py-6 text-center">Nenhuma sala criada ainda.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-slate-800 text-xs text-slate-400 uppercase">
                                        <th class="pb-3">Sala</th>
                                        <th class="pb-3">Código</th>
                                        <th class="pb-3">Jogadores</th>
                                        <th class="pb-3">Status</th>
                                        <th class="pb-3 text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/70" id="tabelaSalasBody">
                                    @foreach($salasRecentes as $s)
                                        <tr id="salaRow_{{ $s->id }}" class="hover:bg-slate-800/30 transition">
                                            <td class="py-3.5 font-bold text-white">{{ $s->nome }}</td>
                                            <td class="py-3.5">
                                                <span class="font-mono font-black text-amber-300 bg-amber-500/15 border border-amber-500/30 px-2.5 py-1 rounded-lg text-xs">
                                                    {{ $s->codigo_acesso }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 text-slate-300">{{ $s->participantes_count }}</td>
                                            <td class="py-3.5">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $s->status === 'em_curso' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-800 text-slate-400' }}">
                                                    {{ $s->status }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                                <a href="{{ route('apresentador.control', $s->id) }}" class="px-3 py-1.5 rounded-lg bg-amber-500 text-slate-950 font-extrabold text-xs">Controlar</a>
                                                <a href="{{ route('projecao.tela', $s->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-200 font-bold text-xs">Telão</a>
                                                <button type="button" onclick="eliminarSalaAdmin({{ $s->id }}, '{{ addslashes($s->nome) }}')"
                                                        class="px-2.5 py-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white font-bold text-xs border border-rose-500/40 transition">
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

                <!-- Revisão Editorial de Perguntas Pendentes -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-black text-white">Fila Editorial ({{ $stats['questoes_pendentes'] }} pendentes)</h2>
                        <a href="{{ route('perguntas.index', ['aprovada' => 0]) }}" class="text-xs font-bold text-amber-400 hover:underline">Ver todas →</a>
                    </div>

                    @if($questoesPendentes->isEmpty())
                        <div class="p-6 rounded-2xl bg-slate-950/60 border border-slate-800 text-center text-sm text-slate-400">
                            Todas as {{ $stats['questoes_aprovadas'] }} questões estão revisadas e aprovadas para uso imediato no jogo!
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($questoesPendentes as $qp)
                                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-xs text-amber-400 font-bold">{{ $qp->livro?->nome }} • {{ $qp->referencia_biblica }}</p>
                                        <p class="text-sm font-bold text-white truncate">{{ $qp->texto }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <form action="{{ route('perguntas.aprovar', $qp->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">Aprovar</button>
                                        </form>
                                        <a href="{{ route('perguntas.edit', $qp->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-200 text-xs font-bold">Editar</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Coluna Direita (1/3): Usuários e Igrejas -->
            <div class="space-y-8">
                <!-- Cadastrar Novo Usuário -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6">
                    <h2 class="text-base font-black text-white mb-4">Adicionar Apresentador / Editor</h2>
                    <form action="{{ route('admin.usuarios.salvar') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="text" name="nome" required placeholder="Nome completo"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-sm text-white outline-none focus:border-amber-500">
                        <input type="email" name="email" required placeholder="E-mail de acesso"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-sm text-white outline-none focus:border-amber-500">
                        <input type="password" name="password" required minlength="6" placeholder="Senha inicial"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-sm text-white outline-none focus:border-amber-500">
                        <div class="grid grid-cols-2 gap-2">
                            <select name="igreja_id" required class="px-3 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                                @foreach($igrejas as $ig)
                                    <option value="{{ $ig->id }}">{{ $ig->nome }}</option>
                                @endforeach
                            </select>
                            <select name="papel" required class="px-3 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                                <option value="apresentador">Apresentador</option>
                                <option value="editor">Editor</option>
                                <option value="admin_igreja">Admin Igreja</option>
                                <option value="admin_geral">Admin Geral</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-black py-2.5 rounded-xl text-xs transition">
                            Cadastrar Usuário
                        </button>
                    </form>

                    <div class="mt-5 pt-4 border-t border-slate-800 space-y-2 max-h-48 overflow-y-auto">
                        @foreach($usuarios as $u)
                            <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-950/60">
                                <div class="truncate">
                                    <span class="font-bold text-white block truncate">{{ $u->nome }}</span>
                                    <span class="text-slate-400">{{ $u->email }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold shrink-0">{{ $u->papel }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Cadastrar Nova Igreja / Congregação -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6">
                    <h2 class="text-base font-black text-white mb-4">Congregações Cadastradas</h2>
                    <form action="{{ route('admin.igrejas.criar') }}" method="POST" class="flex gap-2 mb-4">
                        @csrf
                        <input type="text" name="nome" required placeholder="Nova Igreja / Célula..."
                               class="flex-1 px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white outline-none focus:border-amber-500">
                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black px-4 py-2 rounded-xl text-xs">
                            Criar
                        </button>
                    </form>

                    <div class="space-y-2">
                        @foreach($igrejas as $ig)
                            <div class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80">
                                <span class="font-bold text-white">{{ $ig->nome }}</span>
                                <span class="text-slate-400">{{ $ig->salas_count }} salas • {{ $ig->usuarios_count }} membros</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast de Notificação Admin em Tempo Real -->
    <div id="toastAdmin" class="fixed bottom-6 right-6 z-50 transform translate-y-24 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-3 border text-white" id="toastAdminContent">
            <span id="toastAdminMsg"></span>
        </div>
    </div>

    <script>
        function mostrarToastAdmin(mensagem, sucesso = true) {
            const toast = document.getElementById('toastAdmin');
            const content = document.getElementById('toastAdminContent');
            const msg = document.getElementById('toastAdminMsg');
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

        async function eliminarSalaAdmin(salaId, nomeSala) {
            if (!confirm(`Tem certeza de que deseja eliminar permanentemente a sala «${nomeSala}»? Todos os jogos e participantes desta sala serão removidos.`)) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const resp = await fetch(`/admin/salas/${salaId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                    }
                });

                const data = await resp.json().catch(() => ({}));

                if (resp.ok && data.sucesso) {
                    const row = document.getElementById(`salaRow_${salaId}`);
                    if (row) {
                        row.style.transition = 'all 0.4s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => row.remove(), 400);
                    }
                    mostrarToastAdmin(data.mensagem || `Sala «${nomeSala}» eliminada com sucesso!`, true);
                } else {
                    mostrarToastAdmin(data.mensagem || 'Erro ao eliminar sala.', false);
                }
            } catch (err) {
                mostrarToastAdmin('Falha de conexão ao eliminar a sala.', false);
            }
        }
    </script>
</body>
</html>

