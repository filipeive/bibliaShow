<?php

/**
 * Auditoria independente do acervo clássico (database/data/cadastro.dbf).
 *
 * Lê o ficheiro DBF original sem depender do Laravel nem do seeder, e produz
 * um relatório JSON com estatísticas de integridade. Não altera nenhum dado.
 *
 * Uso: php database/tools/auditar_acervo.php [caminho.dbf] > relatorio.json
 */

$dbfPath = $argv[1] ?? __DIR__.'/../data/cadastro.dbf';
$h = fopen($dbfPath, 'rb');
if (! $h) {
    fwrite(STDERR, "Não foi possível abrir {$dbfPath}\n");
    exit(1);
}

$header = fread($h, 32);
$meta = unpack('Cver/Cy/Cm/Cd/VnumRecords/vheaderLen/vrecordLen', $header);
$fields = [];
while (true) {
    $fb = fread($h, 32);
    if ($fb === false || strlen($fb) < 32 || ord($fb[0]) === 0x0D) {
        break;
    }
    $fields[] = [
        'name' => rtrim(substr($fb, 0, 11), "\0"),
        'type' => $fb[11],
        'len' => ord($fb[16]),
    ];
}
fseek($h, $meta['headerLen']);

$registos = [];
$apagados = 0;
$bytesSuspeitos = 0;
for ($i = 0; $i < $meta['numRecords']; $i++) {
    $rec = fread($h, $meta['recordLen']);
    if ($rec === false || strlen($rec) < $meta['recordLen']) {
        break;
    }
    $flag = $rec[0];
    $pos = 1;
    $row = ['_indice' => $i, '_apagado' => ord($flag) === 0x2A];
    foreach ($fields as $f) {
        $raw = substr($rec, $pos, $f['len']);
        $pos += $f['len'];
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', rtrim($raw, "\0"))) {
            $bytesSuspeitos++;
        }
        $row[$f['name']] = trim(mb_convert_encoding(rtrim($raw, "\0"), 'UTF-8', 'Windows-1252'));
    }
    if ($row['_apagado']) {
        $apagados++;
    }
    $registos[] = $row;
}
fclose($h);

$norm = function (string $s): string {
    $s = mb_strtolower(trim($s), 'UTF-8');
    $s = strtr($s, ['á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ú' => 'u', 'ü' => 'u', 'ç' => 'c']);
    $s = preg_replace('/[^a-z0-9 ]/', ' ', $s);

    return preg_replace('/\s+/', ' ', trim($s));
};

$ativos = array_values(array_filter($registos, fn ($r) => ! $r['_apagado']));
$porCodigo = [];
$porTexto = [];
$problemas = [
    'pergunta_vazia' => [],
    'alternativa_vazia' => [],
    'resp_invalida' => [],
    'alternativas_duplicadas' => [],
    'sem_referencia' => [],
    'referencia_nao_padrao' => [],
    'nivel_fora_1_4' => [],
    'codigo_duplicado' => [],
    'texto_duplicado' => [],
    'mojibake' => [],
    'ajerrada_invalida' => [],
    'convidados_incompletos' => [],
];
$niveis = [];

foreach ($ativos as $r) {
    $cod = (int) ($r['CODIGO'] ?? 0);
    $porCodigo[$cod][] = $r['_indice'];
    $t = $norm($r['PERGUNTA'] ?? '');
    if ($t !== '') {
        $porTexto[$t][] = $cod;
    }
    $nivel = (int) ($r['NIVEL'] ?? 0);
    $niveis[$nivel] = ($niveis[$nivel] ?? 0) + 1;

    if (($r['PERGUNTA'] ?? '') === '') {
        $problemas['pergunta_vazia'][] = $cod;
    }
    $alts = [$r['ALTA'] ?? '', $r['ALTB'] ?? '', $r['ALTC'] ?? '', $r['ALTD'] ?? ''];
    foreach ($alts as $a) {
        if ($a === '' || $a === '.') {
            $problemas['alternativa_vazia'][] = $cod;
            break;
        }
    }
    $altsNorm = array_map($norm, $alts);
    if (count(array_unique($altsNorm)) < 4) {
        $problemas['alternativas_duplicadas'][] = $cod;
    }
    $resp = strtoupper($r['RESP'] ?? '');
    if (! in_array($resp, ['A', 'B', 'C', 'D'], true)) {
        $problemas['resp_invalida'][] = ['codigo' => $cod, 'resp' => $r['RESP'] ?? null];
    }
    $com = $r['COMENTARIO'] ?? '';
    if ($com === '' || $com === '.') {
        $problemas['sem_referencia'][] = $cod;
    } elseif (! preg_match('/^Ler\s+(.+?)\s+(\d+)(?:\s*[:.,]\s*(\d+)(?:\s*[-a]\s*(\d+))?)?/iu', $com)) {
        $problemas['referencia_nao_padrao'][] = ['codigo' => $cod, 'comentario' => $com];
    }
    if ($nivel < 1 || $nivel > 4) {
        $problemas['nivel_fora_1_4'][] = ['codigo' => $cod, 'nivel' => $r['NIVEL'] ?? null];
    }
    foreach ($r as $k => $v) {
        if (is_string($v) && preg_match('/Ã.|Â.|�/u', $v)) {
            $problemas['mojibake'][] = ['codigo' => $cod, 'campo' => $k];
            break;
        }
    }
    $aj = (int) ($r['AJERRADA'] ?? 0);
    if ($aj < 1 || $aj > 3) {
        $problemas['ajerrada_invalida'][] = ['codigo' => $cod, 'ajerrada' => $r['AJERRADA'] ?? null];
    }
    $conv = array_filter([$r['AJUDA1'] ?? '', $r['AJUDA2'] ?? '', $r['AJUDA3'] ?? ''], fn ($v) => $v !== '' && $v !== '.');
    if (count($conv) < 3) {
        $problemas['convidados_incompletos'][] = $cod;
    }
}
foreach ($porCodigo as $cod => $idx) {
    if (count($idx) > 1) {
        $problemas['codigo_duplicado'][] = ['codigo' => $cod, 'indices' => $idx];
    }
}
foreach ($porTexto as $t => $cods) {
    if (count($cods) > 1) {
        $problemas['texto_duplicado'][] = ['codigos' => $cods, 'texto' => $t];
    }
}
ksort($niveis);

$resumo = [];
foreach ($problemas as $k => $v) {
    $resumo[$k] = count($v);
}

echo json_encode([
    'ficheiro' => realpath($dbfPath),
    'sha256' => hash_file('sha256', $dbfPath),
    'versao_dbf' => $meta['ver'],
    'campos' => $fields,
    'registos_no_cabecalho' => $meta['numRecords'],
    'registos_lidos' => count($registos),
    'registos_apagados' => $apagados,
    'registos_ativos' => count($ativos),
    'campos_com_bytes_de_controlo' => $bytesSuspeitos,
    'distribuicao_niveis' => $niveis,
    'resumo_problemas' => $resumo,
    'problemas' => $problemas,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n";
