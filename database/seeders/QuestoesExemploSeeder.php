<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Questao;
use App\Models\Igreja;
use App\Models\Usuario;

class QuestoesExemploSeeder extends Seeder
{
    public function run(): void
    {
        $igreja = Igreja::first();
        $usuario = Usuario::first();

        if (!$igreja || !$usuario) {
            return;
        }

        $questoes = [
            [
                'livro_id' => 1, // Gênesis
                'texto' => 'Quem foi o primeiro homem criado por Deus?',
                'resposta_correta' => 'Adão',
                'explicacao' => 'Adão foi o primeiro homem, criado por Deus segundo Gênesis 2:7.',
                'contexto' => 'Criação do mundo e da humanidade no Jardim do Éden.',
                'dificuldade_id' => 1,
                'categoria_id' => 2, // Personagens
                'referencia_verificada' => true,
                'aprovada' => true,
                'submetida_por' => $usuario->id,
                'versiculo_de' => 1,
                'versiculo_ate' => 7,
                'capitulo_id' => null,
            ],
            [
                'livro_id' => 44, // João
                'texto' => 'Qual é o versículo mais conhecido do Novo Testamento sobre o amor de Deus?',
                'resposta_correta' => 'João 3:16',
                'explicacao' => '\"Porque Deus amou o mundo de tal maneira que deu o seu Filho unigênito, para que todo aquele que nele crê não pereça, mas tenha a vida eterna.\"',
                'contexto' => 'Conversa de Jesus com Nicodemos sobre o nascimento de cima.',
                'dificuldade_id' => 1,
                'categoria_id' => 6, // Vida de Jesus
                'referencia_verificada' => true,
                'aprovada' => true,
                'submetida_por' => $usuario->id,
                'versiculo_de' => 16,
                'versiculo_ate' => 16,
                'capitulo_id' => null,
            ],
        ];

        foreach ($questoes as $questao) {
            Questao::create(array_merge($questao, [
                'igreja_id' => $igreja->id,
            ]));
        }
    }
}
