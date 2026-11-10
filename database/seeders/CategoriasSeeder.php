<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nome' => 'História', 'slug' => 'historia', 'descricao' => 'Perguntas sobre eventos e narrativas bíblicas'],
            ['nome' => 'Personagens', 'slug' => 'personagens', 'descricao' => 'Perguntas sobre personagens da Bíblia'],
            ['nome' => 'Teologia', 'slug' => 'teologia', 'descricao' => 'Perguntas sobre doutrina e ensinamentos'],
            ['nome' => 'Profecias', 'slug' => 'profecias', 'descricao' => 'Perguntas sobre profecias e cumprimento'],
            ['nome' => 'Salmos e Poesia', 'slug' => 'salmos-poesia', 'descricao' => 'Perguntas sobre livros poéticos'],
            ['nome' => 'Vida de Jesus', 'slug' => 'vida-jesus', 'descricao' => 'Perguntas sobre o ministério de Jesus'],
            ['nome' => 'Igreja Primitiva', 'slug' => 'igreja-primitiva', 'descricao' => 'Perguntas sobre os Atos e cartas'],
            ['nome' => 'Salvação', 'slug' => 'salvacao', 'descricao' => 'Perguntas sobre evangelho e salvação'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::updateOrCreate(['slug' => $categoria['slug']], $categoria);
        }
    }
}
