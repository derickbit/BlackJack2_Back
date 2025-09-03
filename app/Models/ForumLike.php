<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForumLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'likeable_type',
        'likeable_id',
    ];

    /**
     * Relacionamento com o usuário que deu o like
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento polimórfico com o item curtido (tópico ou comentário)
     */
    public function likeable()
    {
        return $this->morphTo();
    }

    /**
     * Criar ou remover like (toggle)
     */
    public static function toggle($userId, $likeable)
    {
        $like = static::where([
            'user_id' => $userId,
            'likeable_type' => get_class($likeable),
            'likeable_id' => $likeable->id,
        ])->first();

        if ($like) {
            $like->delete();
            return false; // Like removido
        } else {
            static::create([
                'user_id' => $userId,
                'likeable_type' => get_class($likeable),
                'likeable_id' => $likeable->id,
            ]);
            return true; // Like adicionado
        }
    }
}
