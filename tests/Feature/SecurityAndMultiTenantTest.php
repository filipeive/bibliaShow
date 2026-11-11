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
}
