<?php

namespace App\Game\Engine\Events;

class GameFinished
{
    public int $jogoId;
    public array $rankingFinal;
    public array $estatisticas;

    public function __construct(int $jogoId, array $rankingFinal, array $estatisticas)
    {
        $this->jogoId = $jogoId;
        $this->rankingFinal = $rankingFinal;
        $this->estatisticas = $estatisticas;
    }
}
