<?php

namespace App\Game\Engine\Contracts;

interface QuestionSelectorInterface
{
    public function select(array $questions, int $count, array $excluded = []): array;
}
