<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JogoEstadoAlterado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $salaId;
    public string $estado;
    public ?array $questaoAtual = null;
    public int $tempoRestante = 0;

    public function __construct(int $salaId, string $estado, ?array $questaoAtual = null, int $tempoRestante = 0)
    {
        $this->salaId = $salaId;
        $this->estado = $estado;
        if (is_array($questaoAtual)) {
            unset($questaoAtual['resposta_correta'], $questaoAtual['referencia_biblica'], $questaoAtual['dica'], $questaoAtual['ajudas'], $questaoAtual['explicacao']);
        }
        $this->questaoAtual = $questaoAtual;
        $this->tempoRestante = $tempoRestante;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("game.{$this->salaId}");
    }

    public function broadcastAs(): string
    {
        return 'jogo-estado-alterado';
    }
}
