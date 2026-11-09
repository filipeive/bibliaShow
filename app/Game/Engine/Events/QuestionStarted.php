<?php

namespace App\Game\Engine\Events;

class QuestionStarted
{
    public array $questao;
    public int $tempoLimite;

    public function __construct(array $questao, int $tempoLimite)
    {
        $this->questao = $questao;
        $this->tempoLimite = $tempoLimite;
    }
}
