<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivroBiblico extends Model
{
    use HasFactory;

    protected $table = 'livros_biblicos';

    protected $fillable = [
        'abreviacao',
        'nome',
        'testamento',
        'codigo_osis',
    ];

    public function capitulos()
    {
        return $this->hasMany(Capitulo::class);
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }
}
