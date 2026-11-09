<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show - Aguardando</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .projection-bg { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    </style>
</head>
<body class="projection-bg text-white min-h-screen p-8">

    <div class="max-w-4xl mx-auto flex flex-col items-center justify-center h-full">
        <div class="w-24 h-24 rounded-full border-8 border-blue-500 border-t-transparent animate-spin mb-8"></div>
        <h1 class="text-4xl font-bold text-white mb-2">Bíblia Show</h1>
        <p class="text-blue-300 text-lg">Sala: {{ $salaId }}</p>
        <p class="text-slate-400 mt-4">Aguardando o apresentador iniciar o jogo...</p>
    </div>

</body>
</html>