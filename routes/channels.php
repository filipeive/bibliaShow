<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes();

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('game.{salaId}', function ($user, $salaId) {
    $sala = \App\Models\Sala::find($salaId);
    if (! $sala || $sala->status === 'terminada') {
        return false;
    }

    // Se o usuário está autenticado no painel/estúdio (Apresentador dono da sala ou Admin)
    if ($user) {
        if ($user->papel === 'admin_geral') {
            return ['id' => $user->id, 'nome' => $user->nome, 'tipo' => 'apresentador'];
        }
        if ((int) $user->igreja_id !== (int) $sala->igreja_id) {
            return false;
        }
        if ($user->papel === 'apresentador' && (int) $user->id !== (int) $sala->apresentador_id) {
            return false;
        }
        if (in_array($user->papel, ['apresentador', 'admin_igreja'], true)) {
            return ['id' => $user->id, 'nome' => $user->nome, 'tipo' => 'apresentador'];
        }

        return false;
    }

    // Para participantes: exige sessão ou código válido da sala
    $participanteId = session('participante_id_' . $salaId) ?? request()->cookie('part_' . $salaId);
    if ($participanteId) {
        $p = \App\Models\Participante::where('sala_id', $sala->id)->find($participanteId);
        if ($p) {
            return ['id' => $p->id, 'nome' => $p->nome, 'tipo' => 'participante'];
        }
    }

    return false;
});
