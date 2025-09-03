<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users'; // Confirme se o nome da tabela está correto

    protected $primaryKey = 'id'; // Chave primária da tabela

    public $incrementing = true; // Se a chave primária é auto-incrementada

    protected $keyType = 'int'; // Tipo da chave primária

    public $timestamps = true; // Se a tabela possui os campos created_at e updated_at

    protected $fillable = ['name', 'email', 'password', 'role']; // Colunas que podem ser atribuídas em massa

    protected $hidden = ['password','remember_token']; // Colunas que devem ser ocultadas

public function isAdmin()
{
    return $this->role === 'admin';
}

    protected function casts() {
        return [ 'email_verified_at' => 'datetime' , 'password' => 'hashed' ];
    }

    public function partidasJogador1()
    {
        return $this->hasMany(Partida::class, 'jogador1_id');
    }

    public function partidasJogador2()
    {
        return $this->hasMany(Partida::class, 'jogador2_id');
    }

    public function partidasVencidas()
    {
        return $this->hasMany(Partida::class, 'vencedor_id');
    }

    public function denunciasFeitas()
    {
        return $this->hasMany(Denuncia::class, 'denunciante_id');
    }

    public function denunciasRecebidas()
    {
        return $this->hasMany(Denuncia::class, 'denunciado_id');
    }

    public function atualizacoes()
    {
        return $this->hasMany(Atualizacao::class);
    }

    // Relacionamentos do Fórum
    public function forumTopics()
    {
        return $this->hasMany(ForumTopic::class);
    }

    public function forumComments()
    {
        return $this->hasMany(ForumComment::class);
    }

    public function forumLikes()
    {
        return $this->hasMany(ForumLike::class);
    }

    public function forumMentions()
    {
        return $this->hasMany(ForumMention::class, 'user_id');
    }

    public function forumMentionsMade()
    {
        return $this->hasMany(ForumMention::class, 'mentioned_by_user_id');
    }
}
