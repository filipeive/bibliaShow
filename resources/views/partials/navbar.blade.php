@php
    $dark = $dark ?? true;
    $rotaAtual = request()->route()?->getName() ?? '';
    $nomeJogadorSessao = session('jogador_nome', auth()->user()?->nome ?? '');
    $hideOnMobile = $hideOnMobile ?? false;

    $jogoAtivoNavbar = null;
    try {
        $dadosEngineNav = session('game_engine');
        if (!empty($dadosEngineNav)) {
            $engineNav = \App\Game\Engine\GameEngine::desserializarSeguro($dadosEngineNav);
            if ($engineNav) {
                $stNav = $engineNav->getState();
                if (($stNav['estado'] ?? '') === 'em_curso' && !empty($stNav['pergunta_atual'])) {
                    $jogoAtivoNavbar = [
                        'numero' => $stNav['numero_pergunta'] ?? 1,
                        'total' => $stNav['total_perguntas'] ?? 24,
                    ];
                }
            }
        }
    } catch (\Throwable $e) {}
@endphp
<nav class="bg-gradient-to-b from-[#0e2169]/95 via-[#0a164a]/95 to-[#060e33]/98 border-b-2 border-amber-400/40 text-slate-100 backdrop-blur-md sticky top-0 z-50 shadow-2xl shadow-blue-950/60 {{ $hideOnMobile ? 'hidden sm:block' : '' }}">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-2">
            <!-- Brand / Logo (Auditório Show do Milhão) -->
            <a href="/" onclick="if(typeof tentarSairJogoAoVivo === 'function') { return tentarSairJogoAoVivo(event, '/'); }" class="flex items-center gap-2.5 shrink-0 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-300 via-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30 text-slate-950 group-hover:scale-105 transition border-2 border-amber-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div class="flex items-center gap-2 whitespace-nowrap">
                    <span class="font-black text-lg sm:text-xl tracking-tight text-white sm-logo-title">Bíblia Show</span>
                    <span class="sm-pill !px-2.5 !py-0.5 !text-[10px] hidden sm:inline-flex uppercase">
                        2.0
                    </span>
                </div>
            </a>

            <!-- Links de Gestão (Exibidos apenas após Login) -->
            @auth
                <div class="hidden lg:flex items-center gap-1 xl:gap-2 text-xs xl:text-sm font-extrabold whitespace-nowrap">
                    <a href="{{ route('apresentador.painel') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full transition border {{ str_starts_with($rotaAtual, 'apresentador.') ? 'bg-amber-500/25 text-amber-300 border-amber-400/60 shadow-lg shadow-amber-500/20' : 'text-blue-200 hover:text-white hover:bg-blue-900/40 border-transparent' }}">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <span>Apresentador</span>
                    </a>

                    <a href="{{ route('perguntas.index') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full transition border {{ str_starts_with($rotaAtual, 'perguntas.') ? 'bg-amber-500/25 text-amber-300 border-amber-400/60 shadow-lg shadow-amber-500/20' : 'text-blue-200 hover:text-white hover:bg-blue-900/40 border-transparent' }}">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                        </svg>
                        <span>Perguntas</span>
                    </a>

                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full transition border {{ str_starts_with($rotaAtual, 'admin.') ? 'bg-amber-500/25 text-amber-300 border-amber-400/60 shadow-lg shadow-amber-500/20' : 'text-blue-200 hover:text-white hover:bg-blue-900/40 border-transparent' }}">
                        <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                        </svg>
                        <span>Admin</span>
                    </a>
                </div>
            @endauth

            <!-- Right Side Actions -->
            <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0 whitespace-nowrap">
                @if(!request()->routeIs('game.*') && !request()->is('jogar*') && !request()->is('entrar*') && !($hideJogarAgora ?? false))
                    @if($jogoAtivoNavbar)
                        <!-- Botão de Continuar Jogo em Andamento -->
                        <a href="{{ route('game.play') }}"
                           class="sm-cta !w-auto !py-2 !px-4 sm:!px-5 !text-xs sm:!text-sm cursor-pointer shadow-lg hidden sm:inline-flex items-center gap-2 animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Continuar Jogo ({{ $jogoAtivoNavbar['numero'] }}/{{ $jogoAtivoNavbar['total'] }})</span>
                        </a>
                    @else
                        <!-- Botão Principal Destacado: JOGAR AGORA (Desktop e Tablets) -->
                        <button type="button" onclick="abrirModalJogarAgora()"
                                class="sm-cta !w-auto !py-2 !px-4 sm:!px-5 !text-xs sm:!text-sm cursor-pointer shadow-lg hidden sm:inline-flex">
                            <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24">
                                <path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                            </svg>
                            <span>Jogar Agora</span>
                        </button>
                    @endif
                @endif

                <!-- Botão de Definições (Sons & Jogo) - Desktop -->
                <button type="button" onclick="abrirModalDefinicoes()" title="Definições de Som e Jogo"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold bg-blue-950/80 hover:bg-blue-900 border border-blue-400/40 text-blue-200 transition shadow">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="hidden md:inline" id="badgeStatusSomNav">Definições</span>
                </button>

                <!-- Link Sobre o Jogo - Desktop -->
                <a href="{{ route('sobre') }}" title="Sobre o Bíblia Show e Créditos"
                   class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold {{ request()->routeIs('sobre') ? 'bg-amber-500/25 text-amber-300 border border-amber-400/60' : 'bg-blue-950/80 hover:bg-blue-900 text-blue-200 border border-blue-400/40' }} transition shadow">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <span class="hidden md:inline">Sobre</span>
                </a>

                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-black bg-blue-950 border border-amber-400/50 text-amber-300 shadow">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span class="max-w-[110px] truncate">{{ auth()->user()->nome }}</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="hidden sm:inline">
                        @csrf
                        <button type="submit" title="Sair da conta"
                                class="inline-flex items-center gap-1 px-3 py-2 rounded-full text-xs font-bold bg-rose-950/70 hover:bg-rose-900 text-rose-300 border border-rose-500/40 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            <span class="hidden sm:inline">Sair</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-black bg-blue-950/80 hover:bg-blue-900 text-amber-300 border border-amber-400/40 shadow transition">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span>Login</span>
                    </a>
                @endauth

                <!-- Menu de 3 Pontinhos no Mobile (⋮) -->
                <button type="button" onclick="toggleMenuTresPontinhosMobile()" title="Mais opções"
                        class="sm:hidden inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-blue-950/90 border-2 border-amber-400/50 text-amber-300 shadow-md active:scale-95 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6.75a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6.75a1.5 1.5 0 110-3 1.5 1.5 0 010 3z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Dropdown / Drawer do Menu de 3 Pontinhos no Mobile -->
    <div id="menuTresPontinhosMobile" class="hidden sm:hidden border-t border-amber-500/20 bg-gradient-to-b from-[#0a164a]/98 to-[#060e33]/98 px-3.5 py-3 space-y-1.5 backdrop-blur-md shadow-2xl">
        @if($jogoAtivoNavbar && !request()->routeIs('game.*'))
            <a href="{{ route('game.play') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-black text-xs text-slate-950 bg-gradient-to-r from-amber-300 via-amber-400 to-amber-500 shadow-lg border border-amber-200 animate-pulse">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping shrink-0"></span>
                <div>
                    <div class="font-black text-xs">Continuar Jogo em Andamento</div>
                    <div class="text-[10px] text-slate-900 font-bold">Pergunta {{ $jogoAtivoNavbar['numero'] }} de {{ $jogoAtivoNavbar['total'] }}</div>
                </div>
            </a>
        @endif
        <button type="button" onclick="abrirModalDefinicoes(); toggleMenuTresPontinhosMobile();"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left font-bold text-xs text-blue-100 hover:bg-blue-900/50 transition border border-transparent hover:border-blue-400/30">
            <div class="w-8 h-8 rounded-lg bg-blue-900/60 border border-blue-400/30 flex items-center justify-center text-amber-400 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <div class="text-white font-extrabold">Definições</div>
                <div class="text-[10px] text-blue-300">Sons de auditório, efeitos e vibrações</div>
            </div>
        </button>

        <a href="{{ route('sobre') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-xs text-blue-100 hover:bg-blue-900/50 transition border border-transparent hover:border-blue-400/30">
            <div class="w-8 h-8 rounded-lg bg-blue-900/60 border border-blue-400/30 flex items-center justify-center text-amber-400 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </div>
            <div>
                <div class="text-white font-extrabold">Sobre o Jogo & Baixar APK</div>
                <div class="text-[10px] text-blue-300">Instale no celular Android ou conheça o projeto</div>
            </div>
        </a>

        <a href="{{ route('curiosidades') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-xs text-blue-100 hover:bg-blue-900/50 transition border border-transparent hover:border-blue-400/30">
            <div class="w-8 h-8 rounded-lg bg-blue-900/60 border border-blue-400/30 flex items-center justify-center text-emerald-400 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <div>
                <div class="text-white font-extrabold">Regras das 4 Fases</div>
                <div class="text-[10px] text-blue-300">Escada de pontuação e dicas bíblicas</div>
            </div>
        </a>

        @auth
            <div class="pt-2 border-t border-blue-400/20 space-y-1">
                <a href="{{ route('apresentador.painel') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold text-xs text-white hover:bg-blue-900/50">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Painel do Apresentador
                </a>
                <a href="{{ route('perguntas.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold text-xs text-white hover:bg-blue-900/50">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> Banco de Perguntas
                </a>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold text-xs text-white hover:bg-blue-900/50">
                    <span class="w-2 h-2 rounded-full bg-rose-400"></span> Painel Admin ({{ auth()->user()->nome }})
                </a>
                <form action="{{ route('logout') }}" method="POST" class="pt-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-left font-bold text-xs text-rose-300 hover:bg-rose-950/60">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                        Sair da Conta
                    </button>
                </form>
            </div>
        @else
            <div class="pt-2 border-t border-blue-400/20">
                <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-black text-xs text-slate-950 bg-gradient-to-r from-amber-300 to-amber-500 shadow-lg">
                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    <span>Entrar / Acesso do Apresentador</span>
                </a>
            </div>
        @endauth
    </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    @auth
        <div id="mobileMenuBiblia" class="hidden lg:hidden border-t bg-gradient-to-b from-[#0a164a] to-[#060e33] border-blue-400/20 px-4 py-3 space-y-2">
            <a href="{{ route('apresentador.painel') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm text-white hover:bg-blue-900/50">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Painel do Apresentador
            </a>
            <a href="{{ route('perguntas.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm text-white hover:bg-blue-900/50">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span> Banco de Perguntas
            </a>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-bold text-sm text-white hover:bg-blue-900/50">
                <span class="w-2 h-2 rounded-full bg-rose-400"></span> Painel de Administração
            </a>
        </div>
    @endauth
</nav>

<!-- Modal "Jogar Agora" (Tema Show do Milhão Palco) -->
<div id="modalJogarAgora" class="hidden fixed inset-0 z-[60] bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
    <div class="sm-panel max-w-2xl w-full p-6 sm:p-8 relative text-white">
        <button type="button" onclick="fecharModalJogarAgora()" title="Fechar"
                class="absolute top-4 right-4 sm-lifeline !w-9 !h-9 cursor-pointer z-10 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center mb-2">
                <span class="sm-pill text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <span>Arena Bíblia Show 2.0</span>
                </span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-white sm-logo-title">Escolha Como Deseja Jogar</h2>
            <p class="text-xs sm:text-sm text-blue-200 mt-1">Partida individual para o Ranking ou salas ao vivo na igreja</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <!-- Opção 1: Jogo Individual com Nome -->
            <form action="{{ route('game.player') }}" method="POST" onsubmit="pararLocucaoNavbar()"
                  class="bg-blue-950/80 border-2 border-amber-400/60 hover:border-amber-300 rounded-2xl p-5 flex flex-col justify-between transition shadow-lg relative overflow-hidden">
                @csrf
                <input type="hidden" name="novo" value="1">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="sm-pill !px-3 !py-1 !text-xs font-black">SOLO</span>
                        <span class="text-xs font-bold text-amber-300">4 Fases • 15/24 Q</span>
                    </div>
                    <h3 class="text-lg font-black text-white">Modo Individual</h3>
                    <p class="text-xs text-blue-200 mt-1 mb-3 leading-relaxed">
                        Cartas 50/50, Dicas Bíblicas, Convidados e seu nome direto no Ranking Geral!
                    </p>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-amber-300 mb-1">Seu Nome para o Ranking:</label>
                    <input type="text" name="jogador_nome" required maxlength="60"
                           id="inputNomeJogadorNavbar"
                           onfocus="anunciarParticipanteNavbar()"
                           value="{{ $nomeJogadorSessao }}"
                           placeholder="Digite seu nome..."
                           class="w-full px-3.5 py-2.5 rounded-xl bg-blue-950 border border-blue-400/40 text-white placeholder-blue-300/40 text-sm font-bold focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none mb-3">
                </div>
                <button type="submit" class="sm-cta !py-2.5 !text-xs cursor-pointer">
                    Iniciar Partida Solo →
                </button>
            </form>

            <!-- Opção 2: Multijogador / Apresentador -->
            <div class="bg-blue-950/80 border-2 border-blue-400/50 hover:border-amber-400 rounded-2xl p-5 flex flex-col justify-between transition shadow-lg">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/40 text-[10px] font-black uppercase">AO VIVO</span>
                        <span class="text-xs font-bold text-blue-200">Igrejas & Grupos</span>
                    </div>
                    <h3 class="text-lg font-black text-white">Estúdio do Apresentador</h3>
                    <p class="text-xs text-blue-200 mt-1 leading-relaxed">
                        Crie uma sala para o culto ou gincana, defina prêmios, projete no telão e comande os celulares da congregação.
                    </p>
                </div>
                <div class="space-y-2 mt-4">
                    <a href="{{ route('apresentador.painel') }}"
                       class="block text-center w-full py-2.5 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 hover:from-emerald-300 hover:to-emerald-500 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg transition">
                        Abrir Painel do Apresentador →
                    </a>
                    <a href="{{ route('curiosidades') }}"
                       class="block text-center w-full py-2 rounded-full bg-blue-900/60 hover:bg-blue-800 border border-blue-400/40 text-blue-200 font-bold text-xs transition">
                        Ver Regras Oficiais
                    </a>
                </div>
            </div>
        </div>

        <!-- Opção 3: Entrar na Sala com Código -->
        <div class="bg-blue-950/90 border-2 border-blue-400/60 rounded-2xl p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="sm-lifeline !w-11 !h-11 shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-white">Entrar numa Sala no Celular</h3>
                        <p class="text-xs text-blue-200">Digite o código de 6 caracteres do telão para responder:</p>
                    </div>
                </div>
                <form onsubmit="event.preventDefault(); const c = document.getElementById('inputCodigoModalNavbar').value.trim().toUpperCase(); window.location.href = c ? ('/entrar/' + c) : '{{ route('entrar.view') }}';"
                      class="flex items-center gap-2 shrink-0">
                    <input type="text" id="inputCodigoModalNavbar" maxlength="6" placeholder="CÓDIGO"
                           class="w-28 px-3 py-2.5 rounded-xl bg-blue-950 border border-blue-400/50 text-amber-400 font-mono font-black uppercase text-center tracking-widest text-sm focus:border-amber-400 outline-none">
                    <button type="submit"
                            class="px-4 py-2.5 rounded-full bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg transition">
                        Entrar na Sala
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Definições (Tema Show do Milhão Palco) -->
<div id="modalDefinicoes" class="hidden fixed inset-0 z-[65] bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
    <div class="sm-panel max-w-lg w-full p-6 sm:p-7 relative text-white">
        <button type="button" onclick="fecharModalDefinicoes()" title="Fechar"
                class="absolute top-4 right-4 sm-lifeline !w-9 !h-9 cursor-pointer z-10 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="mb-4">
            <span class="sm-pill !px-3 !py-0.5 !text-[10px] uppercase mb-1 inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span>Ajustes do Auditório</span>
            </span>
            <h3 class="text-xl font-black text-white sm-logo-title mt-1 flex items-center gap-2">
                Definições de Som e Jogo
            </h3>
            <p class="text-xs text-blue-200">Sons clássicos e efeitos multimídia originais do Show do Milhão Bíblico.</p>
        </div>

        <div class="space-y-3.5">
            <!-- Toggle Sons Gerais -->
            <div class="flex items-center justify-between p-3.5 rounded-2xl bg-blue-950/80 border border-blue-400/40">
                <div>
                    <p class="font-bold text-sm text-white">Locuções & Trilhas Clássicas</p>
                    <p class="text-xs text-blue-200">Efeitos de acerto, erro, suspense e cronômetro</p>
                </div>
                <button type="button" id="btnToggleSomGlobal" onclick="alternarSomGlobal()"
                        class="px-4 py-2 rounded-full font-black text-xs uppercase transition bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow">
                    ATIVO
                </button>
            </div>

            <!-- Controle de Volume -->
            <div class="p-3.5 rounded-2xl bg-blue-950/80 border border-blue-400/40">
                <div class="flex items-center justify-between mb-2">
                    <label for="inputVolumeGlobal" class="font-bold text-sm text-white">Volume do Auditório</label>
                    <span id="labelVolumeGlobal" class="text-xs font-mono font-black text-amber-300">85%</span>
                </div>
                <input type="range" id="inputVolumeGlobal" min="0" max="100" value="85"
                       oninput="ajustarVolumeGlobal(this.value)"
                       class="w-full accent-amber-400 cursor-pointer">
            </div>

            <!-- Trilha de Abertura -->
            <div class="flex items-center justify-between p-3.5 rounded-2xl bg-blue-950/80 border border-blue-400/40">
                <div class="pr-2">
                    <p class="font-bold text-sm text-white">Tema de Abertura Clássica</p>
                    <p class="text-xs text-blue-200">Trilha oficial dos anos 2000</p>
                </div>
                <button type="button" id="btnAberturaDefinicoes" onclick="alternarAberturaDefinicoes()"
                        class="px-3.5 py-2 rounded-full font-black text-xs uppercase transition bg-gradient-to-r from-amber-400 to-amber-500 text-slate-950 shrink-0 flex items-center gap-1.5 shadow">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                        <path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                    </svg>
                    <span id="txtAberturaDefinicoes">Ouvir Trilha</span>
                </button>
            </div>

            <!-- Testar Efeitos Sonoros -->
            <div class="p-3.5 rounded-2xl bg-blue-950/80 border border-blue-400/40 space-y-2">
                <p class="font-bold text-xs uppercase tracking-wider text-amber-300">Testar Efeitos:</p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="testarEfeitoSonoro('inicio')"
                            class="sm-option !justify-center !py-2 !min-h-[38px] !text-xs cursor-pointer">
                        Chime
                    </button>
                    <button type="button" onclick="testarEfeitoSonoro('certo')"
                            class="sm-option opcao-correta-reveal !justify-center !py-2 !min-h-[38px] !text-xs cursor-pointer">
                        Acerto
                    </button>
                    <button type="button" onclick="testarEfeitoSonoro('erro')"
                            class="sm-option opcao-errada-reveal !justify-center !py-2 !min-h-[38px] !text-xs cursor-pointer">
                        Erro
                    </button>
                </div>
            </div>

            <button type="button" onclick="fecharModalDefinicoes()"
                    class="sm-cta !py-3 !text-xs cursor-pointer mt-2">
                Fechar e Salvar Preferências
            </button>
        </div>
    </div>
</div>

<script>
    // Inicializa som como ATIVO ("on") por padrão caso nunca tenha sido configurado
    if (localStorage.getItem('biblia_show_som') === null) {
        localStorage.setItem('biblia_show_som', 'on');
    }
    if (localStorage.getItem('biblia_show_volume') === null) {
        localStorage.setItem('biblia_show_volume', '85');
    }

    let audioAberturaGlobal = null;
    let tocandoAberturaGlobal = false;

    // Desbloqueia áudio do navegador na primeira interação do usuário (respeitando autoplay policy)
    function desbloquearAudioAutomatico() {
        if (window._audioContextDesbloqueado) return;
        window._audioContextDesbloqueado = true;
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) {
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }
            }
        } catch (e) {}
    }
    ['click', 'touchstart', 'keydown'].forEach(evt => {
        document.addEventListener(evt, desbloquearAudioAutomatico, { once: true, passive: true });
    });

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
        if (novo === 'off' && audioAberturaGlobal && tocandoAberturaGlobal) {
            audioAberturaGlobal.pause();
            tocandoAberturaGlobal = false;
            const txt = document.getElementById('txtAberturaDefinicoes');
            if (txt) txt.textContent = 'Ouvir Trilha';
        }
    }

    function ajustarVolumeGlobal(valor) {
        localStorage.setItem('biblia_show_volume', String(valor));
        if (audioAberturaGlobal) {
            audioAberturaGlobal.volume = (valor / 100) * 0.75;
        }
        atualizarUISomGlobal();
    }

    function alternarAberturaDefinicoes() {
        const txt = document.getElementById('txtAberturaDefinicoes');
        const btn = document.getElementById('btnAberturaDefinicoes');
        if (!audioAberturaGlobal) {
            audioAberturaGlobal = new Audio('/audio/classico/abertura.wav');
            audioAberturaGlobal.onended = () => {
                tocandoAberturaGlobal = false;
                if (txt) txt.textContent = 'Ouvir Trilha';
                if (btn) btn.className = 'px-3.5 py-2 rounded-xl font-black text-xs uppercase transition bg-blue-600 hover:bg-blue-500 text-white shrink-0 flex items-center gap-1.5 shadow';
            };
        }

        if (tocandoAberturaGlobal) {
            audioAberturaGlobal.pause();
            audioAberturaGlobal.currentTime = 0;
            tocandoAberturaGlobal = false;
            if (txt) txt.textContent = 'Ouvir Trilha';
            if (btn) btn.className = 'px-3.5 py-2 rounded-xl font-black text-xs uppercase transition bg-blue-600 hover:bg-blue-500 text-white shrink-0 flex items-center gap-1.5 shadow';
        } else {
            if (!somEstaAtivo()) {
                localStorage.setItem('biblia_show_som', 'on');
                atualizarUISomGlobal();
            }
            audioAberturaGlobal.volume = obterVolumeGlobal() * 0.75;
            audioAberturaGlobal.play().catch(() => {});
            tocandoAberturaGlobal = true;
            if (txt) txt.textContent = 'Parar Trilha';
            if (btn) btn.className = 'px-3.5 py-2 rounded-xl font-black text-xs uppercase transition bg-amber-500 hover:bg-amber-400 text-slate-950 shrink-0 flex items-center gap-1.5 shadow';
        }
    }

    function testarEfeitoSonoro(nome) {
        if (!somEstaAtivo()) {
            localStorage.setItem('biblia_show_som', 'on');
            atualizarUISomGlobal();
        }
        const a = new Audio(`/audio/classico/${nome}.wav`);
        a.volume = obterVolumeGlobal();
        a.play().catch(() => {});
    }

    function testarSomClassico() {
        testarEfeitoSonoro('inicio');
    }

    let audioLocucaoNavbar = null;
    let somAnunciadoNavbar = false;

    function anunciarParticipanteNavbar() {
        if (somAnunciadoNavbar) return;
        somAnunciadoNavbar = true;
        setTimeout(() => { somAnunciadoNavbar = false; }, 9000);

        const somLigado = typeof somEstaAtivo === 'function' ? somEstaAtivo() : (localStorage.getItem('biblia_show_som') !== 'off');
        if (!somLigado) return;

        if (!audioLocucaoNavbar) {
            audioLocucaoNavbar = new Audio('/audio/classico/locucao.wav');
            audioLocucaoNavbar.loop = false;
        }
        audioLocucaoNavbar.volume = obterVolumeGlobal() * 0.95;
        audioLocucaoNavbar.currentTime = 0;
        audioLocucaoNavbar.play().catch(() => {});
    }

    function pararLocucaoNavbar() {
        if (audioLocucaoNavbar) {
            try {
                audioLocucaoNavbar.pause();
                audioLocucaoNavbar.currentTime = 0;
            } catch (e) {}
        }
    }

    function abrirModalJogarAgora() {
        const m = document.getElementById('modalJogarAgora');
        if (m) {
            m.classList.remove('hidden');
            anunciarParticipanteNavbar();
            const inp = document.getElementById('inputNomeJogadorNavbar');
            if (inp) setTimeout(() => inp.focus(), 250);
        }
    }
    function fecharModalJogarAgora() {
        pararLocucaoNavbar();
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

    function toggleMenuTresPontinhosMobile() {
        const m = document.getElementById('menuTresPontinhosMobile');
        if (m) m.classList.toggle('hidden');
    }

    document.addEventListener('DOMContentLoaded', atualizarUISomGlobal);
</script>
