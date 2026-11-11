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
        $r1->assertJsonPath('acertou', true);

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
}
