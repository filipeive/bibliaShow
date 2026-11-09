<?php

namespace App\Game\Engine\Events;

class RoundFinished
{
    public int $jogoId;
    public array $resultado;
    public array $ranking;

    public function __construct(int $jogoId, array $resultado, array $ranking)
    {
        $this->jogoId = $jogoId;
        $this->resultado = $resultado;
        $this->ranking = $ranking;
    }
}
