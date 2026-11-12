<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Igreja;
use App\Models\LivroBiblico;
use App\Models\NivelDificuldade;
use App\Models\Questao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlutterApiAndCartasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $igreja = Igreja::firstOrCreate(
            ['slug' => 'central'],
            ['nome' => 'Igreja Central', 'ativa' => true]
        );

        $livro = LivroBiblico::firstOrCreate(
            ['abreviacao' => 'Mt'],
            ['nome' => 'Mateus', 'testamento' => 'Novo', 'capitulos' => 28, 'codigo_osis' => 'Matt']
        );

        $categoria = Categoria::firstOrCreate(
            ['slug' => 'geral'],
            ['nome' => 'Geral', 'descricao' => 'Conhecimentos Gerais']
        );

        for ($nivel = 1; $nivel <= 4; $nivel++) {
            $dif = NivelDificuldade::firstOrCreate(
                ['nivel' => $nivel],
                ['nome' => "Fase {$nivel}"]
            );

            for ($k = 1; $k <= 6; $k++) {
                Questao::firstOrCreate(
                    ['texto' => "Pergunta Teste Nivel {$nivel} Q{$k}"],
                    [
                        'igreja_id' => $igreja->id,
                        'livro_id' => $livro->id,
                        'categoria_id' => $categoria->id,
                        'dificuldade_id' => $dif->id,
                        'opcoes' => ['Opção A', 'Opção B', 'Opção C', 'Opção D'],
                        'resposta_correta' => 'Opção A',
                        'aprovada' => true,
                        'referencia_verificada' => true,
                    ]
                );
            }
        }
    }

    public function test_flutter_api_meta_retorna_sucesso(): void
    {
        $response = $this->getJson('/api/v1/meta');
        $response->assertStatus(200)
            ->assertJsonPath('sucesso', true)
            ->assertJsonStructure([
                'sucesso',
                'app_nome',
                'versao_api',
                'estrutura_jogo',
                'categorias',
                'niveis',
                'livros',
            ]);
    }

    public function test_flutter_api_fluxo_completo_partida(): void
    {
        // 1. Iniciar Partida
        $respStart = $this->postJson('/api/v1/game/start', [
            'jogador_nome' => 'Filipe Teste',
        ]);

        $respStart->assertStatus(200)
            ->assertJsonPath('sucesso', true);

        $token = $respStart->json('session_token');
        $this->assertNotEmpty($token);

        // 2. Usar Cartas (Eliminar 2 alternativas)
        $respCartas = $this->postJson('/api/v1/game/lifeline', [
            'session_token' => $token,
            'tipo' => 'cartas',
        ]);

        $respCartas->assertStatus(200)
            ->assertJsonPath('sucesso', true)
            ->assertJsonPath('tipo', 'cartas');

        $eliminadas = $respCartas->json('dados.opcoes_eliminadas');
        $this->assertIsArray($eliminadas);
        $this->assertCount(2, $eliminadas);

        // 3. Responder Pergunta
        $respAnswer = $this->postJson('/api/v1/game/answer', [
            'session_token' => $token,
            'resposta' => 'Opção A',
            'tempo_resposta' => 10,
        ]);

        $respAnswer->assertStatus(200)
            ->assertJsonPath('sucesso', true)
            ->assertJsonPath('acertou', true);

        // 4. Ranking
        $respRanking = $this->getJson('/api/v1/ranking');
        $respRanking->assertStatus(200)
            ->assertJsonPath('sucesso', true);
    }

    public function test_ajuda_cartas_modo_solo_json_nunca_estoura_500(): void
    {
        // Inicia jogo na sessão web
        $this->get('/jogar?novo=1');

        $response = $this->postJson('/jogar/ajuda', [
            'tipo' => 'cartas',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('sucesso', true)
            ->assertJsonPath('tipo', 'cartas');

        $this->assertIsArray($response->json('opcoes_eliminadas'));
    }
}
