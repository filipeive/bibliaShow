<?php

namespace App\Game\Engine\Events;

class QuestionAnswered
{
    public int $participanteId;
    public int $questaoId;
    public bool $acertou;
    public int $pontos;
    public int $tempoResposta;

    public function __construct(int $participanteId, int $questaoId, bool $acertou, int $pontos, int $tempoResposta)
    {
        $this->participanteId = $participanteId;
        $this->questaoId = $questaoId;
        $this->acertou = $acertou;
        $this->pontos = $pontos;
        $this->tempoResposta = $tempoResposta;
    }
}
