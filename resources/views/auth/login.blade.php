<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Entrar no Painel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .milhao-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
    </style>
</head>
<body class="milhao-bg text-slate-100 min-h-screen flex flex-col">

    @include('partials.navbar', ['dark' => true])

    <main class="flex-1 flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md bg-slate-900/95 border border-slate-700/80 rounded-3xl shadow-2xl p-7 sm:p-8">
            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center mx-auto mb-3 text-amber-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-black text-white">Acesso Administrativo</h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">Entre como Apresentador, Editor ou Administrador da Igreja</p>
            </div>

            @if($errors->any())
                <div class="bg-rose-950/80 border border-rose-500/40 rounded-xl p-3.5 mb-5 text-xs text-rose-200 font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4" id="loginForm">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">E-mail</label>
                    <input type="email" name="email" id="inputEmail" value="{{ old('email', 'admin@igreja.com') }}" required
                           placeholder="seu@email.com"
                           class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Palavra-passe</label>
                    <input type="password" name="password" id="inputPassword" value="password" required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" checked class="rounded text-amber-500 focus:ring-amber-500 bg-slate-950 border-slate-700">
                        <span>Manter sessão iniciada</span>
                    </label>
                    <a href="{{ route('register') }}" class="text-amber-400 hover:underline font-semibold">Criar nova conta</a>
                </div>

                <button type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-black py-3.5 rounded-xl shadow-lg shadow-amber-500/20 transition text-sm">
                    Entrar no Painel →
                </button>
            </form>

            <!-- Acesso Rápido de Demonstração -->
            <div class="mt-6 pt-5 border-t border-slate-800">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-2.5">
                    Acesso Rápido (Credenciais Padrão)
                </p>
                <button type="button"
                        onclick="document.getElementById('inputEmail').value='admin@igreja.com'; document.getElementById('inputPassword').value='password'; document.getElementById('loginForm').submit();"
                        class="w-full py-2.5 px-3 rounded-xl bg-blue-950/70 hover:bg-blue-900 border border-blue-500/30 text-xs font-bold text-blue-200 transition flex items-center justify-between">
                    <span>Administrador Geral (admin@igreja.com)</span>
                    <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 font-mono">1-Clique</span>
                </button>
            </div>
        </div>
    </main>

</body>
</html>

