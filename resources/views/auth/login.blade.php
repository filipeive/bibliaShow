<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Entrar no Painel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/show-milhao.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="sm-stage text-slate-100 min-h-screen flex flex-col">

    @include('partials.navbar', ['dark' => true])

    <main class="flex-1 flex items-center justify-center px-4 py-10 relative z-10">
        <div class="w-full max-w-md sm-panel p-7 sm:p-8 relative">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center mb-3">
                    <span class="sm-pill text-xs uppercase tracking-wider">
                        ★ Bíblia Show 2.0 ★
                    </span>
                </div>
                <h1 class="text-2xl font-black text-white sm-logo-title">Acesso ao Painel</h1>
                <p class="text-xs sm:text-sm text-blue-200 mt-1">Entre como Apresentador ou Administrador</p>
            </div>

            @if($errors->any())
                <div class="bg-rose-950/80 border border-rose-500/50 rounded-xl p-3.5 mb-5 text-xs text-rose-200 font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4" id="loginForm">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">E-mail</label>
                    <input type="email" name="email" id="inputEmail" value="{{ old('email') }}" required
                           placeholder="seu@email.com"
                           class="w-full px-4 py-3 rounded-xl border border-blue-400/40 bg-blue-950/80 text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none text-sm transition">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">Palavra-passe</label>
                    <input type="password" name="password" id="inputPassword" required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl border border-blue-400/40 bg-blue-950/80 text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none text-sm transition">
                </div>

                <div class="flex items-center justify-between text-xs text-blue-200">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" checked class="rounded text-amber-500 focus:ring-amber-500 bg-blue-950 border-blue-500/50">
                        <span>Manter sessão</span>
                    </label>
                    <a href="{{ route('register') }}" class="text-amber-300 hover:text-amber-200 hover:underline font-bold">Criar conta</a>
                </div>

                <button type="submit" class="sm-cta text-sm cursor-pointer mt-2">
                    <span>Entrar no Auditório</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </form>
        </div>
    </main>

    <div class="sm-crowd w-full mt-auto"></div>

</body>
</html>

