<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre o Projeto — Bíblia Show 2.0 Clássico</title>
    <link rel="icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg {
            background: radial-gradient(circle at top, #1e293b 0%, #090d16 75%);
        }
    </style>
</head>
<body class="milhao-bg text-slate-100 min-h-screen flex flex-col">

    @include('partials.navbar', ['dark' => true])

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 w-full space-y-10">

        <!-- Hero Section -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900/90 via-slate-900/70 to-amber-950/30 border border-slate-800 p-8 sm:p-12 shadow-2xl">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-400 text-xs font-black uppercase tracking-wider mb-4">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    Projeto & Créditos Oficiais
                </div>
                <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                    Sobre o <span class="bg-gradient-to-r from-amber-400 via-amber-300 to-yellow-500 bg-clip-text text-transparent">Bíblia Show 2.0</span>
                </h1>
                <p class="mt-4 text-base sm:text-lg text-slate-300 leading-relaxed">
                    Uma recriação moderna e fiel do inesquecível clássico dos anos 2000. Desenvolvido para transformar cultos, gincanas, células de jovens e momentos em família em uma empolgante jornada pelo conhecimento das Sagradas Escrituras.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('game.play') }}"
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black px-6 py-3.5 rounded-2xl text-sm shadow-xl shadow-amber-500/20 transition hover:scale-[1.02]">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                        <span>Jogar Modo Individual</span>
                    </a>
                    <a href="https://wa.me/258862134230" target="_blank"
                       class="inline-flex items-center gap-2 bg-slate-800/90 hover:bg-slate-800 border border-slate-700 text-slate-200 font-bold px-6 py-3.5 rounded-2xl text-sm transition">
                        <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.41a8.173 8.173 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.42 0-2.82-.37-4.06-1.07l-.29-.17-3.02.79.81-2.95-.19-.3a8.156 8.156 0 01-1.25-4.34c0-4.54 3.7-8.24 8.24-8.24m4.52 11.53c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.07-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.47-1.39-1.72-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.66.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.12.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.44.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.11-.22-.17-.47-.29z"/></svg>
                        <span>Colaborar via WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Seção: O Desenvolvedor & Contatos para Colaboração -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-5 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black text-2xl flex items-center justify-center shadow-lg shadow-amber-500/20 shrink-0">
                        FD
                    </div>
                    <div>
                        <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-400">Desenvolvedor da Versão 2.0</span>
                        <h2 class="text-xl sm:text-2xl font-black text-white leading-tight">Filipe Domingos dos Santos</h2>
                        <p class="text-xs sm:text-sm text-slate-400 font-medium mt-0.5">Quelimane, Moçambique 🇲🇿</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-300">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.333A48.243 48.243 0 0012 9.75c-2.551 0-5.056.2-7.5.583V21"/></svg>
                        <span>Igreja Local:</span>
                    </div>
                    <p class="text-sm font-extrabold text-white pl-6">
                        Membro da Life Church Moçambique em Quelimane
                    </p>
                </div>

                <div class="space-y-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Canais de Contato & Colaboração:</h3>

                    <a href="mailto:filipedomingos198@gmail.com" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 text-slate-200 transition group">
                        <div class="w-9 h-9 rounded-xl bg-blue-500/15 border border-blue-500/30 text-blue-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Email Direto</p>
                            <p class="text-xs sm:text-sm font-bold text-white truncate group-hover:text-amber-400">filipedomingos198@gmail.com</p>
                        </div>
                    </a>

                    <a href="https://wa.me/258862134230" target="_blank" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 text-slate-200 transition group">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.41a8.173 8.173 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.42 0-2.82-.37-4.06-1.07l-.29-.17-3.02.79.81-2.95-.19-.3a8.156 8.156 0 01-1.25-4.34c0-4.54 3.7-8.24 8.24-8.24m4.52 11.53c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.07-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.47-1.39-1.72-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.66.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.12.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.44.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.11-.22-.17-.47-.29z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-slate-400 uppercase font-bold">WhatsApp / Telefone</p>
                            <p class="text-xs sm:text-sm font-bold text-white truncate group-hover:text-emerald-400">+258 86 213 4230</p>
                        </div>
                    </a>

                    <a href="https://github.com/filipeive/bibliaShow" target="_blank" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 text-slate-200 transition group">
                        <div class="w-9 h-9 rounded-xl bg-purple-500/15 border border-purple-500/30 text-purple-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Código Aberto no GitHub</p>
                            <p class="text-xs sm:text-sm font-bold text-white truncate group-hover:text-purple-400">github.com/filipeive/bibliaShow</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Coluna 2: Origem Histórica, Adaptação e Tecnologias -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Origem e Créditos do Jogo Original -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white">Adaptação do Clássico dos Anos 2000</h3>
                            <p class="text-xs text-amber-400 font-bold">Show do Milhão Bíblico</p>
                        </div>
                    </div>

                    <p class="text-sm text-slate-300 leading-relaxed">
                        Este projeto é uma homenagem e adaptação moderna do lendário jogo para PC <strong>Bíblia Show</strong>, distribuído originalmente em CD-ROM para Windows 98/2000/XP desenvolvido em Macromedia Director.
                    </p>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Preservamos com reverência a essência que marcou gerações:
                    </p>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1 text-xs">
                        <li class="flex items-center gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800/80">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span><strong>1.000 Perguntas Bíblicas</strong> originais de Gênesis a Apocalipse</span>
                        </li>
                        <li class="flex items-center gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800/80">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span><strong>Efeitos Sonoros Originais</strong> e suspense clássico</span>
                        </li>
                        <li class="flex items-center gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800/80">
                            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                            <span><strong>Dicas Bíblicas de Referência</strong> e Personagens Bíblicos</span>
                        </li>
                        <li class="flex items-center gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800/80">
                            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                            <span><strong>Eliminação de Cartas 50/50</strong> e Pulos estratégicos</span>
                        </li>
                    </ul>
                </div>

                <!-- Inovações da Versão 2.0 Web -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-4">
                    <h3 class="text-lg font-black text-white flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                        O que há de novo na Versão 2.0?
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 text-xs">
                        <div class="bg-slate-950/70 border border-slate-800 p-4 rounded-2xl">
                            <p class="font-extrabold text-amber-300 text-sm mb-1">Multiplayer sem App</p>
                            <p class="text-slate-400 leading-relaxed">Qualquer participante entra escaneando o QR Code pelo navegador do celular.</p>
                        </div>
                        <div class="bg-slate-950/70 border border-slate-800 p-4 rounded-2xl">
                            <p class="font-extrabold text-emerald-300 text-sm mb-1">Telão & Corrida ao Vivo</p>
                            <p class="text-slate-400 leading-relaxed">Modo Projeção para TV/Projetor com corrida em tempo real e letras das respostas.</p>
                        </div>
                        <div class="bg-slate-950/70 border border-slate-800 p-4 rounded-2xl">
                            <p class="font-extrabold text-blue-300 text-sm mb-1">Painel do Apresentador</p>
                            <p class="text-slate-400 leading-relaxed">Controle total da rodada, premiações para 1º e 2º lugar e regras configuráveis.</p>
                        </div>
                    </div>
                </div>

                <!-- Arquitetura e Tecnologia -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-3">Tecnologias Utilizadas</h3>
                    <div class="flex flex-wrap gap-2 text-xs font-mono font-bold text-slate-300">
                        <span class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800">PHP 8.4</span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800">Laravel 12</span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800">Tailwind CSS</span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800">Web Speech API</span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800">SweetAlert2</span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800">HTML5 Audio Engine</span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800">SQLite / MySQL</span>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950/80 py-6 text-center text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>Bíblia Show 2.0 Clássico • Para a glória de Deus e edificação da Sua igreja.</p>
            <p>Criado por <strong>Filipe Domingos dos Santos</strong> (Life Church Quelimane)</p>
        </div>
    </footer>

</body>
</html>