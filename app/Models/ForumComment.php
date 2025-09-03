<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class ForumComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_topic_id',
        'user_id',
        'parent_id',
        'conteudo',
        'imagem',
        'editado',
    ];

    protected $casts = [
        'editado' => 'boolean',
    ];

    protected $appends = [
        'imagem_url',
        'total_likes',
        'total_respostas',
    ];

    /**
     * Relacionamento com o tópico
     */
    public function topic()
    {
        return $this->belongsTo(ForumTopic::class, 'forum_topic_id');
    }

    /**
     * Relacionamento com o usuário que fez o comentário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com comentário pai (para respostas)
     */
    public function parent()
    {
        return $this->belongsTo(ForumComment::class, 'parent_id');
    }

    /**
     * Relacionamento com respostas (comentários filhos)
     */
    public function replies()
    {
        return $this->hasMany(ForumComment::class, 'parent_id')->orderBy('created_at', 'asc');
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
            return Storage::disk('s3')->url($this->imagem);
        }
        return null;
    }

    /**
     * Accessor para total de likes
     */
    public function getTotalLikesAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * Accessor para total de respostas
     */
    public function getTotalRespostasAttribute(): int
    {
        return $this->replies()->count();
    }

    /**
     * Verificar se usuário deu like
     */
    public function isLikedByUser($userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Marcar como editado
     */
    public function markAsEdited()
    {
        $this->update(['editado' => true]);
    }

    /**
     * Scopes
     */
    public function scopeRootComments($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
