<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Criar Conta de Apresentador / Igreja</title>
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
        <div class="w-full max-w-lg bg-slate-900/95 border border-slate-700/80 rounded-3xl shadow-2xl p-7 sm:p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-black text-white">Cadastrar Apresentador / Igreja</h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">Crie sua conta para organizar salas e gerir perguntas da sua congregação</p>
            </div>

            @if($errors->any())
                <div class="bg-rose-950/80 border border-rose-500/40 rounded-xl p-3.5 mb-5 text-xs text-rose-200">
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
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Seu Nome Completo</label>
                    <input type="text" name="nome" value="{{ old('nome') }}" required placeholder="Ex.: Pr. João Silva"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">E-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="seu@email.com"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Perfil de Acesso</label>
                        <select name="papel" class="w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                            <option value="apresentador">Apresentador de Salas</option>
                            <option value="admin_igreja">Administrador da Igreja</option>
                            <option value="editor">Editor de Perguntas</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Nome da Sua Igreja / Congregação</label>
                    <input type="text" name="igreja_nome" value="{{ old('igreja_nome') }}" placeholder="Ex.: Igreja Batista Central"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Palavra-passe</label>
                        <input type="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Confirmar Palavra-passe</label>
                        <input type="password" name="password_confirmation" required minlength="6" placeholder="Repita a senha"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-black py-3.5 rounded-xl shadow-lg transition text-sm">
                    Criar Conta e Acessar Painel →
                </button>
            </form>

            <p class="text-center text-xs text-slate-400 mt-5">
                Já possui uma conta? <a href="{{ route('login') }}" class="text-amber-400 hover:underline font-bold">Entrar aqui</a>
            </p>
        </div>
    </main>

</body>
</html>
