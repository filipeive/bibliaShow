<?php

namespace Tests\Unit;

use Tests\TestCase;

class AcervoBiblicoIntegridadeTest extends TestCase
{
    private string $dbfPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbfPath = database_path('data/cadastro.dbf');
    }

    public function test_ficheiro_dbf_original_existe_e_possui_exatamente_1000_perguntas(): void
    {
        $this->assertFileExists($this->dbfPath);

        $h = fopen($this->dbfPath, 'rb');
        $this->assertIsResource($h);

        $header = fread($h, 32);
        $meta = unpack('Cver/Cy/Cm/Cd/VnumRecords/vheaderLen/vrecordLen', $header);

        $this->assertEquals(1000, $meta['numRecords'], 'O DBF deve conter exatamente 1.000 registos.');

        $fields = [];
        while (true) {
            $fb = fread($h, 32);
            if ($fb === false || ord($fb[0]) === 0x0D) break;
            $fields[] = [
                'name' => rtrim(substr($fb, 0, 11), "\0"),
                'len' => ord($fb[16]),
            ];
        }

        fseek($h, $meta['headerLen']);

        $totalValidos = 0;
        $inconsistenciasGabarito = 0;
        $niveis = [];

        for ($i = 0; $i < $meta['numRecords']; $i++) {
            $rec = fread($h, $meta['recordLen']);
            if ($rec === false || strlen($rec) < $meta['recordLen']) break;

            if (ord($rec[0]) === 0x2A) continue; // Registro deletado

            $pos = 1;
            $row = [];
            foreach ($fields as $f) {
                $raw = substr($rec, $pos, $f['len']);
                $pos += $f['len'];
                $row[$f['name']] = trim(mb_convert_encoding(rtrim($raw, "\0"), 'UTF-8', 'Windows-1252'));
            }

            $totalValidos++;
            $nivel = (int) ($row['NIVEL'] ?? 0);
            $niveis[$nivel] = ($niveis[$nivel] ?? 0) + 1;

            $letra = strtoupper($row['RESP'] ?? '');
            $alts = ['A' => $row['ALTA'] ?? '', 'B' => $row['ALTB'] ?? '', 'C' => $row['ALTC'] ?? '', 'D' => $row['ALTD'] ?? ''];

            if (!isset($alts[$letra]) || $alts[$letra] === '' || $alts[$letra] === '.') {
                $inconsistenciasGabarito++;
            }
        }
        fclose($h);

        $this->assertEquals(1000, $totalValidos, 'Todos os 1.000 registos devem estar ativos (nenhum deletado).');
        $this->assertEquals(0, $inconsistenciasGabarito, 'Nenhuma pergunta pode ter alternativa correta vazia.');

        // Distribuição exata das 4 fases clássicas
        $this->assertEquals(334, $niveis[1] ?? 0, 'Fase 1 (Fácil): 334 perguntas');
        $this->assertEquals(241, $niveis[2] ?? 0, 'Fase 2 (Médio): 241 perguntas');
        $this->assertEquals(224, $niveis[3] ?? 0, 'Fase 3 (Difícil): 224 perguntas');
        $this->assertEquals(201, $niveis[4] ?? 0, 'Fase 4 (Desafio Final): 201 perguntas');
    }
}
