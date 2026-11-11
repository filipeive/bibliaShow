@php
    $dark = $dark ?? true;
    $rotaAtual = request()->route()?->getName() ?? '';
    $nomeJogadorSessao = session('jogador_nome', auth()->user()?->nome ?? '');
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
                    <span class="hidden sm:inline-block text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30">
                        2.0 Clássico
                    </span>
                </div>
            </a>

            <!-- Links de Gestão (Exibidos apenas após Login) -->
            @auth
                <div class="hidden lg:flex items-center gap-1 xl:gap-1.5 text-xs xl:text-sm font-bold whitespace-nowrap">
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
                </div>
            @endauth

            <!-- Right Side Actions (Never wraps) -->
            <div class="flex items-center gap-2 shrink-0 whitespace-nowrap">
                @if(!request()->routeIs('game.*') && !request()->is('jogar*') && !request()->is('entrar*') && !($hideJogarAgora ?? false))
                    <!-- Botão Principal Destacado: JOGAR AGORA -->
                    <button type="button" onclick="abrirModalJogarAgora()"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 px-4 sm:px-5 py-2.5 rounded-xl font-black text-xs sm:text-sm shadow-lg shadow-amber-500/30 ring-2 ring-amber-300/50 hover:scale-[1.02] transition">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                        </svg>
                        <span>Jogar Agora</span>
                    </button>
                @endif

                <!-- Botão de Definições (Sons & Jogador) -->
                <button type="button" onclick="abrirModalDefinicoes()" title="Definições de Som e Jogador"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold {{ $dark ? 'bg-slate-800/90 hover:bg-slate-700 text-slate-200 border border-slate-700' : 'bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200' }} transition">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="hidden md:inline" id="badgeStatusSomNav">Definições</span>
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

                    <!-- Mobile Hamburger Button (Somente autenticado) -->
                    <button type="button" onclick="document.getElementById('mobileMenuBiblia').classList.toggle('hidden')"
                            class="lg:hidden inline-flex items-center justify-center p-2 rounded-xl {{ $dark ? 'bg-slate-800 text-slate-200 hover:bg-slate-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold {{ $dark ? 'bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700' : 'bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200' }} transition">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span>Login</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer (Exibido apenas para utilizadores autenticados) -->
    @auth
        <div id="mobileMenuBiblia" class="hidden lg:hidden border-t {{ $dark ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200' }} px-4 py-3 space-y-1.5">
            <a href="{{ route('apresentador.painel') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm {{ $dark ? 'hover:bg-slate-800 text-white' : 'hover:bg-slate-100 text-slate-900' }}">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Painel do Apresentador
            </a>
            <a href="{{ route('perguntas.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm {{ $dark ? 'hover:bg-slate-800 text-white' : 'hover:bg-slate-100 text-slate-900' }}">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Banco de Perguntas
            </a>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm {{ $dark ? 'hover:bg-slate-800 text-white' : 'hover:bg-slate-100 text-slate-900' }}">
                <span class="w-2 h-2 rounded-full bg-rose-400"></span> Painel de Administração
            </a>
        </div>
    @endauth
</nav>

<!-- Modal "Jogar Agora" — Escolha entre Jogo Individual (com Nome), Multijogador e Entrar na Sala -->
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
            <p class="text-sm text-slate-400 mt-1">Selecione entre desafio individual (com seu nome no Ranking), criar sala multijogador ou entrar numa sala</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <!-- Opção 1: Jogo Individual com Nome do Participante -->
            <form action="{{ route('game.player') }}" method="POST"
                  class="bg-slate-950/90 border-2 border-amber-500/40 hover:border-amber-400 rounded-2xl p-5 flex flex-col justify-between transition shadow-lg">
                @csrf
                <input type="hidden" name="novo" value="1">
                <div>
                    <div class="w-11 h-11 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-400 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-white">Jogo Individual</h3>
                    <p class="text-xs text-slate-400 mt-1 mb-3 leading-relaxed">
                        4 Fases Clássicas • 3 Dicas • 3 Convidados • Seu nome entra no Ranking Geral!
                    </p>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-amber-300 mb-1">Seu Nome para o Ranking:</label>
                    <input type="text" name="jogador_nome" required maxlength="60"
                           value="{{ $nomeJogadorSessao }}"
                           placeholder="Digite seu nome..."
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-bold focus:border-amber-400 outline-none mb-3">
                </div>
                <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow transition">
                    Iniciar Jogo Individual →
                </button>
            </form>

            <!-- Opção 2: Multijogador / Apresentador (Criar Sala) -->
            <div class="bg-slate-950/90 border-2 border-slate-800 hover:border-emerald-400 rounded-2xl p-5 flex flex-col justify-between transition shadow-lg">
                <div>
                    <div class="w-11 h-11 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-400 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-white">Multijogador (Apresentador)</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Crie uma sala ao vivo para sua igreja, defina prêmios de 1º e 2º lugar, projete no telão e acompanhe o ranking.
                    </p>
                </div>
                <div class="space-y-2 mt-4">
                    <a href="{{ route('apresentador.painel') }}"
                       class="block text-center w-full py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow transition">
                        Abrir Painel do Apresentador →
                    </a>
                    <a href="{{ route('curiosidades') }}"
                       class="block text-center w-full py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700 text-slate-300 font-bold text-xs transition">
                        Ver Enciclopédia & Regras Clássicas
                    </a>
                </div>
            </div>
        </div>

        <!-- Opção 3: Entrar na Sala com Código -->
        <div class="bg-slate-950/90 border-2 border-blue-500/40 rounded-2xl p-4 sm:p-5">
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

<!-- Modal de Definições (Áudio Clássico Ativo por Padrão + Volume) -->
<div id="modalDefinicoes" class="hidden fixed inset-0 z-[65] bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-slate-900 border-2 border-slate-700 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl relative text-slate-100">
        <button type="button" onclick="fecharModalDefinicoes()"
                class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h3 class="text-xl font-black text-white mb-1 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
            </svg>
            Definições de Som e Jogo
        </h3>
        <p class="text-xs text-slate-400 mb-5">Por padrão todos os sons originais do Bíblia Show estão ativos.</p>

        <div class="space-y-4">
            <!-- Toggle Sons Gerais -->
            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-950 border border-slate-800">
                <div>
                    <p class="font-bold text-sm text-white">Sons e Locuções Clássicas</p>
                    <p class="text-xs text-slate-400">Efeitos de acerto, erro, fases, dicas e convidados</p>
                </div>
                <button type="button" id="btnToggleSomGlobal" onclick="alternarSomGlobal()"
                        class="px-4 py-2 rounded-xl font-black text-xs uppercase transition bg-emerald-500 text-slate-950">
                    ATIVO
                </button>
            </div>

            <!-- Controle de Volume -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <label for="inputVolumeGlobal" class="font-bold text-sm text-white">Volume do Áudio</label>
                    <span id="labelVolumeGlobal" class="text-xs font-mono font-black text-amber-400">85%</span>
                </div>
                <input type="range" id="inputVolumeGlobal" min="0" max="100" value="85"
                       oninput="ajustarVolumeGlobal(this.value)"
                       class="w-full accent-amber-500 cursor-pointer">
            </div>

            <!-- Testar Som -->
            <div class="flex items-center justify-between gap-2 pt-2">
                <button type="button" onclick="testarSomClassico()"
                        class="flex-1 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 font-bold text-xs border border-slate-700 transition">
                    Testar Áudio Clássico
                </button>
                <button type="button" onclick="fecharModalDefinicoes()"
                        class="flex-1 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs transition">
                    Concluído
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializa som como ATIVO ("on") por padrão caso nunca tenha sido desativado
    if (localStorage.getItem('biblia_show_som') === null) {
        localStorage.setItem('biblia_show_som', 'on');
    }
    if (localStorage.getItem('biblia_show_volume') === null) {
        localStorage.setItem('biblia_show_volume', '85');
    }

    function somEstaAtivo() {
        return localStorage.getItem('biblia_show_som') !== 'off';
    }

    function obterVolumeGlobal() {
        const v = parseInt(localStorage.getItem('biblia_show_volume') || '85', 10);
        return Math.max(0, Math.min(1, v / 100));
    }

    function atualizarUISomGlobal() {
        const ativo = somEstaAtivo();
        const vol = parseInt(localStorage.getItem('biblia_show_volume') || '85', 10);
        const btn = document.getElementById('btnToggleSomGlobal');
        if (btn) {
            btn.textContent = ativo ? 'ATIVO' : 'DESATIVADO';
            btn.className = ativo
                ? 'px-4 py-2 rounded-xl font-black text-xs uppercase transition bg-emerald-500 text-slate-950'
                : 'px-4 py-2 rounded-xl font-black text-xs uppercase transition bg-rose-500/20 text-rose-300 border border-rose-500/40';
        }
        const inputVol = document.getElementById('inputVolumeGlobal');
        if (inputVol) inputVol.value = vol;
        const lblVol = document.getElementById('labelVolumeGlobal');
        if (lblVol) lblVol.textContent = vol + '%';

        window.dispatchEvent(new CustomEvent('biblia-som-alterado', { detail: { ativo, volume: vol / 100 } }));
    }

    function alternarSomGlobal() {
        const novo = somEstaAtivo() ? 'off' : 'on';
        localStorage.setItem('biblia_show_som', novo);
        atualizarUISomGlobal();
    }

    function ajustarVolumeGlobal(valor) {
        localStorage.setItem('biblia_show_volume', String(valor));
        atualizarUISomGlobal();
    }

    function testarSomClassico() {
        if (!somEstaAtivo()) {
            localStorage.setItem('biblia_show_som', 'on');
            atualizarUISomGlobal();
        }
        const a = new Audio('/audio/classico/inicio.wav');
        a.volume = obterVolumeGlobal();
        a.play().catch(() => {});
    }

    function abrirModalJogarAgora() {
        const m = document.getElementById('modalJogarAgora');
        if (m) m.classList.remove('hidden');
    }
    function fecharModalJogarAgora() {
        const m = document.getElementById('modalJogarAgora');
        if (m) m.classList.add('hidden');
    }
    function abrirModalDefinicoes() {
        atualizarUISomGlobal();
        const m = document.getElementById('modalDefinicoes');
        if (m) m.classList.remove('hidden');
    }
    function fecharModalDefinicoes() {
        const m = document.getElementById('modalDefinicoes');
        if (m) m.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', atualizarUISomGlobal);
</script>
