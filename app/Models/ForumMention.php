<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class ForumMention extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mentioned_by_user_id',
        'mentionable_type',
        'mentionable_id',
        'lida',
    ];

    protected $casts = [
        'lida' => 'boolean',
    ];

    /**
     * Relacionamento com o usuário mencionado
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relacionamento com o usuário que fez a menção
     */
    public function mentionedBy()
    {
        return $this->belongsTo(User::class, 'mentioned_by_user_id');
    }

    /**
     * Relacionamento polimórfico com o item onde foi mencionado
     */
    public function mentionable()
    {
        return $this->morphTo();
    }

    /**
     * Marcar menção como lida
     */
    public function markAsRead()
    {
        $this->update(['lida' => true]);
    }

    /**
     * Criar menções a partir do conteúdo de texto
     */
    public static function createFromContent($content, $mentionable, $mentionedByUserId)
    {
        // Regex para encontrar menções no formato @username
        preg_match_all('/@(\w+)/', $content, $matches);

        if (!empty($matches[1])) {
            $usernames = array_unique($matches[1]);

            foreach ($usernames as $username) {
                $user = User::where('name', $username)->first();

                if ($user && $user->id !== $mentionedByUserId) {
                    static::firstOrCreate([
                        'user_id' => $user->id,
                        'mentioned_by_user_id' => $mentionedByUserId,
                        'mentionable_type' => get_class($mentionable),
                        'mentionable_id' => $mentionable->id,
                    ]);
                }
            }
        }
    }

    /**
     * Scopes
     */
    public function scopeNaoLidas($query)
    {
        return $query->where('lida', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
