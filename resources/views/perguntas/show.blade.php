<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bíblia Show - Pergunta #{{ $questao->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-4xl mx-auto px-4 py-8">
        <header class="mb-8">
            <a href="{{ route('perguntas.index') }}" class="text-slate-500 hover:text-slate-700">← Voltar</a>
            <h1 class="text-2xl font-bold text-slate-900">Pergunta #{{ $questao->id }}</h1>
        </header>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                            {{ $questao->aprovada ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $questao->aprovada ? 'Aprovada' : 'Pendente' }}
                        </span>
                        @if(!$questao->referencia_verificada)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                Ref. não verificada
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-4 text-sm text-slate-500">
                        <span>Submetida por: {{ $questao->submetidaPor->nome ?? 'Desconhecido' }}</span>
                        @if($questao->revisada_em)
                            <span>Revisada em: {{ $questao->revisada_em->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2">
                    @if(!$questao->aprovada)
                        <form action="{{ route('perguntas.aprovar', $questao->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">Aprovar</button>
                        </form>
                        <button onclick="document.getElementById('rejeitarForm').submit()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">Rejeitar</button>
                        <form id="rejeitarForm" action="{{ route('perguntas.rejeitar', $questao->id) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @endif
                    <a href="{{ route('perguntas.edit', $questao->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Editar</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-slate-50 p-4 rounded-lg">
                    <p class="text-xs font-semibold text-slate-500 uppercase">Livro</p>
                    <p class="text-lg font-medium text-slate-800">{{ $questao->livro->abreviacao }} - {{ $questao->livro->nome }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <p class="text-xs font-semibold text-slate-500 uppercase">Referência</p>
                    <p class="text-lg font-medium text-slate-800 font-mono">
                        {{ $questao->livro->abreviacao }} {{ $questao->capitulo->numero ?? '?' }}:{{ $questao->versiculo_de ?? '-' }}@if($questao->versiculo_ate)-{{ $questao->versiculo_ate }}@endif
                    </p>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <p class="text-xs font-semibold text-slate-500 uppercase">Categoria</p>
                    <p class="text-lg font-medium text-slate-800">{{ $questao->categoria->nome ?? '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <p class="text-xs font-semibold text-slate-500 uppercase">Dificuldade</p>
                    <p class="text-lg font-medium text-slate-800">Nível {{ $questao->dificuldade->nivel ?? '-' }}</p>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Pergunta</h3>
                <div class="bg-slate-50 p-6 rounded-lg border-l-4 border-l-blue-500">
                    <p class="text-xl text-slate-800 leading-relaxed">{{ $questao->texto }}</p>
                </div>
            </div>

            @if($questao->opcoes)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-slate-900 mb-3">Opções de Resposta</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="list-disc list-inside space-y-2 text-slate-700">
                            @php $opcoes = is_string($questao->opcoes) ? json_decode($questao->opcoes, true) : $questao->opcoes; @endphp
                            @if(is_array($opcoes))
                                @foreach($opcoes as $opcao)
                                    <li>{{ $opcao }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            @endif

            <div class="mb-8">
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Resposta Correta</h3>
                <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                    <p class="text-lg text-green-800 font-medium">{{ $questao->resposta_correta }}</p>
                </div>
            </div>

            @if($questao->explicacao)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-slate-900 mb-3">Explicação</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <p class="text-slate-700 leading-relaxed">{{ $questao->explicacao }}</p>
                    </div>
                </div>
            @endif

            @if($questao->contexto)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-slate-900 mb-3">Contexto da Passagem</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <p class="text-slate-700 leading-relaxed">{{ $questao->contexto }}</p>
                    </div>
                </div>
            @endif

            @if($questao->notas)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-slate-900 mb-3">Notas Editoriais</h3>
                    <div class="bg-amber-50 p-6 rounded-lg border border-amber-200">
                        <p class="text-slate-700 leading-relaxed">{{ $questao->notas }}</p>
                    </div>
                </div>
            @endif

            <div class="mt-8 flex justify-end">
                <a href="{{ route('perguntas.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-6 py-3 rounded-lg font-medium">Voltar à lista</a>
            </div>
        </div>
    </div>

</body>
</html>