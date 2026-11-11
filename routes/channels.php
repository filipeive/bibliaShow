<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes();

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('game.{salaId}', function ($user, $salaId) {
    $sala = \App\Models\Sala::find($salaId);
    if (!$sala || $sala->status === 'terminada') {
        return false;
    }

    // Se o usuário está autenticado no painel/estúdio (Apresentador ou Admin da Igreja)
    if ($user) {
        if ($user->papel === 'admin_geral' || (int) $user->igreja_id === (int) $sala->igreja_id) {
            return ['id' => $user->id, 'nome' => $user->nome, 'tipo' => 'apresentador'];
        }
        return false;
    }

    // Para participantes e telão: autoriza conexão se o código do participante na sessão pertencer a esta sala
    $participanteId = session('participante_id_' . $salaId) ?? request()->cookie('part_' . $salaId);
    if ($participanteId) {
        $p = \App\Models\Participante::where('sala_id', $sala->id)->find($participanteId);
        if ($p) {
            return ['id' => $p->id, 'nome' => $p->nome, 'tipo' => 'participante'];
        }
    }

    // Se for acesso direto autenticado para telão/projeção da sala existente
    return ['id' => null, 'nome' => 'espectador', 'salaId' => (int) $salaId, 'tipo' => 'projecao'];
});
