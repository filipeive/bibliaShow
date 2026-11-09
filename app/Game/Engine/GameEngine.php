<?php

namespace App\Game\Engine;

use App\Game\Engine\Contracts\GameStateInterface;
use App\Game\Engine\Contracts\QuestionSelectorInterface;
use App\Game\Engine\Contracts\ScoringInterface;
use App\Game\Engine\Contracts\TimerInterface;
use App\Game\Engine\Events\GameFinished;
use App\Game\Engine\Events\QuestionAnswered;
use App\Game\Engine\Events\QuestionStarted;
use App\Game\Engine\Events\RoundFinished;
use App\Game\Engine\State\GameState;

class GameEngine
{
    private QuestionSelectorInterface $selector;
    private ScoringInterface $scoring;
    private TimerInterface $timer;
    private GameStateInterface $state;
    private array $questions = [];
    private int $currentIndex = 0;
    private array $participants = [];
    private array $answers = [];

    public function __construct(
        QuestionSelectorInterface $selector,
        ScoringInterface $scoring,
        TimerInterface $timer,
        ?GameStateInterface $state = null
    ) {
        $this->selector = $selector;
        $this->scoring = $scoring;
        $this->timer = $timer;
        $this->state = $state ?? new GameState();
    }

    public function start(array $questions, array $participants = []): void
    {
        $this->questions = $this->selector->select($questions, count($questions));
        $this->participants = $participants;
        $this->currentIndex = 0;
        $this->answers = [];
        $this->state->setEstado('em_curso');
        $this->state->setPontuacao(0);

        $this->startNextQuestion();
    }

    public function startNextQuestion(): void
    {
        if ($this->currentIndex >= count($this->questions)) {
            $this->finishGame();
            return;
        }

        $question = $this->questions[$this->currentIndex];
        $this->state->setPerguntaAtual($question);
        $this->state->adicionarHistoricoPergunta($question);
        $this->timer->iniciar(30);

        $this->dispatchEvent(new QuestionStarted($question, 30));
    }

    public function answerQuestion(int $participantId, int $questionId, string $answer, int $responseTime): array
    {
        $question = $this->state->getPerguntaAtual();

        $correct = strtolower(trim($answer)) === strtolower(trim($question['resposta_correta'] ?? $question->resposta_correta ?? ''));
        $sequence = count(array_filter($this->answers, fn ($a) => $a['acertou']));
        $points = $this->scoring->calcularPontos($correct, $responseTime, 30, $sequence);

        $this->answers[] = [
            'participante_id' => $participantId,
            'questao_id' => $questionId,
            'acertou' => $correct,
            'pontos' => $points,
            'tempo_resposta' => $responseTime,
        ];

        if ($correct) {
            $this->state->adicionarPontuacao($points);
        }

        $this->currentIndex++;

        $this->dispatchEvent(new QuestionAnswered($participantId, $questionId, $correct, $points, $responseTime));

        $this->startNextQuestion();

        return [
            'acertou' => $correct,
            'pontos' => $points,
            'resposta_correta' => $question['resposta_correta'] ?? $question->resposta_correta ?? '',
            'explicacao' => $question['explicacao'] ?? $question->explicacao ?? '',
            'referencia' => $question['texto'] ?? $question->texto ?? '',
        ];
    }

    public function pause(): void
    {
        $this->timer->parar();
        $this->state->setEstado('pausado');
    }

    public function resume(): void
    {
        $this->state->setEstado('em_curso');
    }

    public function finishGame(): void
    {
        $this->timer->parar();
        $this->state->setEstado('terminado');

        $ranking = $this->buildRanking();

        $this->dispatchEvent(new GameFinished(
            $this->state->getPontuacao(),
            $ranking,
            [
                'total_perguntas' => count($this->questions),
                'total_respostas' => count($this->answers),
                'pontuacao_total' => $this->state->getPontuacao(),
            ]
        ));
    }

    private function dispatchEvent($event): void
    {
        try {
            if (function_exists('event')) {
                event($event);
            }
        } catch (\Throwable $e) {
            // Event dispatching unavailable outside Laravel context; safe to ignore.
        }
    }

    public function getState(): array
    {
        return [
            'estado' => $this->state->getEstado(),
            'pergunta_atual' => $this->state->getPerguntaAtual(),
            'pontuacao' => $this->state->getPontuacao(),
            'ranking' => $this->state->getRanking(),
            'tempo_restante' => $this->timer->tempoRestante(),
            'tempo_esgotado' => $this->timer->esgotado(),
        ];
    }

    public function getResults(): array
    {
        return [
            'estado' => $this->state->getEstado(),
            'pontuacao_total' => $this->state->getPontuacao(),
            'respostas' => $this->answers,
            'historico_perguntas' => $this->state->getHistoricoPerguntas(),
        ];
    }

    private function buildRanking(): array
    {
        $ranking = [];
        foreach ($this->participants as $participant) {
            $ranking[] = [
                'participante_id' => $participant['id'] ?? $participant,
                'nome' => $participant['nome'] ?? 'Participante',
                'pontuacao' => $this->state->getPontuacao(),
            ];
        }

        usort($ranking, fn ($a, $b) => $b['pontuacao'] <=> $a['pontuacao']);

        $this->state->setRanking($ranking);

        return $ranking;
    }
}
