<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RespostaEnviada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $salaId;
    public int $participanteId;
    public string $nomeParticipante;
    public int $questaoId;
    public bool $correta;
    public int $pontos;
    public int $tempoResposta;

    public function __construct(
        int $salaId,
        int $participanteId,
        string $nomeParticipante,
        int $questaoId,
        bool $correta,
        int $pontos,
        int $tempoResposta
    ) {
        $this->salaId = $salaId;
        $this->participanteId = $participanteId;
        $this->nomeParticipante = $nomeParticipante;
        $this->questaoId = $questaoId;
        $this->correta = $correta;
        $this->pontos = $pontos;
        $this->tempoResposta = $tempoResposta;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("game.{$this->salaId}");
    }

    public function broadcastAs(): string
    {
        return 'resposta-enviada';
    }

    public function broadcastWith(): array
    {
        return [
            'salaId' => $this->salaId,
            'participanteId' => $this->participanteId,
            'nomeParticipante' => $this->nomeParticipante,
            'questaoId' => $this->questaoId,
            'tempoResposta' => $this->tempoResposta,
            'respondido' => true,
        ];
    }
}
