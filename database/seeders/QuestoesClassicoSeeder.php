<?php

namespace Database\Seeders;

use App\Models\Capitulo;
use App\Models\Categoria;
use App\Models\Igreja;
use App\Models\LivroBiblico;
use App\Models\NivelDificuldade;
use App\Models\Questao;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestoesClassicoSeeder extends Seeder
{
    public function run(): void
    {
        $dbfPath = database_path('data/cadastro.dbf');
        if (!file_exists($dbfPath)) {
            $dbfPath = base_path('../Biblia Show/media/cadastro.dbf');
        }
        if (!file_exists($dbfPath)) {
            $this->command?->warn("Arquivo clássico não encontrado em: {$dbfPath}");
            return;
        }

        $igreja = Igreja::firstOrCreate(
            ['slug' => 'igreja-exemplo'],
            ['nome' => 'Igreja Exemplo']
        );

        $usuario = Usuario::firstOrCreate(
            ['email' => 'admin@igreja.com'],
            [
                'igreja_id' => $igreja->id,
                'nome' => 'Administrador',
                'password' => bcrypt('password'),
                'papel' => 'admin_geral',
            ]
        );

        // Garantir que livros, níveis e categorias existam
        if (LivroBiblico::count() < 66) {
            $this->call(LivrosBiblicosSeeder::class);
        }
        if (NivelDificuldade::count() === 0) {
            $this->call(NiveisDificuldadeSeeder::class);
        }
        if (Categoria::count() === 0) {
            $this->call(CategoriasSeeder::class);
        }

        $livrosMap = $this->construirMapaDeLivros();
        $livroPadraoId = LivroBiblico::where('codigo_osis', 'Gen')->value('id') ?? LivroBiblico::first()->id;

        $niveisMap = NivelDificuldade::pluck('id', 'nivel')->toArray();
        $categoriasMap = Categoria::pluck('id', 'slug')->toArray();
        $capitulosCache = [];

        $handle = fopen($dbfPath, 'rb');
        if (!$handle) {
            return;
        }

        $header = fread($handle, 32);
        $meta = unpack('Cver/Cy/Cm/Cd/VnumRecords/vheaderLen/vrecordLen', $header);

        $fields = [];
        while (!feof($handle)) {
            $fieldBytes = fread($handle, 32);
            if ($fieldBytes === false || strlen($fieldBytes) === 0 || ord($fieldBytes[0]) === 0x0D) {
                break;
            }
            $name = rtrim(substr($fieldBytes, 0, 11), "\0");
            $type = $fieldBytes[11];
            $len = ord($fieldBytes[16]);
            $fields[] = ['name' => $name, 'type' => $type, 'len' => $len];
        }

        fseek($handle, $meta['headerLen']);

        $now = now();
        $lote = [];
        $totalImportadas = 0;

        // Limpar perguntas importadas anteriormente do clássico para evitar duplicatas
        DB::transaction(function () use (
            $handle,
            $meta,
            $fields,
            $igreja,
            $usuario,
            $livrosMap,
            $livroPadraoId,
            $niveisMap,
            $categoriasMap,
            &$capitulosCache,
            &$lote,
            &$totalImportadas,
            $now
        ) {
            Questao::withTrashed()->whereNotNull('codigo_classico')->forceDelete();

            for ($i = 0; $i < $meta['numRecords']; $i++) {
                $rec = fread($handle, $meta['recordLen']);
                if ($rec === false || strlen($rec) < $meta['recordLen']) {
                    break;
                }

                // Registro deletado no DBF
                if (ord($rec[0]) === 0x2A) {
                    continue;
                }

                $pos = 1;
                $row = [];
                foreach ($fields as $fld) {
                    $raw = substr($rec, $pos, $fld['len']);
                    $pos += $fld['len'];
                    $row[$fld['name']] = trim(mb_convert_encoding($raw, 'UTF-8', 'Windows-1252'));
                }

                $codigo = (int) ($row['CODIGO'] ?? 0);
                $pergunta = $row['PERGUNTA'] ?? '';
                if ($pergunta === '') {
                    continue;
                }

                $opcoes = [
                    $row['ALTA'] ?? '',
                    $row['ALTB'] ?? '',
                    $row['ALTC'] ?? '',
                    $row['ALTD'] ?? '',
                ];

                $letraCorreta = strtoupper($row['RESP'] ?? 'A');
                $indiceCorreto = match ($letraCorreta) {
                    'A' => 0,
                    'B' => 1,
                    'C' => 2,
                    'D' => 3,
                    default => 0,
                };
                $respostaCorreta = $opcoes[$indiceCorreto] ?? $opcoes[0];

                $nivelNum = max(1, min(5, (int) ($row['NIVEL'] ?? 1)));
                $dificuldadeId = $niveisMap[$nivelNum] ?? ($niveisMap[1] ?? null);

                $comentarioRaw = $row['COMENTARIO'] ?? '';
                $comentarioLimpo = ($comentarioRaw === '.' || $comentarioRaw === '') ? null : $comentarioRaw;

                [$livroId, $capituloNumero, $versDe, $versAte, $refBiblica] = $this->extrairReferencia(
                    $comentarioLimpo,
                    $pergunta,
                    $livrosMap,
                    $livroPadraoId
                );

                $capituloId = null;
                if ($capituloNumero !== null && $capituloNumero > 0) {
                    $capKey = $livroId . ':' . $capituloNumero;
                    if (!isset($capitulosCache[$capKey])) {
                        $cap = Capitulo::firstOrCreate([
                            'livro_id' => $livroId,
                            'numero' => $capituloNumero,
                        ]);
                        $capitulosCache[$capKey] = $cap->id;
                    }
                    $capituloId = $capitulosCache[$capKey];
                }

                $categoriaId = $this->classificarCategoria($pergunta, $refBiblica, $categoriasMap);

                $ajudaComentario = $row['CAJUDA'] ?? '';
                if ($ajudaComentario === '.' || $ajudaComentario === '') {
                    $ajudaComentario = null;
                }

                $ajudas = [
                    'convidados' => array_values(array_filter([
                        $row['AJUDA1'] ?? null,
                        $row['AJUDA2'] ?? null,
                        $row['AJUDA3'] ?? null,
                    ], fn ($v) => $v !== null && $v !== '' && $v !== '.')),
                    'errado' => (int) ($row['AJERRADA'] ?? 1),
                    'comentario_ajuda' => $ajudaComentario,
                ];

                $explicacao = $comentarioLimpo;
                if ($ajudaComentario && $explicacao) {
                    $explicacao .= ' — Dica histórica: ' . $ajudaComentario;
                } elseif ($ajudaComentario) {
                    $explicacao = $ajudaComentario;
                }

                $lote[] = [
                    'codigo_classico' => $codigo,
                    'igreja_id' => $igreja->id,
                    'livro_id' => $livroId,
                    'capitulo_id' => $capituloId,
                    'versiculo_de' => $versDe,
                    'versiculo_ate' => $versAte,
                    'texto' => $pergunta,
                    'opcoes' => json_encode($opcoes, JSON_UNESCAPED_UNICODE),
                    'resposta_correta' => $respostaCorreta,
                    'explicacao' => $explicacao,
                    'contexto' => $refBiblica ? "Referência bíblica clássica: {$refBiblica}" : ' Acervo Clássico Bíblia Show',
                    'referencia_biblica' => $refBiblica,
                    'ajudas' => json_encode($ajudas, JSON_UNESCAPED_UNICODE),
                    'dificuldade_id' => $dificuldadeId,
                    'categoria_id' => $categoriaId,
                    'referencia_verificada' => $refBiblica !== null,
                    'aprovada' => true,
                    'submetida_por' => $usuario->id,
                    'revisada_por' => $usuario->id,
                    'revisada_em' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $totalImportadas++;

                if (count($lote) >= 200) {
                    DB::table('questoes')->insert($lote);
                    $lote = [];
                }
            }

            if (!empty($lote)) {
                DB::table('questoes')->insert($lote);
            }
        });

        fclose($handle);

        $this->command?->info("Importadas {$totalImportadas} perguntas originais do clássico Bíblia Show!");
    }

    private function construirMapaDeLivros(): array
    {
        $livros = LivroBiblico::all();
        $mapa = [];

        foreach ($livros as $livro) {
            $chave = $this->normalizarNomeLivro($livro->nome);
            $mapa[$chave] = $livro->id;
            $mapa[$this->normalizarNomeLivro($livro->abreviacao)] = $livro->id;
        }

        // Sinônimos usados no clássico cadastro.dbf
        $sinonimos = [
            'i samuel' => '1 samuel',
            'ii samuel' => '2 samuel',
            'i sm' => '1 samuel',
            'ii sm' => '2 samuel',
            'i reis' => '1 reis',
            'ii reis' => '2 reis',
            'i cronicas' => '1 cronicas',
            'ii cronicas' => '2 cronicas',
            'i corintios' => '1 corintios',
            'ii corintios' => '2 corintios',
            'corintios' => '1 corintios',
            'i tessalonicenses' => '1 tessalonicenses',
            'ii tessalonicenses' => '2 tessalonicenses',
            'i timoteo' => '1 timoteo',
            'ii timoteo' => '2 timoteo',
            'i pedro' => '1 pedro',
            'ii pedro' => '2 pedro',
            'i joao' => '1 joao',
            'ii joao' => '2 joao',
            'iii joao' => '3 joao',
            'sao joao' => 'joao',
            'em sao joao' => 'joao',
            'em genesis' => 'genesis',
            'salmo' => 'salmos',
            'salmoss' => 'salmos',
            'filemon' => 'filemom',
            'cantico dos canticos' => 'cantares',
        ];

        foreach ($sinonimos as $alias => $alvo) {
            if (isset($mapa[$alvo])) {
                $mapa[$alias] = $mapa[$alvo];
            }
        }

        return $mapa;
    }

    private function normalizarNomeLivro(string $nome): string
    {
        $nome = mb_strtolower(trim($nome), 'UTF-8');
        $nome = strtr($nome, [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a',
            'é' => 'e', 'ê' => 'e',
            'í' => 'i',
            'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ú' => 'u', 'ü' => 'u',
            'ç' => 'c',
            ':' => '', '.' => '', ',' => '',
        ]);

        return preg_replace('/\s+/', ' ', trim($nome));
    }

    private function extrairReferencia(?string $comentario, string $pergunta, array $livrosMap, int $livroPadraoId): array
    {
        $livroId = $livroPadraoId;
        $capitulo = null;
        $versDe = null;
        $versAte = null;
        $refBiblica = null;

        if ($comentario && $comentario !== '.' && $comentario !== 'x') {
            // Normalizar prefixos como "Ler,", "Ler:", "Leia", "Lei", e juntar letras com números colados (ex: "I Reis10:1")
            $limpo = preg_replace('/^(?:Ler|Leia|Lei)\s*[,.:]?\s*/iu', '', trim($comentario));
            $limpo = preg_replace('/([a-zA-Záàâãéêíóôõúç])(\d+)/u', '$1 $2', $limpo);

            if (preg_match('/^([a-zA-Z0-9\sáàâãéêíóôõúç]+?)\s*[,.:]?\s*(\d+)(?:\s*[:.,]\s*(\d+)(?:\s*[-a]\s*(\d+))?)?/iu', $limpo, $m)) {
                $nomeLivroRaw = trim($m[1]);
                $chave = $this->normalizarNomeLivro($nomeLivroRaw);
                if (isset($livrosMap[$chave])) {
                    $livroId = $livrosMap[$chave];
                }
                $capitulo = isset($m[2]) ? (int) $m[2] : null;
                $versDe = isset($m[3]) && $m[3] !== '' ? (int) $m[3] : null;
                $versAte = isset($m[4]) && $m[4] !== '' ? (int) $m[4] : $versDe;
                $refBiblica = trim(preg_replace('/^(?:Ler|Leia|Lei)\s*[,.:]?\s*/iu', '', $comentario));
            } elseif (preg_match('/^(?:Ler|Leia|Lei)?\s*([a-zA-Z0-9\sáàâãéêíóôõúç]+)$/iu', $comentario, $m)) {
                // Caso em que só há o nome do livro (ex: "Ler Filemon", "Isaías", "Ler Obadias")
                $chave = $this->normalizarNomeLivro($m[1]);
                if (isset($livrosMap[$chave])) {
                    $livroId = $livrosMap[$chave];
                    $refBiblica = trim(preg_replace('/^(?:Ler|Leia|Lei)\s*[,.:]?\s*/iu', '', $comentario));
                }
            }
        }

        if ($refBiblica === null) {
            // Tentar identificar livro citado no texto da pergunta
            $perguntaNorm = $this->normalizarNomeLivro($pergunta);
            foreach ($livrosMap as $nomeNorm => $id) {
                if (strlen($nomeNorm) >= 4 && str_contains($perguntaNorm, $nomeNorm)) {
                    $livroId = $id;
                    break;
                }
            }
        }

        return [$livroId, $capitulo, $versDe, $versAte, $refBiblica];
    }

    private function classificarCategoria(string $pergunta, ?string $refBiblica, array $categoriasMap): ?int
    {
        $p = mb_strtolower($pergunta . ' ' . ($refBiblica ?? ''), 'UTF-8');

        if (str_contains($p, 'jesus') || str_contains($p, 'mateus') || str_contains($p, 'marcos') || str_contains($p, 'lucas') || str_contains($p, 'são joão')) {
            return $categoriasMap['vida-jesus'] ?? null;
        }
        if (str_contains($p, 'atos') || str_contains($p, 'paulo') || str_contains($p, 'coríntios') || str_contains($p, 'romanos') || str_contains($p, 'efésios')) {
            return $categoriasMap['igreja-primitiva'] ?? null;
        }
        if (str_contains($p, 'apocalipse') || str_contains($p, 'daniel') || str_contains($p, 'profeta') || str_contains($p, 'visão') || str_contains($p, 'isaías')) {
            return $categoriasMap['profecias'] ?? null;
        }
        if (str_contains($p, 'salmo') || str_contains($p, 'provérbios') || str_contains($p, 'eclesiastes') || str_contains($p, 'cantares')) {
            return $categoriasMap['salmos-poesia'] ?? null;
        }
        if (str_contains($p, 'quem ') || str_contains($p, 'qual o nome') || str_contains($p, 'qual é o nome')) {
            return $categoriasMap['personagens'] ?? null;
        }
        if (str_contains($p, 'significa') || str_contains($p, 'lei') || str_contains($p, 'dízimo')) {
            return $categoriasMap['teologia'] ?? null;
        }

        return $categoriasMap['historia'] ?? null;
    }
}

