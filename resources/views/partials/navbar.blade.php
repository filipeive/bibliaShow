@php
    $dark = $dark ?? true;
    $rotaAtual = request()->route()?->getName() ?? '';
@endphp
<nav class="{{ $dark ? 'bg-slate-950/90 border-slate-800/80 text-slate-100' : 'bg-white/95 border-slate-200 text-slate-900' }} backdrop-blur-md sticky top-0 z-50 border-b">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-2">
            <!-- Brand / Logo (Never wraps) -->
            <a href="/" class="flex items-center gap-2.5 shrink-0 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-md shadow-amber-500/20 text-slate-950 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div class="flex items-center gap-1.5 whitespace-nowrap">
                    <span class="font-black text-lg tracking-tight {{ $dark ? 'text-white' : 'text-slate-900' }}">Bíblia Show</span>
                    <span class="hidden xl:inline-block text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30">
                        2.0 Clássico
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Links (Single Line, whitespace-nowrap) -->
            <div class="hidden lg:flex items-center gap-1 xl:gap-1.5 text-xs xl:text-sm font-bold whitespace-nowrap">
                <a href="/"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition {{ request()->is('/') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : ($dark ? 'text-slate-300 hover:text-white hover:bg-slate-800/70' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100') }}">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span>Início</span>
                </a>

                <a href="{{ route('curiosidades') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition {{ $rotaAtual === 'curiosidades' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : ($dark ? 'text-slate-300 hover:text-white hover:bg-slate-800/70' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100') }}">
                    <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>Enciclopédia & Regras</span>
                </a>

                @auth
                    <a href="{{ route('game.play', ['novo' => 1]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition {{ str_starts_with($rotaAtual, 'game.play') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : ($dark ? 'text-slate-300 hover:text-white hover:bg-slate-800/70' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100') }}">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                        </svg>
                        <span>Jogo Individual</span>
                    </a>

                    <a href="{{ route('entrar.view') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition {{ str_starts_with($rotaAtual, 'entrar.') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : ($dark ? 'text-slate-300 hover:text-white hover:bg-slate-800/70' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100') }}">
                        <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                        <span>Entrar na Sala</span>
                    </a>

                    <a href="{{ route('apresentador.painel') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition {{ str_starts_with($rotaAtual, 'apresentador.') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : ($dark ? 'text-slate-300 hover:text-white hover:bg-slate-800/70' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100') }}">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <span>Apresentador</span>
                    </a>

                    <a href="{{ route('perguntas.index') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition {{ str_starts_with($rotaAtual, 'perguntas.') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : ($dark ? 'text-slate-300 hover:text-white hover:bg-slate-800/70' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100') }}">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                        </svg>
                        <span>Perguntas</span>
                    </a>

                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition {{ str_starts_with($rotaAtual, 'admin.') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : ($dark ? 'text-slate-300 hover:text-white hover:bg-slate-800/70' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100') }}">
                        <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                        </svg>
                        <span>Admin</span>
                    </a>
                @endauth
            </div>

            <!-- Right Side Actions (Never wraps) -->
            <div class="flex items-center gap-2 shrink-0 whitespace-nowrap">
                <!-- Botão Principal Destacado: JOGAR AGORA (abre seletor de modos) -->
                <button type="button" onclick="abrirModalJogarAgora()"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 px-4 sm:px-5 py-2.5 rounded-xl font-black text-xs sm:text-sm shadow-lg shadow-amber-500/30 ring-2 ring-amber-300/50 hover:scale-[1.02] transition">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                    </svg>
                    <span>Jogar Agora</span>
                </button>

                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold {{ $dark ? 'bg-slate-800 text-amber-300 border border-slate-700' : 'bg-slate-100 text-slate-800 border border-slate-200' }}">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span class="max-w-[110px] truncate">{{ auth()->user()->nome }}</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Sair da conta"
                                class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-xs font-bold {{ $dark ? 'bg-rose-950/60 hover:bg-rose-900 text-rose-300 border border-rose-500/30' : 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' }} transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            <span class="hidden sm:inline">Sair</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold {{ $dark ? 'bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700' : 'bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200' }} transition">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span>Login</span>
                    </a>
                @endauth

                <!-- Mobile Hamburger Button -->
                <button type="button" onclick="document.getElementById('mobileMenuBiblia').classList.toggle('hidden')"
                        class="lg:hidden inline-flex items-center justify-center p-2 rounded-xl {{ $dark ? 'bg-slate-800 text-slate-200 hover:bg-slate-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobileMenuBiblia" class="hidden lg:hidden border-t {{ $dark ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200' }} px-4 py-3 space-y-1.5">
        <a href="/" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm {{ $dark ? 'hover:bg-slate-800 text-white' : 'hover:bg-slate-100 text-slate-900' }}">
            <span class="w-2 h-2 rounded-full bg-amber-400"></span> Início
        </a>
        <a href="{{ route('curiosidades') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm {{ $dark ? 'hover:bg-slate-800 text-white' : 'hover:bg-slate-100 text-slate-900' }}">
            <span class="w-2 h-2 rounded-full bg-purple-400"></span> Enciclopédia & Regras Clássicas
        </a>
        @auth
            <a href="{{ route('game.play', ['novo' => 1]) }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm {{ $dark ? 'hover:bg-slate-800 text-white' : 'hover:bg-slate-100 text-slate-900' }}">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span> Jogar Individual
            </a>
            <a href="{{ route('entrar.view') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm {{ $dark ? 'hover:bg-slate-800 text-white' : 'hover:bg-slate-100 text-slate-900' }}">
                <span class="w-2 h-2 rounded-full bg-blue-400"></span> Entrar em Sala ao Vivo
            </a>
            <a href="{{ route('apresentador.painel') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm {{ $dark ? 'hover:bg-slate-800 text-white' : 'hover:bg-slate-100 text-slate-900' }}">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Painel do Apresentador
            </a>
            <a href="{{ route('perguntas.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm {{ $dark ? 'hover:bg-slate-800 text-white' : 'hover:bg-slate-100 text-slate-900' }}">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Banco de Perguntas
            </a>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm {{ $dark ? 'hover:bg-slate-800 text-white' : 'hover:bg-slate-100 text-slate-900' }}">
                <span class="w-2 h-2 rounded-full bg-rose-400"></span> Painel de Administração
            </a>
        @endauth
    </div>
</nav>

<!-- Modal "Jogar Agora" — Escolha entre Jogo Individual, Multijogador e Entrar na Sala -->
<div id="modalJogarAgora" class="hidden fixed inset-0 z-[60] bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-slate-900 border-2 border-amber-400/70 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl relative text-slate-100">
        <button type="button" onclick="fecharModalJogarAgora()"
                class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="text-center mb-6">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 text-xs font-black uppercase tracking-widest mb-2">
                Arena Bíblia Show 2.0
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white">Escolha Como Deseja Jogar</h2>
            <p class="text-sm text-slate-400 mt-1">Selecione entre desafio individual, partida multijogador em grupo ou entrar numa sala com código</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <!-- Opção 1: Jogo Individual -->
            <a href="{{ route('game.play', ['novo' => 1]) }}"
               class="group bg-slate-950/90 hover:bg-amber-500/10 border-2 border-slate-800 hover:border-amber-400 rounded-2xl p-5 flex flex-col justify-between transition shadow-lg">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-400 flex items-center justify-center mb-3 group-hover:scale-105 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-white group-hover:text-amber-300">Jogo Individual</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Desafio clássico em 4 fases com 3 dicas bíblicas, 3 convidados, cartas 50/50 e 3 vidas.
                    </p>
                </div>
                <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-black text-amber-400 uppercase tracking-wider">
                    Iniciar Partida Solo →
                </span>
            </a>

            <!-- Opção 2: Multijogador / Criar Sala -->
            <a href="{{ route('apresentador.painel') }}"
               class="group bg-slate-950/90 hover:bg-emerald-500/10 border-2 border-slate-800 hover:border-emerald-400 rounded-2xl p-5 flex flex-col justify-between transition shadow-lg">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-400 flex items-center justify-center mb-3 group-hover:scale-105 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-white group-hover:text-emerald-300">Multijogador (Criar Sala)</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Crie uma sala para sua igreja ou grupo, defina prêmios para 1º e 2º lugar e projete no telão.
                    </p>
                </div>
                <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-black text-emerald-400 uppercase tracking-wider">
                    Abrir Estúdio Multijogador →
                </span>
            </a>
        </div>

        <!-- Opção 3: Entrar na Sala com Código -->
        <div class="bg-slate-950/90 border-2 border-blue-500/40 rounded-2xl p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-blue-500/20 border border-blue-400/40 text-blue-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-white">Entrar numa Sala ao Vivo</h3>
                        <p class="text-xs text-slate-400">Possui o código de 6 dígitos exibido no telão? Entre para responder pelo celular:</p>
                    </div>
                </div>
                <form onsubmit="event.preventDefault(); const c = document.getElementById('inputCodigoModalNavbar').value.trim().toUpperCase(); window.location.href = c ? ('/entrar/' + c) : '{{ route('entrar.view') }}';"
                      class="flex items-center gap-2 shrink-0">
                    <input type="text" id="inputCodigoModalNavbar" maxlength="6" placeholder="CÓDIGO"
                           class="w-28 px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-amber-400 font-mono font-black uppercase text-center tracking-widest text-sm focus:border-amber-400 outline-none">
                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs uppercase tracking-wider shadow transition">
                        Entrar na Sala
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function abrirModalJogarAgora() {
        const m = document.getElementById('modalJogarAgora');
        if (m) m.classList.remove('hidden');
    }
    function fecharModalJogarAgora() {
        const m = document.getElementById('modalJogarAgora');
        if (m) m.classList.add('hidden');
    }
</script>
