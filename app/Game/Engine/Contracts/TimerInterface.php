<?php

namespace App\Game\Engine\Contracts;

interface TimerInterface
{
    public function iniciar(int $segundos): void;
    public function parar(): void;
    public function tempoRestante(): int;
    public function esgotado(): bool;
}
