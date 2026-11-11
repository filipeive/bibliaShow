<?php

namespace Tests\Unit;

use App\Game\Engine\GameEngine;
use App\Game\Engine\Scoring\StandardScoring;
use App\Game\Engine\Selectors\RandomSelector;
use App\Game\Engine\State\GameState;
use App\Game\Engine\Timers\CountdownTimer;
use PHPUnit\Framework\TestCase;

class GameEngineJsonSerializationTest extends TestCase
{
    private function criarEngine(): GameEngine
    {
        return new GameEngine(
            new RandomSelector(),
            new StandardScoring(),
            new CountdownTimer(),
            new GameState()
        );
    }

    public function test_serializacao_e_desserializacao_em_json_versionado_preserva_estado_completo(): void
    {
        $engine = $this->criarEngine();
        $perguntas = [
            [
                'id' => 10,
                'texto' => 'Quem construiu a arca?',
                'opcoes' => ['Moisés', 'Noé', 'Abraão', 'Davi'],
                'resposta_correta' => 'Noé',
                'explicacao' => 'Gênesis 6',
                'referencia_biblica' => 'Gênesis 6:14',
            ],
            [
                'id' => 20,
                'texto' => 'Onde Jesus nasceu?',
                'opcoes' => ['Nazaré', 'Belém', 'Jerusalém', 'Jericó'],
                'resposta_correta' => 'Belém',
                'explicacao' => 'Miqueias 5:2',
                'referencia_biblica' => 'Lucas 2:4',
            ],
        ];

        $engine->start($perguntas, [['id' => 1, 'nome' => 'Filipe']]);
        $engine->usarDica();
        $engine->usarCartas();

        $json = $engine->toJsonState();
        $this->assertJson($json);

        $decoded = json_decode($json, true);
        $this->assertEquals(1, $decoded['version']);

        $restaurado = GameEngine::desserializarSeguro($json);
        $this->assertInstanceOf(GameEngine::class, $restaurado);

        $sOriginal = $engine->getState();
        $sRestaurado = $restaurado->getState();

        $this->assertEquals($sOriginal['estado'], $sRestaurado['estado']);
        $this->assertEquals($sOriginal['numero_pergunta'], $sRestaurado['numero_pergunta']);
        $this->assertEquals($sOriginal['total_perguntas'], $sRestaurado['total_perguntas']);
        $this->assertEquals($sOriginal['fase_atual'], $sRestaurado['fase_atual']);
        $this->assertEquals($sOriginal['ajudas']['cartas_usada'], $sRestaurado['ajudas']['cartas_usada']);
        $this->assertEquals($sOriginal['ajudas']['dica_biblica'], $sRestaurado['ajudas']['dica_biblica']);
        $this->assertEquals($sOriginal['ajudas']['opcoes_eliminadas'], $sRestaurado['ajudas']['opcoes_eliminadas']);
    }

    public function test_desserializacao_bloqueia_classes_arbitrarias_maliciosas(): void
    {
        $payloadMalicioso = 'O:8:"stdClass":1:{s:4:"test";s:4:"fail";}';
        $resultado = GameEngine::desserializarSeguro($payloadMalicioso);
        $this->assertNull($resultado, 'Payload arbitrário deve ser estritamente bloqueado');
    }
}
