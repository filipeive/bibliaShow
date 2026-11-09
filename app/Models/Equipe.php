<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sala_id',
        'nome',
        'cor',
        'pontuacao_total',
    ];

    protected $casts = [
        'pontuacao_total' => 'integer',
    ];

    public function sala()
    {
        return $this->belongsTo(Sala::class);
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class);
    }

    public function respostas()
    {
        return $this->hasMany(Resposta::class);
    }
}
