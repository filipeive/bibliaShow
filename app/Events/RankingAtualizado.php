<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RankingAtualizado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $salaId;
    public array $ranking;

    public function __construct(int $salaId, array $ranking)
    {
        $this->salaId = $salaId;
        $this->ranking = $ranking;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("game.{$this->salaId}");
    }

    public function broadcastAs(): string
    {
        return 'ranking-atualizado';
    }
}
