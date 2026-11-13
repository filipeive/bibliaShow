<?php

namespace Tests\Feature;

use App\Models\Igreja;
use App\Models\Questao;
use App\Models\Sala;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndMultiTenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_rotas_administrativas_bloqueiam_usuarios_nao_autenticados(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');

        $responsePost = $this->post('/admin/igrejas', ['nome' => 'Igreja Teste']);
        $responsePost->assertRedirect('/login');
    }

    public function test_rotas_apresentador_bloqueiam_usuarios_sem_papel_autorizado(): void
    {
        $igreja = Igreja::create(['nome' => 'Igreja A', 'slug' => 'igreja-a']);
        $usuarioEditor = Usuario::create([
            'igreja_id' => $igreja->id,
            'nome' => 'Editor',
            'email' => 'editor@igreja.com',
            'password' => bcrypt('password'),
            'papel' => 'editor',
        ]);

        // Editor não pode aceder ao painel do apresentador
        $response = $this->actingAs($usuarioEditor)->get('/apresentador');
        $response->assertStatus(403);
    }

    public function test_isolamento_multi_igreja_apresentador_nao_pode_gerir_sala_de_outra_igreja(): void
    {
        $igrejaA = Igreja::create(['nome' => 'Igreja Alpha', 'slug' => 'igreja-alpha']);
        $igrejaB = Igreja::create(['nome' => 'Igreja Beta', 'slug' => 'igreja-beta']);

        $apresentadorA = Usuario::create([
            'igreja_id' => $igrejaA->id,
            'nome' => 'Apresentador A',
            'email' => 'aprA@igreja.com',
            'password' => bcrypt('password'),
            'papel' => 'apresentador',
        ]);

        $apresentadorB = Usuario::create([
            'igreja_id' => $igrejaB->id,
            'nome' => 'Apresentador B',
            'email' => 'aprB@igreja.com',
            'password' => bcrypt('password'),
            'papel' => 'apresentador',
        ]);

        $salaB = Sala::create([
            'igreja_id' => $igrejaB->id,
            'codigo_acesso' => 'SALAB1',
            'nome' => 'Sala Beta',
            'apresentador_id' => $apresentadorB->id,
            'status' => 'em_curso',
        ]);

        // Apresentador A tenta aceder ao controle da sala B
        $response = $this->actingAs($apresentadorA)->get("/apresentador/control/{$salaB->id}");
        $response->assertStatus(403);
    }

    public function test_gestao_de_perguntas_protegida_contra_acesso_anonimo(): void
    {
        $response = $this->get('/perguntas');
        $response->assertRedirect('/login');

        $responseCriar = $this->get('/perguntas/criar');
        $responseCriar->assertRedirect('/login');
    }

    public function test_pwa_manifest_e_service_worker_respondem_com_sucesso(): void
    {
        $manifest = $this->get('/manifest.json');
        $manifest->assertStatus(200);
        $manifest->assertHeader('Content-Type', 'application/json');

        $sw = $this->get('/sw.js');
        $sw->assertStatus(200);
    }

    public function test_rejeita_equipe_de_outra_sala_ao_entrar(): void
    {
        $igreja = Igreja::create(['nome' => 'Igreja Central', 'slug' => 'central']);
        $apresentador = Usuario::create([
            'igreja_id' => $igreja->id,
            'nome' => 'Apr',
            'email' => 'apr@igreja.com',
            'password' => bcrypt('password'),
            'papel' => 'apresentador',
        ]);

        $sala1 = Sala::create([
            'igreja_id' => $igreja->id,
            'codigo_acesso' => 'SALA01',
            'nome' => 'Sala 1',
            'apresentador_id' => $apresentador->id,
            'status' => 'em_curso',
        ]);

        $sala2 = Sala::create([
            'igreja_id' => $igreja->id,
            'codigo_acesso' => 'SALA02',
            'nome' => 'Sala 2',
            'apresentador_id' => $apresentador->id,
            'status' => 'em_curso',
        ]);

        $equipeSala2 = \App\Models\Equipe::create([
            'sala_id' => $sala2->id,
            'nome' => 'Equipe Exclusiva Sala 2',
            'cor' => '#ff0000',
        ]);

        $resp = $this->postJson('/api/salas/entrar', [
            'codigo_acesso' => 'SALA01',
            'nome' => 'Jogador Invasor',
            'equipe_id' => $equipeSala2->id,
        ]);

        $resp->assertStatus(422);
    }

    public function test_autorizacao_participante_rejeita_codigo_invalido(): void
    {
        $igreja = Igreja::create(['nome' => 'Igreja Central', 'slug' => 'central']);
        $apresentador = Usuario::create([
            'igreja_id' => $igreja->id,
            'nome' => 'Apr',
            'email' => 'apr2@igreja.com',
            'password' => bcrypt('password'),
            'papel' => 'apresentador',
        ]);
        $sala = Sala::create([
            'igreja_id' => $igreja->id,
            'codigo_acesso' => 'AUTH01',
            'nome' => 'Sala Auth',
            'apresentador_id' => $apresentador->id,
            'status' => 'em_curso',
        ]);
        $participante = \App\Models\Participante::create([
            'sala_id' => $sala->id,
            'nome' => 'Lucas',
            'codigo_acesso' => 'SECRET',
            'conectado' => true,
            'pontuacao_total' => 0,
        ]);

        $resp = $this->withHeaders(['X-Participante-Codigo' => 'ERRADO'])
            ->postJson("/api/salas/{$sala->id}/responder", [
                'participante_id' => $participante->id,
                'resposta' => '0',
            ]);

        $resp->assertStatus(403);
    }

    public function test_editor_nao_pode_editar_questao_global_nem_de_outra_igreja(): void
    {
        $igrejaA = Igreja::create(['nome' => 'Igreja A', 'slug' => 'igreja-a']);
        $igrejaB = Igreja::create(['nome' => 'Igreja B', 'slug' => 'igreja-b']);

        $editorA = Usuario::create([
            'igreja_id' => $igrejaA->id,
            'nome' => 'Editor A',
            'email' => 'edA@igreja.com',
            'password' => bcrypt('password'),
            'papel' => 'editor',
        ]);

        $livro = \App\Models\LivroBiblico::create([
            'codigo_osis' => 'Gen',
            'abreviacao' => 'Gn',
            'nome' => 'Gênesis',
            'testamento' => 'Antigo',
        ]);

        $questaoB = Questao::create([
            'igreja_id' => $igrejaB->id,
            'livro_id' => $livro->id,
            'texto' => 'Pergunta igreja B?',
            'opcoes' => ['A', 'B', 'C', 'D'],
            'resposta_correta' => 'A',
            'aprovada' => true,
        ]);

        $this->actingAs($editorA)->get("/perguntas/{$questaoB->id}/editar")->assertStatus(403);
        $this->actingAs($editorA)->delete("/perguntas/{$questaoB->id}")->assertStatus(403);
    }

    public function test_eventos_broadcast_sanitizam_gabarito(): void
    {
        $evPergunta = new \App\Events\PerguntaIniciada(1, [
            'id' => 10,
            'texto' => 'Quem construiu a arca?',
            'opcoes' => ['Noé', 'Moisés', 'Davi', 'Paulo'],
            'resposta_correta' => 'Noé',
            'referencia_biblica' => 'Gênesis 6',
        ], 60);

        $this->assertArrayNotHasKey('resposta_correta', $evPergunta->questao);
        $this->assertArrayNotHasKey('referencia_biblica', $evPergunta->questao);

        $evResposta = new \App\Events\RespostaEnviada(1, 5, 'Pedro', 10, true, 10, 4);
        $payload = $evResposta->broadcastWith();
        $this->assertArrayNotHasKey('correta', $payload);
        $this->assertArrayNotHasKey('pontos', $payload);
        $this->assertTrue($payload['respondido']);
    }
}
