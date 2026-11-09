<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NivelDificuldade;

class NiveisDificuldadeSeeder extends Seeder
{
    public function run(): void
    {
        $niveis = [
            ['nome' => 'Iniciante', 'nivel' => 1],
            ['nome' => 'Básico', 'nivel' => 2],
            ['nome' => 'Intermediário', 'nivel' => 3],
            ['nome' => 'Avançado', 'nivel' => 4],
            ['nome' => 'Mestre', 'nivel' => 5],
        ];

        foreach ($niveis as $nivel) {
            NivelDificuldade::create($nivel);
        }
    }
}
