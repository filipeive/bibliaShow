<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bíblia Show — Game Master Console (Admin)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="sm-stage text-slate-100 min-h-screen flex flex-col justify-between">

    @include('partials.navbar', ['dark' => true])

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full relative z-10">
        <!-- Cabeçalho do Game Master Console -->
        <header class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 mb-2">
                    <span class="sm-pill text-xs uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                        Game Master • Central de Controle
                    </span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-white sm-logo-title">Painel de Administração</h1>
                <p class="text-xs sm:text-sm text-blue-200 mt-1">Gestão global de fases, acervo clássico, salas ao vivo, congregações e apresentadores</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <form action="{{ route('admin.reimportar') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="sm-arcade-btn text-xs py-2 px-3.5 cursor-pointer">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span>Sincronizar Acervo (1.000 Q)</span>
                    </button>
                </form>

                <a href="{{ route('admin.exportar', ['formato' => 'csv']) }}" class="sm-arcade-btn is-emerald text-xs py-2 px-3.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Exportar CSV</span>
                </a>

                <a href="{{ route('perguntas.create') }}" class="sm-arcade-btn is-gold text-xs py-2 px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Nova Pergunta</span>
                </a>
            </div>
        </header>

        @if(session('success'))
            <div class="bg-emerald-950/80 border border-emerald-500/50 rounded-2xl p-4 mb-6 text-emerald-200 text-sm font-semibold flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Placar Arcade de KPIs Principais -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-8">
            <div class="sm-panel p-4 text-center">
                <p class="text-[10px] font-black uppercase tracking-wider text-amber-300">Total Perguntas</p>
                <p class="text-2xl sm:text-3xl font-black text-white mt-1">{{ $stats['total_questoes'] }}</p>
                <p class="text-[10px] text-blue-200 mt-1">{{ $stats['questoes_aprovadas'] }} aprovadas</p>
            </div>
            <div class="sm-panel p-4 text-center">
                <p class="text-[10px] font-black uppercase tracking-wider text-amber-300">Livros Bíblicos</p>
                <p class="text-2xl sm:text-3xl font-black text-emerald-400 mt-1">{{ $stats['total_livros'] }}</p>
                <p class="text-[10px] text-blue-200 mt-1">{{ $stats['total_capitulos'] }} capítulos</p>
            </div>
            <div class="sm-panel p-4 text-center">
                <p class="text-[10px] font-black uppercase tracking-wider text-amber-300">Salas Ao Vivo</p>
                <p class="text-2xl sm:text-3xl font-black text-cyan-400 mt-1">{{ $stats['salas_ativas'] }}</p>
                <p class="text-[10px] text-blue-200 mt-1">{{ $stats['total_salas'] }} criadas</p>
            </div>
            <div class="sm-panel p-4 text-center">
                <p class="text-[10px] font-black uppercase tracking-wider text-amber-300">Jogadores</p>
                <p class="text-2xl sm:text-3xl font-black text-purple-300 mt-1">{{ $stats['total_participantes'] }}</p>
                <p class="text-[10px] text-blue-200 mt-1">{{ $stats['total_respostas'] }} respostas</p>
            </div>
            <div class="sm-panel p-4 text-center">
                <p class="text-[10px] font-black uppercase tracking-wider text-amber-300">Igrejas</p>
                <p class="text-2xl sm:text-3xl font-black text-amber-400 mt-1">{{ $stats['total_igrejas'] }}</p>
                <p class="text-[10px] text-blue-200 mt-1">Multi-tenant ativo</p>
            </div>
            <div class="sm-panel p-4 text-center">
                <p class="text-[10px] font-black uppercase tracking-wider text-amber-300">Apresentadores</p>
                <p class="text-2xl sm:text-3xl font-black text-rose-400 mt-1">{{ $stats['total_usuarios'] }}</p>
                <p class="text-[10px] text-blue-200 mt-1">Equipes & Mestres</p>
            </div>
        </div>

        <!-- Seletor de Fases do Jogo (1ª a 4ª Fase) -->
        <div class="sm-panel p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <h2 class="text-base sm:text-lg font-black text-white flex items-center gap-2">
                    <span class="text-amber-400 text-lg">⚡</span>
                    Distribuição das Questões pelas 4 Fases Clássicas
                </h2>
                <a href="{{ route('perguntas.index') }}" class="text-xs font-bold text-amber-300 hover:text-amber-200 hover:underline">
                    Ver Acervo Completo →
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($distribuicaoFases as $f)
                    <a href="{{ route('perguntas.index', ['dificuldade_id' => $f['nivel']]) }}"
                       class="p-4 rounded-2xl bg-blue-950/70 border border-blue-400/30 hover:border-amber-400/60 transition flex items-center justify-between group">
                        <div>
                            <span class="sm-badge-stage stage-{{ $f['nivel'] }}">{{ $f['nivel'] }}ª FASE • {{ $f['pontos'] }} PTS</span>
                            <p class="text-sm font-bold text-white mt-1 group-hover:text-amber-300 transition">{{ $f['nome'] }}</p>
                        </div>
                        <span class="text-2xl font-black text-amber-300 font-mono">{{ $f['total'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna Esquerda (2/3): Salas Ao Vivo e Fila Editorial -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Salas Recentes e Controles de Transmissão -->
                <div class="sm-panel p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base sm:text-lg font-black text-white flex items-center gap-2">
                            <span class="text-xl">🎙️</span>
                            Salas de Apresentação Ao Vivo
                        </h2>
                        <a href="{{ route('apresentador.painel') }}" class="sm-arcade-btn is-gold text-xs py-1.5 px-3">
                            <span>+ Criar Sala</span>
                        </a>
                    </div>

                    @if($salasRecentes->isEmpty())
                        <p class="text-xs sm:text-sm text-blue-200 py-6 text-center">Nenhuma sala criada no momento.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                                <thead>
                                    <tr class="border-b border-blue-400/20 text-xs text-amber-300 font-black uppercase">
                                        <th class="pb-3">Sala</th>
                                        <th class="pb-3">Código</th>
                                        <th class="pb-3">Jogadores</th>
                                        <th class="pb-3">Status</th>
                                        <th class="pb-3 text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-blue-400/10" id="tabelaSalasBody">
                                    @foreach($salasRecentes as $s)
                                        <tr id="salaRow_{{ $s->id }}" class="hover:bg-blue-900/30 transition">
                                            <td class="py-3.5 font-bold text-white">{{ $s->nome }}</td>
                                            <td class="py-3.5">
                                                <span class="font-mono font-black text-amber-300 bg-amber-500/15 border border-amber-500/40 px-2 py-0.5 rounded-lg text-xs">
                                                    {{ $s->codigo_acesso }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 text-blue-200">{{ $s->participantes_count }}</td>
                                            <td class="py-3.5">
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $s->status === 'em_curso' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-slate-800 text-slate-400' }}">
                                                    {{ $s->status }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                                <a href="{{ route('apresentador.control', $s->id) }}" class="sm-arcade-btn is-gold text-[11px] py-1 px-2.5 inline-flex">
                                                    <span>Controlar</span>
                                                </a>
                                                <a href="{{ route('projecao.tela', $s->id) }}" target="_blank" class="sm-arcade-btn text-[11px] py-1 px-2.5 inline-flex">
                                                    <span>Telão</span>
                                                </a>
                                                <button type="button" onclick="eliminarSalaAdmin({{ $s->id }}, '{{ addslashes($s->nome) }}')"
                                                        class="sm-arcade-btn is-rose text-[11px] py-1 px-2.5 inline-flex cursor-pointer">
                                                    <span>Excluir</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Fila Editorial de Perguntas Pendentes -->
                <div class="sm-panel p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base sm:text-lg font-black text-white flex items-center gap-2">
                            <span class="text-xl">✍️</span>
                            Fila Editorial ({{ $stats['questoes_pendentes'] }} pendentes)
                        </h2>
                        <a href="{{ route('perguntas.index', ['aprovada' => 0]) }}" class="text-xs font-bold text-amber-300 hover:text-amber-200 hover:underline">
                            Ver Todas →
                        </a>
                    </div>

                    @if($questoesPendentes->isEmpty())
                        <div class="p-6 rounded-2xl bg-blue-950/50 border border-blue-400/20 text-center text-xs sm:text-sm text-blue-200">
                            ✨ Todas as {{ $stats['questoes_aprovadas'] }} questões estão revisadas e prontas para o jogo ao vivo!
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($questoesPendentes as $qp)
                                <div class="p-4 rounded-2xl bg-blue-950/60 border border-blue-400/25 flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-xs text-amber-300 font-bold">{{ $qp->livro?->nome }} • {{ $qp->referencia_biblica }}</p>
                                        <p class="text-sm font-bold text-white truncate">{{ $qp->texto }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <form action="{{ route('perguntas.aprovar', $qp->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="sm-arcade-btn is-emerald text-[11px] py-1 px-3 cursor-pointer">
                                                <span>Aprovar</span>
                                            </button>
                                        </form>
                                        <a href="{{ route('perguntas.edit', $qp->id) }}" class="sm-arcade-btn text-[11px] py-1 px-2.5">
                                            <span>Editar</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Coluna Direita (1/3): Gestão de Apresentadores e Congregações -->
            <div class="space-y-8">
                <!-- Cadastrar Novo Apresentador / Editor -->
                <div class="sm-panel p-6">
                    <h2 class="text-base font-black text-white mb-4 flex items-center gap-2">
                        <span class="text-amber-400">👤</span>
                        Adicionar Apresentador / Editor
                    </h2>
                    <form action="{{ route('admin.usuarios.salvar') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="text" name="nome" required placeholder="Nome completo"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-blue-950/80 border border-blue-400/40 text-xs sm:text-sm text-white placeholder-blue-300/40 outline-none focus:ring-2 focus:ring-amber-400">
                        <input type="email" name="email" required placeholder="E-mail de acesso"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-blue-950/80 border border-blue-400/40 text-xs sm:text-sm text-white placeholder-blue-300/40 outline-none focus:ring-2 focus:ring-amber-400">
                        <input type="password" name="password" required minlength="6" placeholder="Senha inicial"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-blue-950/80 border border-blue-400/40 text-xs sm:text-sm text-white placeholder-blue-300/40 outline-none focus:ring-2 focus:ring-amber-400">
                        <div class="grid grid-cols-2 gap-2">
                            <select name="igreja_id" required class="px-3 py-2 rounded-xl bg-blue-950/80 border border-blue-400/40 text-xs text-white outline-none">
                                @foreach($igrejas as $ig)
                                    <option value="{{ $ig->id }}">{{ $ig->nome }}</option>
                                @endforeach
                            </select>
                            <select name="papel" required class="px-3 py-2 rounded-xl bg-blue-950/80 border border-blue-400/40 text-xs text-white outline-none">
                                <option value="apresentador">Apresentador</option>
                                <option value="editor">Editor</option>
                                <option value="admin_igreja">Admin Igreja</option>
                                <option value="admin_geral">Admin Geral</option>
                            </select>
                        </div>
                        <button type="submit" class="sm-arcade-btn is-gold w-full text-xs py-2.5 mt-1 cursor-pointer">
                            <span>Cadastrar Usuário</span>
                        </button>
                    </form>

                    <div class="mt-5 pt-4 border-t border-blue-400/20 space-y-2 max-h-48 overflow-y-auto pr-1">
                        @foreach($usuarios as $u)
                            <div class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-blue-950/50 border border-blue-400/20">
                                <div class="truncate mr-2">
                                    <span class="font-bold text-white block truncate">{{ $u->nome }}</span>
                                    <span class="text-blue-300 text-[11px]">{{ $u->email }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 text-[10px] font-bold border border-amber-500/30 shrink-0">{{ $u->papel }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Congregações Cadastradas -->
                <div class="sm-panel p-6">
                    <h2 class="text-base font-black text-white mb-4 flex items-center gap-2">
                        <span class="text-emerald-400">⛪</span>
                        Congregações Cadastradas
                    </h2>
                    <form action="{{ route('admin.igrejas.criar') }}" method="POST" class="flex gap-2 mb-4">
                        @csrf
                        <input type="text" name="nome" required placeholder="Nova Igreja / Célula..."
                               class="flex-1 px-3.5 py-2 rounded-xl bg-blue-950/80 border border-blue-400/40 text-xs text-white placeholder-blue-300/40 outline-none focus:ring-2 focus:ring-amber-400">
                        <button type="submit" class="sm-arcade-btn is-emerald text-xs py-2 px-3.5 cursor-pointer">
                            <span>Criar</span>
                        </button>
                    </form>

                    <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                        @foreach($igrejas as $ig)
                            <div class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-blue-950/50 border border-blue-400/20">
                                <span class="font-bold text-white">{{ $ig->nome }}</span>
                                <span class="text-blue-300 text-[11px]">{{ $ig->salas_count }} salas • {{ $ig->usuarios_count }} membros</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="sm-crowd w-full mt-auto"></div>

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