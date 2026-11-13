<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RegistrarEstatisticasRespostaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $modo,
        public readonly ?int $questaoId,
        public readonly bool $correta,
        public readonly int $tempoResposta = 0,
        public readonly ?int $salaId = null,
    ) {}

    public function handle(): void
    {
        if ($this->questaoId) {
            $cacheKey = "questao_stats_{$this->questaoId}";
            $stats = Cache::get($cacheKey, ['total' => 0, 'acertos' => 0, 'erros' => 0]);
            $stats['total']++;
            if ($this->correta) {
                $stats['acertos']++;
            } else {
                $stats['erros']++;
            }
            Cache::put($cacheKey, $stats, now()->addDays(7));
        }

        // Invalida ranking consolidado para refletir novas pontuações
        Cache::forget('api_v1_ranking');

        Log::info('biblia_show.resposta_processada', [
            'modo' => $this->modo,
            'questao_id' => $this->questaoId,
            'correta' => $this->correta,
            'tempo_resposta' => $this->tempoResposta,
            'sala_id' => $this->salaId,
        ]);
    }
}
