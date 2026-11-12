<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') • Bíblia Show 2.0</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        .milhao-bg {
            background: radial-gradient(circle at 50% 25%, #1e3a8a 0%, #0a1128 55%, #030712 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="milhao-bg text-slate-100 min-h-screen flex flex-col justify-between selection:bg-amber-400 selection:text-slate-950 font-sans">

    <!-- Header Simples com Marca -->
    <header class="w-full py-4 px-6 border-b border-amber-400/20 bg-blue-950/60 backdrop-blur-md">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-300 via-amber-500 to-amber-700 flex items-center justify-center text-slate-950 font-black shadow-lg shadow-amber-500/30 border-2 border-amber-200">
                    <svg class="w-5 h-5 fill-none stroke-current" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="font-black text-lg text-white">Bíblia Show</span>
                    <span class="sm-pill !px-2 !py-0.2 !text-[9px] uppercase">2.0</span>
                </div>
            </a>

            <a href="{{ url('/') }}" class="text-xs font-bold text-amber-300 hover:text-amber-200 transition flex items-center gap-1">
                <span>Início</span>
                <span>→</span>
            </a>
        </div>
    </header>

    <!-- Centro: Card de Erro Show do Milhão -->
    <main class="w-full max-w-lg mx-auto px-4 py-8 sm:py-12 flex-1 flex flex-col items-center justify-center">
        <div class="sm-panel p-6 sm:p-8 w-full text-center shadow-2xl relative border-amber-400/30">
            <!-- Pílula Código de Erro -->
            <div class="inline-flex items-center justify-center mb-3">
                <span class="sm-pill !px-4 !py-1 !text-sm font-black font-mono tracking-widest text-amber-300 border-amber-400/50">
                    ERRO @yield('code')
                </span>
            </div>

            <!-- Título Principal -->
            <h1 class="text-2xl sm:text-3xl font-black text-white mt-1 mb-3 tracking-tight">
                @yield('title')
            </h1>

            <!-- Mensagem Explicativa -->
            <p class="text-sm text-blue-200/90 leading-relaxed mb-6">
                @yield('message')
            </p>

            <!-- Versículo Bíblico Reconfortante -->
            <div class="sm-ingot !p-3.5 mb-6 text-left border border-amber-400/30 bg-blue-950/70">
                <div class="flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <div class="text-xs">
                        <p class="italic text-amber-100/90">@yield('verse')</p>
                        <p class="font-black text-amber-400 text-[10px] mt-1 uppercase tracking-wider">@yield('reference')</p>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação Solicitados pelo Usuário -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <button type="button"
                        onclick="if(window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ url('/') }}'; }"
                        class="py-3 px-4 rounded-xl bg-blue-900/80 hover:bg-blue-800 text-blue-200 font-black text-xs uppercase tracking-wider border border-blue-400/40 transition flex items-center justify-center gap-2 shadow-md cursor-pointer active:scale-95">
                    <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    <span>Voltar Atrás</span>
                </button>

                <a href="{{ url('/') }}"
                   class="sm-arcade-btn is-gold !py-3 !px-4 !text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg active:scale-95">
                    <svg class="w-4 h-4 text-slate-950" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                    <span>Ir para o Início</span>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer Discreto -->
    <footer class="py-4 text-center text-xs text-blue-300/60 border-t border-blue-900/40">
        <p>Bíblia Show 2.0 • Edição Show do Milhão</p>
    </footer>

</body>
</html>
