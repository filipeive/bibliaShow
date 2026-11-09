<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Igreja extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'slug',
        'configuracoes',
    ];

    protected $casts = [
        'configuracoes' => 'array',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }

    public function salas()
    {
        return $this->hasMany(Sala::class);
    }
}
