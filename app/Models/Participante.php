<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participante extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sala_id',
        'equipe_id',
        'nome',
        'codigo_acesso',
        'conectado',
        'pontuacao_total',
    ];

    protected $casts = [
        'conectado' => 'boolean',
        'pontuacao_total' => 'integer',
    ];

    public function sala()
    {
        return $this->belongsTo(Sala::class);
    }

    public function equipe()
    {
        return $this->belongsTo(Equipe::class);
    }

    public function respostas()
    {
        return $this->hasMany(Resposta::class);
    }

    public function scopeConectados($query)
    {
        return $query->where('conectado', true);
    }
}
