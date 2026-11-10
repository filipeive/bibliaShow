<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Aguardando Início</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .projection-bg { background: radial-gradient(circle at top, #172554 0%, #0f172a 55%, #020617 100%); }
    </style>
</head>
<body class="projection-bg text-white min-h-screen flex items-center justify-center p-8">

    <div class="max-w-2xl w-full bg-slate-900/90 border border-amber-500/40 rounded-3xl p-10 text-center shadow-2xl">
        <div class="w-20 h-20 rounded-full border-4 border-amber-500 border-t-transparent animate-spin mx-auto mb-6"></div>
        <h1 class="text-4xl font-black text-white mb-2">Bíblia Show</h1>
        @if(isset($sala))
            <p class="text-amber-400 text-2xl font-mono font-black mt-2">Código: {{ $sala->codigo_acesso }}</p>
            <p class="text-slate-300 text-lg mt-1">{{ $sala->nome }}</p>
        @else
            <p class="text-amber-300 text-lg">Sala #{{ $salaId }}</p>
        @endif
        <p class="text-slate-400 mt-4 text-sm">Aguardando o apresentador iniciar a partida...</p>
    </div>

    <script>
        setInterval(() => location.reload(), 3000);
    </script>
</body>
</html>
