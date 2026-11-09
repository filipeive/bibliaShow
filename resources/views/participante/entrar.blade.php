<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar na Sala — Bíblia Show</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-md mx-auto px-4 py-8">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-2xl">B</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Bíblia Show</h1>
                <p class="text-slate-600 text-sm mt-2">Entre na sala de jogo</p>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                    <span class="text-sm font-semibold text-amber-800">Sala:</span>
                    <span class="text-sm font-mono font-bold text-amber-800">{{ $sala->codigo_acesso }}</span>
                </div>
                <p class="text-xs text-amber-600/70">{{ $sala->nome }}</p>
            </div>

            <form id="entrarForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Seu nome</label>
                    <input type="text" name="nome" required
                           placeholder="Digite seu nome"
                           class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Equipe (opcional)</label>
                    <input type="text" name="equipe_nome"
                           placeholder="Nome da equipe"
                           class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition">
                </div>

                <button type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 rounded-xl transition shadow-lg transform active:scale-95">
                    Entrar na sala
                </button>
            </form>

            <div id="error" class="hidden bg-red-50 border border-red-200 p-3 rounded-lg text-red-700 text-sm mt-4"></div>
            <div id="success" class="hidden bg-green-50 border border-green-200 p-3 rounded-lg text-green-700 text-sm mt-4"></div>
        </div>
    </div>

    <script>
        document.getElementById('entrarForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = this.querySelector('button');
            const originalText = btn.innerText;
            btn.innerText = 'Entrando...';
            btn.disabled = true;

            document.getElementById('error').classList.add('hidden');
            document.getElementById('success').classList.add('hidden');

            const formData = new FormData(this);
            const codigo = '{{ $sala->codigo_acesso }}';

            try {
                const response = await fetch('/api/salas/entrar', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        codigo_acesso: codigo,
                        nome: formData.get('nome'),
                        equipe_nome: formData.get('equipe_nome') || null,
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Erro ao entrar na sala.');
                }

                document.getElementById('success').innerText = 'Conectado, ' + data.participante.nome + '! Código do participante: ' + data.participante.codigo_acesso;
                document.getElementById('success').classList.remove('hidden');
                this.reset();

            } catch (err) {
                document.getElementById('error').innerText = err.message;
                document.getElementById('error').classList.remove('hidden');
                btn.innerText = originalText;
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>
