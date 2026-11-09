<?php

namespace App\Game\Engine\Contracts;

interface ScoringInterface
{
    public function calcularPontos(bool $acertou, int $tempoResposta, int $tempoLimite, int $sequenciaAcertos): int;
}
