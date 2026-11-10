<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show — Pergunta #{{ $questao->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900">

    <div class="max-w-4xl mx-auto px-4 py-8">
        <header class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('perguntas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Voltar à lista
                </a>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1">
                    Pergunta {{ $questao->codigo_classico ? ('#' . $questao->codigo_classico) : ('#' . $questao->id) }}
                </h1>
            </div>
            <a href="{{ route('perguntas.edit', $questao->id) }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-5 py-2.5 rounded-xl font-extrabold text-sm shadow">
                Editar Pergunta
            </a>
        </header>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl mb-6 text-emerald-800 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                    <p class="text-xs font-bold text-slate-400 uppercase">Livro Bíblico</p>
                    <p class="text-base font-extrabold text-slate-900 mt-0.5">{{ $questao->livro->abreviacao ?? '' }} - {{ $questao->livro->nome ?? '-' }}</p>
                </div>
                <div class="bg-amber-50 p-4 rounded-xl border border-amber-200/80">
                    <p class="text-xs font-bold text-amber-700 uppercase">Dica / Referência Bíblica</p>
                    <p class="text-base font-extrabold text-amber-950 mt-0.5">
                        {{ $questao->referencia_biblica ?: (($questao->livro->abreviacao ?? '') . ' ' . ($questao->capitulo->numero ?? '?') . ':' . ($questao->versiculo_de ?? '-')) }}
                    </p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                    <p class="text-xs font-bold text-slate-400 uppercase">Fase do Jogo</p>
                    <p class="text-base font-extrabold text-slate-900 mt-0.5">
                        {{ $questao->dificuldade->nivel ?? 1 }}ª Fase ({{ $questao->dificuldade->pontos_base ?? 10 }} pts)
                    </p>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Enunciado</h3>
                <div class="bg-slate-900 text-white p-6 rounded-2xl">
                    <p class="text-xl font-bold leading-relaxed">{{ $questao->texto }}</p>
                </div>
            </div>

            @php
                $opcoes = is_string($questao->opcoes) ? json_decode($questao->opcoes, true) : ($questao->opcoes ?? []);
                $letras = ['A', 'B', 'C', 'D'];
            @endphp

            @if(is_array($opcoes) && count($opcoes) > 0)
                <div class="mb-8">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Alternativas</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($opcoes as $idx => $opcao)
                            @php
                                $ehCorreta = mb_strtolower(trim((string) $opcao), 'UTF-8') === mb_strtolower(trim((string) $questao->resposta_correta), 'UTF-8');
                            @endphp
                            <div class="p-4 rounded-xl border-2 flex items-center gap-3 {{ $ehCorreta ? 'border-emerald-500 bg-emerald-50 text-emerald-950' : 'border-slate-200 bg-slate-50 text-slate-800' }}">
                                <span class="w-8 h-8 rounded-lg font-extrabold flex items-center justify-center text-sm shrink-0 {{ $ehCorreta ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $letras[$idx] ?? ($idx + 1) }}
                                </span>
                                <span class="font-bold text-sm flex-1">{{ $opcao }}</span>
                                @if($ehCorreta)
                                    <span class="text-xs font-extrabold bg-emerald-200 text-emerald-800 px-2 py-0.5 rounded">Correta</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @php
                $ajudas = is_string($questao->ajudas) ? json_decode($questao->ajudas, true) : ($questao->ajudas ?? []);
            @endphp
            @if(!empty($ajudas['convidados']))
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-blue-700 mb-2">Convidados Bíblicos (Ajudas do Clássico)</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ajudas['convidados'] as $i => $conv)
                            @php $naoSabe = (($i + 1) === (int) ($ajudas['errado'] ?? 0)); @endphp
                            <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $naoSabe ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $conv }} — {{ $naoSabe ? 'Responde que não sabe' : 'Informa a resposta correta' }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

</body>
</html>
