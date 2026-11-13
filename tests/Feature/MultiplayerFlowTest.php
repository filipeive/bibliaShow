<?php

namespace Tests\Feature;

use App\Models\Equipe;
use App\Models\Igreja;
use App\Models\LivroBiblico;
use App\Models\NivelDificuldade;
use App\Models\Participante;
use App\Models\Questao;
use App\Models\Sala;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiplayerFlowTest extends TestCase
{
    use RefreshDatabase;

    private function criarCenarioBase(): array
    {
        $igreja = Igreja::create(['nome' => 'Igreja Central', 'slug' => 'central']);
        $apresentador = Usuario::create([
            'igreja_id' => $igreja->id,
            'nome' => 'Apresentador Oficial',
            'email' => 'apresentador@igreja.com',
            'password' => bcrypt('password'),
            'papel' => 'apresentador',
        ]);

        $livro = LivroBiblico::create([
            'codigo_osis' => 'Gen',
            'abreviacao' => 'Gn',
            'nome' => 'Gênesis',
            'testamento' => 'Antigo',
        ]);

        $nivel1 = NivelDificuldade::create(['nome' => '1ª Fase', 'nivel' => 1]);

        $questao = Questao::create([
            'igreja_id' => $igreja->id,
            'livro_id' => $livro->id,
            'dificuldade_id' => $nivel1->id,
            'texto' => 'Quem criou os céus e a terra?',
            'opcoes' => ['Deus', 'Moisés', 'Abraão', 'Paulo'],
            'resposta_correta' => 'Deus',
            'referencia_biblica' => 'Gênesis 1:1',
            'aprovada' => true,
        ]);

        $sala = Sala::create([
            'igreja_id' => $igreja->id,
            'codigo_acesso' => 'TEST01',
            'nome' => 'Sala da Juventude',
            'apresentador_id' => $apresentador->id,
            'status' => 'em_curso',
        ]);

        return compact('igreja', 'apresentador', 'sala', 'questao');
    }

    public function test_participante_entra_na_sala_e_escolhe_equipe(): void
    {
        $cenario = $this->criarCenarioBase();
        $sala = $cenario['sala'];

        $resp = $this->postJson('/api/salas/entrar', [
            'codigo_acesso' => 'TEST01',
            'nome' => 'Marcos Lucas',
            'equipe_nome' => 'Leões de Judá',
        ]);

        $resp->assertStatus(200);
        $resp->assertJsonPath('participante.nome', 'Marcos Lucas');
        $resp->assertJsonPath('participante.equipe_nome', 'Leões de Judá');

        $this->assertDatabaseHas('participantes', [
            'sala_id' => $sala->id,
            'nome' => 'Marcos Lucas',
        ]);
        $this->assertDatabaseHas('equipes', [
            'sala_id' => $sala->id,
            'nome' => 'Leões de Judá',
        ]);
    }

    public function test_previne_resposta_duplicada_do_mesmo_participante(): void
    {
        $cenario = $this->criarCenarioBase();
        $sala = $cenario['sala'];
        $questao = $cenario['questao'];

        $participante = Participante::create([
            'sala_id' => $sala->id,
            'nome' => 'Débora',
            'codigo_acesso' => 'DEB001',
            'conectado' => true,
            'pontuacao_total' => 0,
        ]);

        // 1ª Resposta
        $r1 = $this->postJson("/api/salas/{$sala->id}/responder", [
            'participante_id' => $participante->id,
            'questao_id' => $questao->id,
            'resposta' => 'Deus',
            'tempo_resposta' => 15,
        ]);
        $r1->assertStatus(200);
        $r1->assertJsonPath('bloqueada', true);
        $this->assertNull($r1->json('resposta_correta'));

        // 2ª Tentativa de Resposta na mesma questão
        $r2 = $this->postJson("/api/salas/{$sala->id}/responder", [
            'participante_id' => $participante->id,
            'questao_id' => $questao->id,
            'resposta' => 'Deus',
            'tempo_resposta' => 20,
        ]);
        $r2->assertStatus(409);
        $r2->assertJsonPath('error', 'Você já respondeu esta pergunta!');
    }

    public function test_participante_pode_usar_ajudas_concedidas_pelas_regras_do_jogo(): void
    {
        $cenario = $this->criarCenarioBase();
        $sala = $cenario['sala'];
        $apresentador = $cenario['apresentador'];
        $questao = $cenario['questao'];

        // Inicia jogo na sala como apresentador
        $this->actingAs($apresentador)->get("/apresentador/control/{$sala->id}");

        $participante = Participante::create([
            'sala_id' => $sala->id,
            'nome' => 'Tiago',
            'codigo_acesso' => 'TIA001',
            'conectado' => true,
            'pontuacao_total' => 0,
        ]);

        // 1. Usa Dica Bíblica
        $rDica = $this->postJson("/api/salas/{$sala->id}/ajuda", [
            'participante_id' => $participante->id,
            'tipo' => 'dica',
        ]);
        $rDica->assertStatus(200);
        $rDica->assertJsonPath('sucesso', true);
        $rDica->assertJsonPath('tipo', 'dica');
        $rDica->assertJsonPath('dicas_restantes', 2);
        $this->assertNotEmpty($rDica->json('dica_biblica'));

        // 2. Usa Cartas 50/50
        $rCartas = $this->postJson("/api/salas/{$sala->id}/ajuda", [
            'participante_id' => $participante->id,
            'tipo' => 'cartas',
        ]);
        $rCartas->assertStatus(200);
        $rCartas->assertJsonPath('sucesso', true);
        $rCartas->assertJsonPath('tipo', 'cartas');
        $this->assertCount(2, $rCartas->json('opcoes_eliminadas'));

        // 3. Usa Convidado Bíblico
        $rConv = $this->postJson("/api/salas/{$sala->id}/ajuda", [
            'participante_id' => $participante->id,
            'tipo' => 'convidado',
            'indice_convidado' => 1,
        ]);
        $rConv->assertStatus(200);
        $rConv->assertJsonPath('sucesso', true);
        $rConv->assertJsonPath('convidados_restantes', 2);
        $this->assertNotEmpty($rConv->json('dica_convidado.mensagem'));
    }

    public function test_rejeita_resposta_quando_tempo_esgotado_no_motor(): void
    {
        $cenario = $this->criarCenarioBase();
        $sala = $cenario['sala'];
        $apresentador = $cenario['apresentador'];
        $questao = $cenario['questao'];

        $this->actingAs($apresentador)->get("/apresentador/control/{$sala->id}");
        $jogo = $sala->jogoAtivo()->first();

        // Simula motor com tempo esgotado (início há mais de 60 segundos)
        $engine = \App\Game\Engine\GameEngine::desserializarSeguro($jogo->dados_motor);
        $stateArray = $engine->toStateArray();
        $stateArray['timer_data']['inicio'] = time() - 100;
        $engine = \App\Game\Engine\GameEngine::fromStateArray($stateArray);
        $jogo->update(['dados_motor' => $engine->toJsonState()]);

        $participante = Participante::create([
            'sala_id' => $sala->id,
            'nome' => 'Priscila',
            'codigo_acesso' => 'PRI001',
            'conectado' => true,
            'pontuacao_total' => 0,
        ]);

        $resp = $this->postJson("/api/salas/{$sala->id}/responder", [
            'participante_id' => $participante->id,
            'questao_id' => $questao->id,
            'resposta' => 'Deus',
            'tempo_resposta' => 61,
        ]);

        $resp->assertStatus(408);
        $resp->assertJsonPath('error', 'Tempo esgotado para esta pergunta! Resposta não computada.');
    }

    public function test_gabarito_somente_e_revelado_ao_participante_quando_apresentador_autoriza(): void
    {
        $cenario = $this->criarCenarioBase();
        $sala = $cenario['sala'];
        $apresentador = $cenario['apresentador'];
        $questao = $cenario['questao'];

        $this->actingAs($apresentador)->get("/apresentador/control/{$sala->id}");

        $participante = Participante::create([
            'sala_id' => $sala->id,
            'nome' => 'Mateus',
            'codigo_acesso' => 'MAT001',
            'conectado' => true,
            'pontuacao_total' => 0,
        ]);

        // Participante bloqueia resposta
        $respResp = $this->postJson("/api/salas/{$sala->id}/responder", [
            'participante_id' => $participante->id,
            'questao_id' => $questao->id,
            'resposta' => 'Deus',
            'tempo_resposta' => 12,
        ]);
        $respResp->assertStatus(200);
        $respResp->assertJsonPath('bloqueada', true);
        $this->assertNull($respResp->json('resposta_correta'));

        // Antes de revelar: rota de estado NÃO vaza o gabarito
        $estadoAntes = $this->getJson("/api/salas/{$sala->id}?participante_id={$participante->id}");
        $estadoAntes->assertStatus(200);
        $this->assertNull($estadoAntes->json('pergunta_atual.resposta_correta'));
        $this->assertFalse($estadoAntes->json('ajudas.gabarito_revelado'));

        // Apresentador clica para revelar gabarito no telão
        $this->actingAs($apresentador)->postJson("/apresentador/control/{$sala->id}/ajuda", [
            'tipo' => 'revelar',
        ])->assertStatus(200);

        // Após revelar: rota de estado agora envia gabarito revelado oficialmente
        $estadoDepois = $this->getJson("/api/salas/{$sala->id}?participante_id={$participante->id}");
        $estadoDepois->assertStatus(200);
        $this->assertTrue($estadoDepois->json('ajudas.gabarito_revelado'));
        $this->assertEquals('Deus', $estadoDepois->json('pergunta_atual.resposta_correta'));
    }

    public function test_fluxo_completo_arena_com_equipa_ranking_e_reconnexao(): void
    {
        $cenario = $this->criarCenarioBase();
        $sala = $cenario['sala'];
        $apresentador = $cenario['apresentador'];
        $questao = $cenario['questao'];

        $equipa = \App\Models\Equipe::create([
            'sala_id' => $sala->id,
            'nome' => 'Judá',
            'cor' => '#F59E0B',
            'pontuacao' => 0,
        ]);

        // 1. Apresentador inicia o controlo da sala
        $this->actingAs($apresentador)->get("/apresentador/control/{$sala->id}")->assertStatus(200);

        // 2. Participante entra por código e escolhe equipa
        $entrar = $this->postJson('/api/salas/entrar', [
            'codigo_acesso' => $sala->codigo_acesso,
            'nome' => 'Rute',
            'equipe_id' => $equipa->id,
        ]);
        $entrar->assertStatus(200);
        $participanteId = $entrar->json('participante.id');
        $codigo = $entrar->json('participante.codigo_acesso');
        $this->assertNotEmpty($codigo);

        // 3. Estado da sala oculta gabarito antes da revelação
        $estadoInicial = $this->getJson("/api/salas/{$sala->id}?participante_id={$participanteId}");
        $estadoInicial->assertStatus(200);
        $this->assertNull($estadoInicial->json('pergunta_atual.resposta_correta'));

        // 4. Participante submete resposta autenticada com X-Participante-Codigo
        $responder = $this->withHeaders([
            'X-Participante-Codigo' => $codigo,
        ])->postJson("/api/salas/{$sala->id}/responder", [
            'participante_id' => $participanteId,
            'questao_id' => $questao->id,
            'resposta' => 'Deus',
            'tempo_resposta' => 8,
        ]);
        $responder->assertStatus(200);
        $responder->assertJsonPath('bloqueada', true);
        $this->assertNull($responder->json('correta'));

        // 5. Apresentador revela o gabarito e o ranking é atualizado
        $this->actingAs($apresentador)->postJson("/apresentador/control/{$sala->id}/ajuda", [
            'tipo' => 'revelar',
        ])->assertStatus(200);

        $estadoPosRevelacao = $this->getJson("/api/salas/{$sala->id}?participante_id={$participanteId}");
        $estadoPosRevelacao->assertStatus(200);
        $this->assertEquals('Deus', $estadoPosRevelacao->json('pergunta_atual.resposta_correta'));
        $ranking = $estadoPosRevelacao->json('ranking');
        $this->assertNotEmpty($ranking);
        $pontuacaoObtida = $ranking[0]['pontuacao'];
        $this->assertGreaterThan(0, $pontuacaoObtida);

        // 6. Reconexão do mesmo participante preserva pontuação acumulada
        $reconectar = $this->postJson('/api/salas/entrar', [
            'codigo_acesso' => $sala->codigo_acesso,
            'nome' => 'Rute',
        ]);
        $reconectar->assertStatus(200);
        $reconectar->assertJsonPath('participante.id', $participanteId);
        $reconectar->assertJsonPath('participante.pontuacao_total', $pontuacaoObtida);
    }
}
