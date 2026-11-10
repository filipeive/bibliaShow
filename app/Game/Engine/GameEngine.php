<?php

namespace App\Game\Engine;

use App\Game\Engine\Contracts\GameStateInterface;
use App\Game\Engine\Contracts\QuestionSelectorInterface;
use App\Game\Engine\Contracts\ScoringInterface;
use App\Game\Engine\Contracts\TimerInterface;
use App\Game\Engine\Events\GameFinished;
use App\Game\Engine\Events\QuestionAnswered;
use App\Game\Engine\Events\QuestionStarted;
use App\Game\Engine\State\GameState;

class GameEngine
{
    private QuestionSelectorInterface $selector;
    private ScoringInterface $scoring;
    private TimerInterface $timer;
    private GameStateInterface $state;
    private array $questions = [];
    private int $currentIndex = -1;
    private array $participants = [];
    private array $answers = [];
    private int $pulosRestantes = 3;
    private int $dicasRestantes = 3;
    private int $ajudasConvidadosRestantes = 3;
    private bool $ajudaCartasUsada = false;
    private array $opcoesEliminadasAtual = [];
    private ?array $dicaConvidadoAtual = null;
    private ?string $dicaBiblicaAtual = null;
    private bool $convidadoConsultadoNaPergunta = false;

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
        $this->questions = array_values($this->selector->select($questions, count($questions)));
        $this->participants = $participants;
        $this->currentIndex = -1;
        $this->answers = [];
        $this->pulosRestantes = 3;
        $this->dicasRestantes = 3;
        $this->ajudasConvidadosRestantes = 3;
        $this->ajudaCartasUsada = false;
        $this->opcoesEliminadasAtual = [];
        $this->dicaConvidadoAtual = null;
        $this->dicaBiblicaAtual = null;
        $this->convidadoConsultadoNaPergunta = false;
        $this->state->setEstado('em_curso');
        $this->state->setPontuacao(0);

        $this->startNextQuestion();
    }

    public function startNextQuestion(): void
    {
        $this->currentIndex++;
        $this->opcoesEliminadasAtual = [];
        $this->dicaConvidadoAtual = null;
        $this->dicaBiblicaAtual = null;
        $this->convidadoConsultadoNaPergunta = false;

        if ($this->currentIndex >= count($this->questions)) {
            $this->finishGame();
            return;
        }

        $question = $this->questions[$this->currentIndex];
        $this->state->setPerguntaAtual($question);
        $this->state->adicionarHistoricoPergunta($question);
        $this->timer->iniciar(60);

        $this->dispatchEvent(new QuestionStarted($question, 60));
    }

    public function calcularFaseAtual(): int
    {
        $idx = max(0, $this->currentIndex);
        if ($idx < 6) {
            return 1;
        }
        if ($idx < 12) {
            return 2;
        }
        if ($idx < 18) {
            return 3;
        }

        return 4;
    }

    public function calcularValorPerguntaFase(): int
    {
        return match ($this->calcularFaseAtual()) {
            1 => 10,
            2 => 20,
            3 => 30,
            default => 50,
        };
    }

    public function getPlacarMilhao(): array
    {
        $atual = $this->state->getPontuacao();
        $fase = $this->calcularFaseAtual();
        $valor = $this->calcularValorPerguntaFase();

        return [
            'errar' => $fase >= 4 ? 0 : (int) floor($atual / 2),
            'parar' => $atual,
            'acertar' => $atual + $valor,
            'valor_pergunta' => $valor,
            'fase' => $fase,
        ];
    }

    public function answerQuestion(int $participantId, int $questionId, string $answer, int $responseTime): array
    {
        $question = $this->state->getPerguntaAtual();
        $faseAntes = $this->calcularFaseAtual();

        $respostaCorreta = $question['resposta_correta'] ?? $question->resposta_correta ?? '';
        $correct = mb_strtolower(trim($answer), 'UTF-8') === mb_strtolower(trim($respostaCorreta), 'UTF-8');

        $sequence = $this->calcularSequenciaAtual();
        $points = $this->scoring->calcularPontos($correct, $responseTime, 60, $sequence);

        $explicacao = $question['explicacao'] ?? $question->explicacao ?? '';
        $referenciaBiblica = $question['referencia_biblica'] ?? $question->referencia_biblica ?? '';
        $textoPergunta = $question['texto'] ?? $question->texto ?? '';

        $this->answers[] = [
            'participante_id' => $participantId,
            'questao_id' => $questionId,
            'fase' => $faseAntes,
            'pergunta_texto' => $textoPergunta,
            'resposta_dada' => $answer,
            'resposta_correta' => $respostaCorreta,
            'referencia_biblica' => $referenciaBiblica,
            'explicacao' => $explicacao,
            'acertou' => $correct,
            'pontos' => $points,
            'tempo_resposta' => $responseTime,
        ];

        if ($correct) {
            $this->state->adicionarPontuacao($points);
        }

        $this->dispatchEvent(new QuestionAnswered($participantId, $questionId, $correct, $points, $responseTime));

        $this->startNextQuestion();
        $faseDepois = $this->calcularFaseAtual();

        return [
            'acertou' => $correct,
            'pontos' => $points,
            'fase_anterior' => $faseAntes,
            'nova_fase' => ($this->state->getEstado() === 'em_curso' && $faseDepois > $faseAntes) ? $faseDepois : null,
            'som_efeito' => $correct ? ('certa' . rand(1, 3)) : ('errou' . rand(1, 3)),
            'resposta_dada' => $answer,
            'resposta_correta' => $respostaCorreta,
            'explicacao' => $explicacao,
            'referencia_biblica' => $referenciaBiblica,
            'referencia' => $textoPergunta,
        ];
    }

    public function usarDica(): array
    {
        if ($this->dicasRestantes <= 0 || $this->state->getEstado() !== 'em_curso') {
            return ['sucesso' => false, 'mensagem' => 'Você já utilizou as 3 dicas bíblicas disponíveis nesta partida.'];
        }

        if ($this->dicaBiblicaAtual !== null) {
            return ['sucesso' => false, 'mensagem' => 'A dica bíblica desta pergunta já está visível.'];
        }

        $question = $this->state->getPerguntaAtual();
        if (!$question) {
            return ['sucesso' => false, 'mensagem' => 'Nenhuma pergunta ativa.'];
        }

        $this->dicasRestantes--;
        $ref = $question['referencia_biblica'] ?? null;
        $exp = $question['explicacao'] ?? null;
        $livro = $question['livro_nome'] ?? null;

        if ($ref) {
            $this->dicaBiblicaAtual = "Referência Bíblica: Ler {$ref}";
        } elseif ($exp) {
            $this->dicaBiblicaAtual = "Dica Bíblica: {$exp}";
        } elseif ($livro) {
            $this->dicaBiblicaAtual = "Esta passagem encontra-se no livro de {$livro}.";
        } else {
            $this->dicaBiblicaAtual = 'Analise o contexto histórico dos personagens citados nas alternativas.';
        }

        return [
            'sucesso' => true,
            'som_efeito' => 'dica',
            'dicas_restantes' => $this->dicasRestantes,
            'dica_biblica' => $this->dicaBiblicaAtual,
            'mensagem' => 'Dica bíblica revelada! Restam ' . $this->dicasRestantes . ' dica(s).',
        ];
    }

    public function usarPulo(?array $perguntaReserva = null): array
    {
        if ($this->pulosRestantes <= 0 || $this->state->getEstado() !== 'em_curso') {
            return ['sucesso' => false, 'mensagem' => 'Você não possui mais pulos disponíveis nesta partida.'];
        }

        $this->pulosRestantes--;
        $this->opcoesEliminadasAtual = [];
        $this->dicaConvidadoAtual = null;
        $this->dicaBiblicaAtual = null;
        $this->convidadoConsultadoNaPergunta = false;

        if ($perguntaReserva) {
            $this->questions[$this->currentIndex] = $perguntaReserva;
            $this->state->setPerguntaAtual($perguntaReserva);
            $this->state->adicionarHistoricoPergunta($perguntaReserva);
            $this->timer->iniciar(60);
            $this->dispatchEvent(new QuestionStarted($perguntaReserva, 60));
        } else {
            $this->startNextQuestion();
        }

        return [
            'sucesso' => true,
            'som_efeito' => 'inicio',
            'pulos_restantes' => $this->pulosRestantes,
            'mensagem' => 'Pergunta trocada! Restam ' . $this->pulosRestantes . ' pulo(s).',
        ];
    }

    public function usarCartas(): array
    {
        if ($this->ajudaCartasUsada || $this->state->getEstado() !== 'em_curso') {
            return ['sucesso' => false, 'mensagem' => 'O recurso de eliminar 2 alternativas já foi utilizado.'];
        }

        $question = $this->state->getPerguntaAtual();
        if (!$question) {
            return ['sucesso' => false, 'mensagem' => 'Nenhuma pergunta ativa.'];
        }

        $opcoes = $question['opcoes'] ?? [];
        if (is_string($opcoes)) {
            $opcoes = json_decode($opcoes, true) ?: [];
        }

        $correta = mb_strtolower(trim($question['resposta_correta'] ?? ''), 'UTF-8');
        $indicesErrados = [];

        foreach ($opcoes as $idx => $opcao) {
            if (mb_strtolower(trim((string) $opcao), 'UTF-8') !== $correta) {
                $indicesErrados[] = (int) $idx;
            }
        }

        shuffle($indicesErrados);
        $this->opcoesEliminadasAtual = array_slice($indicesErrados, 0, 2);
        $this->ajudaCartasUsada = true;

        return [
            'sucesso' => true,
            'som_efeito' => 'ajuda',
            'opcoes_eliminadas' => $this->opcoesEliminadasAtual,
            'mensagem' => 'Duas alternativas incorretas foram eliminadas!',
        ];
    }

    public function consultarConvidado(int $indiceConvidado): array
    {
        if ($this->ajudasConvidadosRestantes <= 0 || $this->state->getEstado() !== 'em_curso') {
            return ['sucesso' => false, 'mensagem' => 'Você já utilizou as 3 ajudas de personagens bíblicos nesta partida.'];
        }

        if ($this->convidadoConsultadoNaPergunta) {
            return ['sucesso' => false, 'mensagem' => 'Você já consultou um personagem bíblico nesta pergunta.'];
        }

        $question = $this->state->getPerguntaAtual();
        if (!$question) {
            return ['sucesso' => false, 'mensagem' => 'Nenhuma pergunta ativa.'];
        }

        $ajudas = $question['ajudas'] ?? [];
        if (is_string($ajudas)) {
            $ajudas = json_decode($ajudas, true) ?: [];
        }

        $convidados = $ajudas['convidados'] ?? ['Paulo', 'Pedro', 'Moisés'];
        $errado = (int) ($ajudas['errado'] ?? 2);
        $comentarioAjuda = $ajudas['comentario_ajuda'] ?? null;

        $nomeConvidado = $convidados[$indiceConvidado] ?? ($convidados[0] ?? 'Sábio Bíblico');
        $numeroEscolhido = $indiceConvidado + 1;
        $respostaCorreta = $question['resposta_correta'] ?? '';

        $opcoes = $question['opcoes'] ?? [];
        if (is_string($opcoes)) {
            $opcoes = json_decode($opcoes, true) ?: [];
        }
        $letras = ['A', 'B', 'C', 'D'];
        $letraCorreta = '';
        foreach ($opcoes as $i => $op) {
            if (mb_strtolower(trim((string) $op), 'UTF-8') === mb_strtolower(trim($respostaCorreta), 'UTF-8')) {
                $letraCorreta = $letras[$i] ?? '';
                break;
            }
        }

        $this->ajudasConvidadosRestantes--;
        $this->convidadoConsultadoNaPergunta = true;
        $somConvidado = 'sabe' . min(3, max(1, $numeroEscolhido));

        if ($numeroEscolhido === $errado) {
            $fala = $comentarioAjuda
                ? "«{$comentarioAjuda}» — Por isso {$nomeConvidado} não teve relação direta com este acontecimento!"
                : "«Não vivi na época deste relato bíblico, portanto não posso confirmar a alternativa correta!»";

            $this->dicaConvidadoAtual = [
                'convidado' => $nomeConvidado,
                'sabe_resposta' => false,
                'mensagem' => $fala,
            ];
        } else {
            $prefixoLetra = $letraCorreta ? "a alternativa {$letraCorreta} — " : '';
            $fala = "«A resposta correta é {$prefixoLetra}{$respostaCorreta}!»";

            $this->dicaConvidadoAtual = [
                'convidado' => $nomeConvidado,
                'sabe_resposta' => true,
                'mensagem' => $fala,
                'resposta_sugerida' => $respostaCorreta,
            ];
        }

        return [
            'sucesso' => true,
            'som_efeito' => $somConvidado,
            'ajudas_restantes' => $this->ajudasConvidadosRestantes,
            'dica' => $this->dicaConvidadoAtual,
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

    private function calcularSequenciaAtual(): int
    {
        $sequencia = 0;
        for ($i = count($this->answers) - 1; $i >= 0; $i--) {
            if (!empty($this->answers[$i]['acertou'])) {
                $sequencia++;
            } else {
                break;
            }
        }

        return $sequencia;
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
        $total = count($this->questions);
        $numeroAtual = $total > 0 ? min($this->currentIndex + 1, $total) : 1;

        return [
            'estado' => $this->state->getEstado(),
            'pergunta_atual' => $this->state->getPerguntaAtual(),
            'numero_pergunta' => max(1, $numeroAtual),
            'total_perguntas' => $total,
            'fase_atual' => $this->calcularFaseAtual(),
            'placar_milhao' => $this->getPlacarMilhao(),
            'sequencia_acertos' => $this->calcularSequenciaAtual(),
            'pontuacao' => $this->state->getPontuacao(),
            'ranking' => $this->state->getRanking(),
            'tempo_restante' => $this->timer->tempoRestante(),
            'tempo_esgotado' => $this->timer->esgotado(),
            'ajudas' => [
                'pulos_restantes' => $this->pulosRestantes,
                'dicas_restantes' => $this->dicasRestantes,
                'convidados_restantes' => $this->ajudasConvidadosRestantes,
                'convidados_usada' => $this->ajudasConvidadosRestantes <= 0 || $this->convidadoConsultadoNaPergunta,
                'cartas_usada' => $this->ajudaCartasUsada,
                'opcoes_eliminadas' => $this->opcoesEliminadasAtual,
                'dica_convidado' => $this->dicaConvidadoAtual,
                'dica_biblica' => $this->dicaBiblicaAtual,
            ],
        ];
    }

    public function getResults(): array
    {
        $acertos = count(array_filter($this->answers, fn ($a) => !empty($a['acertou'])));

        return [
            'estado' => $this->state->getEstado(),
            'pontuacao_total' => $this->state->getPontuacao(),
            'total_acertos' => $acertos,
            'total_respondidas' => count($this->answers),
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
