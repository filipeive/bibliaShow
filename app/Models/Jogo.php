<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jogo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sala_id',
        'estado',
        'pergunta_atual_id',
        'pontuacao_total',
    ];

    protected $casts = [
        'pontuacao_total' => 'integer',
    ];

    public function sala()
    {
        return $this->belongsTo(Sala::class);
    }

    public function perguntaAtual()
    {
        return $this->belongsTo(Questao::class, 'pergunta_atual_id');
    }

    public function respostas()
    {
        return $this->hasMany(Resposta::class);
    }
}
