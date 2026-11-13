<?php

namespace Tests\Feature;

use App\Jobs\RegistrarEstatisticasRespostaJob;
use App\Models\Igreja;
use App\Models\Sala;
use App\Models\Usuario;
use App\Services\ArenaCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheQueuePrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_politica_de_privacidade_e_cookies_carrega(): void
    {
        $response = $this->get('/privacidade');

        $response->assertStatus(200);
        $response->assertSee('Política de Privacidade e Cookies');
        $response->assertSee('laravel_session');
    }

    public function test_arena_cache_service_cria_e_invalida_snapshot_da_sala(): void
    {
        Cache::flush();

        $igreja = Igreja::create([
            'nome' => 'Igreja Teste Cache',
            'slug' => 'igreja-teste-cache',
        ]);

        $usuario = Usuario::create([
            'igreja_id' => $igreja->id,
            'nome' => 'Apresentador Teste',
            'email' => 'apresentador.cache@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'apresentador',
        ]);

        $sala = Sala::create([
            'igreja_id' => $igreja->id,
            'apresentador_id' => $usuario->id,
            'nome' => 'Sala Teste Cache',
            'codigo' => 'CCH999',
            'codigo_acesso' => 'CCH999',
            'status' => 'aguardando',
        ]);

        $snapshot = ArenaCacheService::getSalaSnapshot($sala);

        $this->assertArrayHasKey('version', $snapshot);
        $this->assertSame('Sala Teste Cache', $snapshot['sala']['nome']);
        $this->assertTrue(Cache::has(ArenaCacheService::snapshotKey($sala->id)));

        ArenaCacheService::invalidateSala($sala->id);

        $this->assertFalse(Cache::has(ArenaCacheService::snapshotKey($sala->id)));
    }

    public function test_job_de_estatisticas_de_resposta_atualiza_cache(): void
    {
        Cache::flush();
        Cache::put('api_v1_ranking', [['nome' => 'Antigo']], 60);

        $job = new RegistrarEstatisticasRespostaJob(
            modo: 'arena',
            questaoId: 77,
            correta: true,
            tempoResposta: 8,
            salaId: 1
        );
        $job->handle();

        $stats = Cache::get('questao_stats_77');
        $this->assertSame(1, $stats['total']);
        $this->assertSame(1, $stats['acertos']);
        $this->assertSame(0, $stats['erros']);
        $this->assertFalse(Cache::has('api_v1_ranking'));
    }

    public function test_api_v1_meta_e_ranking_respondem_com_sucesso_e_cache(): void
    {
        Cache::flush();

        $this->getJson('/api/v1/meta')->assertStatus(200)->assertJson(['sucesso' => true]);
        $this->assertTrue(Cache::has('api_v1_meta'));

        $this->getJson('/api/v1/ranking')->assertStatus(200)->assertJson(['sucesso' => true]);
        $this->assertTrue(Cache::has('api_v1_ranking'));
    }
}
