<?php

namespace App\Game\Engine\Selectors;

use App\Game\Engine\Contracts\QuestionSelectorInterface;

class RandomSelector implements QuestionSelectorInterface
{
    public function select(array $questions, int $count, array $excluded = []): array
    {
        $available = array_values(array_filter($questions, fn ($q) => !in_array($q['id'] ?? $q->id ?? null, $excluded)));

        shuffle($available);

        return array_slice($available, 0, $count);
    }
}
