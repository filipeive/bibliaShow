<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Criar Conta de Apresentador / Igreja</title>
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
        <div class="w-full max-w-lg sm-panel p-7 sm:p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center mb-3">
                    <span class="sm-pill text-xs uppercase tracking-wider">
                        ★ Cadastro do Auditório ★
                    </span>
                </div>
                <h1 class="text-2xl font-black text-white sm-logo-title">Novo Apresentador / Igreja</h1>
                <p class="text-xs sm:text-sm text-blue-200 mt-1">Crie sua conta para gerenciar perguntas e salas ao vivo</p>
            </div>

            @if($errors->any())
                <div class="bg-rose-950/80 border border-rose-500/50 rounded-xl p-3.5 mb-5 text-xs text-rose-200">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">Seu Nome Completo</label>
                    <input type="text" name="nome" value="{{ old('nome') }}" required placeholder="Ex.: Pr. João Silva"
                           class="w-full px-4 py-2.5 rounded-xl border border-blue-400/40 bg-blue-950/80 text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none text-sm transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">E-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="seu@email.com"
                               class="w-full px-4 py-2.5 rounded-xl border border-blue-400/40 bg-blue-950/80 text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none text-sm transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">Perfil de Acesso</label>
                        <select name="papel" class="w-full px-3 py-2.5 rounded-xl border border-blue-400/40 bg-blue-950/80 text-white focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none text-sm transition">
                            <option value="usuario">Jogador / Usuário Normal</option>
                            <option value="apresentador" selected>Apresentador de Salas</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">Nome da Sua Igreja / Congregação</label>
                    <input type="text" name="igreja_nome" value="{{ old('igreja_nome') }}" placeholder="Ex.: Igreja Batista Central"
                           class="w-full px-4 py-2.5 rounded-xl border border-blue-400/40 bg-blue-950/80 text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none text-sm transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">Palavra-passe</label>
                        <input type="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres"
                               class="w-full px-4 py-2.5 rounded-xl border border-blue-400/40 bg-blue-950/80 text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none text-sm transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-1.5">Confirmar Palavra-passe</label>
                        <input type="password" name="password_confirmation" required minlength="6" placeholder="Repita a senha"
                               class="w-full px-4 py-2.5 rounded-xl border border-blue-400/40 bg-blue-950/80 text-white placeholder-blue-300/40 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none text-sm transition">
                    </div>
                </div>

                <button type="submit" class="sm-cta text-sm cursor-pointer mt-3">
                    <span>Criar Conta e Acessar Painel</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </form>

            <p class="text-center text-xs text-blue-200 mt-5">
                Já possui uma conta? <a href="{{ route('login') }}" class="text-amber-300 hover:text-amber-200 hover:underline font-bold">Entrar aqui</a>
            </p>
        </div>
    </main>

    <div class="sm-crowd w-full mt-auto"></div>

</body>
</html>

