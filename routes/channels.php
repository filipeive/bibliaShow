<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('game.{salaId}', function ($user, $salaId) {
    // Se o usuário está autenticado, verificar se pertence à igreja da sala
    if ($user) {
        $sala = \App\Models\Sala::find($salaId);
        if ($sala && $user->igreja_id === $sala->igreja_id) {
            return ['id' => $user->id, 'nome' => $user->nome];
        }
    }

    // Para participantes convidados (não autenticados), autorizar se a sala existir
    // O participante já foi validado ao entrar na sala via código
    $sala = \App\Models\Sala::find($salaId);
    if ($sala) {
        return ['id' => null, 'nome' => 'participante', 'salaId' => $salaId];
    }

    return false;
});
