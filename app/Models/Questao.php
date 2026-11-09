<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'questoes';

    protected $fillable = [
        'igreja_id',
        'livro_id',
        'capitulo_id',
        'versiculo_de',
        'versiculo_ate',
        'texto',
        'opcoes',
        'resposta_correta',
        'explicacao',
        'contexto',
        'dificuldade_id',
        'categoria_id',
        'referencia_verificada',
        'aprovada',
        'submetida_por',
        'revisada_por',
        'revisada_em',
        'notas',
    ];

    protected $casts = [
        'referencia_verificada' => 'boolean',
        'aprovada' => 'boolean',
        'versiculo_de' => 'integer',
        'versiculo_ate' => 'integer',
        'opcoes' => 'array',
        'revisada_em' => 'datetime',
    ];

    public function igreja()
    {
        return $this->belongsTo(Igreja::class);
    }

    public function livro()
    {
        return $this->belongsTo(LivroBiblico::class);
    }

    public function capitulo()
    {
        return $this->belongsTo(Capitulo::class);
    }

    public function dificuldade()
    {
        return $this->belongsTo(NivelDificuldade::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function submetidaPor()
    {
        return $this->belongsTo(Usuario::class, 'submetida_por');
    }

    public function revisadaPor()
    {
        return $this->belongsTo(Usuario::class, 'revisada_por');
    }

    public function scopeAprovadas($query)
    {
        return $query->where('aprovada', true);
    }

    public function scopePorIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopePorDificuldade($query, $nivel)
    {
        return $query->whereHas('dificuldade', fn ($q) => $q->where('nivel', $nivel));
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopePorLivro($query, $livroId)
    {
        return $query->where('livro_id', $livroId);
    }
}
