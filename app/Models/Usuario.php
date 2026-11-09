<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = [
        'igreja_id',
        'nome',
        'email',
        'password',
        'papel',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function igreja()
    {
        return $this->belongsTo(Igreja::class);
    }

    public function questoesSubmetidas()
    {
        return $this->hasMany(Questao::class, 'submetida_por');
    }

    public function questoesRevisadas()
    {
        return $this->hasMany(Questao::class, 'revisada_por');
    }

    public function salasApresentadas()
    {
        return $this->hasMany(Sala::class, 'apresentador_id');
    }

    public function podeEditarConteudos(): bool
    {
        return in_array($this->papel, ['admin_geral', 'admin_igreja', 'editor']);
    }

    public function podeApresentar(): bool
    {
        return in_array($this->papel, ['admin_geral', 'admin_igreja', 'apresentador']);
    }

    public function eAdminGeral(): bool
    {
        return $this->papel === 'admin_geral';
    }

    public function eAdminIgreja(): bool
    {
        return $this->papel === 'admin_igreja';
    }
}
