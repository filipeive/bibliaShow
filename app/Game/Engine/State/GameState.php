<?php

namespace App\Game\Engine\State;

use App\Game\Engine\Contracts\GameStateInterface;

class GameState implements GameStateInterface
{
    private string $estado = 'aguardando';
    private ?array $perguntaAtual = null;
    private int $pontuacao = 0;
    private array $ranking = [];
    private array $historicoPerguntas = [];

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function getPerguntaAtual(): ?array
    {
        return $this->perguntaAtual;
    }

    public function setPerguntaAtual(?array $pergunta): void
    {
        $this->perguntaAtual = $pergunta;
    }

    public function getPontuacao(): int
    {
        return $this->pontuacao;
    }

    public function adicionarPontuacao(int $pontos): void
    {
        $this->pontuacao += $pontos;
    }

    public function setPontuacao(int $pontos): void
    {
        $this->pontuacao = $pontos;
    }

    public function getRanking(): array
    {
        return $this->ranking;
    }

    public function setRanking(array $ranking): void
    {
        $this->ranking = $ranking;
    }

    public function getHistoricoPerguntas(): array
    {
        return $this->historicoPerguntas;
    }

    public function adicionarHistoricoPergunta(array $pergunta): void
    {
        $this->historicoPerguntas[] = $pergunta;
    }
}
