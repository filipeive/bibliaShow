<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LivroBiblico;

class LivrosBiblicosSeeder extends Seeder
{
    public function run(): void
    {
        $livros = [
            // Antigo Testamento
            ['abreviacao' => 'Gn', 'nome' => 'Gênesis', 'testamento' => 'Antigo', 'codigo_osis' => 'Gen'],
            ['abreviacao' => 'Ex', 'nome' => 'Êxodo', 'testamento' => 'Antigo', 'codigo_osis' => 'Exod'],
            ['abreviacao' => 'Lv', 'nome' => 'Levítico', 'testamento' => 'Antigo', 'codigo_osis' => 'Lev'],
            ['abreviacao' => 'Nm', 'nome' => 'Números', 'testamento' => 'Antigo', 'codigo_osis' => 'Num'],
            ['abreviacao' => 'Dt', 'nome' => 'Deuteronômio', 'testamento' => 'Antigo', 'codigo_osis' => 'Deut'],
            ['abreviacao' => 'Js', 'nome' => 'Josué', 'testamento' => 'Antigo', 'codigo_osis' => 'Josh'],
            ['abreviacao' => 'Jz', 'nome' => 'Juízes', 'testamento' => 'Antigo', 'codigo_osis' => 'Judg'],
            ['abreviacao' => 'Rt', 'nome' => 'Rute', 'testamento' => 'Antigo', 'codigo_osis' => 'Ruth'],
            ['abreviacao' => '1Sm', 'nome' => '1 Samuel', 'testamento' => 'Antigo', 'codigo_osis' => '1Sam'],
            ['abreviacao' => '2Sm', 'nome' => '2 Samuel', 'testamento' => 'Antigo', 'codigo_osis' => '2Sam'],
            ['abreviacao' => '1Rs', 'nome' => '1 Reis', 'testamento' => 'Antigo', 'codigo_osis' => '1Kgs'],
            ['abreviacao' => '2Rs', 'nome' => '2 Reis', 'testamento' => 'Antigo', 'codigo_osis' => '2Kgs'],
            ['abreviacao' => '1Cr', 'nome' => '1 Crônicas', 'testamento' => 'Antigo', 'codigo_osis' => '1Chr'],
            ['abreviacao' => '2Cr', 'nome' => '2 Crônicas', 'testamento' => 'Antigo', 'codigo_osis' => '2Chr'],
            ['abreviacao' => 'Ed', 'nome' => 'Esdras', 'testamento' => 'Antigo', 'codigo_osis' => 'Ezra'],
            ['abreviacao' => 'Ne', 'nome' => 'Neemias', 'testamento' => 'Antigo', 'codigo_osis' => 'Neh'],
            ['abreviacao' => 'Et', 'nome' => 'Ester', 'testamento' => 'Antigo', 'codigo_osis' => 'Esth'],
            ['abreviacao' => 'Jó', 'nome' => 'Jó', 'testamento' => 'Antigo', 'codigo_osis' => 'Job'],
            ['abreviacao' => 'Sl', 'nome' => 'Salmos', 'testamento' => 'Antigo', 'codigo_osis' => 'Ps'],
            ['abreviacao' => 'Pv', 'nome' => 'Provérbios', 'testamento' => 'Antigo', 'codigo_osis' => 'Prov'],
            ['abreviacao' => 'Ec', 'nome' => 'Eclesiastes', 'testamento' => 'Antigo', 'codigo_osis' => 'Eccl'],
            ['abreviacao' => 'Ct', 'nome' => 'Cantares', 'testamento' => 'Antigo', 'codigo_osis' => 'Song'],
            ['abreviacao' => 'Is', 'nome' => 'Isaías', 'testamento' => 'Antigo', 'codigo_osis' => 'Isa'],
            ['abreviacao' => 'Jer', 'nome' => 'Jeremias', 'testamento' => 'Antigo', 'codigo_osis' => 'Jer'],
            ['abreviacao' => 'Ez', 'nome' => 'Ezequiel', 'testamento' => 'Antigo', 'codigo_osis' => 'Ezek'],
            ['abreviacao' => 'Dn', 'nome' => 'Daniel', 'testamento' => 'Antigo', 'codigo_osis' => 'Dan'],
            ['abreviacao' => 'Os', 'nome' => 'Oseias', 'testamento' => 'Antigo', 'codigo_osis' => 'Hos'],
            ['abreviacao' => 'Jl', 'nome' => 'Joel', 'testamento' => 'Antigo', 'codigo_osis' => 'Joel'],
            ['abreviacao' => 'Am', 'nome' => 'Amós', 'testamento' => 'Antigo', 'codigo_osis' => 'Amos'],
            ['abreviacao' => 'Jonas', 'nome' => 'Jonas', 'testamento' => 'Antigo', 'codigo_osis' => 'Jonah'],
            ['abreviacao' => 'Mq', 'nome' => 'Miquéias', 'testamento' => 'Antigo', 'codigo_osis' => 'Mic'],
            ['abreviacao' => 'Na', 'nome' => 'Naum', 'testamento' => 'Antigo', 'codigo_osis' => 'Nah'],
            ['abreviacao' => 'Hc', 'nome' => 'Habacuque', 'testamento' => 'Antigo', 'codigo_osis' => 'Hab'],
            ['abreviacao' => 'Sf', 'nome' => 'Sofonias', 'testamento' => 'Antigo', 'codigo_osis' => 'Zeph'],
            ['abreviacao' => 'Ag', 'nome' => 'Ageu', 'testamento' => 'Antigo', 'codigo_osis' => 'Hag'],
            ['abreviacao' => 'Zc', 'nome' => 'Zacarias', 'testamento' => 'Antigo', 'codigo_osis' => 'Zech'],
            ['abreviacao' => 'Ml', 'nome' => 'Malaquias', 'testamento' => 'Antigo', 'codigo_osis' => 'Mal'],
            // Novo Testamento
            ['abreviacao' => 'Mt', 'nome' => 'Mateus', 'testamento' => 'Novo', 'codigo_osis' => 'Matt'],
            ['abreviacao' => 'Mc', 'nome' => 'Marcos', 'testamento' => 'Novo', 'codigo_osis' => 'Mark'],
            ['abreviacao' => 'Lc', 'nome' => 'Lucas', 'testamento' => 'Novo', 'codigo_osis' => 'Luke'],
            ['abreviacao' => 'Jo', 'nome' => 'João', 'testamento' => 'Novo', 'codigo_osis' => 'John'],
            ['abreviacao' => 'At', 'nome' => 'Atos', 'testamento' => 'Novo', 'codigo_osis' => 'Acts'],
            ['abreviacao' => 'Rm', 'nome' => 'Romanos', 'testamento' => 'Novo', 'codigo_osis' => 'Rom'],
            ['abreviacao' => '1Co', 'nome' => '1 Coríntios', 'testamento' => 'Novo', 'codigo_osis' => '1Cor'],
            ['abreviacao' => '2Co', 'nome' => '2 Coríntios', 'testamento' => 'Novo', 'codigo_osis' => '2Cor'],
            ['abreviacao' => 'Gl', 'nome' => 'Gálatas', 'testamento' => 'Novo', 'codigo_osis' => 'Gal'],
            ['abreviacao' => 'Ef', 'nome' => 'Efésios', 'testamento' => 'Novo', 'codigo_osis' => 'Eph'],
            ['abreviacao' => 'Fp', 'nome' => 'Filipenses', 'testamento' => 'Novo', 'codigo_osis' => 'Phil'],
            ['abreviacao' => 'Cl', 'nome' => 'Colossenses', 'testamento' => 'Novo', 'codigo_osis' => 'Col'],
            ['abreviacao' => '1Ts', 'nome' => '1 Tessalonicenses', 'testamento' => 'Novo', 'codigo_osis' => '1Thess'],
            ['abreviacao' => '2Ts', 'nome' => '2 Tessalonicenses', 'testamento' => 'Novo', 'codigo_osis' => '2Thess'],
            ['abreviacao' => '1Tm', 'nome' => '1 Timóteo', 'testamento' => 'Novo', 'codigo_osis' => '1Tim'],
            ['abreviacao' => '2Tm', 'nome' => '2 Timóteo', 'testamento' => 'Novo', 'codigo_osis' => '2Tim'],
            ['abreviacao' => 'Tt', 'nome' => 'Tito', 'testamento' => 'Novo', 'codigo_osis' => 'Titus'],
            ['abreviacao' => 'Fm', 'nome' => 'Filemom', 'testamento' => 'Novo', 'codigo_osis' => 'Phlm'],
            ['abreviacao' => 'Hb', 'nome' => 'Hebreus', 'testamento' => 'Novo', 'codigo_osis' => 'Heb'],
            ['abreviacao' => 'Tg', 'nome' => 'Tiago', 'testamento' => 'Novo', 'codigo_osis' => 'Jas'],
            ['abreviacao' => '1Pe', 'nome' => '1 Pedro', 'testamento' => 'Novo', 'codigo_osis' => '1Pet'],
            ['abreviacao' => '2Pe', 'nome' => '2 Pedro', 'testamento' => 'Novo', 'codigo_osis' => '2Pet'],
            ['abreviacao' => '1Jo', 'nome' => '1 João', 'testamento' => 'Novo', 'codigo_osis' => '1John'],
            ['abreviacao' => '2Jo', 'nome' => '2 João', 'testamento' => 'Novo', 'codigo_osis' => '2John'],
            ['abreviacao' => '3Jo', 'nome' => '3 João', 'testamento' => 'Novo', 'codigo_osis' => '3John'],
            ['abreviacao' => 'Jd', 'nome' => 'Judas', 'testamento' => 'Novo', 'codigo_osis' => 'Jude'],
            ['abreviacao' => 'Ap', 'nome' => 'Apocalipse', 'testamento' => 'Novo', 'codigo_osis' => 'Rev'],
        ];

        foreach ($livros as $livro) {
            LivroBiblico::create($livro);
        }
    }
}
