<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capitulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'livro_id',
        'numero',
    ];

    public function livro()
    {
        return $this->belongsTo(LivroBiblico::class);
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }
}
