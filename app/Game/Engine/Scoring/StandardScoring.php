<?php

namespace App\Game\Engine\Scoring;

use App\Game\Engine\Contracts\ScoringInterface;

class StandardScoring implements ScoringInterface
{
    public function calcularPontos(bool $acertou, int $tempoResposta, int $tempoLimite, int $sequenciaAcertos): int
    {
        if (!$acertou) {
            return 0;
        }

        $base = 100;

        $bonusTempo = $tempoResposta <= $tempoLimite / 2 ? 50 : 0;

        $bonusSequencia = min($sequenciaAcertos * 10, 100);

        return $base + $bonusTempo + $bonusSequencia;
    }
}
