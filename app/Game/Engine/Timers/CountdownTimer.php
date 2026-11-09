<?php

namespace App\Game\Engine\Timers;

use App\Game\Engine\Contracts\TimerInterface;

class CountdownTimer implements TimerInterface
{
    private int $limite;
    private int $inicio;
    private bool $ativo = false;

    public function iniciar(int $segundos): void
    {
        $this->limite = $segundos;
        $this->inicio = time();
        $this->ativo = true;
    }

    public function parar(): void
    {
        $this->ativo = false;
    }

    public function tempoRestante(): int
    {
        if (!$this->ativo) {
            return 0;
        }

        $passado = time() - $this->inicio;
        $restante = $this->limite - $passado;

        return max(0, $restante);
    }

    public function esgotado(): bool
    {
        return $this->ativo && $this->tempoRestante() <= 0;
    }
}
