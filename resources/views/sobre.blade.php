<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre o Projeto — Bíblia Show 2.0 Clássico</title>
    <link rel="icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="sm-stage text-slate-100 min-h-screen flex flex-col justify-between">

    @include('partials.navbar', ['dark' => true])

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 w-full space-y-8 relative z-10">

        <!-- Hero Section: Hall da Criação & Download do APK -->
        <div class="sm-panel p-6 sm:p-10 lg:p-12 relative overflow-hidden">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 mb-3">
                    <span class="sm-pill !px-3.5 !py-1 !text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <span>Game Credits & Hall da Criação</span>
                    </span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-black text-white sm-logo-title tracking-tight leading-tight">
                    Bíblia Show 2.0
                </h1>
                <p class="mt-3 text-xs sm:text-base text-blue-200 leading-relaxed">
                    A lendária experiência multimídia do Show do Milhão Bíblico dos anos 2000, completamente restaurada e expandida para smartphones Android, computadores, cultos de jovens, gincanas bíblicas e famílias cristãs.
                </p>

                <!-- Botões de Ação do Hero: Jogar Solo + BAIXAR APK ANDROID + WhatsApp -->
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <!-- Botão de Download APK Android em Destaque -->
                    <a href="{{ asset('downloads/biblia-show-v2.apk') }}" download="biblia-show-v2.apk"
                       class="sm-arcade-btn is-gold !py-3 !px-6 !text-xs sm:!text-sm inline-flex items-center gap-2.5 shadow-2xl">
                        <!-- Ícone Android SVG -->
                        <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.551 0 .9993.4482.9993.9993.0001.5511-.4483.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4126 13.8533 8.125 12 8.125c-1.8533 0-3.5902.2876-5.1368.8247L4.8409 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.75h24c-.3432-4.0911-2.6889-7.5633-6.1185-9.4286"/>
                        </svg>
                        <span class="font-black">Descarregar APK Android</span>
                        <span class="text-[10px] bg-slate-950/40 text-amber-200 px-2 py-0.5 rounded-full font-mono">12 MB</span>
                    </a>

                    <!-- Botão Jogar Solo -->
                    <a href="{{ route('game.play', ['novo' => 1]) }}"
                       class="sm-cta !w-auto !py-3 !px-6 !text-xs sm:!text-sm cursor-pointer shadow-xl">
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                        <span>START GAME (Jogar Solo)</span>
                    </a>

                    <!-- Botão WhatsApp do Desenvolvedor -->
                    <a href="https://wa.me/258862134230?text=Ol%C3%A1%20Filipe%2C%20vim%20pelo%20B%C3%ADblia%20Show%202.0" target="_blank"
                       class="sm-arcade-btn is-emerald !py-3 !px-5 !text-xs cursor-pointer">
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.41a8.173 8.173 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.42 0-2.82-.37-4.06-1.07l-.29-.17-3.02.79.81-2.95-.19-.3a8.156 8.156 0 01-1.25-4.34c0-4.54 3.7-8.24 8.24-8.24m4.52 11.53c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.07-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.47-1.39-1.72-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.66.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.12.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.44.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.11-.22-.17-.47-.29z"/>
                        </svg>
                        <span>Falar com o Autor</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Seção: Perfil do Game Developer & Especificações do Jogo -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
            <!-- Coluna 1: Perfil do Desenvolvedor & Contatos -->
            <div class="lg:col-span-5 sm-panel p-6 sm:p-8 space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-300 via-amber-500 to-amber-700 text-slate-950 font-black text-2xl flex items-center justify-center shadow-lg shadow-amber-500/30 border-2 border-amber-200 shrink-0">
                        FD
                    </div>
                    <div>
                        <span class="sm-pill !px-2.5 !py-0.5 !text-[9px] uppercase tracking-wider">CREATOR</span>
                        <h2 class="text-xl sm:text-2xl font-black text-white mt-1">Filipe Domingos</h2>
                        <p class="text-xs text-blue-200 font-bold flex items-center gap-1.5 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            <span>Quelimane, Moçambique</span>
                        </p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-blue-950/80 border border-blue-400/30 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-bold text-amber-300">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.333A48.243 48.243 0 0012 9.75c-2.551 0-5.056.2-7.5.583V21"/></svg>
                        <span>Igreja Local:</span>
                    </div>
                    <p class="text-xs sm:text-sm font-bold text-white pl-6">
                        Life Church Moçambique em Quelimane
                    </p>
                </div>

                <!-- Box de Download do APK para Android Direto no Card -->
                <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-500/15 via-blue-950/80 to-blue-950/90 border-2 border-amber-400/50 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center shrink-0 shadow-lg">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.551 0 .9993.4482.9993.9993.0001.5511-.4483.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4126 13.8533 8.125 12 8.125c-1.8533 0-3.5902.2876-5.1368.8247L4.8409 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.75h24c-.3432-4.0911-2.6889-7.5633-6.1185-9.4286"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-amber-300 uppercase tracking-wider">Aplicativo Móvel Android</p>
                            <p class="text-[11px] text-blue-200">Instale no celular para jogar com vibração e sons</p>
                        </div>
                    </div>
                    <a href="{{ asset('downloads/biblia-show-v2.apk') }}" download="biblia-show-v2.apk"
                       class="w-full sm-arcade-btn is-gold !py-2.5 !text-xs flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 fill-none stroke-current" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Baixar APK (12 MB)</span>
                    </a>
                </div>

                <div class="space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-amber-300">Canais de Contato & Colaboração:</h3>

                    <a href="mailto:filipedomingos198@gmail.com" class="flex items-center gap-3 p-3 rounded-xl bg-blue-950/80 hover:bg-blue-900 border border-blue-400/30 text-white transition group">
                        <div class="sm-lifeline !w-9 !h-9 shrink-0 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-blue-200 uppercase font-bold">Email Direto</p>
                            <p class="text-xs sm:text-sm font-bold text-amber-300 truncate">filipedomingos198@gmail.com</p>
                        </div>
                    </a>

                    <a href="https://wa.me/258862134230?text=Ol%C3%A1%20Filipe%2C%20vim%20pelo%20B%C3%ADblia%20Show%202.0" target="_blank"
                       class="flex items-center justify-between p-3.5 rounded-xl bg-emerald-950/70 hover:bg-emerald-900 border-2 border-emerald-400/50 text-white transition group shadow-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#25D366] text-slate-950 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.41a8.173 8.173 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.42 0-2.82-.37-4.06-1.07l-.29-.17-3.02.79.81-2.95-.19-.3a8.156 8.156 0 01-1.25-4.34c0-4.54 3.7-8.24 8.24-8.24m4.52 11.53c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.07-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.47-1.39-1.72-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.66.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.12.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.44.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.11-.22-.17-.47-.29z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-emerald-300 uppercase font-black tracking-wider">WhatsApp Direto</p>
                                <p class="text-xs sm:text-sm font-black text-white">+258 86 213 4230</p>
                            </div>
                        </div>
                        <span class="text-xs font-black px-3 py-1 rounded-full bg-[#25D366] text-slate-950 group-hover:bg-[#20ba5a] transition shrink-0 hidden sm:inline">
                            Conversar →
                        </span>
                    </a>

                    <a href="https://github.com/filipeive/bibliaShow" target="_blank" class="flex items-center gap-3 p-3 rounded-xl bg-blue-950/80 hover:bg-blue-900 border border-blue-400/30 text-white transition group">
                        <div class="sm-lifeline !w-9 !h-9 shrink-0 flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-300 fill-current" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-blue-200 uppercase font-bold">Código Aberto no GitHub</p>
                            <p class="text-xs sm:text-sm font-bold text-purple-300 truncate">github.com/filipeive/bibliaShow</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Coluna 2: Origem do Game, Especificações e Fases -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Origem e Preservação do Jogo Clássico -->
                <div class="sm-panel p-6 sm:p-8 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/50 flex items-center justify-center text-amber-400 shrink-0 shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-black text-white sm-logo-title">Adaptação do Clássico dos Anos 2000</h3>
                            <p class="text-xs text-amber-300 font-bold">Show do Milhão Bíblico em CD-ROM</p>
                        </div>
                    </div>

                    <p class="text-xs sm:text-sm text-blue-100 leading-relaxed">
                        Este projeto é uma recriação fiel do clássico Bíblia Show dos anos 2000 (originalmente distribuído em CD-ROM para Windows 98/2000/XP). Preservamos toda a mecânica de auditório, perguntas, pontuações e efeitos sonoros originais:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1 text-xs">
                        <div class="p-3 rounded-xl bg-blue-950/80 border border-blue-400/30 flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0"></span>
                            <span><strong>1.000 Questões</strong> de Gênesis a Apocalipse</span>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-950/80 border border-blue-400/30 flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span>
                            <span><strong>25 Efeitos Sonoros</strong> e trilhas clássicas WAV</span>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-950/80 border border-blue-400/30 flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-400 shrink-0"></span>
                            <span><strong>Dicas & Personagens</strong> das Escrituras</span>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-950/80 border border-blue-400/30 flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-400 shrink-0"></span>
                            <span><strong>Cartas 50/50 & Pulos</strong> táticos</span>
                        </div>
                    </div>
                </div>

                <!-- Novidades da Engine 2.0 -->
                <div class="sm-panel p-6 sm:p-8 space-y-4">
                    <h3 class="text-base sm:text-lg font-black text-white flex items-center gap-2 sm-logo-title">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                        <span>Novidades da Game Engine 2.0</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 text-xs">
                        <div class="bg-blue-950/80 border border-blue-400/30 p-4 rounded-2xl">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-300 flex items-center justify-center mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                            </div>
                            <p class="font-extrabold text-amber-300 text-xs mb-1">Multiplayer Instantâneo</p>
                            <p class="text-blue-200 leading-relaxed text-[11px]">Entrada por QR Code pelo navegador ou app móvel sem cadastro.</p>
                        </div>
                        <div class="bg-blue-950/80 border border-blue-400/30 p-4 rounded-2xl">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" /></svg>
                            </div>
                            <p class="font-extrabold text-emerald-300 text-xs mb-1">Telão de Auditório</p>
                            <p class="text-blue-200 leading-relaxed text-[11px]">Modo Projeção com letras gigantes e ranking em tempo real.</p>
                        </div>
                        <div class="bg-blue-950/80 border border-blue-400/30 p-4 rounded-2xl">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-300 flex items-center justify-center mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" /></svg>
                            </div>
                            <p class="font-extrabold text-blue-300 text-xs mb-1">Estúdio do Apresentador</p>
                            <p class="text-blue-200 leading-relaxed text-[11px]">Mesa de áudio, controle de suspense e premiações.</p>
                        </div>
                    </div>
                </div>

                <!-- Arquitetura e Tecnologia -->
                <div class="sm-panel p-6 sm:p-8 space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-amber-300">Stack Tecnológica do Jogo:</h3>
                    <div class="flex flex-wrap gap-2 text-xs font-mono font-bold text-blue-200">
                        <span class="px-3 py-1.5 rounded-xl bg-blue-950/80 border border-blue-400/30">Android APK</span>
                        <span class="px-3 py-1.5 rounded-xl bg-blue-950/80 border border-blue-400/30">PWA / Service Worker</span>
                        <span class="px-3 py-1.5 rounded-xl bg-blue-950/80 border border-blue-400/30">PHP 8.4</span>
                        <span class="px-3 py-1.5 rounded-xl bg-blue-950/80 border border-blue-400/30">Laravel 12</span>
                        <span class="px-3 py-1.5 rounded-xl bg-blue-950/80 border border-blue-400/30">Tailwind CSS</span>
                        <span class="px-3 py-1.5 rounded-xl bg-blue-950/80 border border-blue-400/30">Web Audio API</span>
                        <span class="px-3 py-1.5 rounded-xl bg-blue-950/80 border border-blue-400/30">SSE Real-Time</span>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Silhueta da Plateia no Rodapé -->
    <div class="sm-crowd w-full mt-auto"></div>

    <footer class="border-t border-blue-400/20 bg-blue-950/90 py-5 text-center text-xs text-blue-300 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>Bíblia Show 2.0 Clássico • Para a glória de Deus e edificação da Sua igreja.</p>
            <p>Criado por <strong>Filipe Domingos dos Santos</strong> (Life Church Quelimane)</p>
        </div>
    </footer>

</body>
</html>