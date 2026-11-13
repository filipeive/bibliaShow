<?php

namespace App\Services;

use App\Game\Engine\GameEngine;
use App\Models\Jogo;
use App\Models\Participante;
use App\Models\Questao;
use App\Models\Resposta;
use App\Models\Sala;
use Illuminate\Support\Facades\Cache;

class ArenaCacheService
{
    public const SNAPSHOT_TTL_SECONDS = 10;

    /**
     * Retorna o snapshot em cache da sala para evitar sobrecarga no MySQL durante polling.
     */
    public static function getSalaSnapshot(Sala $sala): array
    {
        $key = self::snapshotKey($sala->id);

        return Cache::remember($key, self::SNAPSHOT_TTL_SECONDS, function () use ($sala) {
            return self::buildSalaSnapshot($sala);
        });
    }

    /**
     * Invalida o snapshot da sala após qualquer alteração de estado (nova pergunta, resposta, ajuda, etc.).
     */
    public static function invalidateSala(int $salaId): void
    {
        Cache::forget(self::snapshotKey($salaId));
    }

    public static function snapshotKey(int $salaId): string
    {
        return "arena_snapshot_sala_{$salaId}";
    }

    private static function buildSalaSnapshot(Sala $sala): array
    {
        $salaFresh = Sala::find($sala->id) ?? $sala;
        $participantes = $salaFresh->participantes()->conectados()->with('equipe')->orderByDesc('pontuacao_total')->get();
        $equipes = $salaFresh->equipes()->get();

        $jogo = Jogo::where('sala_id', $salaFresh->id)
            ->whereIn('estado', ['em_curso', 'pausado', 'aguardando'])
            ->latest('id')
            ->first()
            ?? Jogo::where('sala_id', $salaFresh->id)->latest('id')->first();

        $estadoJogo = $jogo?->estado ?? ($salaFresh->status === 'terminada' ? 'terminado' : 'aguardando');
        $perguntaPublica = null;
        $numeroPergunta = 1;
        $totalPerguntas = 24;
        $faseAtual = 1;
        $placarMilhao = ['errar' => 0, 'parar' => 0, 'acertar' => 10, 'valor_pergunta' => 10, 'fase' => 1];
        $ajudasAtivas = [
            'opcoes_eliminadas' => [],
            'dica_biblica' => null,
            'dica_convidado' => null,
            'gabarito_revelado' => false,
        ];
        $tempoRestante = 60;

        if ($jogo && $jogo->dados_motor) {
            $engine = GameEngine::desserializarSeguro($jogo->dados_motor);
            if ($engine instanceof GameEngine) {
                $st = $engine->getState();
                $estadoJogo = $st['estado'];
                $numeroPergunta = $st['numero_pergunta'] ?? 1;
                $totalPerguntas = $st['total_perguntas'] ?? 24;
                $faseAtual = $st['fase_atual'] ?? 1;
                $placarMilhao = $st['placar_milhao'] ?? $placarMilhao;
                $tempoRestante = $st['tempo_restante'] ?? 60;
                $ajudasMotor = $st['ajudas'] ?? [];

                $gabaritoRevelado = ! empty($ajudasMotor['gabarito_revelado']);
                $ajudasAtivas = [
                    'opcoes_eliminadas' => $ajudasMotor['opcoes_eliminadas'] ?? [],
                    'dica_biblica' => $ajudasMotor['dica_biblica'] ?? null,
                    'dica_convidado' => $ajudasMotor['dica_convidado'] ?? null,
                    'gabarito_revelado' => $gabaritoRevelado,
                ];

                if (! empty($st['pergunta_atual'])) {
                    $q = $st['pergunta_atual'];
                    $opcoes = $q['opcoes'] ?? [];
                    if (is_string($opcoes)) {
                        $opcoes = json_decode($opcoes, true) ?: [];
                    }

                    $qId = $q['id'] ?? null;
                    if ($qId && ! Questao::where('id', $qId)->exists()) {
                        $qReal = null;
                        if (! empty($q['codigo_classico'])) {
                            $qReal = Questao::where('codigo_classico', $q['codigo_classico'])->first();
                        }
                        if (! $qReal && ! empty($q['texto'])) {
                            $qReal = Questao::where('texto', $q['texto'])->first();
                        }
                        if ($qReal) {
                            $qId = $qReal->id;
                        }
                    }

                    $perguntaPublica = [
                        'id' => $qId,
                        'texto' => $q['texto'] ?? '',
                        'opcoes' => $opcoes,
                        'livro_nome' => $q['livro_nome'] ?? null,
                        'dificuldade_nome' => $q['dificuldade_nome'] ?? '1ª Fase',
                        'dificuldade_nivel' => $q['dificuldade_nivel'] ?? $faseAtual,
                        'categoria_nome' => $q['categoria_nome'] ?? null,
                        'resposta_correta' => $gabaritoRevelado ? ($q['resposta_correta'] ?? null) : null,
                        'referencia_biblica' => $gabaritoRevelado ? ($q['referencia_biblica'] ?? null) : null,
                    ];
                }
            }
        }

        $respostasRodada = [];
        if ($jogo && $perguntaPublica && ! empty($perguntaPublica['id'])) {
            $todasRespostas = Resposta::where('jogo_id', $jogo->id)
                ->where('questao_id', $perguntaPublica['id'])
                ->get();

            $letras = ['A', 'B', 'C', 'D'];
            $opcoesQ = $perguntaPublica['opcoes'] ?? [];
            $gabaritoRevelado = ! empty($ajudasAtivas['gabarito_revelado']);

            foreach ($todasRespostas as $r) {
                $letra = null;
                $idxEscolhido = null;
                foreach ($opcoesQ as $idx => $opTxt) {
                    if (mb_strtolower(trim((string) $opTxt), 'UTF-8') === mb_strtolower(trim((string) $r->resposta), 'UTF-8')) {
                        $idxEscolhido = (int) $idx;
                        $letra = $letras[$idx] ?? (string) ($idx + 1);
                        break;
                    }
                }
                if ($letra === null && is_numeric($r->resposta)) {
                    $idxNum = (int) $r->resposta;
                    $idxEscolhido = $idxNum;
                    $letra = $letras[$idxNum] ?? '?';
                }

                $respostasRodada[$r->participante_id] = [
                    'participante_id' => $r->participante_id,
                    'resposta' => $r->resposta,
                    'letra' => $letra ?? '?',
                    'indice' => $idxEscolhido,
                    'correta' => $gabaritoRevelado ? (bool) $r->correta : null,
                    'tempo' => $r->tempo_resposta,
                    'pontos' => $gabaritoRevelado ? $r->pontos_ganhos : 0,
                ];
            }
        }

        $ranking = $participantes->map(fn ($p) => [
            'id' => $p->id,
            'nome' => $p->nome,
            'equipe' => $p->equipe?->nome,
            'pontuacao' => $p->pontuacao_total,
        ])->values()->all();

        $ajudasAtivas['dicas_restantes'] = 3;
        $ajudasAtivas['cartas_restantes'] = 1;
        $ajudasAtivas['convidados_restantes'] = 3;

        $payload = [
            'sala' => [
                'id' => $salaFresh->id,
                'nome' => $salaFresh->nome,
                'codigo_acesso' => $salaFresh->codigo_acesso,
                'status' => $salaFresh->status,
                'premio_primeiro_lugar' => $salaFresh->premio_primeiro_lugar,
                'premio_segundo_lugar' => $salaFresh->premio_segundo_lugar,
            ],
            'estado' => $estadoJogo,
            'numero_pergunta' => $numeroPergunta,
            'total_perguntas' => $totalPerguntas,
            'fase_atual' => $faseAtual,
            'placar_milhao' => $placarMilhao,
            'ajudas' => $ajudasAtivas,
            'tempo_restante' => $tempoRestante,
            'pergunta_atual' => $perguntaPublica,
            'respostas_rodada' => $respostasRodada,
            'participantes' => $participantes->toArray(),
            'equipes' => $equipes->toArray(),
            'ranking' => $ranking,
        ];

        $payload['version'] = substr(md5(json_encode([
            $estadoJogo,
            $numeroPergunta,
            $ajudasAtivas['gabarito_revelado'] ?? false,
            $ajudasAtivas['opcoes_eliminadas'] ?? [],
            count($respostasRodada),
            count($participantes),
            $ranking,
        ])), 0, 12);

        return $payload;
    }

    /**
     * Aplica os dados individuais do participante (sem consultar o banco quando já está no snapshot).
     */
    public static function aplicarContextoParticipante(array $snapshot, ?int $partId): array
    {
        $jaRespondeu = false;
        if ($partId && ! empty($snapshot['pergunta_atual'])) {
            $respostasRodada = $snapshot['respostas_rodada'] ?? [];
            $jaRespondeu = isset($respostasRodada[$partId]);

            $partData = collect($snapshot['participantes'] ?? [])->firstWhere('id', $partId);
            $partAjudas = is_array($partData) ? ($partData['ajudas_usadas'] ?? []) : (Participante::find($partId)?->ajudas_usadas ?? []);

            $snapshot['ajudas']['dicas_restantes'] = max(0, 3 - ($partAjudas['dicas_usadas'] ?? 0));
            $snapshot['ajudas']['cartas_restantes'] = max(0, 1 - ($partAjudas['cartas_usadas'] ?? 0));
            $snapshot['ajudas']['convidados_restantes'] = max(0, 3 - ($partAjudas['convidados_usados'] ?? 0));

            $pIdKey = $snapshot['pergunta_atual']['id'] ?? $snapshot['numero_pergunta'];
            $hist = $partAjudas['historico_por_pergunta'][$pIdKey] ?? [];
            if (! empty($hist['dica_biblica'])) {
                $snapshot['ajudas']['dica_biblica'] = $hist['dica_biblica'];
            }
            if (! empty($hist['dica_convidado'])) {
                $snapshot['ajudas']['dica_convidado'] = $hist['dica_convidado'];
            }
            if (! empty($hist['opcoes_eliminadas'])) {
                $snapshot['ajudas']['opcoes_eliminadas'] = array_values(array_unique(array_merge(
                    $snapshot['ajudas']['opcoes_eliminadas'] ?? [],
                    $hist['opcoes_eliminadas']
                )));
            }
        }

        $snapshot['ja_respondeu'] = $jaRespondeu;

        return $snapshot;
    }
}
