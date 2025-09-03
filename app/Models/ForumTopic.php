<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class ForumTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titulo',
        'conteudo',
        'imagem',
        'fixado',
        'fechado',
        'visualizacoes',
    ];

    protected $casts = [
        'fixado' => 'boolean',
        'fechado' => 'boolean',
        'visualizacoes' => 'integer',
    ];

    protected $appends = [
        'imagem_url',
        'total_comentarios',
        'total_likes',
        'ultimo_comentario',
    ];

    /**
     * Relacionamento com o usuário que criou o tópico
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com os comentários do tópico
     */
    public function comments()
    {
        return $this->hasMany(ForumComment::class)->whereNull('parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Todos os comentários (incluindo respostas)
     */
    public function allComments()
    {
        return $this->hasMany(ForumComment::class);
    }

    /**
     * Relacionamento polimórfico com likes
     */
    public function likes()
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    /**
     * Relacionamento polimórfico com menções
     */
    public function mentions()
    {
        return $this->morphMany(ForumMention::class, 'mentionable');
    }

    /**
     * Accessor para URL da imagem
     */
    public function getImagemUrlAttribute(): ?string
    {
        if ($this->imagem) {
            if (app()->environment('production') && config('filesystems.disks.s3.bucket')) {
                // Em produção usa S3 - construir URL manualmente se necessário
                $bucket = config('filesystems.disks.s3.bucket');
                $region = config('filesystems.disks.s3.region');
                return "https://{$bucket}.s3.{$region}.amazonaws.com/{$this->imagem}";
            } else {
                // Localmente usa public - gera URL completa
                return asset('storage/' . $this->imagem);
            }
        }
        return null;
    }

    /**
     * Accessor para total de comentários
     */
    public function getTotalComentariosAttribute(): int
    {
        return $this->allComments()->count();
    }

    /**
     * Accessor para total de likes
     */
    public function getTotalLikesAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * Accessor para último comentário
     */
    public function getUltimoComentarioAttribute()
    {
        return $this->allComments()->with('user')->latest()->first();
    }

    /**
     * Incrementar visualizações
     */
    public function incrementViews()
    {
        $this->increment('visualizacoes');
    }

    /**
     * Verificar se usuário deu like
     */
    public function isLikedByUser($userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Scopes
     */
    public function scopeFixados($query)
    {
        return $query->where('fixado', true);
    }

    public function scopeAtivos($query)
    {
        return $query->where('fechado', false);
    }

    public function scopeFechados($query)
    {
        return $query->where('fechado', true);
    }
}
