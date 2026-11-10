<?php

namespace Tests\Unit\GameEngine;

use App\Game\Engine\GameEngine;
use App\Game\Engine\Selectors\RandomSelector;
use App\Game\Engine\Scoring\StandardScoring;
use App\Game\Engine\Timers\CountdownTimer;
use App\Game\Engine\State\GameState;
use PHPUnit\Framework\TestCase;

class GameEngineTest extends TestCase
{
    public function test_instancia_engine_com_sucesso(): void
    {
        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );

        $this->assertInstanceOf(GameEngine::class, $engine);
    }

    public function test_inicia_partida_e_retorna_estado_em_curso(): void
    {
        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );

        $perguntas = [
            ['id' => 1, 'texto' => 'Q1', 'resposta_correta' => 'A', 'explicacao' => 'Exp1'],
            ['id' => 2, 'texto' => 'Q2', 'resposta_correta' => 'B', 'explicacao' => 'Exp2'],
        ];

        $engine->start($perguntas, [['id' => 1, 'nome' => 'João']]);

        $estado = $engine->getState();

        $this->assertEquals('em_curso', $estado['estado']);
        $this->assertContains($estado['pergunta_atual']['id'], [1, 2]);
    }

    public function test_resposta_correta_retorna_acertou_e_pontos(): void
    {
        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );

        $perguntas = [
            ['id' => 1, 'texto' => 'Q1', 'resposta_correta' => 'A', 'explicacao' => 'Exp1'],
        ];

        $engine->start($perguntas, [['id' => 1, 'nome' => 'João']]);

        $resultado = $engine->answerQuestion(1, 1, 'A', 10);

        $this->assertTrue($resultado['acertou']);
        $this->assertGreaterThan(0, $resultado['pontos']);
    }

    public function test_resposta_incorreta_retorna_sem_pontos(): void
    {
        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );

        $perguntas = [
            ['id' => 1, 'texto' => 'Q1', 'resposta_correta' => 'A', 'explicacao' => 'Exp1'],
        ];

        $engine->start($perguntas, [['id' => 1, 'nome' => 'João']]);

        $resultado = $engine->answerQuestion(1, 1, 'B', 10);

        $this->assertFalse($resultado['acertou']);
        $this->assertEquals(0, $resultado['pontos']);
    }

    public function test_selecao_nao_repete_perguntas(): void
    {
        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );

        $perguntas = [
            ['id' => 1, 'texto' => 'Q1', 'resposta_correta' => 'A'],
            ['id' => 2, 'texto' => 'Q2', 'resposta_correta' => 'B'],
            ['id' => 3, 'texto' => 'Q3', 'resposta_correta' => 'C'],
        ];

        $engine->start($perguntas, []);

        $ids = [];
        while ($engine->getState()['estado'] === 'em_curso') {
            $atual = $engine->getState()['pergunta_atual'];
            if ($atual) {
                $ids[] = $atual['id'];
            }
            $engine->answerQuestion(1, $atual['id'] ?? 1, 'A', 5);
        }

        $this->assertCount(count(array_unique($ids)), $ids);
    }

    public function test_ajuda_cartas_elimina_duas_opcoes_incorretas(): void
    {
        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );

        $perguntas = [[
            'id' => 1,
            'texto' => 'Quem construiu a arca?',
            'opcoes' => ['Adão', 'Noé', 'Moisés', 'Abraão'],
            'resposta_correta' => 'Noé',
        ]];

        $engine->start($perguntas, []);
        $res = $engine->usarCartas();

        $this->assertTrue($res['sucesso']);
        $this->assertCount(2, $res['opcoes_eliminadas']);
        $this->assertNotContains(1, $res['opcoes_eliminadas']); // Índice 1 é 'Noé'
    }

    public function test_ajuda_convidados_biblicos_respeita_convidado_errado(): void
    {
        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );

        $perguntas = [[
            'id' => 2,
            'texto' => 'Quem contemplou Jesus à direita de Deus?',
            'opcoes' => ['Pedro', 'Tiago', 'Estevão', 'Paulo'],
            'resposta_correta' => 'Estevão',
            'ajudas' => [
                'convidados' => ['Tito', 'Abraão', 'Silas'],
                'errado' => 2,
                'comentario_ajuda' => 'Abraão não tinha conhecimento dos fatos Pós-Cristo.',
            ],
        ]];

        $engine->start($perguntas, []);
        $res = $engine->consultarConvidado(0); // Índice 0 = Tito (sabe a resposta)

        $this->assertTrue($res['sucesso']);
        $this->assertTrue($res['dica']['sabe_resposta']);
        $this->assertEquals('Estevão', $res['dica']['resposta_sugerida']);
    }

    public function test_usar_pulo_substitui_pergunta_e_decrementa_pulos(): void
    {
        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );

        $perguntas = [
            ['id' => 1, 'texto' => 'Q1', 'resposta_correta' => 'A'],
        ];
        $reserva = ['id' => 99, 'texto' => 'Reserva', 'resposta_correta' => 'B'];

        $engine->start($perguntas, []);
        $res = $engine->usarPulo($reserva);

        $this->assertTrue($res['sucesso']);
        $this->assertEquals(2, $res['pulos_restantes']);
        $this->assertEquals(99, $engine->getState()['pergunta_atual']['id']);
    }

    public function test_usar_dica_biblica_retorna_referencia_e_decrementa_dicas(): void
    {
        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );

        $perguntas = [[
            'id' => 1,
            'texto' => 'No princípio criou Deus...',
            'resposta_correta' => 'Os céus e a terra',
            'referencia_biblica' => 'Gênesis 1:1',
        ]];

        $engine->start($perguntas, []);
        $res = $engine->usarDica();

        $this->assertTrue($res['sucesso']);
        $this->assertStringContainsString('Gênesis 1:1', $res['dica_biblica']);
        $this->assertEquals(2, $res['dicas_restantes']);
    }

    public function test_tres_erros_sinalizam_reinicio_de_partida(): void
    {
        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );

        $perguntas = [
            ['id' => 1, 'texto' => 'Q1', 'resposta_correta' => 'A'],
            ['id' => 2, 'texto' => 'Q2', 'resposta_correta' => 'A'],
            ['id' => 3, 'texto' => 'Q3', 'resposta_correta' => 'A'],
            ['id' => 4, 'texto' => 'Q4', 'resposta_correta' => 'A'],
        ];

        $engine->start($perguntas, []);

        $r1 = $engine->answerQuestion(1, 1, 'Errada', 5);
        $this->assertFalse($r1['reiniciar_por_erros']);
        $this->assertEquals(1, $r1['erros_count']);

        $r2 = $engine->answerQuestion(1, 2, 'Errada', 5);
        $this->assertFalse($r2['reiniciar_por_erros']);
        $this->assertEquals(2, $r2['erros_count']);

        $r3 = $engine->answerQuestion(1, 3, 'Errada', 5);
        $this->assertTrue($r3['reiniciar_por_erros']);
        $this->assertEquals(3, $r3['erros_count']);
    }

    public function test_tempo_esgotado_passa_proxima_pergunta_e_tres_tempos_esgotados_reiniciam(): void
    {
        $engine = new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );

        $perguntas = [
            ['id' => 10, 'texto' => 'Q1', 'resposta_correta' => 'A'],
            ['id' => 20, 'texto' => 'Q2', 'resposta_correta' => 'A'],
            ['id' => 30, 'texto' => 'Q3', 'resposta_correta' => 'A'],
            ['id' => 40, 'texto' => 'Q4', 'resposta_correta' => 'A'],
        ];

        $engine->start($perguntas, []);

        $t1 = $engine->registrarTempoEsgotado(1);
        $this->assertTrue($t1['tempo_esgotado']);
        $this->assertFalse($t1['reiniciar_por_tempo']);
        $this->assertEquals(1, $t1['tempos_esgotados_count']);
        $this->assertEquals(2, $engine->getState()['numero_pergunta']);

        $t2 = $engine->registrarTempoEsgotado(1);
        $this->assertFalse($t2['reiniciar_por_tempo']);
        $this->assertEquals(2, $t2['tempos_esgotados_count']);
        $this->assertEquals(3, $engine->getState()['numero_pergunta']);

        $t3 = $engine->registrarTempoEsgotado(1);
        $this->assertTrue($t3['reiniciar_por_tempo']);
        $this->assertEquals(3, $t3['tempos_esgotados_count']);
    }
}
