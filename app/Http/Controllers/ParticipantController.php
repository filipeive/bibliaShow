<?php

namespace App\Http\Controllers;

use App\Events\ParticipanteEntrou;
use App\Events\RankingAtualizado;
use App\Models\Equipe;
use App\Models\Jogo;
use App\Models\Participante;
use App\Models\Sala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ParticipantController extends Controller
{
    public function entrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_acesso' => 'required|string|size:6',
            'nome' => 'required|string|max:100',
            'equipe_id' => 'nullable|exists:equipes,id',
            'equipe_nome' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $sala = Sala::where('codigo_acesso', strtoupper($validated['codigo_acesso']))
            ->where('status', '!=', 'terminada')
            ->first();

        if (!$sala) {
            return response()->json(['error' => 'Sala não encontrada ou encerrada.'], 404);
        }

        // Se especificou equipe que não existe, criar
        $equipeId = $validated['equipe_id'] ?? null;
        if (!$equipeId && $validated['equipe_nome']) {
            $equipe = Equipe::create([
                'sala_id' => $sala->id,
                'nome' => $validated['equipe_nome'],
                'cor' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
            ]);
            $equipeId = $equipe->id;
        }

        // Gerar código de acesso curto para o participante
        $participanteCodigo = Str::upper(Str::random(6));

        $participante = Participante::create([
            'sala_id' => $sala->id,
            'equipe_id' => $equipeId,
            'nome' => $validated['nome'],
            'codigo_acesso' => $participanteCodigo,
            'conectado' => true,
        ]);

        $participante->load('equipe');

        // Broadcast para todos na sala
        broadcast(new ParticipanteEntrou($sala->id, [
            'id' => $participante->id,
            'nome' => $participante->nome,
            'equipe' => $participante->equipe?->nome,
            'codigo_acesso' => $participante->codigo_acesso,
        ]))->toOthers();

        return response()->json([
            'sala' => [
                'id' => $sala->id,
                'nome' => $sala->nome,
                'codigo_acesso' => $sala->codigo_acesso,
                'status' => $sala->status,
            ],
            'participante' => [
                'id' => $participante->id,
                'nome' => $participante->nome,
                'codigo_acesso' => $participante->codigo_acesso,
                'equipe_id' => $participante->equipe_id,
                'equipe_nome' => $participante->equipe?->nome,
            ],
        ]);
    }

    public function entrarView($codigo)
    {
        $sala = Sala::where('codigo_acesso', strtoupper($codigo))
            ->where('status', '!=', 'terminada')
            ->first();

        if (!$sala) {
            abort(404, 'Sala não encontrada ou encerrada.');
        }

        return view('participante.entrar', compact('sala'));
    }

    public function mostrar(Request $request, Sala $sala)
    {
        $participantes = $sala->participantes()->conectados()->with('equipe')->get();
        $equipes = $sala->equipes()->get();

        return response()->json([
            'sala' => [
                'id' => $sala->id,
                'nome' => $sala->nome,
                'codigo_acesso' => $sala->codigo_acesso,
                'status' => $sala->status,
            ],
            'participantes' => $participantes,
            'equipes' => $equipes,
        ]);
    }

    public function responder(Request $request, Sala $sala)
    {
        $validator = Validator::make($request->all(), [
            'participante_id' => 'required|exists:participantes,id',
            'questao_id' => 'required|exists:questoes,id',
            'resposta' => 'required|string',
            'tempo_resposta' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $participante = Participante::find($validated['participante_id']);
        if (!$participante || $participante->sala_id !== $sala->id) {
            return response()->json(['error' => 'Participante não encontrado nesta sala.'], 404);
        }

        $questao = \App\Models\Questao::find($validated['questao_id']);
        $acertou = strtolower(trim($validated['resposta'])) === strtolower(trim($questao->resposta_correta));
        $pontos = $acertou ? 100 : 0;
        $tempo = $validated['tempo_resposta'];

        $jogo = $sala->jogoAtivo();
        if (!$jogo) {
            $jogo = Jogo::create([
                'sala_id' => $sala->id,
                'estado' => 'em_curso',
                'pontuacao_total' => 0,
            ]);
        }

        $resposta = \App\Models\Resposta::create([
            'jogo_id' => $jogo->id,
            'questao_id' => $validated['questao_id'],
            'participante_id' => $participante->id,
            'equipe_id' => $participante->equipe_id,
            'resposta' => $validated['resposta'],
            'correta' => $acertou,
            'tempo_resposta' => $tempo,
            'pontos_ganhos' => $pontos,
        ]);

        // Atualizar pontuação do participante
        if ($acertou) {
            $participante->increment('pontuacao_total', $pontos);
            $participante->refresh();
        }

        // Atualizar pontuação da equipe
        if ($participante->equipe_id && $acertou) {
            $participante->equipe->increment('pontuacao_total', $pontos);
        }

        // Atualizar pontuação total do jogo
        if ($acertou) {
            $jogo->increment('pontuacao_total', $pontos);
        }

        // Broadcast resposta
        broadcast(new RespostaEnviada(
            $sala->id,
            $participante->id,
            $participante->nome,
            $validated['questao_id'],
            $acertou,
            $pontos,
            $tempo
        ));

        // Calcular ranking
        $ranking = $sala->participantes()->conectados()->orderByDesc('pontuacao_total')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'nome' => $p->nome,
                'equipe' => $p->equipe?->nome,
                'pontuacao' => $p->pontuacao_total,
            ];
        });

        broadcast(new RankingAtualizado($sala->id, $ranking->values()->all()));

        return response()->json([
            'acertou' => $acertou,
            'pontos' => $pontos,
            'resposta_correta' => $questao->resposta_correta,
            'explicacao' => $questao->explicacao,
            'participante' => [
                'pontuacao_total' => $participante->pontuacao_total,
            ],
        ]);
    }

    public function sair(Request $request, Sala $sala, Participante $participante)
    {
        if ($participante->sala_id !== $sala->id) {
            return response()->json(['error' => 'Participante não encontrado nesta sala.'], 404);
        }

        $participante->update(['conectado' => false]);

        broadcast(new ParticipanteEntrou($sala->id, [
            'id' => $participante->id,
            'nome' => $participante->nome,
            'saiu' => true,
        ]));

        return response()->json(['success' => true]);
    }
}
