<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categorias';

    protected $fillable = [
        'igreja_id',
        'nome',
        'slug',
        'descricao',
    ];

    public function igreja()
    {
        return $this->belongsTo(Igreja::class);
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }
}
