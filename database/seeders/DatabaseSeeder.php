<?php

namespace Database\Seeders;

use App\Models\Igreja;
use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $igreja = Igreja::firstOrCreate(
            ['slug' => 'igreja-exemplo'],
            ['nome' => 'Igreja Exemplo']
        );

        Usuario::firstOrCreate(
            ['email' => 'admin@igreja.com'],
            [
                'igreja_id' => $igreja->id,
                'nome' => 'Administrador',
                'password' => bcrypt('password'),
                'papel' => 'admin_geral',
            ]
        );

        $this->call([
            LivrosBiblicosSeeder::class,
            NiveisDificuldadeSeeder::class,
            CategoriasSeeder::class,
            QuestoesExemploSeeder::class,
            QuestoesClassicoSeeder::class,
        ]);
    }
}
