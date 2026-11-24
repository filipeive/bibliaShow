<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bíblia Show — Painel do Apresentador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .st-label { display: block; font-size: .65rem; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; color: #bfdbfe; margin-bottom: .3rem; }
        .st-link { display: inline-flex; align-items: center; gap: .4rem; padding: .5rem .8rem; border-radius: .8rem; font-size: .75rem; font-weight: 800; color: #bfdbfe; background: rgba(23, 37, 84, .8); border: 1px solid rgba(96, 165, 250, .35); transition: all .15s ease; }
        .st-link:hover { background: rgba(30, 58, 138, .95); color: #fff; }
    </style>
</head>
<body class="sm-stage sm-viewport-lg min-h-screen lg:min-h-0 text-slate-100 flex flex-col">

    @include('partials.navbar', ['dark' => true, 'compact' => true, 'hideJogarAgora' => true])

    @php
        $rotuloStatus = ['em_curso' => 'Ao vivo', 'aguardando' => 'Aguardando', 'pausada' => 'Pausada', 'pausado' => 'Pausada'];
        $podeVerAdmin = in_array(auth()->user()?->papel, ['admin_geral', 'admin_igreja'], true);
    @endphp

    <div class="sm-shell-lg max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 lg:py-6 w-full flex flex-col gap-4">
        <header class="flex flex-wrap justify-between items-end gap-3 shrink-0">
            <div>
                <span class="sm-pill !px-3 !py-0.5 !text-[10px] uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                    Estúdio ao vivo
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white sm-logo-title mt-1.5">Painel do Apresentador</h1>
                <p class="text-blue-200 text-xs sm:text-sm">Crie uma sala, projete no telão e comande a partida ao vivo.</p>
            </div>
            <div class="flex items-center gap-2">
                @if($podeVerAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="st-link">Painel Admin</a>
                @endif
                <a href="{{ route('perguntas.index') }}" class="sm-cta !w-auto !py-2 !px-4 !text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    Banco de Perguntas
                </a>
            </div>
        </header>

        <div class="flex-1 lg:min-h-0 grid grid-cols-1 lg:grid-cols-12 lg:grid-rows-[minmax(0,1fr)] gap-4">
            <!-- Criar Nova Sala -->
            <section class="lg:col-span-4 sm-panel p-5 shadow-2xl flex flex-col">
                <h2 class="text-base font-black text-amber-300 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Nova sala
                </h2>
                <form action="{{ route('apresentador.criar') }}" method="POST" class="space-y-3 flex-1 flex flex-col">
                    @csrf
                    <div>
                        <label for="nome" class="st-label">Nome da sala / evento</label>
                        <input type="text" id="nome" name="nome" required placeholder="Ex.: Culto de Jovens, Gincana EBD..." class="sm-game-input">
                    </div>
                    <div>
                        <label for="quantidade_perguntas" class="st-label">Formato da partida</label>
                        <select id="quantidade_perguntas" name="quantidade_perguntas" class="sm-game-input">
                            <option value="12">12 perguntas (3 por fase)</option>
                            <option value="16">16 perguntas (4 por fase)</option>
                            <option value="24" selected>24 perguntas (4 fases clássicas)</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-3">
                        <div>
                            <label for="premio_primeiro_lugar" class="st-label !text-amber-300">Prêmio 1º lugar</label>
                            <input type="text" id="premio_primeiro_lugar" name="premio_primeiro_lugar" placeholder="Opcional" class="sm-game-input">
                        </div>
                        <div>
                            <label for="premio_segundo_lugar" class="st-label">Prêmio 2º lugar</label>
                            <input type="text" id="premio_segundo_lugar" name="premio_segundo_lugar" placeholder="Opcional" class="sm-game-input">
                        </div>
                    </div>
                    <div class="mt-auto pt-2">
                        <button type="submit" class="sm-cta text-sm cursor-pointer">
                            Criar e abrir estúdio
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </button>
                    </div>
                </form>
            </section>

            <!-- Salas Ativas -->
            <section class="lg:col-span-8 sm-panel p-5 shadow-2xl flex flex-col lg:min-h-0">
                <h2 class="text-base font-black text-amber-300 mb-3 flex items-center justify-between shrink-0">
                    <span>Salas ativas</span>
                    <span class="sm-pill !px-2.5 !py-0.5 !text-[10px]"><span id="contadorSalas">{{ $salas->count() }}</span> sala(s)</span>
                </h2>

                <div id="listaSalasPainel" class="lg:flex-1 lg:min-h-0 overflow-y-auto pr-1 grid grid-cols-1 xl:grid-cols-2 gap-3 content-start">
                    @forelse($salas as $sala)
                        @php $aoVivo = $sala->status === 'em_curso'; @endphp
                        <article id="salaPainelRow_{{ $sala->id }}" class="rounded-2xl bg-blue-950/60 border {{ $aoVivo ? 'border-emerald-400/40' : 'border-blue-400/25' }} p-4 flex flex-col gap-3 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-white font-black truncate">{{ $sala->nome }}</h3>
                                    <p class="text-xs text-blue-200/80 mt-0.5">{{ $sala->participantes_count ?? 0 }} participante(s)</p>
                                    @if($sala->premio_primeiro_lugar || $sala->premio_segundo_lugar)
                                        <p class="text-[11px] text-amber-300/90 mt-1 truncate">
                                            🏆 {{ $sala->premio_primeiro_lugar ?: '—' }}@if($sala->premio_segundo_lugar) <span class="text-blue-200/70">• 2º {{ $sala->premio_segundo_lugar }}</span>@endif
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-1.5 shrink-0">
                                    <span class="font-mono font-black text-amber-300 bg-amber-500/15 border border-amber-500/30 px-2.5 py-0.5 rounded-lg text-sm">{{ $sala->codigo_acesso }}</span>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $aoVivo ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $aoVivo ? 'bg-emerald-400 animate-pulse' : 'bg-slate-500' }}"></span>
                                        {{ $rotuloStatus[$sala->status] ?? ucfirst(str_replace('_', ' ', $sala->status)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-2">
                                <a href="{{ route('apresentador.control', $sala->id) }}" class="col-span-2 sm-cta !py-2 !text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                                    Controlar
                                </a>
                                <a href="{{ route('projecao.tela', $sala->id) }}" target="_blank" class="st-link justify-center" title="Abrir telão">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" /></svg>
                                    <span class="hidden sm:inline">Telão</span>
                                </a>
                                <button type="button" onclick="eliminarSalaPainel({{ $sala->id }}, @js($sala->nome))" class="st-link justify-center !text-rose-300 hover:!bg-rose-600 hover:!text-white !border-rose-500/40" title="Excluir sala">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    <span class="hidden sm:inline">Excluir</span>
                                </button>
                            </div>
                        </article>
                    @empty
                        <div class="xl:col-span-2 text-center py-12 text-blue-200/70 text-sm">
                            Nenhuma sala ativa. Crie a primeira no formulário ao lado.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <!-- Toast Apresentador -->
    <div id="toastPainel" class="fixed bottom-6 right-6 z-50 transform translate-y-24 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-3 border text-white" id="toastPainelContent">
            <span id="toastPainelMsg"></span>
        </div>
    </div>

    <script>
        const BASE_APP_URL = (() => {
            const subpath = "{{ parse_url(url('/'), PHP_URL_PATH) }}" || '';
            return (window.location.origin + subpath).replace(/\/+$/, '');
        })();

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
                const resp = await fetch(`${BASE_APP_URL}/apresentador/salas/${salaId}`, {
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
                        row.style.transform = 'scale(.96)';
                        setTimeout(() => row.remove(), 400);
                    }
                    const contador = document.getElementById('contadorSalas');
                    if (contador) contador.textContent = Math.max(0, (parseInt(contador.textContent, 10) || 1) - 1);
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
