<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atualizacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'conteudo',
        'imagem',
        'versao',
        'tipo',
        'ativo',
        'user_id'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relacionamento com o usuário (autor)
    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope para buscar apenas atualizações ativas
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    // Scope para ordenar por mais recentes
    public function scopeRecentes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Scope para filtrar por tipo
    public function scopeDoTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Accessor para retornar a URL completa da imagem
    public function getImagemUrlAttribute()
    {
        if ($this->imagem) {
            return asset('storage/' . $this->imagem);
        }
        return null;
    }
}
