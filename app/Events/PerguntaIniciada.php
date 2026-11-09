<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PerguntaIniciada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $salaId;
    public array $questao;
    public int $tempoLimite;

    public function __construct(int $salaId, array $questao, int $tempoLimite)
    {
        $this->salaId = $salaId;
        $this->questao = $questao;
        $this->tempoLimite = $tempoLimite;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("game.{$this->salaId}");
    }

    public function broadcastAs(): string
    {
        return 'pergunta-iniciada';
    }
}
