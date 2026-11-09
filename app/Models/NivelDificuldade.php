<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelDificuldade extends Model
{
    use HasFactory;

    protected $table = 'niveis_dificuldade';

    protected $fillable = [
        'nome',
        'nivel',
    ];

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }
}
