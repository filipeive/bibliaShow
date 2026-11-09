<?php

namespace App\Game\Engine\Contracts;

interface GameStateInterface
{
    public function getEstado(): string;
    public function setEstado(string $estado): void;
    public function getPerguntaAtual(): ?array;
    public function setPerguntaAtual(?array $pergunta): void;
    public function getPontuacao(): int;
    public function adicionarPontuacao(int $pontos): void;
    public function setPontuacao(int $pontos): void;
    public function getRanking(): array;
}
