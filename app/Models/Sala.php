<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sala extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'igreja_id',
        'codigo_acesso',
        'nome',
        'apresentador_id',
        'status',
    ];

    public function igreja()
    {
        return $this->belongsTo(Igreja::class);
    }

    public function apresentador()
    {
        return $this->belongsTo(Usuario::class, 'apresentador_id');
    }

    public function jogos()
    {
        return $this->hasMany(Jogo::class);
    }

    public function jogosAtivos()
    {
        return $this->hasMany(Jogo::class)->whereIn('estado', ['aguardando', 'em_curso', 'pausado']);
    }
}
