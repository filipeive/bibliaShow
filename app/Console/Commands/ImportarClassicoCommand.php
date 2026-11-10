<?php

namespace App\Console\Commands;

use Database\Seeders\QuestoesClassicoSeeder;
use Illuminate\Console\Command;

class ImportarClassicoCommand extends Command
{
    protected $signature = 'biblia:importar-classico';

    protected $description = 'Importa as 1.000 perguntas originais, referências e ajudas bíblicas do jogo clássico Bíblia Show (cadastro.dbf)';

    public function handle(): int
    {
        $this->info('Iniciando importação do acervo clássico Bíblia Show...');

        $seeder = new QuestoesClassicoSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        return self::SUCCESS;
    }
}

