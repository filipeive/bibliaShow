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
        $this->assertEquals(1, $estado['pergunta_atual']['id']);
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
}
