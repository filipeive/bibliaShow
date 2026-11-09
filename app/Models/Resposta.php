<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resposta extends Model
{
    use HasFactory;

    protected $fillable = [
        'jogo_id',
        'questao_id',
        'participante_id',
        'equipe_id',
        'resposta',
        'correta',
        'tempo_resposta',
        'pontos_ganhos',
    ];

    protected $casts = [
        'correta' => 'boolean',
        'tempo_resposta' => 'integer',
        'pontos_ganhos' => 'integer',
    ];

    public function jogo()
    {
        return $this->belongsTo(Jogo::class);
    }

    public function questao()
    {
        return $this->belongsTo(Questao::class);
    }

    public function participante()
    {
        return $this->belongsTo(Participante::class);
    }

    public function equipe()
    {
        return $this->belongsTo(Equipe::class);
    }
}
